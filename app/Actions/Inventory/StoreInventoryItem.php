<?php

namespace App\Actions\Inventory;

use App\Actions\Storage\AssignInventoryToLocation;
use App\Enums\StorageLocationType;
use App\Models\InventoryItem;
use App\Models\StorageLocation;
use App\Models\User;

/**
 * @deprecated Use App\Actions\Storage\AssignInventoryToLocation directly.
 */
class StoreInventoryItem
{
    public function __construct(
        protected AssignInventoryToLocation $assignInventoryToLocation
    ) {}

    /**
     * Store item in warehouse rack location via authoritative AssignInventoryToLocation domain action.
     */
    public function execute(InventoryItem $item, StorageLocation|string $storageLocation, ?User $actor = null): InventoryItem
    {
        if (is_string($storageLocation)) {
            $location = StorageLocation::where('code', $storageLocation)->first()
                ?? StorageLocation::firstOrCreate(
                    ['code' => $storageLocation],
                    [
                        'warehouse' => 'Gudang Utama Malang',
                        'zone' => 'Zone A',
                        'rack' => 'R01',
                        'level' => 'L01',
                        'type' => StorageLocationType::STANDARD_RACK,
                        'capacity' => 10,
                    ]
                );
        } else {
            $location = $storageLocation;
        }

        return $this->assignInventoryToLocation->execute($item, $location, $actor);
    }
}
