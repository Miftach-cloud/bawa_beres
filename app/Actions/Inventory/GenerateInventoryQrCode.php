<?php

namespace App\Actions\Inventory;

use App\Models\InventoryItem;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class GenerateInventoryQrCode
{
    /**
     * Generate or ensure QR code and return scannable URL & SVG markup
     */
    public function execute(InventoryItem $item, int $size = 180): array
    {
        if (empty($item->qr_code)) {
            $item->update([
                'qr_code' => InventoryItem::generateQrCode(),
            ]);
            $item->refresh();
        }

        $scanUrl = $item->scan_url;
        $svgMarkup = QrCode::size($size)->margin(1)->generate($scanUrl);

        return [
            'qr_code' => $item->qr_code,
            'inventory_code' => $item->inventory_code,
            'scan_url' => $scanUrl,
            'svg' => (string) $svgMarkup,
        ];
    }
}
