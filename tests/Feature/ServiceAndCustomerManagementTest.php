<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\PricingType;
use App\Livewire\Admin\Customers\Index as CustomerIndex;
use App\Livewire\Admin\Customers\Show as CustomerShow;
use App\Livewire\Admin\Services\Index as ServiceIndex;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ServiceAndCustomerManagementTest extends TestCase
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

    #[Test]
    public function admin_can_view_services_list(): void
    {
        $service = Service::factory()->create([
            'name' => 'Layanan Pindahan Premium',
            'pricing_type' => PricingType::PACKAGE,
        ]);

        $this->actingAs($this->admin);

        Livewire::test(ServiceIndex::class)
            ->assertStatus(200)
            ->assertSee('Layanan Pindahan Premium')
            ->assertSee('Katalog Layanan');
    }

    #[Test]
    public function admin_can_create_new_service(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(ServiceIndex::class)
            ->call('openCreateModal')
            ->set('name', 'Pindahan Kantor Eksekutif')
            ->set('slug', 'pindahan-kantor-eksekutif')
            ->set('pricing_type', PricingType::QUOTATION->value)
            ->set('base_price', 500000)
            ->set('description', 'Pindahan kantor lengkap dengan packing dan asuransi')
            ->set('is_active', true)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('services', [
            'slug' => 'pindahan-kantor-eksekutif',
            'name' => 'Pindahan Kantor Eksekutif',
            'base_price' => 500000,
            'is_active' => true,
        ]);
    }

    #[Test]
    public function admin_can_edit_existing_service(): void
    {
        $service = Service::factory()->create([
            'name' => 'Layanan Lama',
            'base_price' => 100000,
        ]);

        $this->actingAs($this->admin);

        Livewire::test(ServiceIndex::class)
            ->call('openEditModal', $service->id)
            ->set('name', 'Layanan Baru Diperbarui')
            ->set('base_price', 175000)
            ->call('save')
            ->assertHasNoErrors();

        $service->refresh();
        $this->assertEquals('Layanan Baru Diperbarui', $service->name);
        $this->assertEquals(175000, (int) $service->base_price);
    }

    #[Test]
    public function admin_can_toggle_service_active_status(): void
    {
        $service = Service::factory()->create([
            'is_active' => true,
        ]);

        $this->actingAs($this->admin);

        Livewire::test(ServiceIndex::class)
            ->call('toggleStatus', $service->id);

        $service->refresh();
        $this->assertFalse($service->is_active);

        Livewire::test(ServiceIndex::class)
            ->call('toggleStatus', $service->id);

        $service->refresh();
        $this->assertTrue($service->is_active);
    }

    #[Test]
    public function operation_role_cannot_access_service_management(): void
    {
        $this->actingAs($this->operation);

        $this->get('/admin/services')->assertStatus(403);
    }

    #[Test]
    public function admin_can_view_and_search_customers_list(): void
    {
        $customer1 = Customer::factory()->create([
            'name' => 'Budi Santoso',
            'phone' => '081234567890',
        ]);

        $customer2 = Customer::factory()->create([
            'name' => 'Siti Nurhaliza',
            'phone' => '089876543210',
        ]);

        $this->actingAs($this->admin);

        Livewire::test(CustomerIndex::class)
            ->assertSee('Budi Santoso')
            ->assertSee('Siti Nurhaliza')
            ->set('search', 'Budi')
            ->assertSee('Budi Santoso')
            ->assertDontSee('Siti Nurhaliza');
    }

    #[Test]
    public function admin_can_create_new_customer(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(CustomerIndex::class)
            ->call('openCreateModal')
            ->set('name', 'Dewi Lestari')
            ->set('phone', '081122334455')
            ->set('email', 'dewi@example.com')
            ->set('notes', 'Pelanggan VIP Kost Dinoyo')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('customers', [
            'name' => 'Dewi Lestari',
            'phone' => '081122334455',
            'email' => 'dewi@example.com',
        ]);
    }

    #[Test]
    public function admin_can_view_customer_detail_with_orders(): void
    {
        $customer = Customer::factory()->create([
            'name' => 'Agus Pratama',
            'phone' => '081299998888',
        ]);

        $service = Service::factory()->create(['name' => 'Penitipan Barang']);

        $order1 = Order::factory()->create([
            'customer_id' => $customer->id,
            'service_id' => $service->id,
            'status' => OrderStatus::COMPLETED,
            'total_amount' => 350000,
        ]);

        $order2 = Order::factory()->create([
            'customer_id' => $customer->id,
            'service_id' => $service->id,
            'status' => OrderStatus::STORED,
            'total_amount' => 150000,
        ]);

        $this->actingAs($this->admin);

        $response = $this->get(route('admin.customers.show', $customer));
        $response->assertStatus(200);

        Livewire::test(CustomerShow::class, ['customer' => $customer])
            ->assertSee('Agus Pratama')
            ->assertSee($order1->order_code)
            ->assertSee($order2->order_code)
            ->assertSee('Selesai')
            ->assertSee('Tersimpan di Gudang Storage')
            ->assertSee('Rp 500.000');

    }

    #[Test]
    public function admin_can_update_customer_from_detail_page(): void
    {
        $customer = Customer::factory()->create([
            'name' => 'Nama Awal',
            'phone' => '081111111111',
        ]);

        $this->actingAs($this->admin);

        Livewire::test(CustomerShow::class, ['customer' => $customer])
            ->call('openEditModal')
            ->set('name', 'Nama Baru Terupdate')
            ->set('phone', '082222222222')
            ->call('updateCustomer')
            ->assertHasNoErrors();

        $customer->refresh();
        $this->assertEquals('Nama Baru Terupdate', $customer->name);
        $this->assertEquals('082222222222', $customer->phone);
    }

    #[Test]
    public function operation_role_cannot_access_customer_management(): void
    {
        $customer = Customer::factory()->create();

        $this->actingAs($this->operation);

        $this->get('/admin/customers')->assertStatus(403);
        $this->get('/admin/customers/'.$customer->id)->assertStatus(403);
    }
}
