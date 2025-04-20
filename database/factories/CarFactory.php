<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Src\Infrastructure\Car\Models\Car;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Src\Infrastructure\Car\Models\Car>
 */
class CarFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Car::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => fake()->uuid(),
            'brand' => fake()->name(),
            'model' => fake()->name(),
            'year' => fake()->year(),
            'price_per_day' => fake()->randomFloat(2, 100, 1000),
            'is_available' => true,
        ];
    }

    public function unavailable(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_available' => false,
        ]);
    }
}
