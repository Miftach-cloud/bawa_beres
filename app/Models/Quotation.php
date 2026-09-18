<?php

namespace App\Models;

use App\Enums\QuotationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quotation extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'quotation_number',
        'version',
        'status',
        'subtotal',
        'discount',
        'tax',
        'total_amount',
        'notes',
        'valid_until',
        'sent_at',
        'responded_at',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'status' => QuotationStatus::class,
            'version' => 'integer',
            'subtotal' => 'decimal:2',
            'discount' => 'decimal:2',
            'tax' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'valid_until' => 'datetime',
            'sent_at' => 'datetime',
            'responded_at' => 'datetime',
        ];
    }

    /**
     * Generate sequential/formatted quotation number for an order: QUO-ORD-YYYY-XXXXXX-v1
     */
    public static function generateNumber(Order $order, int $version = 1): string
    {
        return "QUO-{$order->order_code}-v{$version}";
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(QuotationItem::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isAccepted(): bool
    {
        return $this->status === QuotationStatus::ACCEPTED;
    }

    public function isSent(): bool
    {
        return $this->status === QuotationStatus::SENT;
    }

    public function isDraft(): bool
    {
        return $this->status === QuotationStatus::DRAFT;
    }
}
