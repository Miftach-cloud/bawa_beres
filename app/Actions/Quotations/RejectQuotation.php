<?php

namespace App\Actions\Quotations;

use App\Enums\QuotationStatus;
use App\Models\Quotation;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class RejectQuotation
{
    /**
     * Reject quotation with customer feedback notes
     */
    public function execute(Quotation $quotation, string $reason, ?User $actor = null): Quotation
    {
        return DB::transaction(function () use ($quotation, $reason, $actor) {
            $notes = $quotation->notes ? ($quotation->notes . "\n[Ditolak: {$reason}]") : "[Ditolak: {$reason}]";

            $quotation->update([
                'status' => QuotationStatus::REJECTED,
                'notes' => $notes,
                'responded_at' => now(),
            ]);

            return $quotation->fresh(['order', 'items']);
        });
    }
}
