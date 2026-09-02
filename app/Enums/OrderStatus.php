<?php

namespace App\Enums;

enum OrderStatus: string
{
    case DRAFT = 'DRAFT';
    case SUBMITTED = 'SUBMITTED';
    case PENDING_REVIEW = 'PENDING_REVIEW';
    case QUOTED = 'QUOTED';
    case CONFIRMED = 'CONFIRMED';
    case PAID = 'PAID';
    case SCHEDULED = 'SCHEDULED';
    case PICKED_UP = 'PICKED_UP';
    case PROCESSING = 'PROCESSING';
    case IN_TRANSIT = 'IN_TRANSIT';
    case STORED = 'STORED';
    case OUTBOUND_REQUESTED = 'OUTBOUND_REQUESTED';
    case DELIVERED = 'DELIVERED';
    case COMPLETED = 'COMPLETED';
    case CANCELLED = 'CANCELLED';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::SUBMITTED => 'Pesanan Dibuat',
            self::PENDING_REVIEW => 'Menunggu Review Admin',
            self::QUOTED => 'Penawaran Harga Diberikan',
            self::CONFIRMED => 'Dikonfirmasi Pelanggan',
            self::PAID => 'Pembayaran Diterima',
            self::SCHEDULED => 'Jadwal Ditentukan',
            self::PICKED_UP => 'Barang Telah Dijemput',
            self::PROCESSING => 'Sedang Diproses',
            self::IN_TRANSIT => 'Dalam Pengiriman',
            self::STORED => 'Tersimpan di Gudang Storage',
            self::OUTBOUND_REQUESTED => 'Permintaan Ambil/Kirim Ulang',
            self::DELIVERED => 'Terkirim ke Tujuan',
            self::COMPLETED => 'Selesai',
            self::CANCELLED => 'Dibatalkan',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::DRAFT, self::SUBMITTED, self::PENDING_REVIEW => 'bg-amber-50 text-amber-700 border-amber-200',
            self::QUOTED, self::CONFIRMED => 'bg-blue-50 text-blue-700 border-blue-200',
            self::PAID, self::SCHEDULED => 'bg-indigo-50 text-indigo-700 border-indigo-200',
            self::PICKED_UP, self::PROCESSING, self::IN_TRANSIT => 'bg-cyan-50 text-cyan-700 border-cyan-200',
            self::STORED => 'bg-purple-50 text-purple-700 border-purple-200',
            self::OUTBOUND_REQUESTED => 'bg-orange-50 text-orange-700 border-orange-200',
            self::DELIVERED, self::COMPLETED => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            self::CANCELLED => 'bg-rose-50 text-rose-700 border-rose-200',
        };
    }

    public function isFinal(): bool
    {
        return in_array($this, [self::COMPLETED, self::CANCELLED], true);
    }

    /**
     * Get array of permitted next status transitions from current status
     */
    public function allowedTransitions(): array
    {
        return match ($this) {
            self::DRAFT => [self::SUBMITTED, self::CANCELLED],
            self::SUBMITTED => [self::PENDING_REVIEW, self::CANCELLED],
            self::PENDING_REVIEW => [self::QUOTED, self::CONFIRMED, self::CANCELLED],
            self::QUOTED => [self::CONFIRMED, self::PENDING_REVIEW, self::CANCELLED],
            self::CONFIRMED => [self::PAID, self::SCHEDULED, self::CANCELLED],
            self::PAID => [self::SCHEDULED, self::CONFIRMED, self::CANCELLED],
            self::SCHEDULED => [self::PICKED_UP, self::CONFIRMED, self::CANCELLED],
            self::PICKED_UP => [self::PROCESSING, self::IN_TRANSIT, self::STORED, self::CANCELLED],
            self::PROCESSING => [self::IN_TRANSIT, self::STORED, self::DELIVERED, self::COMPLETED, self::CANCELLED],
            self::IN_TRANSIT => [self::DELIVERED, self::STORED, self::CANCELLED],
            self::STORED => [self::OUTBOUND_REQUESTED, self::CANCELLED],
            self::OUTBOUND_REQUESTED => [self::SCHEDULED, self::IN_TRANSIT, self::DELIVERED, self::CANCELLED],
            self::DELIVERED => [self::COMPLETED, self::CANCELLED],
            self::COMPLETED => [],
            self::CANCELLED => [],
        };
    }

    /**
     * Check if transition to target status is valid
     */
    public function canTransitionTo(OrderStatus $target): bool
    {
        if ($this === $target) {
            return false;
        }

        return in_array($target, $this->allowedTransitions(), true);
    }
}
