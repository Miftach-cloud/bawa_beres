<?php

namespace App\Livewire\Admin\Payments;

use App\Actions\Payments\RejectPayment;
use App\Actions\Payments\VerifyPayment;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Pusat Pembayaran — Admin Bawa Beres')]
class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public string $statusFilter = '';

    public string $methodFilter = '';

    public string $dateFilter = 'all';

    public ?string $startDate = null;

    public ?string $endDate = null;

    // Proof Preview
    public bool $showProofModal = false;

    public ?string $previewProofUrl = null;

    // Rejection Modal
    public bool $showRejectModal = false;

    public ?int $selectedPaymentId = null;

    public string $rejectionReason = '';

    public function mount(): void
    {
        Gate::authorize('manage-payments');
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingMethodFilter(): void
    {
        $this->resetPage();
    }

    public function updatingDateFilter(): void
    {
        $this->resetPage();
    }

    public function verify(int $paymentId, VerifyPayment $verifyPayment): void
    {
        Gate::authorize('manage-payments');

        $payment = Payment::findOrFail($paymentId);
        $verifyPayment->execute($payment, Auth::user());

        session()->flash('message', "Pembayaran #{$payment->payment_number} telah diverifikasi (PAID).");
    }

    public function openRejectModal(int $paymentId): void
    {
        $this->selectedPaymentId = $paymentId;
        $this->rejectionReason = '';
        $this->showRejectModal = true;
    }

    public function closeRejectModal(): void
    {
        $this->showRejectModal = false;
        $this->selectedPaymentId = null;
        $this->rejectionReason = '';
    }

    public function confirmReject(RejectPayment $rejectPayment): void
    {
        Gate::authorize('manage-payments');

        $this->validate([
            'rejectionReason' => 'required|string|min:3|max:500',
        ]);

        $payment = Payment::findOrFail($this->selectedPaymentId);
        $rejectPayment->execute($payment, $this->rejectionReason, Auth::user());

        $this->showRejectModal = false;
        session()->flash('message', "Pembayaran #{$payment->payment_number} telah ditolak.");
    }

    public function viewProof(string $url): void
    {
        $this->previewProofUrl = $url;
        $this->showProofModal = true;
    }

    public function closeProofModal(): void
    {
        $this->showProofModal = false;
        $this->previewProofUrl = null;
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'statusFilter', 'methodFilter', 'dateFilter', 'startDate', 'endDate']);
        $this->resetPage();
    }

    public function render()
    {
        $query = Payment::query()
            ->with(['order.customer', 'verifier']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('payment_number', 'LIKE', "%{$this->search}%")
                    ->orWhereHas('order', function ($oq) {
                        $oq->where('order_code', 'LIKE', "%{$this->search}%")
                            ->orWhereHas('customer', function ($cq) {
                                $cq->where('name', 'LIKE', "%{$this->search}%")
                                    ->orWhere('phone', 'LIKE', "%{$this->search}%");
                            });
                    });
            });
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->methodFilter) {
            $query->where('method', $this->methodFilter);
        }

        if ($this->dateFilter === 'today') {
            $query->whereDate('created_at', Carbon::today());
        } elseif ($this->dateFilter === 'this_week') {
            $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        } elseif ($this->dateFilter === 'this_month') {
            $query->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]);
        } elseif ($this->dateFilter === 'custom' && $this->startDate && $this->endDate) {
            $query->whereBetween('created_at', [
                Carbon::parse($this->startDate)->startOfDay(),
                Carbon::parse($this->endDate)->endOfDay(),
            ]);
        }

        $payments = $query->latest('id')->paginate(15);

        return view('livewire.admin.payments.index', [
            'payments' => $payments,
            'statuses' => PaymentStatus::cases(),
            'methods' => PaymentMethod::cases(),
            'pendingCount' => Payment::where('status', PaymentStatus::WAITING_VERIFICATION->value)->count(),
        ]);
    }
}
