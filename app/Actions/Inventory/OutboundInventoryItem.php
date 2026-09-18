<?php

namespace App\Actions\Inventory;

use App\Actions\Storage\VacateInventoryFromLocation;
use App\Enums\InventoryStatus;
use App\Models\InventoryItem;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OutboundInventoryItem
{
    public function __construct(
        protected VacateInventoryFromLocation $vacateInventoryFromLocation
    ) {}

    public function execute(InventoryItem $item, ?User $performer = null): InventoryItem
    {
        return DB::transaction(function () use ($item, $performer) {
            $item->refresh();

            if ($item->status !== InventoryStatus::STORED) {
                throw ValidationException::withMessages([
                    'inventory' => 'Hanya barang berstatus STORED yang dapat diproses outbound.',
                ]);
            }

            $this->vacateInventoryFromLocation->execute($item, $performer);
            $item->update([
                'status' => InventoryStatus::OUTBOUND,
            ]);

            return $item->fresh();
        });
    }
}
