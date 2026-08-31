<?php

namespace App\Actions\Inventory;

use App\Actions\Orders\ChangeOrderStatus;
use App\Enums\InventoryStatus;
use App\Enums\OrderStatus;
use App\Models\InventoryItem;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class StoreInventoryItem
{
    public function __construct(
        protected ChangeOrderStatus $changeOrderStatus
    ) {}

    /**
     * Store item in warehouse rack location and advance order to STORED if applicable
     */
    public function execute(InventoryItem $item, string $storageLocation, ?User $actor = null): InventoryItem
    {
        return DB::transaction(function () use ($item, $storageLocation, $actor) {
            $item->update([
                'status' => InventoryStatus::STORED,
                'storage_location' => $storageLocation,
            ]);

            $order = $item->order;

            // If this is a storage service order, and all items are now STORED, transition order to STORED
            $unstoredCount = $order->inventoryItems()
                ->where('status', '!=', InventoryStatus::STORED->value)
                ->count();

            if ($unstoredCount === 0 && in_array($order->status, [OrderStatus::PICKED_UP, OrderStatus::PROCESSING], true)) {
                if ($order->status->canTransitionTo(OrderStatus::STORED)) {
                    $this->changeOrderStatus->execute(
                        $order,
                        OrderStatus::STORED,
                        "Seluruh barang fisik telah tersimpan aman di rak storage gudang.",
                        $actor
                    );
                }
            }

            return $item->fresh(['order']);
        });
    }
}
