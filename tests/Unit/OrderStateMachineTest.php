<?php

namespace Tests\Unit;

use App\Actions\Orders\CancelOrder;
use App\Actions\Orders\ChangeOrderStatus;
use App\Actions\Orders\CreateOrder;
use App\Actions\Orders\UpdateOrder;
use App\Enums\OrderStatus;
use App\Exceptions\InvalidOrderStateTransitionException;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderStateMachineTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function state_machine_allows_valid_status_transitions(): void
    {
        $this->assertTrue(OrderStatus::DRAFT->canTransitionTo(OrderStatus::SUBMITTED));
        $this->assertTrue(OrderStatus::SUBMITTED->canTransitionTo(OrderStatus::PENDING_REVIEW));
        $this->assertTrue(OrderStatus::PENDING_REVIEW->canTransitionTo(OrderStatus::QUOTED));
        $this->assertTrue(OrderStatus::QUOTED->canTransitionTo(OrderStatus::CONFIRMED));
        $this->assertTrue(OrderStatus::CONFIRMED->canTransitionTo(OrderStatus::PAID));
        $this->assertTrue(OrderStatus::PAID->canTransitionTo(OrderStatus::SCHEDULED));
        $this->assertTrue(OrderStatus::SCHEDULED->canTransitionTo(OrderStatus::PICKED_UP));
        $this->assertTrue(OrderStatus::DELIVERED->canTransitionTo(OrderStatus::COMPLETED));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function state_machine_rejects_invalid_status_jumps(): void
    {
        $this->assertFalse(OrderStatus::SUBMITTED->canTransitionTo(OrderStatus::COMPLETED));
        $this->assertFalse(OrderStatus::DRAFT->canTransitionTo(OrderStatus::DELIVERED));
        $this->assertFalse(OrderStatus::PENDING_REVIEW->canTransitionTo(OrderStatus::STORED));
        $this->assertFalse(OrderStatus::COMPLETED->canTransitionTo(OrderStatus::PENDING_REVIEW));
        $this->assertFalse(OrderStatus::CANCELLED->canTransitionTo(OrderStatus::QUOTED));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function change_order_status_action_throws_exception_on_forbidden_transition(): void
    {
        $order = Order::factory()->create(['status' => OrderStatus::SUBMITTED]);
        $action = app(ChangeOrderStatus::class);

        $this->expectException(InvalidOrderStateTransitionException::class);
        $action->execute($order, OrderStatus::COMPLETED);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function change_order_status_action_executes_valid_transition_and_records_history(): void
    {
        $admin = User::factory()->admin()->create();
        $order = Order::factory()->create(['status' => OrderStatus::PENDING_REVIEW]);
        $action = app(ChangeOrderStatus::class);

        $updated = $action->execute($order, OrderStatus::QUOTED, 'Penawaran harga Rp 200.000', $admin);

        $this->assertEquals(OrderStatus::QUOTED, $updated->status);
        $this->assertDatabaseHas('order_status_histories', [
            'order_id' => $order->id,
            'from_status' => OrderStatus::PENDING_REVIEW->value,
            'to_status' => OrderStatus::QUOTED->value,
            'changed_by' => $admin->id,
            'notes' => 'Penawaran harga Rp 200.000',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function create_order_action_creates_full_entity_graph(): void
    {
        $service = Service::factory()->create();
        $action = app(CreateOrder::class);

        $order = $action->execute([
            'service_id' => $service->id,
            'customer_name' => 'Doni Firmansyah',
            'customer_phone' => '081234567890',
            'customer_email' => 'doni@example.com',
            'customer_notes' => 'Tolong hati-hati barang kaca',
            'pickup_address' => [
                'address' => 'Jl. Kalpataru No. 5',
                'district' => 'Lowokwaru',
            ],
            'destination_address' => [
                'address' => 'Jl. Sulfat No. 88',
                'district' => 'Blimbing',
            ],
            'items' => [
                ['name' => 'Lemari 2 Pintu', 'quantity' => 1, 'estimated_size' => 'Besar'],
                ['name' => 'Kardus Piring', 'quantity' => 2, 'estimated_size' => 'Fragile'],
            ],
        ]);

        $this->assertNotNull($order->id);
        $this->assertNotNull($order->customer_id);
        $this->assertEquals('Doni Firmansyah', $order->customer->name);
        $this->assertCount(2, $order->items);
        $this->assertCount(2, $order->addresses);
        $this->assertEquals('Jl. Kalpataru No. 5', $order->pickupAddress->address);
        $this->assertEquals('Jl. Sulfat No. 88', $order->destinationAddress->address);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function cancel_order_action_cancels_order_with_audit_trail(): void
    {
        $admin = User::factory()->admin()->create();
        $order = Order::factory()->create(['status' => OrderStatus::PENDING_REVIEW]);
        $action = app(CancelOrder::class);

        $cancelled = $action->execute($order, 'Customer membatalkan permintaan', $admin);

        $this->assertEquals(OrderStatus::CANCELLED, $cancelled->status);
        $this->assertDatabaseHas('order_status_histories', [
            'order_id' => $order->id,
            'to_status' => OrderStatus::CANCELLED->value,
            'notes' => 'Pembatalan pesanan: Customer membatalkan permintaan',
            'changed_by' => $admin->id,
        ]);
    }
}
