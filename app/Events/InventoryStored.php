<?php

namespace App\Events;

use App\Models\InventoryItem;
use App\Models\StorageLocation;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InventoryStored
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public InventoryItem $inventoryItem,
        public StorageLocation $location
    ) {}
}
