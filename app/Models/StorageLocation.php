<?php

namespace App\Models;

use App\Enums\InventoryStatus;
use App\Enums\StorageLocationStatus;
use App\Enums\StorageLocationType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StorageLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'warehouse',
        'zone',
        'rack',
        'level',
        'type',
        'status',
        'capacity',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'type' => StorageLocationType::class,
            'status' => StorageLocationStatus::class,
            'capacity' => 'integer',
        ];
    }

    /**
     * Build location code from parts: WAREHOUSE-ZONE-RACK-LEVEL (e.g. MLG01-A-R02-L03)
     */
    public static function formatCode(string $warehouse, string $zone, string $rack, string $level): string
    {
        $w = strtoupper(trim(preg_replace('/[^a-zA-Z0-9]/', '', $warehouse)));
        $z = strtoupper(trim(preg_replace('/[^a-zA-Z0-9]/', '', $zone)));
        $r = strtoupper(trim(preg_replace('/[^a-zA-Z0-9]/', '', $rack)));
        $l = strtoupper(trim(preg_replace('/[^a-zA-Z0-9]/', '', $level)));

        return "{$w}-{$z}-{$r}-{$l}";
    }

    public function inventoryItems(): HasMany
    {
        return $this->hasMany(InventoryItem::class);
    }

    public function storedItems(): HasMany
    {
        return $this->hasMany(InventoryItem::class)->where('status', InventoryStatus::STORED->value);
    }

    public function occupiedCount(): int
    {
        return $this->storedItems()->count();
    }

    public function remainingCapacity(): int
    {
        return max(0, $this->capacity - $this->occupiedCount());
    }

    public function isFull(): bool
    {
        return $this->occupiedCount() >= $this->capacity;
    }

    public function isAvailable(): bool
    {
        return $this->status === StorageLocationStatus::AVAILABLE && ! $this->isFull();
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('status', StorageLocationStatus::AVAILABLE->value);
    }

    public function scopeByWarehouse(Builder $query, string $warehouse): Builder
    {
        return $query->where('warehouse', $warehouse);
    }

    public function scopeByZone(Builder $query, string $zone): Builder
    {
        return $query->where('zone', $zone);
    }
}
