<?php

namespace App\Actions\Inventory;

use App\Enums\InventoryStatus;
use App\Events\InventoryReceived;
use App\Models\InventoryItem;
use App\Models\User;

class ReceiveInventoryItem
{
    /**
     * Mark physical inventory item as RECEIVED by field team
     */
    public function execute(InventoryItem $item, ?User $receiver = null): InventoryItem
    {
        $item->update([
            'status' => InventoryStatus::RECEIVED,
            'received_at' => now(),
            'received_by' => $receiver?->id,
        ]);

        $freshItem = $item->fresh(['order.customer', 'receiver']);
        InventoryReceived::dispatch($freshItem->order, $freshItem);

        return $freshItem;
    }
}
