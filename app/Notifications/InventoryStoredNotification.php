<?php

namespace App\Notifications;

use App\Models\InventoryItem;
use App\Models\StorageLocation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InventoryStoredNotification extends Notification
{
    use Queueable;

    public function __construct(
        public InventoryItem $inventoryItem,
        public StorageLocation $location
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'inventory_item_id' => $this->inventoryItem->id,
            'item_name' => $this->inventoryItem->name,
            'qr_code' => $this->inventoryItem->qr_code,
            'storage_location_id' => $this->location->id,
            'location_code' => $this->location->code,
            'title' => "Barang Tersimpan Aman di Rak: {$this->location->code}",
            'message' => "Item '{$this->inventoryItem->name}' (QR: {$this->inventoryItem->qr_code}) telah dialokasikan dan disimpan di Rak {$this->location->code}.",
            'action_url' => url("/admin/inventory/qr-print/{$this->inventoryItem->id}"),
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Barang Tersimpan Aman — {$this->inventoryItem->qr_code}")
            ->line("Item '{$this->inventoryItem->name}' telah disimpan di fasilitas storage BawaBeres.")
            ->action('Lihat Detail', url("/qr/{$this->inventoryItem->qr_code}"));
    }
}
