<?php

namespace App\Actions\Schedules;

use App\Actions\Orders\ChangeOrderStatus;
use App\Enums\OrderStatus;
use App\Enums\ScheduleStatus;
use App\Enums\ScheduleType;
use App\Models\Order;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CreateSchedule
{
    public function __construct(
        protected ChangeOrderStatus $changeOrderStatus
    ) {}

    /**
     * Create operational schedule and synchronize order status
     */
    public function execute(Order $order, array $data, ?User $creator = null): Schedule
    {
        return DB::transaction(function () use ($order, $data, $creator) {
            $type = $data['type'] instanceof ScheduleType ? $data['type'] : ScheduleType::from($data['type']);
            $status = $data['status'] ?? ScheduleStatus::SCHEDULED;
            if (!($status instanceof ScheduleStatus)) {
                $status = ScheduleStatus::from($status);
            }

            $schedule = Schedule::create([
                'order_id' => $order->id,
                'type' => $type,
                'status' => $status,
                'scheduled_date' => $data['scheduled_date'],
                'start_time' => $data['start_time'] ?? null,
                'end_time' => $data['end_time'] ?? null,
                'assigned_team' => $data['assigned_team'] ?? null,
                'vehicle' => $data['vehicle'] ?? null,
                'notes' => $data['notes'] ?? null,
                'created_by' => $creator?->id,
            ]);

            // Advance order to SCHEDULED if currently in CONFIRMED or PAID
            if (in_array($order->status, [OrderStatus::CONFIRMED, OrderStatus::PAID], true)) {
                if ($order->status->canTransitionTo(OrderStatus::SCHEDULED)) {
                    $this->changeOrderStatus->execute(
                        $order,
                        OrderStatus::SCHEDULED,
                        "Jadwal operasional {$type->label()} telah ditetapkan pada tanggal {$data['scheduled_date']} " . ($data['start_time'] ? "pukul {$data['start_time']}" : '') . ".",
                        $creator
                    );
                }
            }

            return $schedule->fresh(['order', 'creator']);
        });
    }
}
