<?php

namespace App\Enums;

enum UserRole: string
{
    case OWNER = 'OWNER';
    case ADMIN = 'ADMIN';
    case OPERATION = 'OPERATION';

    public function label(): string
    {
        return match ($this) {
            self::OWNER => 'Owner',
            self::ADMIN => 'Admin',
            self::OPERATION => 'Operasional',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::OWNER => 'bg-purple-100 text-purple-700 border-purple-200',
            self::ADMIN => 'bg-blue-100 text-blue-700 border-blue-200',
            self::OPERATION => 'bg-emerald-100 text-emerald-700 border-emerald-200',
        };
    }
}
