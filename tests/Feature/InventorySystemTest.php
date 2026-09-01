<?php

namespace Tests\Feature;

use App\Enums\InventoryStatus;
use App\Enums\ItemCondition;
use App\Enums\OrderStatus;
use App\Livewire\Admin\Inventory\Index as InventoryIndex;
use App\Livewire\Admin\Inventory\Manager as InventoryManager;
use App\Models\InventoryItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class InventorySystemTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $operation;

    protected Order $order;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
        $this->operation = User::factory()->operation()->create();
        $this->order = Order::factory()->create([
            'status' => OrderStatus::PICKED_UP,
        ]);

        OrderItem::create([
            'order_id' => $this->order->id,
            'name' => 'Kulkas 2 Pintu',
            'quantity' => 1,
            'estimated_size' => 'Besar',
        ]);
    }

    #[Test]
    public function operation_can_access_inventory_register_while_admin_is_denied(): void
    {
        $this->actingAs($this->operation);
        $this->get('/admin/inventory')->assertStatus(200);

        $this->actingAs($this->admin);
        $this->get('/admin/inventory')->assertStatus(403);
    }

    #[Test]
    public function field_team_can_generate_and_receive_inventory_items(): void
    {
        $this->actingAs($this->operation);

        Livewire::test(InventoryManager::class, ['order' => $this->order])
            ->call('generateExpected')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('inventory_items', [
            'order_id' => $this->order->id,
            'name' => 'Kulkas 2 Pintu',
            'status' => InventoryStatus::EXPECTED->value,
        ]);

        $item = $this->order->inventoryItems()->first();

        Livewire::test(InventoryManager::class, ['order' => $this->order])
            ->call('receive', $item->id);

        $item->refresh();
        $this->assertEquals(InventoryStatus::RECEIVED, $item->status);
        $this->assertEquals($this->operation->id, $item->received_by);
        $this->assertNotNull($item->received_at);
    }

    #[Test]
    public function team_can_perform_qc_check_and_store_to_rack(): void
    {
        $this->actingAs($this->operation);

        $item = InventoryItem::create([
            'order_id' => $this->order->id,
            'name' => 'Lemari Pakaian',
            'status' => InventoryStatus::RECEIVED,
        ]);

        // Quality check
        Livewire::test(InventoryManager::class, ['order' => $this->order])
            ->call('openCheckModal', $item->id)
            ->set('condition', ItemCondition::SCRATCHED->value)
            ->set('checkNotes', 'Ada lecet di bagian engsel pintu')
            ->call('confirmCheck');

        $item->refresh();
        $this->assertEquals(InventoryStatus::CHECKED, $item->status);
        $this->assertEquals(ItemCondition::SCRATCHED, $item->condition);
        $this->assertEquals('Ada lecet di bagian engsel pintu', $item->notes);

        // Store to rack
        Livewire::test(InventoryManager::class, ['order' => $this->order])
            ->call('openStoreModal', $item->id)
            ->set('storageLocation', 'Rak B-04 Gudang Dinoyo')
            ->call('confirmStore');

        $item->refresh();
        $this->assertEquals(InventoryStatus::STORED, $item->status);
        $this->assertEquals('Rak B-04 Gudang Dinoyo', $item->storage_location);
    }

    #[Test]
    public function inventory_index_can_filter_by_status_and_condition(): void
    {
        $itemGood = InventoryItem::create([
            'order_id' => $this->order->id,
            'name' => 'Barang Sempurna',
            'condition' => ItemCondition::GOOD,
            'status' => InventoryStatus::STORED,
        ]);

        $itemDamaged = InventoryItem::create([
            'order_id' => $this->order->id,
            'name' => 'Barang Cacat',
            'condition' => ItemCondition::DAMAGED,
            'status' => InventoryStatus::CHECKED,
        ]);

        $this->actingAs($this->operation);

        Livewire::test(InventoryIndex::class)
            ->set('statusFilter', InventoryStatus::STORED->value)
            ->assertSee($itemGood->inventory_code)
            ->assertDontSee($itemDamaged->inventory_code)
            ->set('statusFilter', '')
            ->set('conditionFilter', ItemCondition::DAMAGED->value)
            ->assertSee($itemDamaged->inventory_code)
            ->assertDontSee($itemGood->inventory_code);
    }
}
