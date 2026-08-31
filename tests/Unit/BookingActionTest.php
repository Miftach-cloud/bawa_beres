<?php

namespace Tests\Unit;

use App\Actions\Orders\CreateOrder;
use App\Enums\AddressType;
use App\Enums\OrderStatus;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingActionTest extends TestCase
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
    public function create_order_action_creates_customer_and_order_atomically(): void
    {
        $action = app(CreateOrder::class);

        $order = $action->execute([
            'customer_name' => 'Ahmad Dahlan',
            'customer_phone' => '081298765432',
            'customer_email' => 'ahmad@example.com',
            'service_id' => $this->service->id,
            'preferred_date' => '2026-11-15',
            'customer_notes' => 'Tolong bawa troli barang',
            'items' => [
                ['name' => 'Kasur Springbed', 'quantity' => 1],
                ['name' => 'Kardus Buku', 'quantity' => 3],
            ],
            'pickup_address' => [
                'address' => 'Jl. Sumbersari No. 10',
                'city' => 'Kota Malang',
                'notes' => 'Kost Putra Lantai 2',
            ],
            'destination_address' => [
                'address' => 'Perumahan Tidar Indah Blok A-2',
                'city' => 'Kota Malang',
            ],
        ]);

        $this->assertInstanceOf(Order::class, $order);
        $this->assertNotEmpty($order->order_code);
        $this->assertEquals(OrderStatus::PENDING_REVIEW, $order->status);
        $this->assertEquals('2026-11-15', $order->preferred_date->format('Y-m-d'));

        // Customer created without registration requirement
        $this->assertEquals('Ahmad Dahlan', $order->customer->name);
        $this->assertEquals('081298765432', $order->customer->phone);

        // Items created
        $this->assertCount(2, $order->items);

        // Addresses created
        $this->assertEquals('Jl. Sumbersari No. 10', $order->pickupAddress->address);
        $this->assertEquals(AddressType::PICKUP, $order->pickupAddress->type);
        $this->assertEquals('Perumahan Tidar Indah Blok A-2', $order->destinationAddress->address);
        $this->assertEquals(AddressType::DESTINATION, $order->destinationAddress->type);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function subsequent_booking_reuses_existing_customer_by_phone(): void
    {
        $action = app(CreateOrder::class);

        // 1st Booking
        $order1 = $action->execute([
            'customer_name' => 'Siti Nurhaliza',
            'customer_phone' => '081122334455',
            'service_id' => $this->service->id,
            'pickup_address' => 'Alamat Asal 1',
        ]);

        // 2nd Booking with same phone
        $order2 = $action->execute([
            'customer_name' => 'Siti Nurhaliza',
            'customer_phone' => '081122334455',
            'service_id' => $this->service->id,
            'pickup_address' => 'Alamat Asal 2',
        ]);

        $this->assertEquals($order1->customer_id, $order2->customer_id);
        $this->assertEquals(1, Customer::where('phone', '081122334455')->count());
    }
}
