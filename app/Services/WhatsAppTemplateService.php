<?php

namespace App\Services;

use App\Models\InventoryItem;
use App\Models\Order;
use App\Models\Quotation;
use App\Models\Schedule;
use App\Models\StorageLocation;

class WhatsAppTemplateService
{
    /**
     * Sanitize phone number to standard international format (e.g. 6281234567890)
     */
    public function sanitizePhoneNumber(?string $phone): string
    {
        if (empty($phone)) {
            return '';
        }

        // Strip non-digit characters
        $digits = preg_replace('/[^\d]/', '', $phone);

        // Convert leading 0 to 62
        if (str_starts_with($digits, '0')) {
            $digits = '62' . substr($digits, 1);
        }

        return $digits;
    }

    /**
     * Generate complete wa.me click-to-chat link
     */
    public function generateWhatsAppLink(string $phoneNumber, string $message): string
    {
        $sanitizedPhone = $this->sanitizePhoneNumber($phoneNumber);
        $encodedMessage = rawurlencode($message);

        return "https://wa.me/{$sanitizedPhone}?text={$encodedMessage}";
    }

    /**
     * 1. Manual Template: Order Created
     */
    public function orderCreated(Order $order): string
    {
        $customerName = $order->customer?->name ?? 'Pelanggan';
        $serviceName = $order->service?->name ?? 'Pindahan & Storage';
        $trackUrl = url("/track/{$order->order_code}");
        $preferredDate = $order->preferred_date ? $order->preferred_date->format('d M Y') : 'Sesuai kesepakatan';

        return "Halo Kak *{$customerName}*,\n\n"
            . "Terima kasih telah memesan layanan *BawaBeres*! 📦\n\n"
            . "📌 *Detail Pesanan Anda:*\n"
            . "• Nomor Order: *{$order->order_code}*\n"
            . "• Layanan: {$serviceName}\n"
            . "• Rencana Tanggal: {$preferredDate}\n\n"
            . "Tim kami sedang meninjau rincian barang Anda untuk menerbitkan rincian penawaran resmi.\n\n"
            . "🔎 Pantau progres pesanan Anda secara realtime di:\n"
            . "{$trackUrl}\n\n"
            . "Ada yang ingin ditanyakan? Balas pesan ini untuk terhubung langsung dengan Tim BawaBeres. 😊";
    }

    /**
     * 2. Manual Template: Quotation Created
     */
    public function quotationCreated(Quotation $quotation): string
    {
        $order = $quotation->order;
        $customerName = $order?->customer?->name ?? 'Pelanggan';
        $totalAmount = number_format($quotation->total_amount, 0, ',', '.');
        $trackUrl = url("/track/{$order?->order_code}");

        return "Halo Kak *{$customerName}*,\n\n"
            . "Estimasi dan Penawaran Resmi dari *BawaBeres* telah siap! 📄\n\n"
            . "📌 *Rincian Penawaran:*\n"
            . "• No. Penawaran: *{$quotation->quotation_number}*\n"
            . "• Nomor Order: *{$order?->order_code}*\n"
            . "• Total Biaya: *Rp {$totalAmount}*\n\n"
            . "Silakan cek rincian lengkap atau setujui penawaran melalui tautan berikut:\n"
            . "{$trackUrl}\n\n"
            . "Jika sudah sesuai, silakan konfirmasi pembayaran agar armada segera kami jadwalkan. Terima kasih! 🙏";
    }

    /**
     * 3. Manual Template: Order Confirmed
     */
    public function orderConfirmed(Order $order): string
    {
        $customerName = $order->customer?->name ?? 'Pelanggan';
        $trackUrl = url("/track/{$order->order_code}");

        return "Halo Kak *{$customerName}*,\n\n"
            . "Pembayaran & konfirmasi untuk pesanan *{$order->order_code}* telah berhasil diverifikasi! ✅\n\n"
            . "Tim operasional kami saat ini sedang mengatur penugasan driver dan armada jemput.\n\n"
            . "Pantau status jadwal penjemputan di:\n"
            . "{$trackUrl}\n\n"
            . "BawaBeres — Beres Pindahannya, Aman Simpannya!";
    }

