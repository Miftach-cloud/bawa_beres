<?php

namespace App\Actions\Movements;

use App\Enums\MovementType;
use App\Enums\StorageLocationStatus;
use App\Models\InventoryItem;
use App\Models\StorageLocation;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class RelocateInventoryItem
{
    public function __construct(
        protected RecordMovement $recordMovement
    ) {}

    /**
     * Relocate inventory item to a new storage slot and record movement
     */
    public function execute(
        InventoryItem $item,
        StorageLocation $toLocation,
        ?User $performer = null,
        ?string $notes = null
    ): InventoryItem {
        return DB::transaction(function () use ($item, $toLocation, $performer, $notes) {
            $fromLocation = $item->storageLocation;
            $fromCode = $fromLocation?->code ?? 'Area Transit Gudang';

            $item->update([
                'storage_location_id' => $toLocation->id,
            ]);
            $item->unsetRelation('storageLocation');

            // Update status on former location if it now has free capacity
            if ($fromLocation && $fromLocation->status === StorageLocationStatus::OCCUPIED && ! $fromLocation->fresh()->isFull()) {
                $fromLocation->update(['status' => StorageLocationStatus::AVAILABLE]);
            }

            // Update status on target location if it now is full
            if ($toLocation->fresh()->isFull()) {
                $toLocation->update(['status' => StorageLocationStatus::OCCUPIED]);
            }

            // Record immutable movement log
            $this->recordMovement->execute(
                item: $item,
                type: MovementType::RELOCATION,
                fromLocation: $fromLocation,
                toLocation: $toLocation,
                fromLocationCode: $fromCode,
                toLocationCode: $toLocation->code,
                performer: $performer,
                notes: $notes ?: "Pemindahan barang dari rak {$fromCode} ke {$toLocation->code}"
            );

            return $item->fresh(['storageLocation', 'movements']);
        });
    }
}
