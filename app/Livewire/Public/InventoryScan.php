<?php

namespace App\Livewire\Public;

use App\Actions\Inventory\CheckInventoryItem;
use App\Actions\Inventory\ReceiveInventoryItem;
use App\Actions\Inventory\ReleaseInventoryItem;
use App\Actions\Movements\RelocateInventoryItem;
use App\Actions\Storage\AssignInventoryToLocation;
use App\Actions\Storage\VacateInventoryFromLocation;
use App\Enums\ItemCondition;
use App\Models\InventoryItem;
use App\Models\StorageLocation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class InventoryScan extends Component
{
    public string $code;

    public ?InventoryItem $item = null;

    // Quick Store / Relocate Modal states
    public bool $showStoreModal = false;

    public ?int $selectedLocationId = null;

    public string $storageLocation = '';

    public bool $showRelocateModal = false;

    public ?int $relocateLocationId = null;

    public string $relocateNotes = '';

    // Quick QC Modal
    public bool $showCheckModal = false;

    public string $condition = 'GOOD';

    public string $checkNotes = '';

    public function mount(string $code): void
    {
        $this->code = $code;

        $query = InventoryItem::with([
            'order.customer',
            'storageLocation',
            'photos',
            'movements.fromLocation',
            'movements.toLocation',
            'movements.performer',
        ]);

        if (Auth::check()) {
            $this->item = $query->where('qr_code', $code)
                ->orWhere('inventory_code', $code)
                ->first();
        } else {
            $this->item = $query->where('qr_code', $code)->first();
        }

        if ($this->item && Auth::check()) {
            $this->condition = $this->item->condition->value;
            $this->checkNotes = $this->item->notes ?? '';
            $this->storageLocation = $this->item->storage_location ?? '';
            $this->selectedLocationId = $this->item->storage_location_id;
        }
    }

    public function receive(ReceiveInventoryItem $action): void
    {
        Gate::authorize('manage-inventory');

        if (! $this->item) {
            return;
        }

        $action->execute($this->item, Auth::user());
        $this->item->refresh();

        session()->flash('scan_message', "Barang #{$this->item->inventory_code} telah ditandai Diterima (RECEIVED).");
    }

    public function openCheckModal(): void
    {
        Gate::authorize('manage-inventory');
        $this->showCheckModal = true;
    }

    public function closeCheckModal(): void
    {
        $this->showCheckModal = false;
    }

    public function confirmCheck(CheckInventoryItem $action): void
    {
        Gate::authorize('manage-inventory');

        if (! $this->item) {
            return;
        }

        $action->execute($this->item, $this->condition, $this->checkNotes);
        $this->showCheckModal = false;
        $this->item->refresh();

        session()->flash('scan_message', 'Pemeriksaan QC kondisi fisik berhasil disimpan (CHECKED).');
    }

    public function openStoreModal(): void
    {
        Gate::authorize('manage-inventory');
        $this->showStoreModal = true;
    }

    public function closeStoreModal(): void
    {
        $this->showStoreModal = false;
    }

    public function confirmStore(AssignInventoryToLocation $assignAction): void
    {
        Gate::authorize('manage-inventory');

        if (! $this->item) {
            return;
        }

        $this->validate([
            'selectedLocationId' => 'required|exists:storage_locations,id',
        ]);

        $location = StorageLocation::findOrFail($this->selectedLocationId);
        $assignAction->execute($this->item, $location, Auth::user());

        $this->showStoreModal = false;
        $this->selectedLocationId = null;
        $this->item->refresh();

        session()->flash('scan_message', "Barang fisik berhasil disimpan di rak gudang {$location->code}.");
    }

    public function openRelocateModal(): void
    {
        Gate::authorize('manage-storage');
        $this->showRelocateModal = true;
    }

    public function closeRelocateModal(): void
    {
        $this->showRelocateModal = false;
    }

    public function confirmRelocate(RelocateInventoryItem $relocateAction): void
    {
        Gate::authorize('manage-storage');

        $this->validate([
            'relocateLocationId' => 'required|exists:storage_locations,id',
        ]);

        if (! $this->item) {
            return;
        }

        $targetLocation = StorageLocation::findOrFail($this->relocateLocationId);
        $relocateAction->execute($this->item, $targetLocation, Auth::user(), $this->relocateNotes ?: null);

        $this->showRelocateModal = false;
        $this->item->refresh();

        session()->flash('scan_message', "Barang berhasil direlokasi ke rak {$targetLocation->code}.");
    }

    public function release(ReleaseInventoryItem $action, VacateInventoryFromLocation $vacateAction): void
    {
        Gate::authorize('manage-inventory');

        if (! $this->item) {
            return;
        }

        $vacateAction->execute($this->item, Auth::user());
        $action->execute($this->item);
        $this->item->refresh();

        session()->flash('scan_message', 'Barang telah diserahterimakan (RELEASED).');
    }

    public function render()
    {
        $availableLocations = collect();
        if (Auth::check()) {
            $availableLocations = StorageLocation::available()->get();
        }

        return view('livewire.public.inventory-scan', [
            'item' => $this->item,
            'availableLocations' => $availableLocations,
            'conditions' => ItemCondition::cases(),
            'isInternalStaff' => Auth::check(),
        ])->layout('layouts.public', ['title' => $this->item ? "Scan #{$this->item->inventory_code} - BawaBeres" : 'Scan QR Barang']);
    }
}
