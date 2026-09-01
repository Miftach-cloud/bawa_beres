<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\ScheduleStatus;
use App\Enums\ScheduleType;
use App\Livewire\Admin\Schedules\Index as ScheduleIndex;
use App\Livewire\Admin\Schedules\Manager as ScheduleManager;
use App\Models\Order;
use App\Models\Schedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SchedulingSystemTest extends TestCase
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
            'status' => OrderStatus::PAID,
        ]);
    }

    #[Test]
    public function both_admin_and_operation_can_access_schedule_board(): void
    {
        $this->actingAs($this->admin);
        $this->get('/admin/schedule')->assertStatus(200);

        $this->actingAs($this->operation);
        $this->get('/admin/schedule')->assertStatus(200);
    }

    #[Test]
    public function creating_schedule_transitions_paid_order_to_scheduled(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(ScheduleManager::class, ['order' => $this->order])
            ->call('openCreateModal', ScheduleType::PICKUP->value)
            ->set('scheduledDate', Carbon::tomorrow()->format('Y-m-d'))
            ->set('startTime', '08:30')
            ->set('endTime', '11:30')
            ->set('assignedTeam', 'Tim Lapangan 2')
            ->set('vehicle', 'GranMax N 4321 CD')
            ->call('saveSchedule')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('schedules', [
            'order_id' => $this->order->id,
            'type' => ScheduleType::PICKUP->value,
            'status' => ScheduleStatus::SCHEDULED->value,
            'assigned_team' => 'Tim Lapangan 2',
        ]);

        $this->order->refresh();
        $this->assertEquals(OrderStatus::SCHEDULED, $this->order->status);
    }

    #[Test]
    public function completing_pickup_schedule_transitions_order_to_picked_up(): void
    {
        $this->actingAs($this->operation);

        $schedule = Schedule::create([
            'order_id' => $this->order->id,
            'type' => ScheduleType::PICKUP,
            'status' => ScheduleStatus::IN_PROGRESS,
            'scheduled_date' => Carbon::today(),
            'assigned_team' => 'Tim Operasional A',
        ]);

        // Advance order to SCHEDULED
        $this->order->update(['status' => OrderStatus::SCHEDULED]);

        Livewire::test(ScheduleManager::class, ['order' => $this->order])
            ->call('completeMission', $schedule->id);

        $schedule->refresh();
        $this->order->refresh();

        $this->assertEquals(ScheduleStatus::COMPLETED, $schedule->status);
        $this->assertNotNull($schedule->completed_at);
        $this->assertEquals(OrderStatus::PICKED_UP, $this->order->status);
    }

    #[Test]
    public function operational_schedule_tabs_filter_by_today_and_tomorrow(): void
    {
        $todaySchedule = Schedule::create([
            'order_id' => $this->order->id,
            'type' => ScheduleType::PICKUP,
            'status' => ScheduleStatus::SCHEDULED,
            'scheduled_date' => Carbon::today(),
            'assigned_team' => 'Tim Hari Ini',
        ]);

        $tomorrowSchedule = Schedule::create([
            'order_id' => $this->order->id,
            'type' => ScheduleType::DELIVERY,
            'status' => ScheduleStatus::SCHEDULED,
            'scheduled_date' => Carbon::tomorrow(),
            'assigned_team' => 'Tim Besok Pagi',
        ]);

        $this->actingAs($this->operation);

        Livewire::test(ScheduleIndex::class)
            ->set('activeTab', 'today')
            ->assertSee('Tim Hari Ini')
            ->assertDontSee('Tim Besok Pagi')
            ->set('activeTab', 'tomorrow')
            ->assertSee('Tim Besok Pagi')
            ->assertDontSee('Tim Hari Ini');
    }
}
