<?php

namespace Tests\Unit;

use App\Actions\Inventory\GenerateInventoryQrCode;
use App\Models\InventoryItem;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class InventoryQrTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function inventory_item_automatically_generates_unique_qr_code(): void
    {
        $order = Order::factory()->create();

        $item = InventoryItem::create([
            'order_id' => $order->id,
            'name' => 'Kulkas 2 Pintu',
        ]);

        $this->assertNotEmpty($item->qr_code);
        $this->assertEquals(8, strlen($item->qr_code));
        $this->assertStringContainsString($item->qr_code, $item->scan_url);
    }

    #[Test]
    public function qr_svg_generation_returns_valid_svg_markup(): void
    {
        $order = Order::factory()->create();
        $item = InventoryItem::create([
            'order_id' => $order->id,
            'name' => 'Meja Belajar Kayu',
        ]);

        $svg = $item->getQrSvg(150);

        $this->assertStringContainsString('<svg', $svg);
        $this->assertStringContainsString('</svg>', $svg);

        $action = app(GenerateInventoryQrCode::class);
        $result = $action->execute($item);

        $this->assertArrayHasKey('qr_code', $result);
        $this->assertArrayHasKey('scan_url', $result);
        $this->assertArrayHasKey('svg', $result);
        $this->assertEquals($item->qr_code, $result['qr_code']);
    }
}
