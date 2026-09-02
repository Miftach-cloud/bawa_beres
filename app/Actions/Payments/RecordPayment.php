<?php

namespace App\Actions\Payments;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class RecordPayment
{
    /**
     * Record payment transaction for an order
     */
    public function execute(Order $order, array $data, ?UploadedFile $proofFile = null): Payment
    {
        return DB::transaction(function () use ($order, $data, $proofFile) {
            $proofPath = null;
            if ($proofFile) {
                $proofPath = $proofFile->store('payment-proofs', 'local');
            }

            $method = $data['method'] instanceof PaymentMethod ? $data['method'] : PaymentMethod::from($data['method']);
            $status = $data['status'] ?? ($proofPath ? PaymentStatus::WAITING_VERIFICATION : PaymentStatus::PENDING);
            if (! ($status instanceof PaymentStatus)) {
                $status = PaymentStatus::from($status);
            }

            $paymentNumber = Payment::generateNumber($order);

            $payment = Payment::create([
                'order_id' => $order->id,
                'payment_number' => $paymentNumber,
                'method' => $method,
                'status' => $status,
                'amount' => (float) ($data['amount'] ?? 0),
                'bank_name' => $data['bank_name'] ?? null,
                'account_name' => $data['account_name'] ?? null,
                'proof_path' => $proofPath,
                'notes' => $data['notes'] ?? null,
                'paid_at' => $data['paid_at'] ?? now(),
            ]);

            return $payment->fresh(['order', 'verifier']);
        });
    }
}
