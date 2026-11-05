<?php

namespace Tests\Feature;

use App\Enums\AddressType;
use App\Enums\OrderStatus;
use App\Enums\PricingType;
use App\Models\Address;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DomainCoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_have_orders(): void
    {
        $customer = Customer::factory()->create([
            'name' => 'Ahmad Rizki',
            'phone' => '081234567890',
        ]);

        $service = Service::factory()->create([
            'name' => 'Jasa Pindahan',
            'pricing_type' => PricingType::QUOTATION,
        ]);

        $order1 = Order::factory()->create([
            'customer_id' => $customer->id,
            'service_id' => $service->id,
        ]);

        $order2 = Order::factory()->create([
            'customer_id' => $customer->id,
            'service_id' => $service->id,
        ]);

        $this->assertCount(2, $customer->orders);
        $this->assertTrue($customer->orders->contains($order1));
        $this->assertTrue($customer->orders->contains($order2));
        $this->assertEquals($customer->id, $order1->customer->id);
    }

    public function test_order_belongs_to_service(): void
    {
        $service = Service::factory()->create([
            'name' => 'Penitipan Barang',
            'pricing_type' => PricingType::PACKAGE,
        ]);

        $order = Order::factory()->create([
            'service_id' => $service->id,
        ]);

        $this->assertInstanceOf(Service::class, $order->service);
        $this->assertEquals('Penitipan Barang', $order->service->name);
        $this->assertEquals(PricingType::PACKAGE, $order->service->pricing_type);
    }

    public function test_order_can_have_multiple_items(): void
    {
        $order = Order::factory()->create();

        $item1 = OrderItem::factory()->create([
            'order_id' => $order->id,
            'name' => 'Kulkas 1 Pintu',
            'quantity' => 1,
            'estimated_size' => 'Besar',
        ]);

        $item2 = OrderItem::factory()->create([
            'order_id' => $order->id,
            'name' => 'Kardus Buku',
            'quantity' => 4,
            'estimated_size' => 'Sedang',
        ]);

        $this->assertCount(2, $order->items);
        $this->assertTrue($order->items->contains($item1));
        $this->assertTrue($order->items->contains($item2));
        $this->assertEquals($order->id, $item1->order->id);
    }

    public function test_order_can_have_pickup_and_destination_addresses(): void
    {
        $order = Order::factory()->create();

        $pickup = Address::factory()->pickup()->create([
            'order_id' => $order->id,
            'address' => 'Jl. Soekarno Hatta No. 12',
            'city' => 'Kota Malang',
            'district' => 'Lowokwaru',
        ]);

        $destination = Address::factory()->destination()->create([
            'order_id' => $order->id,
            'address' => 'Jl. Ijen No. 45',
            'city' => 'Kota Malang',
            'district' => 'Klojen',
        ]);

        $this->assertCount(2, $order->addresses);
        $this->assertNotNull($order->pickupAddress);
        $this->assertEquals('Jl. Soekarno Hatta No. 12', $order->pickupAddress->address);
        $this->assertEquals(AddressType::PICKUP, $order->pickupAddress->type);

        $this->assertNotNull($order->destinationAddress);
        $this->assertEquals('Jl. Ijen No. 45', $order->destinationAddress->address);
        $this->assertEquals(AddressType::DESTINATION, $order->destinationAddress->type);
    }

    public function test_status_history_is_created_on_order_creation_and_transition(): void
    {
        $admin = User::factory()->create();

        $order = Order::factory()->create([
            'status' => OrderStatus::PENDING_REVIEW,
        ]);

        // Upon creation, initial history is recorded
        $this->assertCount(1, $order->statusHistories);
        $this->assertEquals(OrderStatus::PENDING_REVIEW, $order->statusHistories->first()->to_status);
        $this->assertNull($order->statusHistories->first()->from_status);

        // Transition status
        $order->transitionTo(OrderStatus::QUOTED, 'Estimasi biaya Rp 250.000', $admin);

        $order->refresh();
        $this->assertEquals(OrderStatus::QUOTED, $order->status);
        $this->assertCount(2, $order->statusHistories);

        $latestHistory = $order->statusHistories()->latest('id')->first();
        $this->assertEquals(OrderStatus::PENDING_REVIEW, $latestHistory->from_status);
        $this->assertEquals(OrderStatus::QUOTED, $latestHistory->to_status);
        $this->assertEquals($admin->id, $latestHistory->changed_by);
        $this->assertEquals('Estimasi biaya Rp 250.000', $latestHistory->notes);
    }

    public function test_order_code_is_unique(): void
    {
        $code = 'ORD-2026-000099';

        Order::factory()->create(['order_code' => $code]);

        $this->expectException(QueryException::class);
        Order::factory()->create(['order_code' => $code]);
    }

    public function test_customer_code_is_unique(): void
    {
        $code = 'CUS-2026-000099';

        Customer::factory()->create(['customer_code' => $code]);

        $this->expectException(QueryException::class);
        Customer::factory()->create(['customer_code' => $code]);
    }

    public function test_customer_without_account_can_exist(): void
    {
        $customer = Customer::factory()->create([
            'user_id' => null,
            'name' => 'Budi Santoso',
        ]);

        $this->assertNull($customer->user);
        $this->assertNotNull($customer->customer_code);
    }
}
