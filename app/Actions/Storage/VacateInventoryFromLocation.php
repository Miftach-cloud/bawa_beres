<?php

namespace App\Actions\Storage;

use App\Actions\Movements\RecordMovement;
use App\Enums\MovementType;
use App\Enums\StorageLocationStatus;
use App\Models\InventoryItem;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class VacateInventoryFromLocation
{
    public function __construct(
        protected RecordMovement $recordMovement
    ) {}

    public function execute(InventoryItem $item, ?User $performer = null, ?string $notes = null): InventoryItem
    {
        return DB::transaction(function () use ($item, $performer, $notes) {
            $location = $item->storageLocation;
            $fromCode = $location ? $location->code : ($item->storage_location ?: 'Rak Gudang');

            $item->update([
                'storage_location_id' => null,
            ]);

            if ($location && $location->status === StorageLocationStatus::OCCUPIED && !$location->fresh()->isFull()) {
                $location->update(['status' => StorageLocationStatus::AVAILABLE]);
            }

            // Record Outbound Movement
            $this->recordMovement->execute(
                item: $item,
                type: MovementType::OUTBOUND,
                fromLocation: $location,
                toLocation: null,
                fromLocationCode: $fromCode,
                toLocationCode: 'Outbound / Area Pengeluaran',
                performer: $performer,
                notes: $notes ?: "Pengeluaran barang dari rak {$fromCode} untuk serah terima/delivery"
            );

            return $item->fresh();
        });
    }
}

