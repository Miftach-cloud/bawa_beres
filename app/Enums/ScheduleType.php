<?php

namespace App\Enums;

enum ScheduleType: string
{
    case PICKUP = 'PICKUP';
    case DELIVERY = 'DELIVERY';
    case REDELIVERY = 'REDELIVERY';

    public function label(): string
    {
        return match ($this) {
            self::PICKUP => 'Penjemputan Barang (Pickup)',
            self::DELIVERY => 'Pengantaran ke Tujuan (Delivery)',
            self::REDELIVERY => 'Pengambilan Ulang dari Gudang (Redelivery)',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::PICKUP => '📦 ➔',
            self::DELIVERY => '➔ 🏠',
            self::REDELIVERY => '🏢 ➔',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::PICKUP => 'bg-amber-50 text-amber-800 border-amber-200',
            self::DELIVERY => 'bg-blue-50 text-blue-800 border-blue-200',
            self::REDELIVERY => 'bg-purple-50 text-purple-800 border-purple-200',
        };
    }
}
