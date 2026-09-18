<?php

namespace App\Livewire\Admin\Schedules;

use App\Actions\Schedules\CancelSchedule;
use App\Actions\Schedules\CompleteSchedule;
use App\Actions\Schedules\CreateSchedule;
use App\Actions\Schedules\StartSchedule;
use App\Enums\ScheduleType;
use App\Models\Order;
use App\Models\Schedule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Manager extends Component
{
    public Order $order;

    // Modal state
    public bool $showCreateModal = false;

    public string $type = 'PICKUP';

    public string $scheduledDate = '';

    public string $startTime = '';

    public string $endTime = '';

    public string $assignedTeam = '';

    public string $vehicle = '';

    public string $notes = '';

    protected function rules(): array
    {
        return [
            'type' => 'required|string|in:PICKUP,DELIVERY,REDELIVERY',
            'scheduledDate' => 'required|date',
            'startTime' => 'nullable|string|max:10',
            'endTime' => 'nullable|string|max:10',
            'assignedTeam' => 'nullable|string|max:150',
            'vehicle' => 'nullable|string|max:150',
            'notes' => 'nullable|string',
        ];
    }

    public function mount(Order $order): void
    {
        $this->order = $order;
        $this->scheduledDate = now()->addDay()->format('Y-m-d');
        $this->resetOperationalDefaults();
    }

    public function openCreateModal(?string $defaultType = null): void
    {
        Gate::authorize('manage-schedule');

        $this->type = $defaultType ?: 'PICKUP';
        $this->scheduledDate = now()->addDay()->format('Y-m-d');
        $this->resetOperationalDefaults();
        $this->notes = '';
        $this->resetValidation();
        $this->showCreateModal = true;
    }

    private function resetOperationalDefaults(): void
    {
        $this->startTime = (string) config('business.operations.schedule_start', '09:00');
        $this->endTime = (string) config('business.operations.schedule_end', '12:00');
        $this->assignedTeam = (string) config('business.operations.default_team', '');
        $this->vehicle = (string) config('business.operations.default_vehicle', '');
    }

    public function closeCreateModal(): void
    {
        $this->showCreateModal = false;
        $this->resetValidation();
    }

    public function saveSchedule(CreateSchedule $createSchedule): void
    {
        Gate::authorize('manage-schedule');

        $this->validate();

        $schedule = $createSchedule->execute($this->order, [
            'type' => $this->type,
            'scheduled_date' => $this->scheduledDate,
            'start_time' => $this->startTime ?: null,
            'end_time' => $this->endTime ?: null,
            'assigned_team' => $this->assignedTeam ?: null,
            'vehicle' => $this->vehicle ?: null,
            'notes' => $this->notes ?: null,
        ], Auth::user());

        $this->showCreateModal = false;
        $this->order->refresh();

        session()->flash('schedule_message', "Jadwal operasional {$schedule->type->label()} berhasil dibuat untuk tanggal {$schedule->scheduled_date->format('d M Y')}.");
    }

    public function startMission(int $scheduleId, StartSchedule $startSchedule): void
    {
        Gate::authorize('manage-schedule');

        $schedule = Schedule::findOrFail($scheduleId);
        $startSchedule->execute($schedule, Auth::user());

        $this->order->refresh();
        session()->flash('schedule_message', "Misi {$schedule->type->label()} sedang berjalan.");
    }

    public function completeMission(int $scheduleId, CompleteSchedule $completeSchedule): void
    {
        Gate::authorize('manage-schedule');

        $schedule = Schedule::findOrFail($scheduleId);
        $completeSchedule->execute($schedule, Auth::user());

        $this->order->refresh();
        session()->flash('schedule_message', "Misi {$schedule->type->label()} berhasil diselesaikan!");
    }

    public function cancelMission(int $scheduleId, CancelSchedule $cancelSchedule): void
    {
        Gate::authorize('manage-schedule');

        $schedule = Schedule::findOrFail($scheduleId);
        $cancelSchedule->execute($schedule, 'Dibatalkan oleh admin');

        $this->order->refresh();
        session()->flash('schedule_message', 'Jadwal telah dibatalkan.');
    }

    public function render()
    {
        $schedules = $this->order->schedules()->get();

        return view('livewire.admin.schedules.manager', [
            'schedules' => $schedules,
            'types' => ScheduleType::cases(),
        ]);
    }
}
