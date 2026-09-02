<?php

namespace App\Actions\Movements;

use App\Enums\MovementType;
use App\Models\InventoryItem;
use App\Models\InventoryMovement;
use App\Models\StorageLocation;
use App\Models\User;

class RecordMovement
{
    /**
     * Record an immutable inventory movement audit log
     */
    public function execute(
        InventoryItem $item,
        MovementType|string $type,
        ?StorageLocation $fromLocation = null,
        ?StorageLocation $toLocation = null,
        ?string $fromLocationCode = null,
        ?string $toLocationCode = null,
        ?User $performer = null,
        ?string $notes = null
    ): InventoryMovement {
        $movementType = ($type instanceof MovementType) ? $type : MovementType::from($type);

        $fromCode = $fromLocation ? $fromLocation->code : $fromLocationCode;
        $toCode = $toLocation ? $toLocation->code : $toLocationCode;

        return InventoryMovement::create([
            'inventory_item_id' => $item->id,
            'from_location_id' => $fromLocation?->id,
            'to_location_id' => $toLocation?->id,
            'from_location_code' => $fromCode,
            'to_location_code' => $toCode,
            'movement_type' => $movementType,
            'performed_by' => $performer?->id,
            'notes' => $notes,
            'moved_at' => now(),
        ]);
    }
}
