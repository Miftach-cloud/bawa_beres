<?php

namespace App\Livewire\Admin\Storage;

use App\Actions\Storage\CreateStorageLocation;
use App\Enums\InventoryStatus;
use App\Enums\StorageLocationStatus;
use App\Enums\StorageLocationType;
use App\Models\InventoryItem;
use App\Models\StorageLocation;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Lokasi Rak & Gudang Storage — Admin Bawa Beres')]
class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public string $warehouseFilter = '';

    public string $zoneFilter = '';

    public string $statusFilter = '';

    // Slot Detail Drawer
    public ?StorageLocation $selectedLocation = null;

    public bool $showDetailDrawer = false;

    // Create Modal
    public bool $showCreateModal = false;

    public string $warehouse = 'MLG01';

    public string $zone = 'A';

    public string $rack = 'R01';

    public string $level = 'L01';

    public string $type = 'STANDARD_RACK';

    public int $capacity = 5;

    public string $notes = '';

    protected function rules(): array
    {
        return [
            'warehouse' => 'required|string|max:50',
            'zone' => 'required|string|max:50',
            'rack' => 'required|string|max:30',
            'level' => 'required|string|max:30',
            'type' => 'required|string|in:STANDARD_RACK,HEAVY_DUTY,PALLET_FLOOR,FRAGILE_CAGE,SECURE_LOCKER',
            'capacity' => 'required|integer|min:1|max:100',
            'notes' => 'nullable|string',
        ];
    }

    public function mount(): void
    {
        Gate::authorize('manage-storage');
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingWarehouseFilter(): void
    {
        $this->resetPage();
    }

    public function updatingZoneFilter(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        Gate::authorize('manage-storage');

        $this->warehouse = 'MLG01';
        $this->zone = 'A';
        $this->rack = 'R01';
        $this->level = 'L01';
        $this->type = 'STANDARD_RACK';
        $this->capacity = 5;
        $this->notes = '';
        $this->resetValidation();
        $this->showCreateModal = true;
    }

    public function closeCreateModal(): void
    {
        $this->showCreateModal = false;
        $this->resetValidation();
    }

    public function saveLocation(CreateStorageLocation $action): void
    {
        Gate::authorize('manage-storage');

        $this->validate();

        $code = StorageLocation::formatCode(
            $this->warehouse,
            $this->zone,
            $this->rack,
            $this->level
        );

        if (StorageLocation::where('code', $code)->exists()) {
            $this->addError('code', "Kode lokasi rak {$code} sudah ada dalam sistem.");

            return;
        }

        $location = $action->execute([
            'code' => $code,
            'warehouse' => $this->warehouse,
            'zone' => $this->zone,
            'rack' => $this->rack,
            'level' => $this->level,
            'type' => $this->type,
            'capacity' => $this->capacity,
            'notes' => $this->notes ?: null,
        ]);

        $this->showCreateModal = false;
        session()->flash('message', "Lokasi rak baru #{$location->code} berhasil dibuat.");
    }

    public function viewLocation(int $locationId): void
    {
        $this->selectedLocation = StorageLocation::with(['storedItems.order.customer', 'storedItems.photos'])->findOrFail($locationId);
        $this->showDetailDrawer = true;
    }

    public function closeDetailDrawer(): void
    {
        $this->showDetailDrawer = false;
        $this->selectedLocation = null;
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'warehouseFilter', 'zoneFilter', 'statusFilter']);
        $this->resetPage();
    }

    public function render()
    {
        $query = StorageLocation::query()
            ->withCount('storedItems');

        if ($this->search) {
            $query->where('code', 'LIKE', "%{$this->search}%")
                ->orWhere('warehouse', 'LIKE', "%{$this->search}%")
                ->orWhere('zone', 'LIKE', "%{$this->search}%")
                ->orWhere('notes', 'LIKE', "%{$this->search}%");
        }

        if ($this->warehouseFilter) {
            $query->where('warehouse', $this->warehouseFilter);
        }

        if ($this->zoneFilter) {
            $query->where('zone', $this->zoneFilter);
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        $locations = $query->orderBy('warehouse')->orderBy('zone')->orderBy('rack')->orderBy('level')->paginate(16);

        $stats = [
            'total_slots' => StorageLocation::count(),
            'available_slots' => StorageLocation::where('status', StorageLocationStatus::AVAILABLE->value)->count(),
            'occupied_slots' => StorageLocation::where('status', StorageLocationStatus::OCCUPIED->value)->count(),
            'stored_items' => InventoryItem::where('status', InventoryStatus::STORED->value)->count(),
        ];

        $warehouses = StorageLocation::select('warehouse')->distinct()->pluck('warehouse');
        $zones = StorageLocation::select('zone')->distinct()->pluck('zone');

        return view('livewire.admin.storage.index', [
            'locations' => $locations,
            'stats' => $stats,
            'warehouses' => $warehouses,
            'zones' => $zones,
            'types' => StorageLocationType::cases(),
            'statuses' => StorageLocationStatus::cases(),
        ]);
    }
}
