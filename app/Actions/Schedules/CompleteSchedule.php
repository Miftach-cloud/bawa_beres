<?php

namespace App\Actions\Schedules;

use App\Actions\Orders\ChangeOrderStatus;
use App\Enums\OrderStatus;
use App\Enums\ScheduleStatus;
use App\Enums\ScheduleType;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CompleteSchedule
{
    public function __construct(
        protected ChangeOrderStatus $changeOrderStatus
    ) {}

    /**
     * Complete the operational schedule and synchronize order status
     */
    public function execute(Schedule $schedule, ?User $actor = null): Schedule
    {
        return DB::transaction(function () use ($schedule, $actor) {
            $schedule->update([
                'status' => ScheduleStatus::COMPLETED,
                'completed_at' => now(),
            ]);

            $order = $schedule->order;

            // Trigger order status transition based on schedule type
            if ($schedule->type === ScheduleType::PICKUP) {
                if ($order->status->canTransitionTo(OrderStatus::PICKED_UP)) {
                    $this->changeOrderStatus->execute(
                        $order,
                        OrderStatus::PICKED_UP,
                        "Misi penjemputan barang (Pickup) selesai dilaksanakan oleh {$schedule->assigned_team}.",
                        $actor
                    );
                }
            } elseif (in_array($schedule->type, [ScheduleType::DELIVERY, ScheduleType::REDELIVERY], true)) {
                if ($order->status->canTransitionTo(OrderStatus::DELIVERED)) {
                    $this->changeOrderStatus->execute(
                        $order,
                        OrderStatus::DELIVERED,
                        'Misi pengantaran barang (Delivery) selesai diantar ke alamat tujuan.',
                        $actor
                    );
                }
            }

            return $schedule->fresh(['order']);
        });
    }
}
