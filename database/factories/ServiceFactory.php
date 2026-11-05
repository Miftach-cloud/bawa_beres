<?php

namespace Database\Factories;

use App\Enums\PricingType;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Service>
 */
class ServiceFactory extends Factory
{
    protected $model = Service::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'name' => ucwords($name),
            'slug' => Str::slug($name),
            'description' => fake()->paragraph(),
            'pricing_type' => fake()->randomElement(PricingType::cases()),
            'base_price' => fake()->randomElement([50000, 100000, 250000, 500000]),
            'is_active' => true,
            'requires_pickup' => true,
            'requires_destination' => true,
            'requires_storage' => false,
        ];
    }

    public function storage(): static
    {
        return $this->state(fn (array $attributes) => [
            'requires_pickup' => true,
            'requires_destination' => false,
            'requires_storage' => true,
        ]);
    }

    public function moving(): static
    {
        return $this->state(fn (array $attributes) => [
            'requires_pickup' => true,
            'requires_destination' => true,
            'requires_storage' => false,
        ]);
    }

    public function delivery(): static
    {
        return $this->state(fn (array $attributes) => [
            'requires_pickup' => true,
            'requires_destination' => true,
            'requires_storage' => false,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
