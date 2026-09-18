<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderCreatedNotification extends Notification
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
            'customer_name' => $this->order->customer?->name,
            'service_name' => $this->order->service?->name,
            'status' => $this->order->status->value,
            'title' => "Pesanan Baru Diterima: {$this->order->order_code}",
            'message' => "Pesanan {$this->order->order_code} untuk {$this->order->customer?->name} telah berhasil dibuat.",
            'action_url' => url("/track/{$this->order->order_code}"),
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Pesanan Diterima — {$this->order->order_code}")
            ->greeting("Halo, {$this->order->customer?->name}")
            ->line("Pesanan Anda dengan kode {$this->order->order_code} telah kami terima.")
            ->action('Lacak Pesanan', url("/track/{$this->order->order_code}"))
            ->line('Tim BawaBeres akan segera menghubungi Anda melalui WhatsApp.');
    }
}
