<?php

namespace App\Actions\Payments;

use App\Actions\Orders\ChangeOrderStatus;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class VerifyPayment
{
    public function __construct(
        protected ChangeOrderStatus $changeOrderStatus
    ) {}

    /**
     * Verify payment proof, mark as PAID, and advance order status if fully settled
     */
    public function execute(Payment $payment, User $verifier, ?string $notes = null): Payment
    {
        return DB::transaction(function () use ($payment, $verifier, $notes) {
            $payment->update([
                'status' => PaymentStatus::PAID,
                'verified_at' => now(),
                'verified_by' => $verifier->id,
                'notes' => $notes ? ($payment->notes ? "{$payment->notes}\n[Verifikasi: {$notes}]" : $notes) : $payment->notes,
            ]);

            $order = $payment->order;

            // Check if order is now fully paid and currently in CONFIRMED state
            if ($order->isFullyPaid()) {
                if ($order->status === OrderStatus::CONFIRMED && $order->status->canTransitionTo(OrderStatus::PAID)) {
                    $this->changeOrderStatus->execute(
                        $order,
                        OrderStatus::PAID,
                        "Pembayaran order #{$order->order_code} telah lunas dan diverifikasi oleh {$verifier->name}.",
                        $verifier
                    );
                }
            }

            return $payment->fresh(['order', 'verifier']);
        });
    }
}
