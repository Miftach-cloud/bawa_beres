<?php

namespace App\Enums;

enum StorageLocationType: string
{
    case STANDARD_RACK = 'STANDARD_RACK';
    case HEAVY_DUTY = 'HEAVY_DUTY';
    case PALLET_FLOOR = 'PALLET_FLOOR';
    case FRAGILE_CAGE = 'FRAGILE_CAGE';
    case SECURE_LOCKER = 'SECURE_LOCKER';

    public function label(): string
    {
        return match ($this) {
            self::STANDARD_RACK => 'Rak Standar (Boxes & Medium Items)',
            self::HEAVY_DUTY => 'Rak Heavy Duty (Furniture & Kasur)',
            self::PALLET_FLOOR => 'Lantai Pallet (Bulk / Barang Besar)',
            self::FRAGILE_CAGE => 'Kandang Khusus Pecah Belah (Fragile)',
            self::SECURE_LOCKER => 'Locker Keamanan Tinggi (High-Value)',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::STANDARD_RACK => '📦',
            self::HEAVY_DUTY => '🛋️',
            self::PALLET_FLOOR => '🪵',
            self::FRAGILE_CAGE => '⚠️',
            self::SECURE_LOCKER => '🔒',
        };
    }
}
