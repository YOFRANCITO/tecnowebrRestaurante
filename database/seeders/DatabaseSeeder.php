<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Llamar al seeder del restaurante
        $this->call([
            RestaurantDatabaseSeeder::class,
        ]);
    }
}
