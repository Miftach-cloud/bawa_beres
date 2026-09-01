<?php

namespace Tests\Feature;

use App\Actions\Storage\AssignInventoryToLocation;
use App\Actions\Storage\CreateStorageLocation;
use App\Actions\Storage\VacateInventoryFromLocation;
use App\Enums\InventoryStatus;
use App\Enums\OrderStatus;
use App\Enums\StorageLocationStatus;
use App\Enums\StorageLocationType;
use App\Livewire\Admin\Storage\Index as StorageIndex;
use App\Models\InventoryItem;
use App\Models\Order;
use App\Models\StorageLocation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class StorageLocationSystemTest extends TestCase
{
    use RefreshDatabase;

    protected User $operation;
    protected User $admin;
    protected Order $order;
    protected StorageLocation $location;

    protected function setUp(): void
    {
        parent::setUp();

        $this->operation = User::factory()->operation()->create();
        $this->admin = User::factory()->admin()->create();
        $this->order = Order::factory()->create([
            'status' => OrderStatus::PICKED_UP,
        ]);

        $this->location = StorageLocation::create([
            'code' => 'MLG01-A-R01-L01',
            'warehouse' => 'MLG01',
            'zone' => 'A',
            'rack' => 'R01',
            'level' => 'L01',
            'type' => StorageLocationType::STANDARD_RACK,
            'status' => StorageLocationStatus::AVAILABLE,
            'capacity' => 1,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function operation_role_can_access_storage_management_board(): void
    {
        $this->actingAs($this->operation);
        $this->get('/admin/storage')->assertStatus(200);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function action_assigns_inventory_item_and_updates_location_status_when_full(): void
    {
        $item = InventoryItem::create([
            'order_id' => $this->order->id,
            'name' => 'Meja Kayu Jati',
            'status' => InventoryStatus::CHECKED,
        ]);

        $action = app(AssignInventoryToLocation::class);
        $action->execute($item, $this->location, $this->operation);

        $item->refresh();
        $this->location->refresh();
        $this->order->refresh();

        $this->assertEquals(InventoryStatus::STORED, $item->status);
        $this->assertEquals($this->location->id, $item->storage_location_id);
        $this->assertEquals($this->location->code, $item->storage_location);

        // Location has reached max capacity of 1
        $this->assertEquals(StorageLocationStatus::OCCUPIED, $this->location->status);

        // Order has all items stored -> transitions to STORED
        $this->assertEquals(OrderStatus::STORED, $this->order->status);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function action_vacates_item_and_restores_location_availability(): void
    {
        $item = InventoryItem::create([
            'order_id' => $this->order->id,
            'storage_location_id' => $this->location->id,
            'name' => 'Meja Kayu Jati',
            'status' => InventoryStatus::STORED,
        ]);

        $this->location->update(['status' => StorageLocationStatus::OCCUPIED]);

        $vacateAction = app(VacateInventoryFromLocation::class);
        $vacateAction->execute($item);

        $item->refresh();
        $this->location->refresh();

        $this->assertNull($item->storage_location_id);
        $this->assertEquals(StorageLocationStatus::AVAILABLE, $this->location->status);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function livewire_storage_index_renders_and_filters_locations(): void
    {
        $this->actingAs($this->operation);

        Livewire::test(StorageIndex::class)
            ->assertSee($this->location->code)
            ->set('warehouseFilter', 'MLG01')
            ->assertSee($this->location->code)
            ->set('warehouseFilter', 'MLG02')
            ->assertDontSee($this->location->code);
    }
}
