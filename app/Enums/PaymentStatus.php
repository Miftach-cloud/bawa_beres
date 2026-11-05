<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case PENDING = 'PENDING';
    case WAITING_VERIFICATION = 'WAITING_VERIFICATION';
    case PAID = 'PAID';
    case REJECTED = 'REJECTED';
    case REFUNDED = 'REFUNDED';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Menunggu Pembayaran',
            self::WAITING_VERIFICATION => 'Menunggu Verifikasi Admin',
            self::PAID => 'Pembayaran Terverifikasi (Lunas)',
            self::REJECTED => 'Bukti Pembayaran Ditolak',
            self::REFUNDED => 'Dikembalikan (Refund)',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::PENDING => 'bg-slate-100 text-slate-700 border-slate-200',
            self::WAITING_VERIFICATION => 'bg-amber-50 text-amber-700 border-amber-200',
            self::PAID => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            self::REJECTED => 'bg-rose-50 text-rose-700 border-rose-200',
            self::REFUNDED => 'bg-purple-50 text-purple-700 border-purple-200',
        };
    }

    public function isVerified(): bool
    {
        return $this === self::PAID;
    }
}
