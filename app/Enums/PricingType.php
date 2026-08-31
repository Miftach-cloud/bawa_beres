<?php

namespace App\Enums;

enum PricingType: string
{
    case FIXED = 'FIXED';
    case PACKAGE = 'PACKAGE';
    case QUOTATION = 'QUOTATION';

    public function label(): string
    {
        return match ($this) {
            self::FIXED => 'Harga Tetap (Fixed)',
            self::PACKAGE => 'Paket Layanan',
            self::QUOTATION => 'Estimasi / Quotation Admin',
        };
    }
}
