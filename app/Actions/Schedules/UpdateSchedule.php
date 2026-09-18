<?php

namespace App\Actions\Schedules;

use App\Enums\ScheduleType;
use App\Models\Schedule;

class UpdateSchedule
{
    public function execute(Schedule $schedule, array $data): Schedule
    {
        $type = isset($data['type'])
            ? ($data['type'] instanceof ScheduleType ? $data['type'] : ScheduleType::from($data['type']))
            : $schedule->type;

        $schedule->update([
            'type' => $type,
            'scheduled_date' => $data['scheduled_date'] ?? $schedule->scheduled_date,
            'start_time' => $data['start_time'] ?? $schedule->start_time,
            'end_time' => $data['end_time'] ?? $schedule->end_time,
            'assigned_team' => $data['assigned_team'] ?? $schedule->assigned_team,
            'vehicle' => $data['vehicle'] ?? $schedule->vehicle,
            'notes' => $data['notes'] ?? $schedule->notes,
        ]);

        return $schedule->fresh();
    }
}
