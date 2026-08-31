<?php

namespace App\Models;

use App\Enums\AddressType;
use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_code',
        'customer_id',
        'service_id',
        'status',
        'customer_notes',
        'admin_notes',
        'total_amount',
    ];

    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'total_amount' => 'decimal:2',
        ];
    }

    /**
     * Generate sequential/formatted order code: ORD-YYYY-XXXXXX
     */
    public static function generateCode(): string
    {
        $year = date('Y');
        $prefix = "ORD-{$year}-";

        $latest = static::query()
            ->where('order_code', 'LIKE', "{$prefix}%")
            ->orderByDesc('id')
            ->value('order_code');

        if ($latest) {
            $number = (int) substr($latest, strlen($prefix)) + 1;
        } else {
            $number = 1;
        }

        return $prefix . str_pad((string) $number, 6, '0', STR_PAD_LEFT);
    }

    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            if (empty($order->order_code)) {
                $order->order_code = static::generateCode();
            }
            if (empty($order->status)) {
                $order->status = OrderStatus::PENDING_REVIEW;
            }
        });

        static::created(function (Order $order) {
            // Record initial status in history
            $order->statusHistories()->create([
                'from_status' => null,
                'to_status' => $order->status->value,
                'notes' => 'Order created',
            ]);
        });
    }

    /**
     * Transition order status with history audit trail
     */
    public function transitionTo(OrderStatus $newStatus, ?string $notes = null, ?User $changedBy = null): bool
    {
        return DB::transaction(function () use ($newStatus, $notes, $changedBy) {
            $oldStatus = $this->status;

            $this->update([
                'status' => $newStatus,
            ]);

            $this->statusHistories()->create([
                'from_status' => $oldStatus instanceof OrderStatus ? $oldStatus->value : $oldStatus,
                'to_status' => $newStatus->value,
                'changed_by' => $changedBy?->id,
                'notes' => $notes,
            ]);

            return true;
        });
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    public function pickupAddress(): HasOne
    {
        return $this->hasOne(Address::class)->where('type', AddressType::PICKUP->value);
    }

    public function destinationAddress(): HasOne
    {
        return $this->hasOne(Address::class)->where('type', AddressType::DESTINATION->value);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class);
    }
}
