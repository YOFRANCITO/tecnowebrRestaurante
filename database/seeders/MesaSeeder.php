<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Mesa;

class MesaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mesas = [
            ['codigo' => 'M01', 'capacidad' => 2],
            ['codigo' => 'M02', 'capacidad' => 2],
            ['codigo' => 'M03', 'capacidad' => 4],
            ['codigo' => 'M04', 'capacidad' => 4],
            ['codigo' => 'M05', 'capacidad' => 4],
            ['codigo' => 'M06', 'capacidad' => 6],
            ['codigo' => 'M07', 'capacidad' => 6],
            ['codigo' => 'M08', 'capacidad' => 8],
            ['codigo' => 'VIP01', 'capacidad' => 10],
            ['codigo' => 'VIP02', 'capacidad' => 12],
            ['codigo' => 'TERRAZA01', 'capacidad' => 4],
            ['codigo' => 'TERRAZA02', 'capacidad' => 4],
            ['codigo' => 'BAR01', 'capacidad' => 2],
            ['codigo' => 'BAR02', 'capacidad' => 2],
            ['codigo' => 'BAR03', 'capacidad' => 2],
        ];

        foreach ($mesas as $mesa) {
            Mesa::create($mesa);
        }

        $this->command->info('✓ Creadas 15 mesas de ejemplo');
    }
}
