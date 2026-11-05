<?php

namespace Tests\Unit;

use App\Actions\Inventory\GenerateExpectedInventory;
use App\Enums\InventoryStatus;
use App\Enums\ItemCondition;
use App\Models\InventoryItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class InventoryItemModelTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function inventory_item_generates_sequential_inv_code_and_qr_payload(): void
    {
        $order = Order::factory()->create();

        $item1 = InventoryItem::create([
            'order_id' => $order->id,
            'name' => 'Meja Belajar Kayu',
            'status' => InventoryStatus::EXPECTED,
        ]);

        $year = date('Y');
        $this->assertEquals("INV-{$year}-000001", $item1->inventory_code);
        $this->assertNotNull($item1->qr_code_payload);
        $this->assertStringContainsString("INV-{$year}-000001", $item1->qr_code_payload);

        $item2 = InventoryItem::create([
            'order_id' => $order->id,
            'name' => 'Kursi Ergonomis',
            'status' => InventoryStatus::EXPECTED,
        ]);

        $this->assertEquals("INV-{$year}-000002", $item2->inventory_code);
    }

    #[Test]
    public function generate_expected_inventory_creates_physical_units_from_booking_items(): void
    {
        $order = Order::factory()->create();

        OrderItem::create([
            'order_id' => $order->id,
            'name' => 'Kardus Pakaian',
            'quantity' => 3,
            'estimated_size' => 'Sedang',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'name' => 'Cermin Dinding',
            'quantity' => 1,
            'estimated_size' => 'Fragile',
        ]);

        $action = app(GenerateExpectedInventory::class);
        $inventoryItems = $action->execute($order);

        $this->assertCount(4, $inventoryItems);
        $this->assertEquals(4, $order->inventoryItems()->count());

        $cermin = $order->inventoryItems()->where('name', 'Cermin Dinding')->first();
        $this->assertEquals(ItemCondition::FRAGILE, $cermin->condition);
        $this->assertEquals(InventoryStatus::EXPECTED, $cermin->status);
    }
}
