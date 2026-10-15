<?php

namespace Database\Seeders;

use Src\Car\Infrastructure\Persistence\Eloquent\CarModel;
use Illuminate\Database\Seeder;

class CarSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        CarModel::factory()->create([
            'id' => '836fbc9c-923f-429d-8ec4-1633bd0450b9',
            'brand' => 'VINFAST',
            'model' => 'VF8',
            'year' => '2022',
            'price_per_day' => '500'
        ]);
    }
}
