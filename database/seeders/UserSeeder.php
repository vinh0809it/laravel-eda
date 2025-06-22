<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Src\Infrastructure\User\ReadModels\User;

class UserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'id' => 1,
            'name' => 'Example Account',
            'email' => 'example@gmail.com',
            'password' => '$2y$12$v2xQslo/806.bsAEfTQRQ.rI0YgHDwxWaksps5TibZVS97SZVJsH6'
        ]);
    }
}
