<?php

namespace App\Models;

use App\Enums\MovementType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'inventory_item_id',
        'from_location_id',
        'to_location_id',
        'from_location_code',
        'to_location_code',
        'movement_type',
        'performed_by',
        'notes',
        'moved_at',
    ];

    protected function casts(): array
    {
        return [
            'movement_type' => MovementType::class,
            'moved_at' => 'datetime',
        ];
    }

    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class);
    }

    public function fromLocation(): BelongsTo
    {
        return $this->belongsTo(StorageLocation::class, 'from_location_id');
    }

    public function toLocation(): BelongsTo
    {
        return $this->belongsTo(StorageLocation::class, 'to_location_id');
    }

    public function performer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}
