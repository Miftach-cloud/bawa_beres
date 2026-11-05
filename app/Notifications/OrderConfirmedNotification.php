<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderConfirmedNotification extends Notification
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
            'title' => "Pesanan Terkonfirmasi: {$this->order->order_code}",
            'message' => "Pesanan {$this->order->order_code} telah dikonfirmasi dan siap dijadwalkan untuk penjemputan.",
            'action_url' => url("/track/{$this->order->order_code}"),
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Pesanan Dikonfirmasi — {$this->order->order_code}")
            ->line("Pesanan Anda {$this->order->order_code} telah dikonfirmasi.")
            ->action('Lacak Status Penjemputan', url("/track/{$this->order->order_code}"));
    }
}
