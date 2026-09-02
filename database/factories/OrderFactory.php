<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'order_code' => null, // Let Order model booted hook generate sequential unique code
            'customer_id' => Customer::factory(),
            'service_id' => Service::factory(),
            'status' => OrderStatus::PENDING_REVIEW,
            'customer_notes' => fake()->optional()->sentence(),
            'admin_notes' => null,
            'total_amount' => 0,
        ];
    }

    public function status(OrderStatus $status): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => $status,
        ]);
    }
}
