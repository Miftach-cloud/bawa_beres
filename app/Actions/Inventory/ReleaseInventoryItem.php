<?php

namespace App\Actions\Inventory;

use App\Enums\InventoryStatus;
use App\Models\InventoryItem;

class ReleaseInventoryItem
{
    public function execute(InventoryItem $item): InventoryItem
    {
        $item->update([
            'status' => InventoryStatus::RELEASED,
            'released_at' => now(),
        ]);

        return $item->fresh();
    }
}
