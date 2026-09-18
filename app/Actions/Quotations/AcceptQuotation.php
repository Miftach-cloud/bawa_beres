<?php

namespace App\Actions\Quotations;

use App\Actions\Orders\ChangeOrderStatus;
use App\Enums\OrderStatus;
use App\Enums\QuotationStatus;
use App\Models\Quotation;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AcceptQuotation
{
    public function __construct(
        protected ChangeOrderStatus $changeOrderStatus
    ) {}

    /**
     * Accept quotation, mark older versions expired, sync order total, and advance order to CONFIRMED
     */
    public function execute(Quotation $quotation, ?User $actor = null): Quotation
    {
        return DB::transaction(function () use ($quotation, $actor) {
            $quotation->update([
                'status' => QuotationStatus::ACCEPTED,
                'responded_at' => now(),
            ]);

            $order = $quotation->order;

            // Mark other active/draft quotations for this order as EXPIRED
            $order->quotations()
                ->where('id', '!=', $quotation->id)
                ->whereIn('status', [QuotationStatus::DRAFT->value, QuotationStatus::SENT->value])
                ->update(['status' => QuotationStatus::EXPIRED->value]);

            // Sync total amount on order
            $order->update([
                'total_amount' => $quotation->total_amount,
            ]);

            // Advance order to CONFIRMED if valid transition
            if ($order->status->canTransitionTo(OrderStatus::CONFIRMED)) {
                $this->changeOrderStatus->execute(
                    $order,
                    OrderStatus::CONFIRMED,
                    "Pelanggan menyetujui penawaran harga #{$quotation->quotation_number} (v{$quotation->version}) sebesar Rp ".number_format($quotation->total_amount, 0, ',', '.').'.',
                    $actor
                );
            }

            return $quotation->fresh(['order', 'items']);
        });
    }
}
