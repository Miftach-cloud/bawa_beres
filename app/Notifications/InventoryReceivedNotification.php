<?php

namespace App\Notifications;

use App\Models\InventoryItem;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InventoryReceivedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Order $order,
        public InventoryItem $inventoryItem
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
            'inventory_item_id' => $this->inventoryItem->id,
            'item_name' => $this->inventoryItem->name,
            'qr_code' => $this->inventoryItem->qr_code,
            'title' => "Barang Diterima di Hub: {$this->inventoryItem->name}",
            'message' => "Item '{$this->inventoryItem->name}' (QR: {$this->inventoryItem->qr_code}) telah berhasil diterima di hub/gudang BawaBeres.",
            'action_url' => url("/track/{$this->order->order_code}"),
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Barang Anda Telah Tiba di Hub — {$this->inventoryItem->qr_code}")
            ->line("Item '{$this->inventoryItem->name}' untuk pesanan {$this->order->order_code} telah kami terima.")
            ->action('Cek Riwayat & QR', url("/qr/{$this->inventoryItem->qr_code}"));
    }
}
