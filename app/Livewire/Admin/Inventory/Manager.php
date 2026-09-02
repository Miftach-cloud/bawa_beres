<?php

namespace App\Livewire\Admin\Inventory;

use App\Actions\Inventory\CheckInventoryItem;
use App\Actions\Inventory\GenerateExpectedInventory;
use App\Actions\Inventory\ReceiveInventoryItem;
use App\Actions\Inventory\ReleaseInventoryItem;
use App\Actions\Movements\RelocateInventoryItem;
use App\Actions\Storage\AssignInventoryToLocation;
use App\Actions\Storage\VacateInventoryFromLocation;
use App\Enums\InventoryStatus;
use App\Enums\ItemCondition;
use App\Enums\StorageLocationType;
use App\Models\InventoryItem;
use App\Models\Order;
use App\Models\StorageLocation;
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

    public string $storageLocation = '';

    // Relocate Modal
    public bool $showRelocateModal = false;

    public ?int $relocateLocationId = null;

    public string $relocateNotes = '';

    // Add Item Modal
    public bool $showAddModal = false;

    public string $newItemName = '';

    public string $newItemCategory = 'Sedang';

    public string $newItemCondition = 'GOOD';

    public string $newItemNotes = '';

    public function mount(Order $order): void
    {
        $this->order = $order;
        $this->storageLocation = (string) config('business.operations.default_storage_location', 'Rak A-01');
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
        $this->storageLocation = $item->storage_location ?: (string) config('business.operations.default_storage_location', 'Rak A-01');
        $this->showStoreModal = true;
    }

    public function closeStoreModal(): void
    {
        $this->showStoreModal = false;
        $this->selectedItemId = null;
        $this->selectedLocationId = null;
    }

    public function confirmStore(AssignInventoryToLocation $assignAction): void
    {
        Gate::authorize('manage-inventory');

        $item = InventoryItem::findOrFail($this->selectedItemId);

        if ($this->selectedLocationId) {
            $location = StorageLocation::findOrFail($this->selectedLocationId);
        } elseif (! empty($this->storageLocation)) {
            $location = StorageLocation::where('code', $this->storageLocation)->first()
                ?? StorageLocation::firstOrCreate(
                    ['code' => $this->storageLocation],
                    [
                        'warehouse' => 'Gudang Utama Malang',
                        'zone' => 'Zone A',
                        'rack' => 'R01',
                        'level' => 'L01',
                        'type' => StorageLocationType::STANDARD_RACK,
                        'capacity' => 10,
                    ]
                );
        } else {
            $this->validate([
                'selectedLocationId' => 'required|exists:storage_locations,id',
            ]);

            return;
        }

        $assignAction->execute($item, $location, Auth::user());

        $this->showStoreModal = false;
        $this->selectedItemId = null;
        $this->selectedLocationId = null;
        $this->order->refresh();
        session()->flash('inventory_message', "Barang #{$item->inventory_code} telah tersimpan di rak {$location->code}.");
    }

    public function openRelocateModal(int $itemId): void
    {
        Gate::authorize('manage-storage');

        $item = InventoryItem::findOrFail($itemId);
        $this->selectedItemId = $item->id;
        $this->relocateLocationId = null;
        $this->relocateNotes = '';
        $this->showRelocateModal = true;
    }

    public function closeRelocateModal(): void
    {
        $this->showRelocateModal = false;
        $this->selectedItemId = null;
        $this->relocateLocationId = null;
    }

    public function confirmRelocate(RelocateInventoryItem $action): void
    {
        Gate::authorize('manage-storage');

        $this->validate([
            'relocateLocationId' => 'required|exists:storage_locations,id',
        ]);

        $item = InventoryItem::findOrFail($this->selectedItemId);
        $targetLocation = StorageLocation::findOrFail($this->relocateLocationId);

        $action->execute($item, $targetLocation, Auth::user(), $this->relocateNotes ?: null);

        $this->showRelocateModal = false;
        $this->order->refresh();
        session()->flash('inventory_message', "Barang #{$item->inventory_code} berhasil dipindahkan ke rak {$targetLocation->code}.");
    }

    public function release(int $itemId, ReleaseInventoryItem $action, VacateInventoryFromLocation $vacateAction): void
    {
        Gate::authorize('manage-inventory');

        $item = InventoryItem::findOrFail($itemId);
        $vacateAction->execute($item, Auth::user());
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
        $availableLocations = StorageLocation::available()->get();

        return view('livewire.admin.inventory.manager', [
            'items' => $items,
            'availableLocations' => $availableLocations,
            'conditions' => ItemCondition::cases(),
        ]);
    }
}
