<?php

namespace App\Models;

use App\Enums\InventoryStatus;
use App\Enums\ItemCondition;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'inventory_code',
        'order_id',
        'order_item_id',
        'name',
        'description',
        'category',
        'condition',
        'status',
        'storage_location',
        'qr_code_payload',
        'received_at',
        'checked_at',
        'released_at',
        'received_by',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'condition' => ItemCondition::class,
            'status' => InventoryStatus::class,
            'received_at' => 'datetime',
            'checked_at' => 'datetime',
            'released_at' => 'datetime',
        ];
    }

    /**
     * Generate unique sequential inventory code: INV-YYYY-XXXXXX (e.g. INV-2026-000001)
     */
    public static function generateCode(): string
    {
        $year = date('Y');
        $prefix = "INV-{$year}-";

        $latest = static::query()
            ->where('inventory_code', 'LIKE', "{$prefix}%")
            ->orderByDesc('id')
            ->value('inventory_code');

        if ($latest) {
            $number = (int) substr($latest, strlen($prefix)) + 1;
        } else {
            $number = 1;
        }

        return $prefix . str_pad((string) $number, 6, '0', STR_PAD_LEFT);
    }

    protected static function booted(): void
    {
        static::creating(function (InventoryItem $item) {
            if (empty($item->inventory_code)) {
                $item->inventory_code = static::generateCode();
            }
            if (empty($item->qr_code_payload)) {
                $item->qr_code_payload = json_encode([
                    'code' => $item->inventory_code,
                    'order' => $item->order_id,
                    'name' => $item->name,
                ]);
            }
        });
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function photos(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(InventoryPhoto::class)->latest('id');
    }

    public function damagePhotos(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(InventoryPhoto::class)->where('type', \App\Enums\PhotoType::DAMAGE->value);
    }

    public function isStored(): bool
    {
        return $this->status === InventoryStatus::STORED;
    }


    public function isReceived(): bool
    {
        return in_array($this->status, [
            InventoryStatus::RECEIVED,
            InventoryStatus::CHECKED,
            InventoryStatus::STORED,
            InventoryStatus::OUTBOUND,
            InventoryStatus::RELEASED,
        ], true);
    }
}
