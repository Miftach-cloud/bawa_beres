<?php

namespace Tests\Unit;

use App\Actions\Movements\RecordMovement;
use App\Enums\MovementType;
use App\Enums\StorageLocationStatus;
use App\Enums\StorageLocationType;
use App\Models\InventoryItem;
use App\Models\Order;
use App\Models\StorageLocation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class InventoryMovementModelTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function record_movement_action_creates_immutable_log(): void
    {
        $user = User::factory()->operation()->create();
        $order = Order::factory()->create();
        $item = InventoryItem::create([
            'order_id' => $order->id,
            'name' => 'Lemari 2 Pintu',
        ]);

        $locA = StorageLocation::create([
            'code' => 'MLG01-A-R01-L01',
            'warehouse' => 'MLG01',
            'zone' => 'A',
            'rack' => 'R01',
            'level' => 'L01',
            'type' => StorageLocationType::STANDARD_RACK,
            'status' => StorageLocationStatus::AVAILABLE,
            'capacity' => 5,
        ]);

        $locB = StorageLocation::create([
            'code' => 'MLG01-B-R02-L03',
            'warehouse' => 'MLG01',
            'zone' => 'B',
            'rack' => 'R02',
            'level' => 'L03',
            'type' => StorageLocationType::STANDARD_RACK,
            'status' => StorageLocationStatus::AVAILABLE,
            'capacity' => 5,
        ]);

        $action = app(RecordMovement::class);
        $movement = $action->execute(
            item: $item,
            type: MovementType::RELOCATION,
            fromLocation: $locA,
            toLocation: $locB,
            performer: $user,
            notes: 'Pindah ke zona furniture'
        );

        $this->assertEquals(MovementType::RELOCATION, $movement->movement_type);
        $this->assertEquals($locA->id, $movement->from_location_id);
        $this->assertEquals('MLG01-A-R01-L01', $movement->from_location_code);
        $this->assertEquals($locB->id, $movement->to_location_id);
        $this->assertEquals('MLG01-B-R02-L03', $movement->to_location_code);
        $this->assertEquals($user->id, $movement->performed_by);
        $this->assertEquals('Pindah ke zona furniture', $movement->notes);

        $this->assertCount(1, $item->movements);
        $this->assertEquals($movement->id, $item->latestMovement->id);
    }
}
