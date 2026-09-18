<?php

namespace App\Notifications;

use App\Models\Order;
use App\Models\Schedule;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PickupScheduledNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Order $order,
        public Schedule $schedule
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'order_code' => $this->order->order_code,
            'schedule_id' => $this->schedule->id,
            'scheduled_date' => $this->schedule->scheduled_date?->format('Y-m-d H:i'),
            'driver_name' => $this->schedule->driver_name,
            'vehicle_plate' => $this->schedule->vehicle_plate,
            'title' => "Jadwal Penjemputan Ditetapkan: {$this->order->order_code}",
            'message' => "Penjemputan dijadwalkan pada {$this->schedule->scheduled_date?->format('d M Y H:i')} oleh driver {$this->schedule->driver_name} ({$this->schedule->vehicle_plate}).",
            'action_url' => url("/track/{$this->order->order_code}"),
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Jadwal Penjemputan Barang — {$this->order->order_code}")
            ->line("Jadwal penjemputan untuk pesanan {$this->order->order_code} telah ditetapkan.")
            ->line("Tanggal & Waktu: {$this->schedule->scheduled_date?->format('d M Y H:i')}")
            ->line("Driver: {$this->schedule->driver_name} ({$this->schedule->vehicle_plate})")
            ->action('Lacak Driver & Armada', url("/track/{$this->order->order_code}"));
    }
}
