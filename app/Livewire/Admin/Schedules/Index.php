<?php

namespace App\Livewire\Admin\Schedules;

use App\Actions\Schedules\CompleteSchedule;
use App\Actions\Schedules\CreateSchedule;
use App\Actions\Schedules\StartSchedule;
use App\Enums\ScheduleStatus;
use App\Enums\ScheduleType;
use App\Models\Order;
use App\Models\Schedule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Jadwal & Armada — Admin Bawa Beres')]
class Index extends Component
{
    use WithPagination;

    // View tab: today, tomorrow, upcoming, all
    public string $activeTab = 'today';

    public string $search = '';

    public string $typeFilter = '';

    public string $statusFilter = '';

    // Create Modal
    public bool $showCreateModal = false;

    public ?int $selectedOrderId = null;

    public string $type = 'PICKUP';

    public string $scheduledDate = '';

    public string $startTime = '09:00';

    public string $endTime = '12:00';

    public string $assignedTeam = 'Tim Lapangan 1 (Budi & Eko)';

    public string $vehicle = 'Daihatsu GranMax Pick-up N 1234 AB';

    public string $notes = '';

    protected function rules(): array
    {
        return [
            'selectedOrderId' => 'required|exists:orders,id',
            'type' => 'required|string|in:PICKUP,DELIVERY,REDELIVERY',
            'scheduledDate' => 'required|date',
            'startTime' => 'nullable|string|max:10',
            'endTime' => 'nullable|string|max:10',
            'assignedTeam' => 'nullable|string|max:150',
            'vehicle' => 'nullable|string|max:150',
            'notes' => 'nullable|string',
        ];
    }

    public function mount(): void
    {
        Gate::authorize('manage-schedule');
        $this->scheduledDate = now()->format('Y-m-d');
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingTypeFilter(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        Gate::authorize('manage-schedule');

        $this->selectedOrderId = null;
        $this->type = 'PICKUP';
        $this->scheduledDate = now()->format('Y-m-d');
        $this->startTime = '09:00';
        $this->endTime = '12:00';
        $this->assignedTeam = 'Tim Lapangan 1 (Budi & Eko)';
        $this->vehicle = 'Daihatsu GranMax Pick-up N 1234 AB';
        $this->notes = '';
        $this->resetValidation();
        $this->showCreateModal = true;
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

        $order = Order::findOrFail($this->selectedOrderId);
        $schedule = $createSchedule->execute($order, [
            'type' => $this->type,
            'scheduled_date' => $this->scheduledDate,
            'start_time' => $this->startTime ?: null,
            'end_time' => $this->endTime ?: null,
            'assigned_team' => $this->assignedTeam ?: null,
            'vehicle' => $this->vehicle ?: null,
            'notes' => $this->notes ?: null,
        ], Auth::user());

        $this->showCreateModal = false;
        session()->flash('message', "Jadwal operasional #{$order->order_code} berhasil dibuat.");
    }

    public function startMission(int $scheduleId, StartSchedule $startSchedule): void
    {
        Gate::authorize('manage-schedule');

        $schedule = Schedule::findOrFail($scheduleId);
        $startSchedule->execute($schedule, Auth::user());

        session()->flash('message', "Misi operasional #{$schedule->order->order_code} sedang dikerjakan (On The Way).");
    }

    public function completeMission(int $scheduleId, CompleteSchedule $completeSchedule): void
    {
        Gate::authorize('manage-schedule');

        $schedule = Schedule::findOrFail($scheduleId);
        $completeSchedule->execute($schedule, Auth::user());

        session()->flash('message', "Misi operasional #{$schedule->order->order_code} selesai dilaksanakan.");
    }

    public function render()
    {
        $query = Schedule::query()
            ->with(['order.customer', 'order.pickupAddress', 'order.destinationAddress']);

        // Tab Filter
        if ($this->activeTab === 'today') {
            $query->today();
        } elseif ($this->activeTab === 'tomorrow') {
            $query->tomorrow();
        } elseif ($this->activeTab === 'upcoming') {
            $query->upcoming();
        }

        // Search Filter
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('assigned_team', 'LIKE', "%{$this->search}%")
                    ->orWhere('vehicle', 'LIKE', "%{$this->search}%")
                    ->orWhereHas('order', function ($oq) {
                        $oq->where('order_code', 'LIKE', "%{$this->search}%")
                            ->orWhereHas('customer', function ($cq) {
                                $cq->where('name', 'LIKE', "%{$this->search}%")
                                    ->orWhere('phone', 'LIKE', "%{$this->search}%");
                            });
                    });
            });
        }

        // Type Filter
        if ($this->typeFilter) {
            $query->where('type', $this->typeFilter);
        }

        // Status Filter
        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        $schedules = $query->orderBy('scheduled_date')->orderBy('start_time')->paginate(12);

        $counts = [
            'today' => Schedule::today()->count(),
            'tomorrow' => Schedule::tomorrow()->count(),
            'upcoming' => Schedule::upcoming()->count(),
            'all' => Schedule::count(),
        ];

        return view('livewire.admin.schedules.index', [
            'schedules' => $schedules,
            'counts' => $counts,
            'types' => ScheduleType::cases(),
            'statuses' => ScheduleStatus::cases(),
            'orders' => Order::query()->latest('id')->take(50)->get(),
        ]);
    }
}
