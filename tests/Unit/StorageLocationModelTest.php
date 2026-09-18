<?php

namespace Tests\Unit;

use App\Enums\InventoryStatus;
use App\Enums\StorageLocationStatus;
use App\Enums\StorageLocationType;
use App\Models\InventoryItem;
use App\Models\Order;
use App\Models\StorageLocation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class StorageLocationModelTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function format_code_generates_standard_hierarchical_code(): void
    {
        $code = StorageLocation::formatCode('mlg01', 'a', 'r02', 'l03');
        $this->assertEquals('MLG01-A-R02-L03', $code);
    }

    #[Test]
    public function storage_location_computes_occupancy_and_availability(): void
    {
        $location = StorageLocation::create([
            'code' => 'MLG01-A-R01-L01',
            'warehouse' => 'MLG01',
            'zone' => 'A',
            'rack' => 'R01',
            'level' => 'L01',
            'type' => StorageLocationType::STANDARD_RACK,
            'status' => StorageLocationStatus::AVAILABLE,
            'capacity' => 2,
        ]);

        $this->assertEquals(0, $location->occupiedCount());
        $this->assertEquals(2, $location->remainingCapacity());
        $this->assertFalse($location->isFull());
        $this->assertTrue($location->isAvailable());

        $order = Order::factory()->create();

        // Store 1st item
        InventoryItem::create([
            'order_id' => $order->id,
            'storage_location_id' => $location->id,
            'name' => 'Box 1',
            'status' => InventoryStatus::STORED,
        ]);

        $location->refresh();
        $this->assertEquals(1, $location->occupiedCount());
        $this->assertEquals(1, $location->remainingCapacity());
        $this->assertFalse($location->isFull());
        $this->assertTrue($location->isAvailable());

        // Store 2nd item (reaches full capacity)
        InventoryItem::create([
            'order_id' => $order->id,
            'storage_location_id' => $location->id,
            'name' => 'Box 2',
            'status' => InventoryStatus::STORED,
        ]);

        $location->refresh();
        $this->assertEquals(2, $location->occupiedCount());
        $this->assertEquals(0, $location->remainingCapacity());
        $this->assertTrue($location->isFull());
        $this->assertFalse($location->isAvailable());
    }
}
