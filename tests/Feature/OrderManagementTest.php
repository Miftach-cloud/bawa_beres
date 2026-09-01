<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\PricingType;
use App\Enums\UserRole;
use App\Livewire\Admin\Orders\Index as OrderIndex;
use App\Livewire\Admin\Orders\Show as OrderShow;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class OrderManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $operation;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
        $this->operation = User::factory()->operation()->create();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_view_and_search_orders_list(): void
    {
        $customer1 = Customer::factory()->create(['name' => 'Bambang Sudiro', 'phone' => '0811223344']);
        $customer2 = Customer::factory()->create(['name' => 'Ratna Sari', 'phone' => '0855667788']);

        $order1 = Order::factory()->create([
            'customer_id' => $customer1->id,
            'status' => OrderStatus::PENDING_REVIEW,
        ]);

        $order2 = Order::factory()->create([
            'customer_id' => $customer2->id,
            'status' => OrderStatus::STORED,
        ]);

        $this->actingAs($this->admin);

        Livewire::test(OrderIndex::class)
            ->assertSee($order1->order_code)
            ->assertSee('Bambang Sudiro')
            ->assertSee($order2->order_code)
            ->assertSee('Ratna Sari')
            ->set('search', 'Bambang')
            ->assertSee($order1->order_code)
            ->assertDontSee($order2->order_code);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_filter_orders_by_status_and_service_and_date(): void
    {
        $service1 = Service::factory()->create(['name' => 'Jasa Pindahan']);
        $service2 = Service::factory()->create(['name' => 'Storage Penitipan']);

        $orderPending = Order::factory()->create([
            'service_id' => $service1->id,
            'status' => OrderStatus::PENDING_REVIEW,
            'created_at' => Carbon::today(),
        ]);

        $orderStored = Order::factory()->create([
            'service_id' => $service2->id,
            'status' => OrderStatus::STORED,
            'created_at' => Carbon::now()->subMonths(2),
        ]);

        $this->actingAs($this->admin);

        // Filter by status
        Livewire::test(OrderIndex::class)
            ->set('statusFilter', OrderStatus::PENDING_REVIEW->value)
            ->assertSee($orderPending->order_code)
            ->assertDontSee($orderStored->order_code);

        // Filter by service
        Livewire::test(OrderIndex::class)
            ->set('serviceFilter', (string) $service2->id)
            ->assertSee($orderStored->order_code)
            ->assertDontSee($orderPending->order_code);

        // Filter by date (today)
        Livewire::test(OrderIndex::class)
            ->set('dateFilter', 'today')
            ->assertSee($orderPending->order_code)
            ->assertDontSee($orderStored->order_code);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_create_new_order_via_modal(): void
    {
        $service = Service::factory()->create(['name' => 'Pindahan Kost']);

        $this->actingAs($this->admin);

        Livewire::test(OrderIndex::class)
            ->call('openCreateModal')
            ->set('selectedServiceId', $service->id)
            ->set('newCustomerName', 'Rina Wulandari')
            ->set('newCustomerPhone', '081233445566')
            ->set('newCustomerEmail', 'rina@example.com')
            ->set('pickupAddress', 'Jl. Gajayana No. 20')
            ->set('pickupDistrict', 'Lowokwaru')
            ->set('destinationAddress', 'Jl. Semanggi No. 5')
            ->set('destinationDistrict', 'Lowokwaru')
            ->set('items.0.name', 'Kulkas Mini')
            ->set('items.0.quantity', 1)
            ->call('createOrder')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('customers', [
            'name' => 'Rina Wulandari',
            'phone' => '081233445566',
        ]);

        $this->assertDatabaseHas('addresses', [
            'address' => 'Jl. Gajayana No. 20',
            'type' => 'PICKUP',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_view_order_detail_and_transition_status(): void
    {
        $order = Order::factory()->create([
            'status' => OrderStatus::PENDING_REVIEW,
        ]);

        $this->actingAs($this->admin);

        $response = $this->get(route('admin.orders.show', $order));
        $response->assertStatus(200);

        // Perform transition to QUOTED
        Livewire::test(OrderShow::class, ['order' => $order])
            ->assertSee($order->order_code)
            ->call('openTransitionModal', OrderStatus::QUOTED->value)
            ->set('transitionNotes', 'Penawaran harga sebesar Rp 250.000 sudah dikirimkan')
            ->call('confirmTransition');

        $order->refresh();
        $this->assertEquals(OrderStatus::QUOTED, $order->status);
        $this->assertCount(2, $order->statusHistories);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_cancel_order_with_reason(): void
    {
        $order = Order::factory()->create([
            'status' => OrderStatus::PENDING_REVIEW,
        ]);

        $this->actingAs($this->admin);

        Livewire::test(OrderShow::class, ['order' => $order])
            ->call('openCancelModal')
            ->set('cancelReason', 'Customer pindah tanggal ke bulan depan')
            ->call('confirmCancel');

        $order->refresh();
        $this->assertEquals(OrderStatus::CANCELLED, $order->status);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_update_order_notes_and_amount(): void
    {
        $order = Order::factory()->create([
            'total_amount' => 0,
        ]);

        $this->actingAs($this->admin);

        Livewire::test(OrderShow::class, ['order' => $order])
            ->set('adminNotes', 'Catatan driver pick-up siap jam 10 pagi')
            ->set('totalAmount', 320000)
            ->call('saveAdminNotes');

        $order->refresh();
        $this->assertEquals('Catatan driver pick-up siap jam 10 pagi', $order->admin_notes);
        $this->assertEquals(320000, (int) $order->total_amount);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function operation_role_is_denied_from_order_management(): void
    {
        $order = Order::factory()->create();

        $this->actingAs($this->operation);

        $this->get('/admin/orders')->assertStatus(403);
        $this->get('/admin/orders/' . $order->id)->assertStatus(403);
    }
}
