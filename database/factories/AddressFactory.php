<?php

namespace Database\Factories;

use App\Enums\AddressType;
use App\Models\Address;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Address>
 */
class AddressFactory extends Factory
{
    protected $model = Address::class;

    public function definition(): array
    {
        $districts = ['Lowokwaru', 'Klojen', 'Blimbing', 'Sukun', 'Kedungkandang'];
        $district = fake()->randomElement($districts);

        return [
            'order_id' => Order::factory(),
            'type' => AddressType::PICKUP,
            'address' => fake()->streetAddress().', '.$district,
            'city' => 'Kota Malang',
            'district' => $district,
            'latitude' => fake()->latitude(-7.98, -7.92),
            'longitude' => fake()->longitude(112.60, 112.66),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    public function pickup(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => AddressType::PICKUP,
        ]);
    }

    public function destination(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => AddressType::DESTINATION,
        ]);
    }
}
