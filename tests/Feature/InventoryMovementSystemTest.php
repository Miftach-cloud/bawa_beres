<?php

namespace Tests\Feature;

use App\Actions\Movements\RelocateInventoryItem;
use App\Actions\Storage\AssignInventoryToLocation;
use App\Actions\Storage\VacateInventoryFromLocation;
use App\Enums\InventoryStatus;
use App\Enums\MovementType;
use App\Enums\StorageLocationStatus;
use App\Enums\StorageLocationType;
use App\Livewire\Admin\Inventory\Manager as InventoryManager;
use App\Livewire\Admin\Inventory\MovementTimelineModal;
use App\Models\InventoryItem;
use App\Models\Order;
use App\Models\StorageLocation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class InventoryMovementSystemTest extends TestCase
{
    use RefreshDatabase;

    protected User $operation;

    protected Order $order;

    protected InventoryItem $item;

    protected StorageLocation $locationA;

    protected StorageLocation $locationB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->operation = User::factory()->operation()->create();
        $this->order = Order::factory()->create();
        $this->item = InventoryItem::create([
            'order_id' => $this->order->id,
            'name' => 'Kipas Angin Berdiri',
            'status' => InventoryStatus::RECEIVED,
        ]);

        $this->locationA = StorageLocation::create([
            'code' => 'MLG01-A-R01-L01',
            'warehouse' => 'MLG01',
            'zone' => 'A',
            'rack' => 'R01',
            'level' => 'L01',
            'type' => StorageLocationType::STANDARD_RACK,
            'status' => StorageLocationStatus::AVAILABLE,
            'capacity' => 1,
        ]);

        $this->locationB = StorageLocation::create([
            'code' => 'MLG01-B-R02-L03',
            'warehouse' => 'MLG01',
            'zone' => 'B',
            'rack' => 'R02',
            'level' => 'L03',
            'type' => StorageLocationType::STANDARD_RACK,
            'status' => StorageLocationStatus::AVAILABLE,
            'capacity' => 2,
        ]);
    }

    #[Test]
    public function full_movement_journey_inbound_relocation_and_outbound(): void
    {
        // 1. Inbound to Location A
        $assignAction = app(AssignInventoryToLocation::class);
        $assignAction->execute($this->item, $this->locationA, $this->operation);

        $this->item->refresh();
        $this->locationA->refresh();

        $this->assertEquals(StorageLocationStatus::OCCUPIED, $this->locationA->status); // Capacity 1 full
        $this->assertEquals(1, $this->item->movements()->count());
        $this->assertEquals(MovementType::INBOUND, $this->item->latestMovement->movement_type);
        $this->assertEquals('MLG01-A-R01-L01', $this->item->latestMovement->to_location_code);

        // 2. Relocation to Location B
        $relocateAction = app(RelocateInventoryItem::class);
        $relocateAction->execute($this->item, $this->locationB, $this->operation, 'Pindah ke zona B');

        $this->item->refresh();
        $this->locationA->refresh();
        $this->locationB->refresh();

        $this->assertEquals(StorageLocationStatus::AVAILABLE, $this->locationA->status); // Freed up!
        $this->assertEquals('MLG01-B-R02-L03', $this->item->storage_location);
        $this->assertEquals(2, $this->item->movements()->count());
        $this->assertEquals(MovementType::RELOCATION, $this->item->latestMovement->movement_type);
        $this->assertEquals('MLG01-A-R01-L01', $this->item->latestMovement->from_location_code);
        $this->assertEquals('MLG01-B-R02-L03', $this->item->latestMovement->to_location_code);

        // 3. Outbound from Location B
        $vacateAction = app(VacateInventoryFromLocation::class);
        $vacateAction->execute($this->item, $this->operation, 'Barang diserahterimakan ke driver delivery');

        $this->item->refresh();
        $this->assertNull($this->item->storage_location_id);
        $this->assertEquals(3, $this->item->movements()->count());
        $this->assertEquals(MovementType::OUTBOUND, $this->item->latestMovement->movement_type);
    }

    #[Test]
    public function movement_timeline_modal_renders_chronological_events(): void
    {
        $assignAction = app(AssignInventoryToLocation::class);
        $assignAction->execute($this->item, $this->locationA, $this->operation);

        $relocateAction = app(RelocateInventoryItem::class);
        $relocateAction->execute($this->item, $this->locationB, $this->operation, 'Pindah ke zona B');

        $this->actingAs($this->operation);

        Livewire::test(MovementTimelineModal::class)
            ->call('open', $this->item->id)
            ->assertSee($this->item->inventory_code)
            ->assertSee('MLG01-A-R01-L01')
            ->assertSee('MLG01-B-R02-L03')
            ->assertSee('Pindah ke zona B');
    }

    #[Test]
    public function livewire_manager_performs_relocation(): void
    {
        $assignAction = app(AssignInventoryToLocation::class);
        $assignAction->execute($this->item, $this->locationA, $this->operation);

        $this->actingAs($this->operation);

        Livewire::test(InventoryManager::class, ['order' => $this->order])
            ->call('openRelocateModal', $this->item->id)
            ->set('relocateLocationId', $this->locationB->id)
            ->set('relocateNotes', 'Relokasi via admin panel')
            ->call('confirmRelocate')
            ->assertHasNoErrors();

        $this->item->refresh();
        $this->assertEquals($this->locationB->id, $this->item->storage_location_id);
        $this->assertEquals('MLG01-B-R02-L03', $this->item->storage_location);
    }
}
