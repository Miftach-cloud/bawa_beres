<?php

namespace App\Actions\Inventory;

use App\Enums\InventoryStatus;
use App\Enums\ItemCondition;
use App\Models\InventoryItem;
use App\Models\Order;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class GenerateExpectedInventory
{
    /**
     * Generate physical custody records (EXPECTED) from booking declaration order items
     *
     * @return Collection<int, InventoryItem>
     */
    public function execute(Order $order): Collection
    {
        return DB::transaction(function () use ($order) {
            $created = collect();

            foreach ($order->items as $item) {
                $qty = max(1, (int) $item->quantity);

                // Determine default condition tag if fragile
                $condition = ($item->estimated_size === 'Fragile')
                    ? ItemCondition::FRAGILE
                    : ItemCondition::GOOD;

                for ($i = 1; $i <= $qty; $i++) {
                    $suffix = ($qty > 1) ? " (#{$i})" : '';
                    $inventoryItem = InventoryItem::create([
                        'order_id' => $order->id,
                        'order_item_id' => $item->id,
                        'name' => $item->name.$suffix,
                        'description' => $item->description,
                        'category' => $item->estimated_size,
                        'condition' => $condition,
                        'status' => InventoryStatus::EXPECTED,
                        'notes' => $item->notes,
                    ]);

                    $created->push($inventoryItem);
                }
            }

            return $created;
        });
    }
}
