<?php

namespace App\Actions\Schedules;

use App\Actions\Orders\ChangeOrderStatus;
use App\Enums\OrderStatus;
use App\Enums\ScheduleStatus;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class StartSchedule
{
    public function __construct(
        protected ChangeOrderStatus $changeOrderStatus
    ) {}

    /**
     * Start the operational mission (mark in progress)
     */
    public function execute(Schedule $schedule, ?User $actor = null): Schedule
    {
        return DB::transaction(function () use ($schedule, $actor) {
            $schedule->update([
                'status' => ScheduleStatus::IN_PROGRESS,
            ]);

            $order = $schedule->order;

            // If order was picked up and schedule is delivery, transition to in transit
            if ($schedule->isDelivery() && $order->status->canTransitionTo(OrderStatus::IN_TRANSIT)) {
                $this->changeOrderStatus->execute(
                    $order,
                    OrderStatus::IN_TRANSIT,
                    "Armada {$schedule->vehicle} berangkat menuju alamat pengantaran.",
                    $actor
                );
            }

            return $schedule->fresh(['order']);
        });
    }
}
