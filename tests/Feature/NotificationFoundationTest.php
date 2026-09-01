<?php

namespace Tests\Feature;

use App\Actions\Inventory\ReceiveInventoryItem;
use App\Actions\Orders\ChangeOrderStatus;
use App\Actions\Orders\CreateOrder;
use App\Actions\Quotations\CreateQuotation;
use App\Actions\Schedules\CreateSchedule;
use App\Actions\Storage\AssignInventoryToLocation;
use App\Enums\InventoryStatus;
use App\Enums\OrderStatus;
use App\Enums\ScheduleType;
use App\Enums\StorageLocationStatus;
use App\Enums\StorageLocationType;
use App\Enums\UserRole;

use App\Models\Customer;
use App\Models\InventoryItem;
use App\Models\Order;
use App\Models\Quotation;
use App\Models\Service;
use App\Models\StorageLocation;
use App\Models\User;
use App\Notifications\InventoryReceivedNotification;
use App\Notifications\InventoryStoredNotification;
use App\Notifications\OrderCompletedNotification;
use App\Notifications\OrderConfirmedNotification;
use App\Notifications\OrderCreatedNotification;
use App\Notifications\PickupScheduledNotification;
use App\Notifications\QuotationCreatedNotification;
use App\Services\WhatsAppTemplateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class NotificationFoundationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $operation;
    protected User $owner;
    protected Service $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = User::factory()->create([
            'name' => 'Owner BawaBeres',
            'email' => 'owner@bawaberes.id',
            'role' => UserRole::OWNER,
        ]);

        $this->admin = User::factory()->create([
            'name' => 'Admin BawaBeres',
            'email' => 'admin@bawaberes.id',
            'role' => UserRole::ADMIN,
        ]);

        $this->operation = User::factory()->create([
            'name' => 'Operation Staff',
            'email' => 'operation@bawaberes.id',
            'role' => UserRole::OPERATION,
        ]);

        $this->service = Service::create([
            'name' => 'Jasa Pindahan Kost Mahasiswa',
            'description' => 'Layanan pindahan kost mahasiswa hemat di Malang.',
            'base_price' => 150000,
            'is_active' => true,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function order_created_event_dispatches_database_notifications(): void
    {
        $createOrder = app(CreateOrder::class);

        $order = $createOrder->execute([
            'customer_name' => 'Budi Santoso',
            'customer_phone' => '081234567890',
            'customer_email' => 'budi@example.com',
            'service_id' => $this->service->id,
            'pickup_address' => 'Jl. Bendungan Sigura-gura No. 5',
        ]);

        $customer = $order->customer;

        // Verify notification exists in database for customer and admin
        $this->assertDatabaseHas('notifications', [
            'notifiable_type' => Customer::class,
            'notifiable_id' => $customer->id,
            'type' => OrderCreatedNotification::class,
        ]);

        $this->assertDatabaseHas('notifications', [
            'notifiable_type' => User::class,
            'notifiable_id' => $this->admin->id,
            'type' => OrderCreatedNotification::class,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function quotation_created_event_dispatches_database_notifications(): void
    {
        $customer = Customer::create(['name' => 'Siti Rahma', 'phone' => '081987654321']);
        $order = Order::create([
            'customer_id' => $customer->id,
            'service_id' => $this->service->id,
            'status' => OrderStatus::PENDING_REVIEW,
        ]);

        $createQuotation = app(CreateQuotation::class);
        $quotation = $createQuotation->execute($order, [
            'items' => [
                ['name' => 'Biaya Armada Pick-up', 'quantity' => 1, 'unit_price' => 200000],
            ],
        ], $this->admin);

        $this->assertDatabaseHas('notifications', [
            'notifiable_type' => Customer::class,
            'notifiable_id' => $customer->id,
            'type' => QuotationCreatedNotification::class,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function order_confirmed_event_dispatches_database_notifications(): void
    {
        $customer = Customer::create(['name' => 'Andi Wijaya', 'phone' => '08122334455']);
        $order = Order::create([
            'customer_id' => $customer->id,
            'service_id' => $this->service->id,
            'status' => OrderStatus::QUOTED,
        ]);

        $changeOrderStatus = app(ChangeOrderStatus::class);
        $changeOrderStatus->execute($order, OrderStatus::CONFIRMED, 'Pembayaran DP diterima.', $this->admin);

        $this->assertDatabaseHas('notifications', [
            'notifiable_type' => Customer::class,
            'notifiable_id' => $customer->id,
            'type' => OrderConfirmedNotification::class,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function pickup_scheduled_event_dispatches_database_notifications(): void
    {
        $customer = Customer::create(['name' => 'Dewi Lestari', 'phone' => '08133445566']);
        $order = Order::create([
            'customer_id' => $customer->id,
            'service_id' => $this->service->id,
            'status' => OrderStatus::CONFIRMED,
        ]);

        $createSchedule = app(CreateSchedule::class);
        $createSchedule->execute($order, [
            'type' => ScheduleType::PICKUP,
            'scheduled_date' => now()->addDay()->format('Y-m-d'),
            'start_time' => '09:00',
            'driver_name' => 'Pak Joko',
            'vehicle_plate' => 'N 1234 AB',
        ], $this->operation);

        $this->assertDatabaseHas('notifications', [
            'notifiable_type' => Customer::class,
            'notifiable_id' => $customer->id,
            'type' => PickupScheduledNotification::class,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function inventory_received_event_dispatches_database_notifications(): void
    {
        $customer = Customer::create(['name' => 'Rian Pratama', 'phone' => '08155667788']);
        $order = Order::create([
            'customer_id' => $customer->id,
            'service_id' => $this->service->id,
            'status' => OrderStatus::PICKED_UP,
        ]);

        $item = InventoryItem::create([
            'order_id' => $order->id,
            'name' => 'Kardus Buku & Dokumen',
            'qr_code' => 'BB-BOX-2026-0001',
            'status' => InventoryStatus::EXPECTED,
        ]);

        $receiveAction = app(ReceiveInventoryItem::class);
        $receiveAction->execute($item, $this->operation);

        $this->assertDatabaseHas('notifications', [
            'notifiable_type' => Customer::class,
            'notifiable_id' => $customer->id,
            'type' => InventoryReceivedNotification::class,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function inventory_stored_event_dispatches_database_notifications(): void
    {
        $customer = Customer::create(['name' => 'Maya Indah', 'phone' => '08166778899']);
        $order = Order::create([
            'customer_id' => $customer->id,
            'service_id' => $this->service->id,
            'status' => OrderStatus::PROCESSING,
        ]);

        $item = InventoryItem::create([
            'order_id' => $order->id,
            'name' => 'Koper Pakaian Besar',
            'qr_code' => 'BB-BOX-2026-0002',
            'status' => InventoryStatus::RECEIVED,
        ]);

        $location = StorageLocation::create([
            'code' => 'WH1-A-R01-L01',
            'warehouse' => 'WH1',
            'type' => StorageLocationType::STANDARD_RACK,
            'zone' => 'A',
            'rack' => 'R01',
            'level' => 'L01',
            'capacity' => 10,
            'status' => StorageLocationStatus::AVAILABLE,
        ]);




        $assignAction = app(AssignInventoryToLocation::class);
        $assignAction->execute($item, $location, $this->operation);

        $this->assertDatabaseHas('notifications', [
            'notifiable_type' => Customer::class,
            'notifiable_id' => $customer->id,
            'type' => InventoryStoredNotification::class,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function order_completed_event_dispatches_database_notifications(): void
    {
        $customer = Customer::create(['name' => 'Eko Prasetyo', 'phone' => '08177889900']);
        $order = Order::create([
            'customer_id' => $customer->id,
            'service_id' => $this->service->id,
            'status' => OrderStatus::DELIVERED,
        ]);

        $changeOrderStatus = app(ChangeOrderStatus::class);
        $changeOrderStatus->execute($order, OrderStatus::COMPLETED, 'Semua barang telah diserahterimakan.', $this->admin);

        $this->assertDatabaseHas('notifications', [
            'notifiable_type' => Customer::class,
            'notifiable_id' => $customer->id,
            'type' => OrderCompletedNotification::class,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function whatsapp_template_service_generates_accurate_manual_templates_and_links(): void
    {
        $service = app(WhatsAppTemplateService::class);

        // 1. Phone number sanitization
        $this->assertEquals('6281234567890', $service->sanitizePhoneNumber('081234567890'));
        $this->assertEquals('6281234567890', $service->sanitizePhoneNumber('+62 812-3456-7890'));
        $this->assertEquals('6281234567890', $service->sanitizePhoneNumber('6281234567890'));

        $customer = Customer::create(['name' => 'Budi Santoso', 'phone' => '081234567890']);
        $order = Order::create([
            'customer_id' => $customer->id,
            'service_id' => $this->service->id,
            'status' => OrderStatus::SUBMITTED,
        ]);

        // 2. Order Created Template
        $msgCreated = $service->orderCreated($order);
        $this->assertStringContainsString('Budi Santoso', $msgCreated);
        $this->assertStringContainsString($order->order_code, $msgCreated);

        $waLink = $service->generateWhatsAppLink($customer->phone, $msgCreated);
        $this->assertStringStartsWith('https://wa.me/6281234567890?text=', $waLink);

        // 3. Quotation Created Template
        $quotation = Quotation::create([
            'order_id' => $order->id,
            'quotation_number' => 'QUO-2026-00001',
            'version' => 1,
            'total_amount' => 350000,
        ]);
        $msgQuotation = $service->quotationCreated($quotation);
        $this->assertStringContainsString('350.000', $msgQuotation);
        $this->assertStringContainsString('QUO-2026-00001', $msgQuotation);

        // 4. Order Completed Template
        $msgCompleted = $service->orderCompleted($order);
        $this->assertStringContainsString('selesai seluruhnya', $msgCompleted);
    }
}
