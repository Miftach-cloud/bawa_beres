<?php

namespace Tests\Unit;

use App\Actions\Quotations\CreateQuotation;
use App\Actions\Quotations\CreateQuotationRevision;
use App\Enums\QuotationStatus;
use App\Models\Order;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class QuotationModelTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function quotation_item_calculates_total_price_automatically(): void
    {
        $order = Order::factory()->create();
        $quotation = Quotation::create([
            'order_id' => $order->id,
            'quotation_number' => 'QUO-TEST-001',
            'version' => 1,
            'status' => QuotationStatus::DRAFT,
            'subtotal' => 140000,
            'total_amount' => 140000,
        ]);

        $item = QuotationItem::create([
            'quotation_id' => $quotation->id,
            'name' => 'Pickup Armada Grandmax',
            'quantity' => 2,
            'unit_price' => 80000,
        ]);

        $this->assertEquals(160000, (int) $item->total_price);
    }

    #[Test]
    public function order_can_have_multiple_quotation_revisions_without_overwriting(): void
    {
        $admin = User::factory()->admin()->create();
        $order = Order::factory()->create();

        $createAction = app(CreateQuotation::class);
        $revisionAction = app(CreateQuotationRevision::class);

        // Version 1
        $quo1 = $createAction->execute($order, [
            'items' => [
                ['name' => 'Pickup', 'quantity' => 1, 'unit_price' => 80000],
                ['name' => 'Packing', 'quantity' => 1, 'unit_price' => 40000],
                ['name' => 'Handling', 'quantity' => 1, 'unit_price' => 20000],
            ],
            'discount' => 10000,
        ], $admin);

        $this->assertEquals(1, $quo1->version);
        $this->assertEquals(140000, (int) $quo1->subtotal);
        $this->assertEquals(130000, (int) $quo1->total_amount);
        $this->assertEquals(Quotation::generateNumber($order, 1), $quo1->quotation_number);

        // Version 2 (Revision)
        $quo2 = $revisionAction->execute($order, [
            'items' => [
                ['name' => 'Pickup', 'quantity' => 1, 'unit_price' => 70000],
                ['name' => 'Packing', 'quantity' => 1, 'unit_price' => 35000],
                ['name' => 'Handling', 'quantity' => 1, 'unit_price' => 15000],
            ],
            'discount' => 0,
        ], $admin);

        $this->assertEquals(2, $quo2->version);
        $this->assertEquals(120000, (int) $quo2->subtotal);
        $this->assertEquals(120000, (int) $quo2->total_amount);
        $this->assertEquals(Quotation::generateNumber($order, 2), $quo2->quotation_number);

        // Verify order has both quotations preserved
        $this->assertCount(2, $order->quotations);
        $this->assertEquals($quo2->id, $order->latestQuotation->id);
    }
}
