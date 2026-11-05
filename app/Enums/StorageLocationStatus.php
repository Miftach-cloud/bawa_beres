<?php

namespace App\Enums;

enum StorageLocationStatus: string
{
    case AVAILABLE = 'AVAILABLE';
    case OCCUPIED = 'OCCUPIED';
    case MAINTENANCE = 'MAINTENANCE';

    public function label(): string
    {
        return match ($this) {
            self::AVAILABLE => 'Tersedia (Kosong / Muat)',
            self::OCCUPIED => 'Terisi (Penuh / Terpakai)',
            self::MAINTENANCE => 'Dalam Perbaikan / Maintenance',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::AVAILABLE => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            self::OCCUPIED => 'bg-blue-50 text-blue-700 border-blue-200',
            self::MAINTENANCE => 'bg-amber-50 text-amber-700 border-amber-200',
        };
    }

    public function indicatorDot(): string
    {
        return match ($this) {
            self::AVAILABLE => 'bg-emerald-500',
            self::OCCUPIED => 'bg-blue-500',
            self::MAINTENANCE => 'bg-amber-500',
        };
    }
}
