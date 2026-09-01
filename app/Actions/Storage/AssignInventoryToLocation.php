<?php

namespace App\Actions\Storage;

use App\Actions\Movements\RecordMovement;
use App\Actions\Orders\ChangeOrderStatus;
use App\Enums\InventoryStatus;
use App\Enums\MovementType;
use App\Enums\OrderStatus;
use App\Enums\StorageLocationStatus;
use App\Events\InventoryStored;
use App\Models\InventoryItem;
use App\Models\StorageLocation;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AssignInventoryToLocation
{
    public function __construct(
        protected ChangeOrderStatus $changeOrderStatus,
        protected RecordMovement $recordMovement
    ) {}

    /**
     * Assign inventory item to storage location and synchronize order status
     */
    public function execute(InventoryItem $item, StorageLocation $location, ?User $actor = null): InventoryItem
    {
        return DB::transaction(function () use ($item, $location, $actor) {
            $fromLocation = $item->storageLocation;
            $fromCode = $fromLocation ? $fromLocation->code : ($item->storage_location ?: 'Area Penerimaan / Receiving');

            $item->update([
                'storage_location_id' => $location->id,
                'storage_location' => $location->code,
                'status' => InventoryStatus::STORED,
            ]);

            // Update location status to OCCUPIED if reached full capacity
            if ($location->fresh()->isFull()) {
                $location->update(['status' => StorageLocationStatus::OCCUPIED]);
            }

            // Record Inbound Movement
            $this->recordMovement->execute(
                item: $item,
                type: MovementType::INBOUND,
                fromLocation: $fromLocation,
                toLocation: $location,
                fromLocationCode: $fromCode,
                toLocationCode: $location->code,
                performer: $actor,
                notes: "Penempatan barang fisik ke rak {$location->code} ({$location->warehouse})"
            );

            $order = $item->order->fresh();

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

            $freshItem = $item->fresh(['storageLocation', 'order.customer', 'movements']);
            InventoryStored::dispatch($freshItem, $location);

            return $freshItem;
        });
    }
}
