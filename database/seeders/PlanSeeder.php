<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Plan;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $planes = [
            [
                'nombre' => 'Plan 30 días',
                'tasa_interes_diario' => 0.50, // 0.5% diario
                'plazo_dias' => 30,
            ],
            [
                'nombre' => 'Plan 60 días',
                'tasa_interes_diario' => 0.75, // 0.75% diario
                'plazo_dias' => 60,
            ],
            [
                'nombre' => 'Plan 90 días',
                'tasa_interes_diario' => 1.00, // 1% diario
                'plazo_dias' => 90,
            ],
            [
                'nombre' => 'Plan Express 15 días',
                'tasa_interes_diario' => 0.30, // 0.3% diario
                'plazo_dias' => 15,
            ],
            [
                'nombre' => 'Plan Largo Plazo 120 días',
                'tasa_interes_diario' => 1.25, // 1.25% diario
                'plazo_dias' => 120,
            ],
        ];

        foreach ($planes as $plan) {
            Plan::create($plan);
        }

        $this->command->info('✓ Creados 5 planes de crédito de ejemplo');
    }
}
