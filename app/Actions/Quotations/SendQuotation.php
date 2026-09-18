<?php

namespace App\Actions\Quotations;

use App\Actions\Orders\ChangeOrderStatus;
use App\Enums\OrderStatus;
use App\Enums\QuotationStatus;
use App\Models\Quotation;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class SendQuotation
{
    public function __construct(
        protected ChangeOrderStatus $changeOrderStatus
    ) {}

    /**
     * Send quotation to customer and advance order status to QUOTED if pending
     */
    public function execute(Quotation $quotation, ?User $sender = null): Quotation
    {
        return DB::transaction(function () use ($quotation, $sender) {
            $quotation->update([
                'status' => QuotationStatus::SENT,
                'sent_at' => now(),
            ]);

            $order = $quotation->order;

            // If order is still pending review, transition it to QUOTED
            if ($order->status === OrderStatus::PENDING_REVIEW || $order->status === OrderStatus::DRAFT) {
                if ($order->status->canTransitionTo(OrderStatus::QUOTED)) {
                    $this->changeOrderStatus->execute(
                        $order,
                        OrderStatus::QUOTED,
                        "Penawaran harga #{$quotation->quotation_number} (v{$quotation->version}) sebesar Rp ".number_format($quotation->total_amount, 0, ',', '.').' dikirimkan ke pelanggan.',
                        $sender
                    );
                }
            }

            return $quotation->fresh(['order', 'items']);
        });
    }
}
