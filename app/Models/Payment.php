<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'payment_number',
        'method',
        'status',
        'amount',
        'bank_name',
        'account_name',
        'proof_path',
        'notes',
        'paid_at',
        'verified_at',
        'verified_by',
        'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'method' => PaymentMethod::class,
            'status' => PaymentStatus::class,
            'amount' => 'decimal:2',
            'paid_at' => 'datetime',
            'verified_at' => 'datetime',
        ];
    }

    /**
     * Generate sequential payment number for order: PAY-ORD-YYYY-XXXXXX-01
     */
    public static function generateNumber(Order $order): string
    {
        $count = $order->payments()->count() + 1;
        $seq = str_pad((string) $count, 2, '0', STR_PAD_LEFT);
        return "PAY-{$order->order_code}-{$seq}";
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function getProofUrlAttribute(): ?string
    {
        if (!$this->proof_path) {
            return null;
        }

        return Storage::disk('public')->url($this->proof_path);
    }

    public function isVerified(): bool
    {
        return $this->status === PaymentStatus::PAID;
    }

    public function isWaitingVerification(): bool
    {
        return $this->status === PaymentStatus::WAITING_VERIFICATION;
    }
}
