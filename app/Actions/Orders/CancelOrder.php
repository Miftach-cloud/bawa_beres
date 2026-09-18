<?php

namespace App\Actions\Orders;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;

class CancelOrder
{
    public function __construct(
        protected ChangeOrderStatus $changeOrderStatus
    ) {}

    /**
     * Cancel the order with cancellation reason
     */
    public function execute(Order $order, string $reason, ?User $cancelledBy = null): Order
    {
        return $this->changeOrderStatus->execute(
            $order,
            OrderStatus::CANCELLED,
            "Pembatalan pesanan: {$reason}",
            $cancelledBy
        );
    }
}
