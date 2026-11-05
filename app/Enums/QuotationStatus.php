<?php

namespace App\Enums;

enum QuotationStatus: string
{
    case DRAFT = 'DRAFT';
    case SENT = 'SENT';
    case ACCEPTED = 'ACCEPTED';
    case REJECTED = 'REJECTED';
    case EXPIRED = 'EXPIRED';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft Penawaran',
            self::SENT => 'Terkirim ke Customer',
            self::ACCEPTED => 'Disetujui Pelanggan',
            self::REJECTED => 'Ditolak / Perlu Revisi',
            self::EXPIRED => 'Kadaluarsa',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::DRAFT => 'bg-slate-100 text-slate-700 border-slate-200',
            self::SENT => 'bg-blue-50 text-blue-700 border-blue-200',
            self::ACCEPTED => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            self::REJECTED => 'bg-rose-50 text-rose-700 border-rose-200',
            self::EXPIRED => 'bg-amber-50 text-amber-700 border-amber-200',
        };
    }
}
