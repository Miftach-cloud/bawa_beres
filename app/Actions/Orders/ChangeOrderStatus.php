<?php

namespace App\Actions\Orders;

use App\Enums\OrderStatus;
use App\Exceptions\InvalidOrderStateTransitionException;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ChangeOrderStatus
{
    /**
     * Execute the status change with state transition validation
     *
     * @throws InvalidOrderStateTransitionException
     */
    public function execute(Order $order, OrderStatus $newStatus, ?string $notes = null, ?User $changedBy = null): Order
    {
        if (!$order->status->canTransitionTo($newStatus)) {
            throw new InvalidOrderStateTransitionException($order->status, $newStatus);
        }

        return DB::transaction(function () use ($order, $newStatus, $notes, $changedBy) {
            $fromStatus = $order->status;

            $order->update([
                'status' => $newStatus,
            ]);

            $order->statusHistories()->create([
                'from_status' => $fromStatus->value,
                'to_status' => $newStatus->value,
                'changed_by' => $changedBy?->id,
                'notes' => $notes,
            ]);

            return $order->fresh(['statusHistories', 'customer', 'service']);
        });
    }
}
