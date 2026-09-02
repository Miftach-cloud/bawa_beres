<?php

namespace App\Enums;

enum PhotoType: string
{
    case RECEIVING = 'RECEIVING';
    case CONDITION = 'CONDITION';
    case STORAGE = 'STORAGE';
    case OUTBOUND = 'OUTBOUND';
    case DAMAGE = 'DAMAGE';

    public function label(): string
    {
        return match ($this) {
            self::RECEIVING => 'Foto Serah Terima Awal (Receiving)',
            self::CONDITION => 'Foto Kondisi Keseluruhan (Condition)',
            self::STORAGE => 'Foto Posisi Rak Gudang (Storage)',
            self::OUTBOUND => 'Foto Siap Diantar (Outbound)',
            self::DAMAGE => 'Foto Kerusakan / Cacat Bawaan (Damage)',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::RECEIVING => 'bg-cyan-50 text-cyan-700 border-cyan-200',
            self::CONDITION => 'bg-blue-50 text-blue-700 border-blue-200',
            self::STORAGE => 'bg-purple-50 text-purple-700 border-purple-200',
            self::OUTBOUND => 'bg-amber-50 text-amber-700 border-amber-200',
            self::DAMAGE => 'bg-rose-50 text-rose-700 border-rose-200',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::RECEIVING => '📥',
            self::CONDITION => '📷',
            self::STORAGE => '🏢',
            self::OUTBOUND => '🚚',
            self::DAMAGE => '⚠️',
        };
    }
}
