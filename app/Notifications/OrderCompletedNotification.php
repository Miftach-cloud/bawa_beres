<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderCompletedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Order $order
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
            'status' => $this->order->status->value,
            'title' => "Layanan Selesai: {$this->order->order_code}",
            'message' => "Pesanan {$this->order->order_code} telah selesai dengan sukses. Terima kasih telah mempercayakan kebutuhan logistik Anda kepada BawaBeres.",
            'action_url' => url("/track/{$this->order->order_code}"),
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Layanan Selesai — Terima Kasih Telah Menggunakan BawaBeres")
            ->greeting("Halo, {$this->order->customer?->name}")
            ->line("Pesanan {$this->order->order_code} telah selesai sepenuhnya.")
            ->line('Terima kasih telah mempercayakan kebutuhan pindahan dan storage Anda kepada kami.')
            ->action('Lihat Ringkasan Pesanan', url("/track/{$this->order->order_code}"));
    }
}
