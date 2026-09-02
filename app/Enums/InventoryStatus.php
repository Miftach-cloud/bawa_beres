<?php

namespace App\Enums;

enum InventoryStatus: string
{
    case EXPECTED = 'EXPECTED';
    case RECEIVED = 'RECEIVED';
    case CHECKED = 'CHECKED';
    case STORED = 'STORED';
    case OUTBOUND = 'OUTBOUND';
    case RELEASED = 'RELEASED';

    public function label(): string
    {
        return match ($this) {
            self::EXPECTED => 'Menunggu Fisik Barang (Expected)',
            self::RECEIVED => 'Diterima Tim Lapangan (Received)',
            self::CHECKED => 'Selesai QC & Cek Kondisi (Checked)',
            self::STORED => 'Tersimpan di Rak Gudang (Stored)',
            self::OUTBOUND => 'Siap Keluar / Diantar (Outbound)',
            self::RELEASED => 'Telah Diserahterimakan (Released)',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::EXPECTED => 'bg-slate-100 text-slate-700 border-slate-200',
            self::RECEIVED => 'bg-cyan-50 text-cyan-700 border-cyan-200',
            self::CHECKED => 'bg-blue-50 text-blue-700 border-blue-200',
            self::STORED => 'bg-purple-50 text-purple-700 border-purple-200',
            self::OUTBOUND => 'bg-amber-50 text-amber-700 border-amber-200',
            self::RELEASED => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        };
    }
}
