<?php

namespace Tests\Feature;

use App\Livewire\Admin\Orders\Index as OrderIndex;
use App\Livewire\Public\BookingForm;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PublicBookingSystemTest extends TestCase
{
    use RefreshDatabase;

    protected Service $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = Service::create([
            'name' => 'Jasa Pindahan Kost',
            'base_price' => 150000,
            'is_active' => true,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function public_home_page_renders_booking_form(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Pesan Jasa Pindahan');
        $response->assertSee('Jasa Pindahan Kost');
    }


    #[\PHPUnit\Framework\Attributes\Test]
    public function booking_form_validates_required_fields(): void
    {
        Livewire::test(BookingForm::class)
            ->set('customerName', '')
            ->set('customerPhone', '')
            ->set('pickupAddress', '')
            ->call('submit')
            ->assertHasErrors([
                'customerName' => 'required',
                'customerPhone' => 'required',
                'pickupAddress' => 'required',
            ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function customer_can_submit_booking_and_see_confirmation_with_order_code(): void
    {
        $component = Livewire::test(BookingForm::class)
            ->set('serviceId', $this->service->id)
            ->set('customerName', 'Rina Wulandari')
            ->set('customerPhone', '085712345678')
            ->set('customerEmail', 'rina@gmail.com')
            ->set('pickupAddress', 'Jl. Bendungan Sigura-gura No. 12')
            ->set('destinationAddress', 'Jl. Gajayana No. 50')
            ->set('preferredDate', now()->addDays(2)->format('Y-m-d'))
            ->set('items', [
                ['name' => 'Meja Belajar Lipat', 'category' => 'Sedang', 'quantity' => 1, 'notes' => ''],
                ['name' => 'Koper Pakaian', 'category' => 'Besar', 'quantity' => 2, 'notes' => ''],
            ])
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSet('isSubmitted', true)
            ->assertSee('Pesanan Berhasil Diterima!')
            ->assertSee('Rina Wulandari')
            ->assertSee('ORD-2026-');

        $order = Order::whereHas('customer', function ($q) {
            $q->where('phone', '085712345678');
        })->first();

        $this->assertNotNull($order);
        $this->assertCount(2, $order->items);
        $this->assertEquals('Jl. Bendungan Sigura-gura No. 12', $order->pickupAddress->address);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_view_submitted_public_order_in_dashboard(): void
    {
        $admin = User::factory()->admin()->create();

        $order = Order::create([
            'customer_id' => Customer::factory()->create(['name' => 'Joko Widodo'])->id,
            'service_id' => $this->service->id,
            'order_code' => 'ORD-2026-000999',
            'status' => \App\Enums\OrderStatus::PENDING_REVIEW,
        ]);

        $this->actingAs($admin);

        Livewire::test(OrderIndex::class)
            ->assertSee('ORD-2026-000999')
            ->assertSee('Joko Widodo');
    }
}
