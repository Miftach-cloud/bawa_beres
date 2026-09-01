<?php

namespace Tests\Feature;

use App\Enums\AddressType;
use App\Enums\OrderStatus;
use App\Livewire\Public\OrderTracking;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PublicOrderTrackingTest extends TestCase
{
    use RefreshDatabase;

    protected Customer $customer;

    protected Service $service;

    protected Order $order;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customer = Customer::factory()->create([
            'name' => 'Raden Wijaya',
            'phone' => '081234567890',
        ]);

        $this->service = Service::create([
            'name' => 'Jasa Titip & Storage Barang',
            'base_price' => 50000,
            'is_active' => true,
        ]);

        $this->order = Order::create([
            'customer_id' => $this->customer->id,
            'service_id' => $this->service->id,
            'order_code' => 'ORD-2026-000051',
            'status' => OrderStatus::STORED,
            'preferred_date' => '2026-12-01',
            'admin_notes' => 'INTERNAL SECRET: Driver komisi 15%, kode pin gudang 9988',
        ]);

        $this->order->items()->create([
            'name' => 'Kardus Dokumen',
            'quantity' => 2,
        ]);

        $this->order->addresses()->create([
            'type' => AddressType::PICKUP,
            'address' => 'Jl. Ijen No. 1, Kota Malang',
            'city' => 'Kota Malang',
        ]);
    }

    #[Test]
    public function tracking_page_is_publicly_accessible(): void
    {
        $response = $this->get('/track');

        $response->assertStatus(200);
        $response->assertSee('Lacak Status Pesanan');
        $response->assertSee('Nomor Pesanan (Order Code)');
    }

    #[Test]
    public function tracking_fails_with_invalid_order_code(): void
    {
        Livewire::test(OrderTracking::class)
            ->set('orderCode', 'ORD-9999-999999')
            ->set('phone', '081234567890')
            ->call('track')
            ->assertSee('tidak ditemukan di sistem kami');
    }

    #[Test]
    public function tracking_fails_with_wrong_phone_number(): void
    {
        Livewire::test(OrderTracking::class)
            ->set('orderCode', 'ORD-2026-000051')
            ->set('phone', '089999999999')
            ->call('track')
            ->assertSee('Nomor WhatsApp/HP tidak sesuai');
    }

    #[Test]
    public function tracking_succeeds_with_matching_phone_and_displays_milestones(): void
    {
        Livewire::test(OrderTracking::class)
            ->set('orderCode', 'ORD-2026-000051')
            ->set('phone', '081234567890')
            ->call('track')
            ->assertHasNoErrors()
            ->assertSee('ORD-2026-000051')
            ->assertSee('Jasa Titip & Storage Barang')
            ->assertSee('Kardus Dokumen')
            ->assertSee('Jl. Ijen No. 1')
            ->assertSee('Pesanan Diterima')
            ->assertSee('Penyimpanan & Penguasaan Barang');
    }

    #[Test]
    public function tracking_succeeds_with_last_4_digits_phone_verification(): void
    {
        Livewire::test(OrderTracking::class)
            ->set('orderCode', 'ORD-2026-000051')
            ->set('phone', '7890')
            ->call('track')
            ->assertHasNoErrors()
            ->assertSee('ORD-2026-000051');
    }

    #[Test]
    public function internal_confidential_notes_are_never_leaked_on_public_tracking(): void
    {
        $response = $this->get('/track?code=ORD-2026-000051&phone=081234567890');

        $response->assertStatus(200);
        $response->assertSee('ORD-2026-000051');
        $response->assertDontSee('INTERNAL SECRET');
        $response->assertDontSee('kode pin gudang 9988');
    }
}
