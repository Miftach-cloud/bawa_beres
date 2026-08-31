<?php

namespace App\Actions\Quotations;

use App\Enums\QuotationStatus;
use App\Models\Order;
use App\Models\Quotation;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CreateQuotation
{
    /**
     * Create a new quotation (v1) with itemized calculation
     */
    public function execute(Order $order, array $data, ?User $creator = null): Quotation
    {
        return DB::transaction(function () use ($order, $data, $creator) {
            $items = $data['items'] ?? [];
            $subtotal = 0;

            foreach ($items as $item) {
                $qty = (int) ($item['quantity'] ?? 1);
                $unitPrice = (float) ($item['unit_price'] ?? 0);
                $subtotal += ($qty * $unitPrice);
            }

            $discount = (float) ($data['discount'] ?? 0);
            $tax = (float) ($data['tax'] ?? 0);
            $totalAmount = max(0, $subtotal - $discount + $tax);

            $version = 1;
            $quotationNumber = Quotation::generateNumber($order, $version);

            $quotation = Quotation::create([
                'order_id' => $order->id,
                'quotation_number' => $quotationNumber,
                'version' => $version,
                'status' => $data['status'] ?? QuotationStatus::DRAFT,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'tax' => $tax,
                'total_amount' => $totalAmount,
                'notes' => $data['notes'] ?? null,
                'valid_until' => $data['valid_until'] ?? null,
                'created_by' => $creator?->id,
            ]);

            foreach ($items as $item) {
                if (!empty($item['name'])) {
                    $qty = (int) ($item['quantity'] ?? 1);
                    $unitPrice = (float) ($item['unit_price'] ?? 0);
                    $quotation->items()->create([
                        'name' => $item['name'],
                        'description' => $item['description'] ?? null,
                        'quantity' => $qty,
                        'unit_price' => $unitPrice,
                        'total_price' => $qty * $unitPrice,
                    ]);
                }
            }

            $freshQuotation = $quotation->fresh(['items', 'order.customer']);
            \App\Events\QuotationCreated::dispatch($freshQuotation);

            return $freshQuotation;
        });
    }
}

