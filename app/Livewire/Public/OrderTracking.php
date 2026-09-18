<?php

namespace App\Livewire\Public;

use App\Enums\OrderStatus;
use App\Models\Order;
use Livewire\Component;

class OrderTracking extends Component
{
    public string $orderCode = '';

    public string $phone = '';

    public ?Order $order = null;

    public bool $hasSearched = false;

    public ?string $errorMessage = null;

    protected $queryString = [
        'orderCode' => ['except' => '', 'as' => 'code'],
    ];

    public function mount(?string $order_code = null): void
    {
        if ($order_code) {
            $this->orderCode = $order_code;
        }

        if (! empty($this->orderCode) && ! empty($this->phone)) {
            $this->track();
        }
    }

    public function track(): void
    {
        $this->hasSearched = true;
        $this->errorMessage = null;
        $this->order = null;

        $this->validate([
            'orderCode' => 'required|string|min:4',
            'phone' => 'required|string|min:4',
        ], [
            'orderCode.required' => 'Nomor pesanan (Order Code) wajib diisi.',
            'phone.required' => 'Nomor WhatsApp / HP verifikasi wajib diisi.',
        ]);

        $cleanCode = strtoupper(trim($this->orderCode));
        $cleanPhone = preg_replace('/[^0-9]/', '', $this->phone);

        $order = Order::with([
            'customer',
            'service',
            'items',
            'pickupAddress',
            'destinationAddress',
            'acceptedQuotation',
            'latestSchedule',
        ])
            ->where('order_code', $cleanCode)
            ->first();

        if (! $order) {
            $this->errorMessage = "Pesanan dengan nomor {$cleanCode} tidak ditemukan di sistem kami.";

            return;
        }

        // Verify the complete normalized phone number.
        $customerPhone = preg_replace('/[^0-9]/', '', $order->customer->phone ?? '');
        $isPhoneMatched = $customerPhone !== '' && hash_equals($customerPhone, $cleanPhone);

        if (! $isPhoneMatched) {
            $this->errorMessage = "Nomor WhatsApp/HP tidak sesuai dengan data pemesan nomor order {$cleanCode}.";

            return;
        }

        $this->order = $order;
    }

    public function resetSearch(): void
    {
        $this->order = null;
        $this->hasSearched = false;
        $this->errorMessage = null;
        $this->orderCode = '';
        $this->phone = '';
    }

    public static function buildMilestones(Order $order): array
    {
        $status = $order->status;

        return [
            [
                'key' => 'BOOKING_RECEIVED',
                'title' => 'Pesanan Diterima',
                'description' => 'Pesanan Anda telah masuk dan menunggu peninjauan armada oleh tim kami.',
                'is_completed' => true,
                'is_active' => in_array($status, [OrderStatus::DRAFT, OrderStatus::PENDING_REVIEW], true),
                'icon' => 'clipboard',
            ],
            [
                'key' => 'QUOTATION',
                'title' => 'Penawaran & Estimasi Harga',
                'description' => 'Penawaran harga resmi telah disiapkan untuk Anda.',
                'is_completed' => in_array($status, [
                    OrderStatus::QUOTED,
                    OrderStatus::CONFIRMED,
                    OrderStatus::PAID,
                    OrderStatus::SCHEDULED,
                    OrderStatus::IN_TRANSIT,
                    OrderStatus::PICKED_UP,
                    OrderStatus::STORED,
                    OrderStatus::PROCESSING,
                    OrderStatus::OUTBOUND_REQUESTED,
                    OrderStatus::DELIVERED,
                    OrderStatus::COMPLETED,
                ], true),
                'is_active' => $status === OrderStatus::QUOTED,
                'icon' => 'tag',
            ],
            [
                'key' => 'CONFIRMED',
                'title' => 'Pesanan Terkonfirmasi',
                'description' => 'Penawaran harga dan detail pesanan telah disetujui.',
                'is_completed' => in_array($status, [
                    OrderStatus::CONFIRMED,
                    OrderStatus::PAID,
                    OrderStatus::SCHEDULED,
                    OrderStatus::IN_TRANSIT,
                    OrderStatus::PICKED_UP,
                    OrderStatus::STORED,
                    OrderStatus::PROCESSING,
                    OrderStatus::OUTBOUND_REQUESTED,
                    OrderStatus::DELIVERED,
                    OrderStatus::COMPLETED,
                ], true),
                'is_active' => in_array($status, [OrderStatus::CONFIRMED, OrderStatus::PAID], true),
                'icon' => 'check-circle',
            ],

            [
                'key' => 'SCHEDULED_PICKUP',
                'title' => 'Jadwal & Penjemputan',
                'description' => 'Tim dan armada dijadwalkan untuk penjemputan barang.',
                'is_completed' => in_array($status, [
                    OrderStatus::PICKED_UP,
                    OrderStatus::STORED,
                    OrderStatus::PROCESSING,
                    OrderStatus::OUTBOUND_REQUESTED,
                    OrderStatus::DELIVERED,
                    OrderStatus::COMPLETED,
                ], true),
                'is_active' => in_array($status, [OrderStatus::SCHEDULED, OrderStatus::IN_TRANSIT], true),
                'icon' => 'truck',
            ],
            [
                'key' => 'PROCESSING_STORAGE',
                'title' => 'Penyimpanan & Penguasaan Barang',
                'description' => 'Barang fisik telah terverifikasi dan aman dalam fasilitas BawaBeres.',
                'is_completed' => in_array($status, [OrderStatus::DELIVERED, OrderStatus::COMPLETED], true),
                'is_active' => in_array($status, [
                    OrderStatus::PICKED_UP,
                    OrderStatus::STORED,
                    OrderStatus::PROCESSING,
                    OrderStatus::OUTBOUND_REQUESTED,
                ], true),
                'icon' => 'warehouse',
            ],

            [
                'key' => 'COMPLETED',
                'title' => 'Pesanan Selesai',
                'description' => 'Seluruh rangkaian layanan telah selesai dilaksanakan.',
                'is_completed' => $status === OrderStatus::COMPLETED,
                'is_active' => $status === OrderStatus::COMPLETED,
                'icon' => 'sparkles',
            ],
        ];
    }

    public function render()
    {
        $milestones = [];
        if ($this->order) {
            $milestones = static::buildMilestones($this->order);
        }

        return view('livewire.public.order-tracking', [
            'milestones' => $milestones,
        ])->layout('layouts.public', ['title' => $this->order ? "Lacak {$this->order->order_code} - BawaBeres" : 'Lacak Status Pesanan']);
    }
}
