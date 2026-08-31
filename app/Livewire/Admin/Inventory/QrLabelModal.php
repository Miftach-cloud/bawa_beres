<?php

namespace App\Livewire\Admin\Inventory;

use App\Models\InventoryItem;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class QrLabelModal extends Component
{
    public ?InventoryItem $item = null;
    public bool $show = false;

    protected $listeners = [
        'openQrLabel' => 'open',
    ];

    public function open(int $itemId): void
    {
        Gate::authorize('manage-inventory');

        $this->item = InventoryItem::with(['order.customer', 'storageLocation'])->findOrFail($itemId);
        $this->show = true;
    }

    public function close(): void
    {
        $this->show = false;
        $this->item = null;
    }

    public function render()
    {
        return view('livewire.admin.inventory.qr-label-modal');
    }
}
