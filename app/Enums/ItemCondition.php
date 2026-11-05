<?php

namespace App\Enums;

enum ItemCondition: string
{
    case GOOD = 'GOOD';
    case SCRATCHED = 'SCRATCHED';
    case DAMAGED = 'DAMAGED';
    case FRAGILE = 'FRAGILE';

    public function label(): string
    {
        return match ($this) {
            self::GOOD => 'Baik / Mulus (Good)',
            self::SCRATCHED => 'Lecet / Goresan Wajar (Scratched)',
            self::DAMAGED => 'Kerusakan Bawaan (Damaged)',
            self::FRAGILE => 'Barang Pecah Belah (Fragile)',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::GOOD => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            self::SCRATCHED => 'bg-amber-50 text-amber-700 border-amber-200',
            self::DAMAGED => 'bg-rose-50 text-rose-700 border-rose-200',
            self::FRAGILE => 'bg-purple-50 text-purple-700 border-purple-200',
        };
    }
}
