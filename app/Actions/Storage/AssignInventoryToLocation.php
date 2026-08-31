<?php

namespace App\Actions\Storage;

use App\Actions\Orders\ChangeOrderStatus;
use App\Enums\InventoryStatus;
use App\Enums\OrderStatus;
use App\Enums\StorageLocationStatus;
use App\Models\InventoryItem;
use App\Models\StorageLocation;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AssignInventoryToLocation
{
    public function __construct(
        protected ChangeOrderStatus $changeOrderStatus
    ) {}

    /**
     * Assign inventory item to storage location and synchronize order status
     */
    public function execute(InventoryItem $item, StorageLocation $location, ?User $actor = null): InventoryItem
    {
        return DB::transaction(function () use ($item, $location, $actor) {
            $item->update([
                'storage_location_id' => $location->id,
                'storage_location' => $location->code,
                'status' => InventoryStatus::STORED,
            ]);

            // Update location status to OCCUPIED if reached full capacity
            if ($location->fresh()->isFull()) {
                $location->update(['status' => StorageLocationStatus::OCCUPIED]);
            }

            $order = $item->order;

            // If order has storage requirement and all items are stored, transition order to STORED
            $unstoredCount = $order->inventoryItems()
                ->where('status', '!=', InventoryStatus::STORED->value)
                ->count();

            if ($unstoredCount === 0 && in_array($order->status, [OrderStatus::PICKED_UP, OrderStatus::PROCESSING], true)) {
                if ($order->status->canTransitionTo(OrderStatus::STORED)) {
                    $this->changeOrderStatus->execute(
                        $order,
                        OrderStatus::STORED,
                        "Seluruh barang fisik telah dialokasikan ke rak gudang {$location->warehouse}.",
                        $actor
                    );
                }
            }

            return $item->fresh(['storageLocation', 'order']);
        });
    }
}
