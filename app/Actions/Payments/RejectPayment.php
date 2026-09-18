<?php

namespace App\Actions\Payments;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class RejectPayment
{
    /**
     * Reject payment proof with clear rejection reason
     */
    public function execute(Payment $payment, string $reason, User $verifier): Payment
    {
        return DB::transaction(function () use ($payment, $reason, $verifier) {
            $payment->update([
                'status' => PaymentStatus::REJECTED,
                'rejection_reason' => $reason,
                'verified_at' => now(),
                'verified_by' => $verifier->id,
            ]);

            return $payment->fresh(['order', 'verifier']);
        });
    }
}
