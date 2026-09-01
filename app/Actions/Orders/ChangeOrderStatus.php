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

            $freshOrder = $order->fresh(['statusHistories', 'customer', 'service']);

            if (in_array($newStatus, [OrderStatus::CONFIRMED, OrderStatus::PAID], true) && !in_array($fromStatus, [OrderStatus::CONFIRMED, OrderStatus::PAID], true)) {
                \App\Events\OrderConfirmed::dispatch($freshOrder);
            }

            if ($newStatus === OrderStatus::COMPLETED && $fromStatus !== OrderStatus::COMPLETED) {
                \App\Events\OrderCompleted::dispatch($freshOrder);
            }

            return $freshOrder;
        });
    }
}

