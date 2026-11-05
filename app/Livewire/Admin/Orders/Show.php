<?php

namespace App\Livewire\Admin\Orders;

use App\Actions\Orders\CancelOrder;
use App\Actions\Orders\ChangeOrderStatus;
use App\Actions\Orders\UpdateOrder;
use App\Enums\OrderStatus;
use App\Exceptions\InvalidOrderStateTransitionException;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Detail Pesanan — Admin Bawa Beres')]
class Show extends Component
{
    public Order $order;

    // Status transition modal
    public bool $showTransitionModal = false;

    public ?string $targetStatus = null;

    public string $transitionNotes = '';

    // Cancel modal
    public bool $showCancelModal = false;

    public string $cancelReason = '';

    // Quick notes & amount edit
    public string $adminNotes = '';

    public float|string $totalAmount = 0;

    public function mount(Order $order): void
    {
        Gate::authorize('manage-orders');

        $this->order = $order->loadMissing([
            'customer',
            'service',
            'items',
            'addresses',
            'pickupAddress',
            'destinationAddress',
            'statusHistories.user',
        ]);

        $this->adminNotes = $this->order->admin_notes ?? '';
        $this->totalAmount = (float) $this->order->total_amount;
    }

    public function openTransitionModal(string $status): void
    {
        $this->targetStatus = $status;
        $this->transitionNotes = '';
        $this->showTransitionModal = true;
    }

    public function closeTransitionModal(): void
    {
        $this->showTransitionModal = false;
        $this->targetStatus = null;
        $this->transitionNotes = '';
    }

    public function confirmTransition(ChangeOrderStatus $changeOrderStatus): void
    {
        Gate::authorize('manage-orders');

        if (! $this->targetStatus) {
            return;
        }

        $newStatus = OrderStatus::from($this->targetStatus);

        try {
            $this->order = $changeOrderStatus->execute(
                $this->order,
                $newStatus,
                $this->transitionNotes ?: null,
                Auth::user()
            );

            session()->flash('message', "Status pesanan berhasil diubah menjadi {$newStatus->label()}.");
            $this->showTransitionModal = false;
        } catch (InvalidOrderStateTransitionException $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function openCancelModal(): void
    {
        $this->cancelReason = '';
        $this->showCancelModal = true;
    }

    public function closeCancelModal(): void
    {
        $this->showCancelModal = false;
        $this->cancelReason = '';
    }

    public function confirmCancel(CancelOrder $cancelOrder): void
    {
        Gate::authorize('manage-orders');

        $this->validate([
            'cancelReason' => 'required|string|min:3|max:500',
        ], [
            'cancelReason.required' => 'Alasan pembatalan wajib diisi.',
        ]);

        try {
            $this->order = $cancelOrder->execute(
                $this->order,
                $this->cancelReason,
                Auth::user()
            );

            session()->flash('message', 'Pesanan telah berhasil dibatalkan.');
            $this->showCancelModal = false;
        } catch (InvalidOrderStateTransitionException $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function saveAdminNotes(UpdateOrder $updateOrder): void
    {
        Gate::authorize('manage-orders');

        $this->order = $updateOrder->execute($this->order, [
            'admin_notes' => $this->adminNotes,
            'total_amount' => (float) $this->totalAmount,
        ]);

        session()->flash('message', 'Catatan admin dan tarif berhasil disimpan.');
    }

    public function render()
    {
        $allowedNextStatuses = $this->order->status->allowedTransitions();

        return view('livewire.admin.orders.show', [
            'allowedNextStatuses' => $allowedNextStatuses,
        ]);
    }
}
