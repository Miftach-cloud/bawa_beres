<?php

namespace App\Livewire\Admin\Inventory;

use App\Actions\Inventory\CheckInventoryItem;
use App\Actions\Inventory\GenerateExpectedInventory;
use App\Actions\Inventory\ReceiveInventoryItem;
use App\Actions\Inventory\ReleaseInventoryItem;
use App\Actions\Inventory\StoreInventoryItem;
use App\Enums\InventoryStatus;
use App\Enums\ItemCondition;
use App\Models\InventoryItem;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Manager extends Component
{
    public Order $order;

    // Check & QC Modal
    public bool $showCheckModal = false;
    public ?int $selectedItemId = null;
    public string $condition = 'GOOD';
    public string $checkNotes = '';

    // Store Modal
    public bool $showStoreModal = false;
    public ?int $selectedLocationId = null;
    public string $storageLocation = 'Rak A-01 (Gudang Dinoyo)';

    // Add Item Modal
    public bool $showAddModal = false;
    public string $newItemName = '';
    public string $newItemCategory = 'Sedang';
    public string $newItemCondition = 'GOOD';
    public string $newItemNotes = '';

    public function mount(Order $order): void
    {
        $this->order = $order;
    }

    public function generateExpected(GenerateExpectedInventory $action): void
    {
        Gate::authorize('manage-inventory');

        $items = $action->execute($this->order);
        $this->order->refresh();

        session()->flash('inventory_message', "Berhasil men-generate {$items->count()} barang fisik (INV-XXXXXX) dari deklarasi pesanan.");
    }

    public function receive(int $itemId, ReceiveInventoryItem $action): void
    {
        Gate::authorize('manage-inventory');

        $item = InventoryItem::findOrFail($itemId);
        $action->execute($item, Auth::user());

        $this->order->refresh();
        session()->flash('inventory_message', "Barang #{$item->inventory_code} ({$item->name}) telah ditandai Diterima (RECEIVED).");
    }

    public function openCheckModal(int $itemId): void
    {
        Gate::authorize('manage-inventory');

        $item = InventoryItem::findOrFail($itemId);
        $this->selectedItemId = $item->id;
        $this->condition = $item->condition->value;
        $this->checkNotes = $item->notes ?? '';
        $this->showCheckModal = true;
    }

    public function closeCheckModal(): void
    {
        $this->showCheckModal = false;
        $this->selectedItemId = null;
    }

    public function confirmCheck(CheckInventoryItem $action): void
    {
        Gate::authorize('manage-inventory');

        $item = InventoryItem::findOrFail($this->selectedItemId);
        $action->execute($item, $this->condition, $this->checkNotes);

        $this->showCheckModal = false;
        $this->order->refresh();
        session()->flash('inventory_message', "Kondisi barang #{$item->inventory_code} telah diperiksa (CHECKED).");
    }

    public function openStoreModal(int $itemId): void
    {
        Gate::authorize('manage-inventory');

        $item = InventoryItem::findOrFail($itemId);
        $this->selectedItemId = $item->id;
        $this->selectedLocationId = $item->storage_location_id;
        $this->storageLocation = $item->storage_location ?: 'Rak A-01 (Gudang Dinoyo)';
        $this->showStoreModal = true;
    }

    public function closeStoreModal(): void
    {
        $this->showStoreModal = false;
        $this->selectedItemId = null;
        $this->selectedLocationId = null;
    }

    public function confirmStore(StoreInventoryItem $action, \App\Actions\Storage\AssignInventoryToLocation $assignAction): void
    {
        Gate::authorize('manage-inventory');

        $item = InventoryItem::findOrFail($this->selectedItemId);

        if ($this->selectedLocationId) {
            $location = \App\Models\StorageLocation::findOrFail($this->selectedLocationId);
            $assignAction->execute($item, $location, Auth::user());
        } else {
            $action->execute($item, $this->storageLocation ?: 'Rak A-01 (Gudang Dinoyo)', Auth::user());
        }

        $this->showStoreModal = false;
        $this->order->refresh();
        session()->flash('inventory_message', "Barang #{$item->inventory_code} telah tersimpan di rak gudang.");
    }

    public function release(int $itemId, ReleaseInventoryItem $action): void
    {
        Gate::authorize('manage-inventory');

        $item = InventoryItem::findOrFail($itemId);
        $action->execute($item);

        $this->order->refresh();
        session()->flash('inventory_message', "Barang #{$item->inventory_code} telah diserahterimakan (RELEASED).");
    }


    public function openAddModal(): void
    {
        Gate::authorize('manage-inventory');

        $this->newItemName = '';
        $this->newItemCategory = 'Sedang';
        $this->newItemCondition = 'GOOD';
        $this->newItemNotes = '';
        $this->showAddModal = true;
    }

    public function closeAddModal(): void
    {
        $this->showAddModal = false;
    }

    public function saveNewItem(): void
    {
        Gate::authorize('manage-inventory');

        $this->validate([
            'newItemName' => 'required|string|max:255',
            'newItemCondition' => 'required|string|in:GOOD,SCRATCHED,DAMAGED,FRAGILE',
        ]);

        $item = $this->order->inventoryItems()->create([
            'name' => $this->newItemName,
            'category' => $this->newItemCategory,
            'condition' => ItemCondition::from($this->newItemCondition),
            'status' => InventoryStatus::RECEIVED,
            'received_at' => now(),
            'received_by' => Auth::id(),
            'notes' => $this->newItemNotes ?: 'Barang tambahan fisik di lokasi',
        ]);

        $this->showAddModal = false;
        $this->order->refresh();
        session()->flash('inventory_message', "Barang fisik tambahan #{$item->inventory_code} berhasil dicatat.");
    }

    public function render()
    {
        $items = $this->order->inventoryItems()->with(['orderItem', 'receiver', 'storageLocation', 'photos'])->get();
        $availableLocations = \App\Models\StorageLocation::available()->get();

        return view('livewire.admin.inventory.manager', [
            'items' => $items,
            'availableLocations' => $availableLocations,
            'conditions' => ItemCondition::cases(),
        ]);
    }
}

