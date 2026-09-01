<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderItem>
 */
class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'name' => fake()->randomElement([
                'Kardus Pakaian (Ukuran L)',
                'Kasur Busa Single (90x200)',
                'Meja Belajar Kayu',
                'Kursi Kantor Ergonomis',
                'Kulkas 1 Pintu',
                'Dispenser Galon Bawah',
                'Box Plastik Transparan 50L',
            ]),
            'description' => fake()->optional()->sentence(),
            'quantity' => fake()->numberBetween(1, 5),
            'estimated_size' => fake()->randomElement(['Kecil', 'Sedang', 'Besar', 'Fragile']),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
