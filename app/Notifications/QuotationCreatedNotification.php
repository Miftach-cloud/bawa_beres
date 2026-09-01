<?php

namespace App\Notifications;

use App\Models\Quotation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class QuotationCreatedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Quotation $quotation
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'quotation_id' => $this->quotation->id,
            'quotation_number' => $this->quotation->quotation_number,
            'order_id' => $this->quotation->order_id,
            'order_code' => $this->quotation->order?->order_code,
            'total_amount' => $this->quotation->total_amount,
            'title' => "Penawaran Resmi Siap: {$this->quotation->quotation_number}",
            'message' => 'Penawaran sebesar Rp '.number_format($this->quotation->total_amount, 0, ',', '.')." untuk pesanan {$this->quotation->order?->order_code} telah diterbitkan.",
            'action_url' => url("/track/{$this->quotation->order?->order_code}"),
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Penawaran Resmi BawaBeres — {$this->quotation->quotation_number}")
            ->line("Penawaran resmi untuk pesanan {$this->quotation->order?->order_code} telah siap.")
            ->line('Total Biaya: Rp '.number_format($this->quotation->total_amount, 0, ',', '.'))
            ->action('Lihat Rincian & Lacak', url("/track/{$this->quotation->order?->order_code}"));
    }
}
