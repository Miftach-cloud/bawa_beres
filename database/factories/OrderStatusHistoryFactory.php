<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderStatusHistory>
 */
class OrderStatusHistoryFactory extends Factory
{
    protected $model = OrderStatusHistory::class;

    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'from_status' => OrderStatus::PENDING_REVIEW,
            'to_status' => OrderStatus::QUOTED,
            'changed_by' => null,
            'notes' => fake()->optional()->sentence(),
            'created_at' => now(),
        ];
    }
}
