<?php

namespace App\Actions\Schedules;

use App\Enums\ScheduleStatus;
use App\Models\Schedule;

class CancelSchedule
{
    public function execute(Schedule $schedule, ?string $reason = null): Schedule
    {
        $notes = $reason ? ($schedule->notes ? "{$schedule->notes}\n[Dibatalkan: {$reason}]" : "Dibatalkan: {$reason}") : $schedule->notes;

        $schedule->update([
            'status' => ScheduleStatus::CANCELLED,
            'notes' => $notes,
        ]);

        return $schedule->fresh();
    }
}
