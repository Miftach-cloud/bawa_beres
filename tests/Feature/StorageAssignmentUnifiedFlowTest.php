<?php

namespace Tests\Feature;

use App\Enums\InventoryStatus;
use App\Enums\MovementType;
use App\Enums\OrderStatus;
use App\Enums\StorageLocationType;
use App\Livewire\Admin\Inventory\Index as InventoryIndex;
use App\Livewire\Admin\Inventory\Manager as InventoryManager;
use App\Models\Customer;
use App\Models\InventoryItem;
use App\Models\Order;
use App\Models\Service;
use App\Models\StorageLocation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class StorageAssignmentUnifiedFlowTest extends TestCase
{
    use RefreshDatabase;

    protected User $operation;

    protected Customer $customer;

    protected Service $service;

    protected Order $order;

    protected InventoryItem $item;

    protected StorageLocation $locationA;

    protected StorageLocation $locationB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->operation = User::factory()->operation()->create();

        $this->customer = Customer::factory()->create([
            'name' => 'Rudi Hartono',
            'phone' => '081234111222',
        ]);

        $this->service = Service::factory()->create([
            'name' => 'Storage Mahasiswa Bulanan',
        ]);

        $this->order = Order::factory()->create([
            'customer_id' => $this->customer->id,
            'service_id' => $this->service->id,
            'status' => OrderStatus::PROCESSING,
        ]);

        $this->item = InventoryItem::create([
            'order_id' => $this->order->id,
            'name' => 'Kardus Buku & Dokumen',
            'status' => InventoryStatus::CHECKED,
        ]);

        $this->locationA = StorageLocation::create([
            'code' => 'MLG01-RAK-A01',
            'warehouse' => 'Gudang Utama Malang',
            'zone' => 'Zone A',
            'rack' => 'R01',
            'level' => 'L01',
            'type' => StorageLocationType::STANDARD_RACK,
            'capacity' => 5,
        ]);

        $this->locationB = StorageLocation::create([
            'code' => 'MLG01-RAK-B02',
            'warehouse' => 'Gudang Utama Malang',
            'zone' => 'Zone B',
            'rack' => 'R02',
            'level' => 'L01',
            'type' => StorageLocationType::STANDARD_RACK,
            'capacity' => 5,
        ]);
    }

    #[Test]
    public function inventory_schema_uses_only_the_authoritative_storage_location_foreign_key(): void
    {
        $this->assertTrue(Schema::hasColumn('inventory_items', 'storage_location_id'));
        $this->assertFalse(Schema::hasColumn('inventory_items', 'storage_location'));
    }

    #[Test]
    public function assigning_inventory_to_location_synchronizes_fk_and_records_movement_history(): void
    {
        $this->actingAs($this->operation);

        Livewire::test(InventoryManager::class, ['order' => $this->order])
            ->call('openStoreModal', $this->item->id)
            ->set('selectedLocationId', $this->locationA->id)
            ->call('confirmStore')
            ->assertHasNoErrors();

        $this->item->refresh();

        // Authoritative location relationship
        $this->assertSame($this->locationA->id, $this->item->storage_location_id);
        $this->assertSame('MLG01-RAK-A01', $this->item->storageLocation->code);
        $this->assertSame(InventoryStatus::STORED, $this->item->status);

        // Occupancy updated
        $this->assertSame(1, $this->locationA->fresh()->occupiedCount());

        // Movement record created
        $this->assertDatabaseHas('inventory_movements', [
            'inventory_item_id' => $this->item->id,
            'to_location_id' => $this->locationA->id,
            'movement_type' => MovementType::INBOUND->value,
            'to_location_code' => 'MLG01-RAK-A01',
        ]);
    }

    #[Test]
    public function inventory_index_livewire_component_enforces_authoritative_storage_assignment(): void
    {
        $this->actingAs($this->operation);

        Livewire::test(InventoryIndex::class)
            ->call('openStoreModal', $this->item->id)
            ->set('selectedLocationId', $this->locationB->id)
            ->call('confirmStore')
            ->assertHasNoErrors();

        $this->item->refresh();
        $this->assertSame($this->locationB->id, $this->item->storage_location_id);
        $this->assertSame('MLG01-RAK-B02', $this->item->storageLocation->code);

        $this->assertDatabaseHas('inventory_movements', [
            'inventory_item_id' => $this->item->id,
            'to_location_id' => $this->locationB->id,
            'movement_type' => MovementType::INBOUND->value,
        ]);
    }
}
