<?php

namespace Tests\Unit;

use App\Actions\Schedules\CreateSchedule;
use App\Enums\ScheduleStatus;
use App\Enums\ScheduleType;
use App\Models\Order;
use App\Models\Schedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScheduleModelTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function schedule_scopes_filter_by_relative_dates(): void
    {
        $order = Order::factory()->create();

        $today = Schedule::create([
            'order_id' => $order->id,
            'type' => ScheduleType::PICKUP,
            'status' => ScheduleStatus::SCHEDULED,
            'scheduled_date' => Carbon::today(),
        ]);

        $tomorrow = Schedule::create([
            'order_id' => $order->id,
            'type' => ScheduleType::DELIVERY,
            'status' => ScheduleStatus::SCHEDULED,
            'scheduled_date' => Carbon::tomorrow(),
        ]);

        $upcoming = Schedule::create([
            'order_id' => $order->id,
            'type' => ScheduleType::PICKUP,
            'status' => ScheduleStatus::SCHEDULED,
            'scheduled_date' => Carbon::today()->addDays(5),
        ]);

        $this->assertEquals(1, Schedule::today()->count());
        $this->assertEquals($today->id, Schedule::today()->first()->id);

        $this->assertEquals(1, Schedule::tomorrow()->count());
        $this->assertEquals($tomorrow->id, Schedule::tomorrow()->first()->id);

        $this->assertEquals(1, Schedule::upcoming()->count());
        $this->assertEquals($upcoming->id, Schedule::upcoming()->first()->id);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function order_schedule_relations_resolve_pickup_and_delivery(): void
    {
        $order = Order::factory()->create();

        $pickup = Schedule::create([
            'order_id' => $order->id,
            'type' => ScheduleType::PICKUP,
            'status' => ScheduleStatus::SCHEDULED,
            'scheduled_date' => Carbon::today(),
            'assigned_team' => 'Tim Pickup 1',
        ]);

        $delivery = Schedule::create([
            'order_id' => $order->id,
            'type' => ScheduleType::DELIVERY,
            'status' => ScheduleStatus::SCHEDULED,
            'scheduled_date' => Carbon::tomorrow(),
            'assigned_team' => 'Tim Delivery 1',
        ]);

        $order->refresh();

        $this->assertCount(2, $order->schedules);
        $this->assertEquals($pickup->id, $order->pickupSchedule->id);
        $this->assertEquals($delivery->id, $order->deliverySchedule->id);
    }
}
