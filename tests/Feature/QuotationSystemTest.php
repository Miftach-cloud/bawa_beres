<?php

namespace Tests\Feature;

use App\Actions\Quotations\CreateQuotation;
use App\Actions\Quotations\SendQuotation;
use App\Enums\OrderStatus;
use App\Enums\QuotationStatus;
use App\Livewire\Admin\Quotations\Manager;
use App\Models\Order;
use App\Models\Quotation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class QuotationSystemTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $operation;

    protected Order $order;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
        $this->operation = User::factory()->operation()->create();
        $this->order = Order::factory()->create([
            'status' => OrderStatus::PENDING_REVIEW,
            'total_amount' => 0,
        ]);
    }

    #[Test]
    public function admin_can_compose_and_create_new_quotation(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(Manager::class, ['order' => $this->order])
            ->call('openCreateModal')
            ->set('items', [
                ['name' => 'Biaya Pickup', 'description' => 'Armada GranMax', 'quantity' => 1, 'unit_price' => 80000],
                ['name' => 'Packing Standar', 'description' => 'Kardus & Wrapping', 'quantity' => 2, 'unit_price' => 20000],
                ['name' => 'Handling Jasa Angkut', 'description' => '2 Orang helper', 'quantity' => 1, 'unit_price' => 20000],
            ])
            ->set('discount', 10000)
            ->set('notes', 'Estimasi harga sudah termasuk tol')
            ->call('saveQuotation')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('quotations', [
            'order_id' => $this->order->id,
            'version' => 1,
            'status' => QuotationStatus::DRAFT->value,
            'subtotal' => 140000,
            'discount' => 10000,
            'total_amount' => 130000,
        ]);

        $this->assertDatabaseHas('quotation_items', [
            'name' => 'Biaya Pickup',
            'quantity' => 1,
            'unit_price' => 80000,
        ]);
    }

    #[Test]
    public function admin_can_send_quotation_and_advance_order_status_to_quoted(): void
    {
        $createAction = app(CreateQuotation::class);
        $quotation = $createAction->execute($this->order, [
            'items' => [
                ['name' => 'Jasa Pindahan Basic', 'quantity' => 1, 'unit_price' => 250000],
            ],
            'discount' => 0,
        ], $this->admin);

        $this->actingAs($this->admin);

        Livewire::test(Manager::class, ['order' => $this->order])
            ->call('send', $quotation->id);

        $quotation->refresh();
        $this->order->refresh();

        $this->assertEquals(QuotationStatus::SENT, $quotation->status);
        $this->assertNotNull($quotation->sent_at);
        $this->assertEquals(OrderStatus::QUOTED, $this->order->status);
    }

    #[Test]
    public function accepting_quotation_syncs_order_total_and_advances_status_to_confirmed(): void
    {
        $createAction = app(CreateQuotation::class);
        $quotation = $createAction->execute($this->order, [
            'items' => [
                ['name' => 'Paket Hemat Pindahan', 'quantity' => 1, 'unit_price' => 300000],
            ],
            'discount' => 25000,
        ], $this->admin);

        // Advance order to QUOTED first
        app(SendQuotation::class)->execute($quotation, $this->admin);
        $this->order->refresh();

        $this->actingAs($this->admin);

        Livewire::test(Manager::class, ['order' => $this->order])
            ->call('accept', $quotation->id);

        $quotation->refresh();
        $this->order->refresh();

        $this->assertEquals(QuotationStatus::ACCEPTED, $quotation->status);
        $this->assertEquals(OrderStatus::CONFIRMED, $this->order->status);
        $this->assertEquals(275000, (int) $this->order->total_amount);
        $this->assertEquals($quotation->id, $this->order->acceptedQuotation->id);
    }

    #[Test]
    public function rejecting_quotation_and_creating_revision_v2(): void
    {
        $createAction = app(CreateQuotation::class);
        $quo1 = $createAction->execute($this->order, [
            'items' => [
                ['name' => 'Paket Standar', 'quantity' => 1, 'unit_price' => 500000],
            ],
            'discount' => 0,
        ], $this->admin);

        $this->actingAs($this->admin);

        // Reject quotation 1
        Livewire::test(Manager::class, ['order' => $this->order])
            ->call('openRejectModal', $quo1->id)
            ->set('rejectionReason', 'Customer minta pengurangan harga 50rb')
            ->call('confirmReject');

        $quo1->refresh();
        $this->assertEquals(QuotationStatus::REJECTED, $quo1->status);
        $this->assertStringContainsString('Customer minta pengurangan harga 50rb', $quo1->notes);

        // Create Revision v2
        Livewire::test(Manager::class, ['order' => $this->order])
            ->call('openRevisionModal', $quo1->id)
            ->set('discount', 50000)
            ->call('saveQuotation');

        $this->order->refresh();
        $this->assertCount(2, $this->order->quotations);

        $quo2 = $this->order->latestQuotation;
        $this->assertEquals(2, $quo2->version);
        $this->assertEquals(450000, (int) $quo2->total_amount);
    }
}
