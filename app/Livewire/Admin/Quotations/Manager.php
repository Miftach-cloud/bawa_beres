<?php

namespace App\Livewire\Admin\Quotations;

use App\Actions\Quotations\AcceptQuotation;
use App\Actions\Quotations\CreateQuotation;
use App\Actions\Quotations\CreateQuotationRevision;
use App\Actions\Quotations\RejectQuotation;
use App\Actions\Quotations\SendQuotation;
use App\Models\Order;
use App\Models\Quotation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Manager extends Component
{
    public Order $order;

    // Modal state
    public bool $showQuotationModal = false;

    public bool $isRevision = false;

    public ?int $baseQuotationId = null;

    // Form inputs
    public array $items = [];

    public float|string $discount = 0;

    public float|string $tax = 0;

    public string $notes = '';

    public ?string $validUntil = null;

    // Rejection modal
    public bool $showRejectModal = false;

    public ?int $rejectingQuotationId = null;

    public string $rejectionReason = '';

    protected function rules(): array
    {
        return [
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'validUntil' => 'nullable|date',
        ];
    }

    public function mount(Order $order): void
    {
        $this->order = $order;
        $this->addItemRow();
    }

    public function addItemRow(): void
    {
        $this->items[] = [
            'name' => '',
            'description' => '',
            'quantity' => 1,
            'unit_price' => 0,
        ];
    }

    public function removeItemRow(int $index): void
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
        if (empty($this->items)) {
            $this->addItemRow();
        }
    }

    public function openCreateModal(): void
    {
        Gate::authorize('manage-quotations');

        $this->isRevision = false;
        $this->baseQuotationId = null;
        $this->items = [];

        // Pre-fill default service base price line item
        $this->items[] = [
            'name' => "Biaya Pokok Layanan ({$this->order->service->name})",
            'description' => 'Tarif dasar layanan platform',
            'quantity' => 1,
            'unit_price' => (float) $this->order->service->base_price,
        ];

        $this->discount = 0;
        $this->tax = 0;
        $this->notes = '';
        $this->validUntil = now()->addDays(7)->format('Y-m-d');
        $this->resetValidation();
        $this->showQuotationModal = true;
    }

    public function openRevisionModal(Quotation $quotation): void
    {
        Gate::authorize('manage-quotations');

        $this->isRevision = true;
        $this->baseQuotationId = $quotation->id;
        $this->items = [];

        foreach ($quotation->items as $item) {
            $this->items[] = [
                'name' => $item->name,
                'description' => $item->description ?? '',
                'quantity' => $item->quantity,
                'unit_price' => (float) $item->unit_price,
            ];
        }

        $this->discount = (float) $quotation->discount;
        $this->tax = (float) $quotation->tax;
        $this->notes = $quotation->notes ?? '';
        $this->validUntil = now()->addDays(7)->format('Y-m-d');
        $this->resetValidation();
        $this->showQuotationModal = true;
    }

    public function closeModal(): void
    {
        $this->showQuotationModal = false;
        $this->resetValidation();
    }

    #[Computed]
    public function calculateSubtotal(): float
    {
        $sub = 0;
        foreach ($this->items as $item) {
            $sub += ((int) ($item['quantity'] ?? 1) * (float) ($item['unit_price'] ?? 0));
        }

        return $sub;
    }

    #[Computed]
    public function calculateTotal(): float
    {
        return max(0, $this->calculateSubtotal() - (float) $this->discount + (float) $this->tax);
    }

    public function saveQuotation(CreateQuotation $createAction, CreateQuotationRevision $revisionAction): void
    {
        Gate::authorize('manage-quotations');

        $this->validate();

        $payload = [
            'items' => $this->items,
            'discount' => (float) $this->discount,
            'tax' => (float) $this->tax,
            'notes' => $this->notes ?: null,
            'valid_until' => $this->validUntil,
        ];

        if ($this->isRevision) {
            $quotation = $revisionAction->execute($this->order, $payload, Auth::user());
            session()->flash('quotation_message', "Revisi penawaran #{$quotation->quotation_number} (v{$quotation->version}) berhasil dibuat.");
        } else {
            $quotation = $createAction->execute($this->order, $payload, Auth::user());
            session()->flash('quotation_message', "Penawaran harga baru #{$quotation->quotation_number} berhasil dibuat.");
        }

        $this->showQuotationModal = false;
        $this->order->refresh();
    }

    public function send(int $quotationId, SendQuotation $sendAction): void
    {
        Gate::authorize('manage-quotations');

        $quotation = Quotation::findOrFail($quotationId);
        $sendAction->execute($quotation, Auth::user());

        session()->flash('quotation_message', "Penawaran #{$quotation->quotation_number} telah dikirim ke pelanggan.");
        $this->order->refresh();
    }

    public function accept(int $quotationId, AcceptQuotation $acceptAction): void
    {
        Gate::authorize('manage-quotations');

        $quotation = Quotation::findOrFail($quotationId);
        $acceptAction->execute($quotation, Auth::user());

        session()->flash('quotation_message', "Penawaran #{$quotation->quotation_number} telah disetujui. Order diperbarui menjadi CONFIRMED.");
        $this->order->refresh();
    }

    public function openRejectModal(int $quotationId): void
    {
        $this->rejectingQuotationId = $quotationId;
        $this->rejectionReason = '';
        $this->showRejectModal = true;
    }

    public function closeRejectModal(): void
    {
        $this->showRejectModal = false;
        $this->rejectingQuotationId = null;
        $this->rejectionReason = '';
    }

    public function confirmReject(RejectQuotation $rejectAction): void
    {
        Gate::authorize('manage-quotations');

        $this->validate([
            'rejectionReason' => 'required|string|min:3|max:500',
        ]);

        $quotation = Quotation::findOrFail($this->rejectingQuotationId);
        $rejectAction->execute($quotation, $this->rejectionReason, Auth::user());

        session()->flash('quotation_message', "Penawaran #{$quotation->quotation_number} ditandai sebagai Ditolak / Perlu Revisi.");
        $this->showRejectModal = false;
        $this->order->refresh();
    }

    public function render()
    {
        $quotations = $this->order->quotations()->with(['items', 'creator'])->get();

        return view('livewire.admin.quotations.manager', [
            'quotations' => $quotations,
        ]);
    }
}
