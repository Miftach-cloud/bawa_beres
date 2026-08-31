<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case BANK_TRANSFER = 'BANK_TRANSFER';
    case QRIS = 'QRIS';
    case CASH = 'CASH';

    public function label(): string
    {
        return match ($this) {
            self::BANK_TRANSFER => 'Transfer Bank Manual',
            self::QRIS => 'QRIS Digital',
            self::CASH => 'Tunai / Cash on Delivery',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::BANK_TRANSFER => '🏦',
            self::QRIS => '📱',
            self::CASH => '💵',
        };
    }
}