    /**
     * 4. Manual Template: Pickup Scheduled
     */
    public function pickupScheduled(Order $order, Schedule $schedule): string
    {
        $customerName = $order->customer?->name ?? 'Pelanggan';
        $pickupDate = $schedule->scheduled_date ? $schedule->scheduled_date->format('d M Y H:i') : '-';
        $driverName = $schedule->driver_name ?? 'Driver Tim BawaBeres';
        $vehiclePlate = $schedule->vehicle_plate ?? '-';
        $trackUrl = url("/track/{$order->order_code}");

        return "Halo Kak *{$customerName}*,\n\n"
            . "Jadwal penjemputan pesanan *{$order->order_code}* telah ditetapkan! 🚚\n\n"
            . "📌 *Detail Penjemputan:*\n"
            . "• Tanggal & Jam: *{$pickupDate}*\n"
            . "• Driver Bertugas: *{$driverName}*\n"
            . "• Plat Nomor Armada: *{$vehiclePlate}*\n\n"
            . "Pastikan barang Anda sudah dikemas rapi sebelum tim kami tiba.\n\n"
            . "Lacak posisi & koordinasi:\n"
            . "{$trackUrl}";
    }

    /**
     * 5. Manual Template: Inventory Received
     */
    public function inventoryReceived(Order $order, InventoryItem $item): string
    {
        $customerName = $order->customer?->name ?? 'Pelanggan';
        $qrUrl = url("/qr/{$item->qr_code}");

        return "Halo Kak *{$customerName}*,\n\n"
            . "Barang Anda telah sampai dan berhasil diterima di Hub BawaBeres! 📦\n\n"
            . "📌 *Identitas Barang:*\n"
            . "• Nama Item: *{$item->name}*\n"
            . "• QR Token: *{$item->qr_code}*\n\n"
            . "Setiap item telah ditempel label QR Code unik untuk memastikan tidak tertukar atau hilang.\n\n"
            . "Cek foto & data barang:\n"
            . "{$qrUrl}";
    }

    /**
     * 6. Manual Template: Inventory Stored
     */
    public function inventoryStored(InventoryItem $item, StorageLocation $location): string
    {
        $order = $item->order;
        $customerName = $order?->customer?->name ?? 'Pelanggan';
        $qrUrl = url("/qr/{$item->qr_code}");

        return "Halo Kak *{$customerName}*,\n\n"
            . "Barang Anda telah tersimpan rapi dan aman di Storage Facility BawaBeres! 🔒\n\n"
            . "📌 *Lokasi Penyimpanan:*\n"
            . "• Item: *{$item->name}*\n"
            . "• QR Token: *{$item->qr_code}*\n"
            . "• Lokasi Rak: *{$location->code}* (Zona {$location->zone})\n\n"
            . "Fasilitas kami dilengkapi CCTV 24/7 dan kontrol akses ketat.\n\n"
            . "Status penyimpanan:\n"
            . "{$qrUrl}";
    }

    /**
     * 7. Manual Template: Order Completed
     */
    public function orderCompleted(Order $order): string
    {
        $customerName = $order->customer?->name ?? 'Pelanggan';

        return "Halo Kak *{$customerName}*,\n\n"
            . "Layanan pesanan *{$order->order_code}* telah selesai seluruhnya! 🎉\n\n"
            . "Terima kasih banyak telah mempercayakan kebutuhan logistik dan penyimpanan barang Anda kepada *BawaBeres*.\n\n"
            . "Bagaimana pengalaman Kakak bersama kami? Kami sangat berterima kasih jika Kakak berkenan memberikan ulasan singkat. 😊\n\n"
            . "Sampai jumpa di kebutuhan pindahan & storage berikutnya!";
    }
}
