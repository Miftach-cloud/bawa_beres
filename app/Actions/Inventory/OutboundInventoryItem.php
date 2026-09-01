<?php

namespace App\Actions\Inventory;

use App\Enums\InventoryStatus;
use App\Models\InventoryItem;

class OutboundInventoryItem
{
    public function execute(InventoryItem $item): InventoryItem
    {
        $item->update([
            'status' => InventoryStatus::OUTBOUND,
        ]);

        return $item->fresh();
    }
}
