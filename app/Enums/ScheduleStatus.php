<?php

namespace App\Enums;

enum ScheduleStatus: string
{
    case SCHEDULED = 'SCHEDULED';
    case IN_PROGRESS = 'IN_PROGRESS';
    case COMPLETED = 'COMPLETED';
    case CANCELLED = 'CANCELLED';

    public function label(): string
    {
        return match ($this) {
            self::SCHEDULED => 'Terjadwal',
            self::IN_PROGRESS => 'Sedang Berjalan (On The Way)',
            self::COMPLETED => 'Selesai Dilaksanakan',
            self::CANCELLED => 'Dibatalkan',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::SCHEDULED => 'bg-indigo-50 text-indigo-700 border-indigo-200',
            self::IN_PROGRESS => 'bg-amber-50 text-amber-700 border-amber-200',
            self::COMPLETED => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            self::CANCELLED => 'bg-rose-50 text-rose-700 border-rose-200',
        };
    }
}
