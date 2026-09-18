<?php

namespace App\Livewire\Admin\Inventory;

use App\Models\InventoryItem;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class MovementTimelineModal extends Component
{
    public ?InventoryItem $item = null;

    public bool $show = false;

    protected $listeners = [
        'openMovementTimeline' => 'open',
    ];

    public function open(int $itemId): void
    {
        Gate::authorize('manage-storage');

        $this->item = InventoryItem::with([
            'order.customer',
            'movements.fromLocation',
            'movements.toLocation',
            'movements.performer',
        ])->findOrFail($itemId);

        $this->show = true;
    }

    public function close(): void
    {
        $this->show = false;
        $this->item = null;
    }

    public function render()
    {
        $movements = collect();
        if ($this->item) {
            $movements = $this->item->movements;
        }

        return view('livewire.admin.inventory.movement-timeline-modal', [
            'movements' => $movements,
        ]);
    }
}
