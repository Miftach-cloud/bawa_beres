<?php

namespace App\Models;

use App\Enums\ScheduleStatus;
use App\Enums\ScheduleType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'type',
        'status',
        'scheduled_date',
        'start_time',
        'end_time',
        'assigned_team',
        'vehicle',
        'notes',
        'completed_at',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'type' => ScheduleType::class,
            'status' => ScheduleStatus::class,
            'scheduled_date' => 'date',
            'completed_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('scheduled_date', Carbon::today());
    }

    public function scopeTomorrow(Builder $query): Builder
    {
        return $query->whereDate('scheduled_date', Carbon::tomorrow());
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->whereDate('scheduled_date', '>', Carbon::tomorrow());
    }

    public function scopePast(Builder $query): Builder
    {
        return $query->whereDate('scheduled_date', '<', Carbon::today());
    }

    public function isPickup(): bool
    {
        return $this->type === ScheduleType::PICKUP;
    }

    public function isDelivery(): bool
    {
        return in_array($this->type, [ScheduleType::DELIVERY, ScheduleType::REDELIVERY], true);
    }

    public function isCompleted(): bool
    {
        return $this->status === ScheduleStatus::COMPLETED;
    }
}
