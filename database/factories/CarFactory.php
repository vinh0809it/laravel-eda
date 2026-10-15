<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Src\Car\Infrastructure\Persistence\Eloquent\CarModel;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Src\Car\Infrastructure\Persistence\Eloquent\CarModel>
 */
class CarFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CarModel::class;

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
            'price_per_day' => fake()->randomFloat(2, 100, 1000)
        ];
    }
}
