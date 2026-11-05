<?php

namespace Tests\Feature;

use App\Enums\InventoryStatus;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\ScheduleType;
use App\Models\Customer;
use App\Models\InventoryItem;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Quotation;
use App\Models\Schedule;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CustomerTransactionProtectionTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected Customer $customer;

    protected Service $service;

    protected Order $order;

    protected Quotation $quotation;

    protected Payment $payment;

    protected Schedule $schedule;

    protected InventoryItem $inventoryItem;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();

        $this->customer = Customer::factory()->create([
            'name' => 'Hendro Siswanto',
            'phone' => '081233445566',
            'email' => 'hendro@example.com',
        ]);

        $this->service = Service::factory()->create([
            'name' => 'Jasa Pindahan & Storage',
        ]);

        $this->order = Order::factory()->create([
            'customer_id' => $this->customer->id,
            'service_id' => $this->service->id,
            'status' => OrderStatus::STORED,
            'total_amount' => 750000,
        ]);

        $this->quotation = Quotation::create([
            'order_id' => $this->order->id,
            'quotation_number' => 'QUO-2026-000099-v1',
            'total_amount' => 750000,
            'created_by' => $this->admin->id,
        ]);

        $this->payment = Payment::create([
            'order_id' => $this->order->id,
            'payment_number' => 'PAY-2026-000099',
            'amount' => 750000,
            'method' => PaymentMethod::BANK_TRANSFER,
            'status' => PaymentStatus::PAID,
            'verified_by' => $this->admin->id,
        ]);

        $this->schedule = Schedule::create([
            'order_id' => $this->order->id,
            'type' => ScheduleType::PICKUP,
            'scheduled_date' => now()->addDay()->toDateString(),
            'created_by' => $this->admin->id,
        ]);

        $this->inventoryItem = InventoryItem::create([
            'order_id' => $this->order->id,
            'name' => 'Kulkas 2 Pintu',
            'status' => InventoryStatus::STORED,
        ]);
    }

    #[Test]
    public function deleting_customer_soft_deletes_and_preserves_all_order_history_and_relations(): void
    {
        // Delete the customer
        $this->customer->delete();

        // Customer is soft-deleted
        $this->assertSoftDeleted('customers', [
            'id' => $this->customer->id,
        ]);

        // Customer is not in active default scope
        $this->assertNull(Customer::find($this->customer->id));
        $this->assertNotNull(Customer::withTrashed()->find($this->customer->id));

        // Order and all downstream financial and operational records are intact
        $this->assertDatabaseHas('orders', ['id' => $this->order->id]);
        $this->assertDatabaseHas('quotations', ['id' => $this->quotation->id]);
        $this->assertDatabaseHas('payments', ['id' => $this->payment->id]);
        $this->assertDatabaseHas('schedules', ['id' => $this->schedule->id]);
        $this->assertDatabaseHas('inventory_items', ['id' => $this->inventoryItem->id]);

        // Order still resolves customer relationship via withTrashed
        $orderFresh = Order::find($this->order->id);
        $this->assertNotNull($orderFresh->customer);
        $this->assertSame('Hendro Siswanto', $orderFresh->customer->name);
        $this->assertSame('081233445566', $orderFresh->customer->phone);
    }

    #[Test]
    public function force_deleting_customer_with_orders_is_prevented(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Customer with existing transaction orders cannot be permanently deleted.');

        $this->customer->forceDelete();
    }

    #[Test]
    public function customer_without_orders_can_be_force_deleted(): void
    {
        $standaloneCustomer = Customer::factory()->create([
            'name' => 'Pelanggan Baru Tanpa Order',
        ]);

        $standaloneCustomer->delete();
        $this->assertSoftDeleted('customers', ['id' => $standaloneCustomer->id]);

        $standaloneCustomer->forceDelete();
        $this->assertDatabaseMissing('customers', ['id' => $standaloneCustomer->id]);
    }
}
