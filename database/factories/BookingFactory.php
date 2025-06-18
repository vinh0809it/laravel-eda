<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Src\Infrastructure\Booking\Models\Booking;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Src\Infrastructure\Booking\Models\Booking>
 */
class BookingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Booking::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => fake()->uuid(),
            'car_id' => fake()->uuid(),
            'user_id' => fake()->uuid(),
            'start_date' => fake()->date(),
            'end_date' => fake()->date(),
            'original_price' => fake()->randomFloat(2, 100, 1000),
            'status' => fake()->randomElement(['created', 'confirmed', 'cancelled']),
        ];
    }

    public function created(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'created',
        ]);
    }

    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'confirmed',
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
        ]);
    }
}
