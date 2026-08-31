<?php

namespace App\Actions\Storage;

use App\Enums\StorageLocationStatus;
use App\Models\InventoryItem;
use Illuminate\Support\Facades\DB;

class VacateInventoryFromLocation
{
    public function execute(InventoryItem $item): InventoryItem
    {
        return DB::transaction(function () use ($item) {
            $location = $item->storageLocation;

            $item->update([
                'storage_location_id' => null,
            ]);

            if ($location && $location->status === StorageLocationStatus::OCCUPIED && !$location->fresh()->isFull()) {
                $location->update(['status' => StorageLocationStatus::AVAILABLE]);
            }

            return $item->fresh();
        });
    }
}
