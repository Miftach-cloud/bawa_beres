<?php

namespace App\Livewire\Admin\Inventory;

use App\Actions\Inventory\CheckInventoryItem;
use App\Actions\Inventory\GenerateExpectedInventory;
use App\Actions\Inventory\OutboundInventoryItem;
use App\Actions\Inventory\ReceiveInventoryItem;
use App\Actions\Inventory\ReleaseInventoryItem;
use App\Actions\Movements\RelocateInventoryItem;
use App\Actions\Storage\AssignInventoryToLocation;
use App\Actions\Storage\VacateInventoryFromLocation;
use App\Enums\InventoryStatus;
use App\Enums\ItemCondition;
use App\Enums\OrderStatus;
use App\Enums\StorageLocationType;
use App\Models\InventoryItem;
use App\Models\Order;
use App\Models\StorageLocation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Inventaris Barang Fisik — Admin Bawa Beres')]
class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public string $statusFilter = '';

    public string $conditionFilter = '';

    public ?int $selectedOrderId = null;

    // Quick Store Modal
    public bool $showStoreModal = false;

    public ?int $selectedItemId = null;

    public ?int $selectedLocationId = null;

    public string $storageLocation = '';

    // Quick Relocate Modal
    public bool $showRelocateModal = false;

    public ?int $relocateLocationId = null;

    public string $relocateNotes = '';

    // Quick QC Modal
    public bool $showCheckModal = false;

    public string $condition = 'GOOD';

    public string $checkNotes = '';

    public function mount(): void
    {
        Gate::authorize('manage-inventory');
        $this->storageLocation = (string) config('business.operations.default_storage_location', 'Rak A-01');
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingConditionFilter(): void
    {
        $this->resetPage();
    }

    public function receive(int $itemId, ReceiveInventoryItem $action): void
    {
        Gate::authorize('manage-inventory');

        $item = InventoryItem::findOrFail($itemId);
        $action->execute($item, Auth::user());

        session()->flash('message', "Barang #{$item->inventory_code} telah ditandai Diterima (RECEIVED).");
    }

    public function generateExpected(GenerateExpectedInventory $action): void
    {
        Gate::authorize('manage-inventory');

        $this->validate([
            'selectedOrderId' => ['required', 'integer', 'exists:orders,id'],
        ]);

        $order = Order::query()->findOrFail($this->selectedOrderId);
        $items = $action->execute($order);

        $this->selectedOrderId = null;
        session()->flash('message', "{$items->count()} barang fisik untuk {$order->order_code} berhasil dibuat.");
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
        session()->flash('message', "Hasil QC kondisi barang #{$item->inventory_code} disimpan (CHECKED).");
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
        session()->flash('message', "Barang #{$item->inventory_code} berhasil disimpan di rak {$location->code}.");
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
        session()->flash('message', "Barang #{$item->inventory_code} berhasil dipindahkan ke rak {$targetLocation->code}.");
    }

    public function release(int $itemId, ReleaseInventoryItem $action, VacateInventoryFromLocation $vacateAction): void
    {
        Gate::authorize('manage-inventory');

        $item = InventoryItem::findOrFail($itemId);
        $vacateAction->execute($item, Auth::user());
        $action->execute($item);

        session()->flash('message', "Barang #{$item->inventory_code} berhasil diserahterimakan (RELEASED).");
    }

    public function outbound(int $itemId, OutboundInventoryItem $action): void
    {
        Gate::authorize('manage-inventory');

        $item = InventoryItem::query()->findOrFail($itemId);
        $action->execute($item, Auth::user());

        session()->flash('message', "Barang #{$item->inventory_code} dipindahkan ke area outbound.");
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'statusFilter', 'conditionFilter']);
        $this->resetPage();
    }

    public function render()
    {
        $query = InventoryItem::query()
            ->with(['order.customer', 'receiver'])
            ->withCount(['photos', 'movements']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('inventory_code', 'LIKE', "%{$this->search}%")
                    ->orWhere('name', 'LIKE', "%{$this->search}%")
                    ->orWhere('storage_location', 'LIKE', "%{$this->search}%")
                    ->orWhereHas('order', function ($oq) {
                        $oq->where('order_code', 'LIKE', "%{$this->search}%")
                            ->orWhereHas('customer', function ($cq) {
                                $cq->where('name', 'LIKE', "%{$this->search}%")
                                    ->orWhere('phone', 'LIKE', "%{$this->search}%");
                            });
                    });
            });
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->conditionFilter) {
            $query->where('condition', $this->conditionFilter);
        }

        $items = $query->latest('id')->paginate(15);
        $availableLocations = StorageLocation::available()->get();
        $ordersReadyForInventory = Order::query()
            ->whereIn('status', [OrderStatus::PICKED_UP->value, OrderStatus::PROCESSING->value])
            ->whereHas('items')
            ->whereDoesntHave('inventoryItems')
            ->with('customer:id,name')
            ->orderBy('created_at')
            ->get(['id', 'order_code', 'customer_id']);

        return view('livewire.admin.inventory.index', [
            'items' => $items,
            'availableLocations' => $availableLocations,
            'statuses' => InventoryStatus::cases(),
            'conditions' => ItemCondition::cases(),
            'ordersReadyForInventory' => $ordersReadyForInventory,
        ]);
    }
}
