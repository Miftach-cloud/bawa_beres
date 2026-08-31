<?php

namespace App\Enums;

enum MovementType: string
{
    case INBOUND = 'INBOUND';
    case RELOCATION = 'RELOCATION';
    case OUTBOUND = 'OUTBOUND';

    public function label(): string
    {
        return match ($this) {
            self::INBOUND => 'Masuk Rak Gudang (Inbound)',
            self::RELOCATION => 'Pindah Posisi / Rak (Relocation)',
            self::OUTBOUND => 'Keluar dari Gudang (Outbound)',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::INBOUND => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            self::RELOCATION => 'bg-blue-50 text-blue-700 border-blue-200',
            self::OUTBOUND => 'bg-amber-50 text-amber-700 border-amber-200',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::INBOUND => '📥 ➔ 🏢',
            self::RELOCATION => '🏢 ➔ 🏢',
            self::OUTBOUND => '🏢 ➔ 🚚',
        };
    }
}
