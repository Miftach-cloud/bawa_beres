<?php

namespace App\Actions\Inventory;

use App\Enums\InventoryStatus;
use App\Enums\ItemCondition;
use App\Models\InventoryItem;

class CheckInventoryItem
{
    /**
     * Inspect physical item condition and mark as CHECKED
     */
    public function execute(InventoryItem $item, ItemCondition|string $condition, ?string $notes = null): InventoryItem
    {
        $conditionEnum = ($condition instanceof ItemCondition) ? $condition : ItemCondition::from($condition);

        $item->update([
            'condition' => $conditionEnum,
            'status' => InventoryStatus::CHECKED,
            'checked_at' => now(),
            'notes' => $notes ?: $item->notes,
        ]);

        return $item->fresh();
    }
}
