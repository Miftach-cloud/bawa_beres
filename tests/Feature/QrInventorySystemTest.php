<?php

namespace Tests\Feature;

use App\Enums\InventoryStatus;
use App\Livewire\Admin\Inventory\QrLabelModal;
use App\Models\Customer;
use App\Models\InventoryItem;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class QrInventorySystemTest extends TestCase
{
    use RefreshDatabase;

    protected User $operation;

    protected Customer $customer;

    protected Order $order;

    protected InventoryItem $item;

    protected function setUp(): void
    {
        parent::setUp();

        $this->operation = User::factory()->operation()->create();
        $this->customer = Customer::factory()->create([
            'name' => 'Budi Santoso',
            'phone' => '081234567890',
        ]);
        $this->order = Order::factory()->create([
            'customer_id' => $this->customer->id,
            'order_code' => 'ORD-2026-000088',
        ]);
        $this->item = InventoryItem::create([
            'order_id' => $this->order->id,
            'name' => 'Sofa Bed 3 Seater',
            'status' => InventoryStatus::RECEIVED,
        ]);
    }

    #[Test]
    public function public_scan_url_renders_custody_seal_for_visitors(): void
    {
        $response = $this->get($this->item->scan_url);

        $response->assertStatus(200);
        $response->assertSee($this->item->inventory_code);
        $response->assertSee('Sofa Bed 3 Seater');
        $response->assertSee('Segel Keaslian Fisik BawaBeres');
        $response->assertDontSee('081234567890'); // Protects customer phone from public
    }

    #[Test]
    public function qr_alias_route_renders_custody_seal_for_visitors(): void
    {
        $response = $this->get("/qr/{$this->item->qr_code}");

        $response->assertStatus(200);
        $response->assertSee($this->item->inventory_code);
        $response->assertSee('Sofa Bed 3 Seater');
        $response->assertSee('Segel Keaslian Fisik BawaBeres');
    }

    #[Test]
    public function authenticated_staff_sees_full_operational_details_and_customer_info_on_scan(): void
    {
        $this->actingAs($this->operation);

        $response = $this->get($this->item->scan_url);

        $response->assertStatus(200);
        $response->assertSee('ORD-2026-000088');
        $response->assertSee('Budi Santoso');
        $response->assertSee('081234567890');
        $response->assertSee('Petugas Aktif');
    }

    #[Test]
    public function printable_label_route_renders_correctly(): void
    {
        $this->actingAs($this->operation);

        $response = $this->get(route('admin.inventory.label', $this->item));

        $response->assertStatus(200);
        $response->assertSee($this->item->inventory_code);
        $response->assertSee('Sofa Bed 3 Seater');
        $response->assertSee('BAWABERES');
    }

    #[Test]
    public function livewire_qr_label_modal_opens_and_previews(): void
    {
        $this->actingAs($this->operation);

        Livewire::test(QrLabelModal::class)
            ->call('open', $this->item->id)
            ->assertSee($this->item->inventory_code)
            ->assertSee($this->item->qr_code)
            ->assertSee('Sofa Bed 3 Seater');
    }
}
