<?php

namespace App\Enums;

enum AddressType: string
{
    case PICKUP = 'PICKUP';
    case DESTINATION = 'DESTINATION';

    public function label(): string
    {
        return match ($this) {
            self::PICKUP => 'Lokasi Penjemputan (Pickup)',
            self::DESTINATION => 'Lokasi Tujuan / Pengantaran',
        };
    }
}
