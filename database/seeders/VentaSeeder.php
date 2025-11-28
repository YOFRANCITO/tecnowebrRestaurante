<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Producto;
use App\Models\Mesa;
use App\Models\Plan;
use App\Models\User;
use App\Models\Credito;
use App\Models\Pago;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VentaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener datos necesarios
        $cliente = User::whereHas('roles', function ($query) {
            $query->where('name', 'cliente');
        })->first();

        $mesas = Mesa::all();
        $productos = Producto::all();
        $planes = Plan::all();

        if (!$cliente || $mesas->isEmpty() || $productos->isEmpty()) {
            $this->command->warn('⚠ Faltan datos necesarios (cliente, mesas o productos). Ejecuta los otros seeders primero.');
            return;
        }

        // VENTA 1: Pago Inmediato - Almuerzo simple
        DB::transaction(function () use ($cliente, $mesas, $productos) {
            $venta = Venta::create([
                'total' => 0,
                'fecha_hora' => Carbon::now()->subDays(5),
                'estado' => 'ENTREGADO',
                'tipo_pago' => 'Inmediato',
                'user_id' => $cliente->id,
                'mesa_id' => $mesas->random()->id,
            ]);

            $detalles = [
                ['producto' => $productos->where('nombre', 'Silpancho')->first(), 'cantidad' => 1],
                ['producto' => $productos->where('nombre', 'Coca Cola 500ml')->first(), 'cantidad' => 1],
            ];

            $total = 0;
            foreach ($detalles as $detalle) {
                if ($detalle['producto']) {
                    DetalleVenta::create([
                        'venta_id' => $venta->id,
                        'producto_id' => $detalle['producto']->id,
                        'cantidad' => $detalle['cantidad'],
                        'precio_unitario' => $detalle['producto']->precio_venta,
                    ]);
                    $total += $detalle['producto']->precio_venta * $detalle['cantidad'];
                    
                    // Reducir stock
                    $detalle['producto']->decrement('stock', $detalle['cantidad']);
                }
            }

            $venta->update(['total' => $total]);
        });

        // VENTA 2: Pago a Crédito - Cena familiar
        if ($planes->isNotEmpty()) {
            DB::transaction(function () use ($cliente, $mesas, $productos, $planes) {
                $venta = Venta::create([
                    'total' => 0,
                    'fecha_hora' => Carbon::now()->subDays(10),
                    'estado' => 'ENTREGADO',
                    'tipo_pago' => 'Crédito',
                    'user_id' => $cliente->id,
                    'mesa_id' => $mesas->random()->id,
                ]);

                $detalles = [
                    ['producto' => $productos->where('nombre', 'Pique Macho')->first(), 'cantidad' => 2],
                    ['producto' => $productos->where('nombre', 'Cerveza Paceña 650ml')->first(), 'cantidad' => 3],
                    ['producto' => $productos->where('nombre', 'Papas Fritas')->first(), 'cantidad' => 1],
                ];

                $total = 0;
                foreach ($detalles as $detalle) {
                    if ($detalle['producto']) {
                        DetalleVenta::create([
                            'venta_id' => $venta->id,
                            'producto_id' => $detalle['producto']->id,
                            'cantidad' => $detalle['cantidad'],
                            'precio_unitario' => $detalle['producto']->precio_venta,
                        ]);
                        $total += $detalle['producto']->precio_venta * $detalle['cantidad'];
                        $detalle['producto']->decrement('stock', $detalle['cantidad']);
                    }
                }

                $venta->update(['total' => $total]);

                // Crear crédito
                $plan = $planes->where('nombre', 'Plan 30 días')->first() ?? $planes->first();
                $credito = Credito::create([
                    'nro' => Credito::generarNumeroCredito(),
                    'fecha' => $venta->fecha_hora,
                    'saldo_inicial' => $total,
                    'interes' => 0,
                    'capital' => $total,
                    'cuota' => 0,
                    'saldo_final' => $total,
                    'venta_id' => $venta->id,
                    'plan_id' => $plan->id,
                    'user_id' => $cliente->id,
                ]);

                // Generar cuotas
                $credito->generarCuotas();

                // Hacer un pago parcial
                $montoPago = $total * 0.3; // 30% del total
                $interesesAcumulados = $credito->calcularInteresesDiarios();
                $credito->interes += $interesesAcumulados;
                
                $pagoIntereses = min($montoPago, $credito->interes);
                $pagoCapital = $montoPago - $pagoIntereses;
                
                $credito->interes = max(0, $credito->interes - $pagoIntereses);
                $credito->capital = max(0, $credito->capital - $pagoCapital);
                $credito->actualizarSaldo();

                Pago::create([
                    'credito_id' => $credito->id,
                    'monto' => $montoPago,
                    'fecha' => Carbon::now()->subDays(5),
                ]);
            });
        }

        // VENTA 3: Pago Inmediato - Desayuno
        DB::transaction(function () use ($cliente, $mesas, $productos) {
            $venta = Venta::create([
                'total' => 0,
                'fecha_hora' => Carbon::now()->subDays(3),
                'estado' => 'ENTREGADO',
                'tipo_pago' => 'Inmediato',
                'user_id' => $cliente->id,
                'mesa_id' => $mesas->random()->id,
            ]);

            $detalles = [
                ['producto' => $productos->where('nombre', 'Salteñas (2 unidades)')->first(), 'cantidad' => 2],
                ['producto' => $productos->where('nombre', 'Café con Leche')->first(), 'cantidad' => 1],
                ['producto' => $productos->where('nombre', 'Api Morado')->first(), 'cantidad' => 1],
            ];

            $total = 0;
            foreach ($detalles as $detalle) {
                if ($detalle['producto']) {
                    DetalleVenta::create([
                        'venta_id' => $venta->id,
                        'producto_id' => $detalle['producto']->id,
                        'cantidad' => $detalle['cantidad'],
                        'precio_unitario' => $detalle['producto']->precio_venta,
                    ]);
                    $total += $detalle['producto']->precio_venta * $detalle['cantidad'];
                    $detalle['producto']->decrement('stock', $detalle['cantidad']);
                }
            }

            $venta->update(['total' => $total]);
        });

        // VENTA 4: Pago a Crédito - Almuerzo ejecutivo
        if ($planes->isNotEmpty()) {
            DB::transaction(function () use ($cliente, $mesas, $productos, $planes) {
                $venta = Venta::create([
                    'total' => 0,
                    'fecha_hora' => Carbon::now()->subDays(15),
                    'estado' => 'ENTREGADO',
                    'tipo_pago' => 'Crédito',
                    'user_id' => $cliente->id,
                    'mesa_id' => $mesas->random()->id,
                ]);

                $detalles = [
                    ['producto' => $productos->where('nombre', 'Menú Ejecutivo del Día')->first(), 'cantidad' => 3],
                    ['producto' => $productos->where('nombre', 'Jugo Natural de Naranja')->first(), 'cantidad' => 3],
                ];

                $total = 0;
                foreach ($detalles as $detalle) {
                    if ($detalle['producto']) {
                        DetalleVenta::create([
                            'venta_id' => $venta->id,
                            'producto_id' => $detalle['producto']->id,
                            'cantidad' => $detalle['cantidad'],
                            'precio_unitario' => $detalle['producto']->precio_venta,
                        ]);
                        $total += $detalle['producto']->precio_venta * $detalle['cantidad'];
                        $detalle['producto']->decrement('stock', $detalle['cantidad']);
                    }
                }

                $venta->update(['total' => $total]);

                // Crear crédito
                $plan = $planes->where('nombre', 'Plan 60 días')->first() ?? $planes->first();
                $credito = Credito::create([
                    'nro' => Credito::generarNumeroCredito(),
                    'fecha' => $venta->fecha_hora,
                    'saldo_inicial' => $total,
                    'interes' => 0,
                    'capital' => $total,
                    'cuota' => 0,
                    'saldo_final' => $total,
                    'venta_id' => $venta->id,
                    'plan_id' => $plan->id,
                    'user_id' => $cliente->id,
                ]);

                // Generar cuotas
                $credito->generarCuotas();

                // Hacer dos pagos parciales
                $montoPago1 = $total * 0.25; // 25% del total
                $interesesAcumulados1 = $credito->calcularInteresesDiarios();
                $credito->interes += $interesesAcumulados1;
                
                $pagoIntereses1 = min($montoPago1, $credito->interes);
                $pagoCapital1 = $montoPago1 - $pagoIntereses1;
                
                $credito->interes = max(0, $credito->interes - $pagoIntereses1);
                $credito->capital = max(0, $credito->capital - $pagoCapital1);
                $credito->actualizarSaldo();

                Pago::create([
                    'credito_id' => $credito->id,
                    'monto' => $montoPago1,
                    'fecha' => Carbon::now()->subDays(10),
                ]);

                // Segundo pago
                $montoPago2 = $total * 0.30; // 30% del total
                $interesesAcumulados2 = $credito->calcularInteresesDiarios();
                $credito->interes += $interesesAcumulados2;
                
                $pagoIntereses2 = min($montoPago2, $credito->interes);
                $pagoCapital2 = $montoPago2 - $pagoIntereses2;
                
                $credito->interes = max(0, $credito->interes - $pagoIntereses2);
                $credito->capital = max(0, $credito->capital - $pagoCapital2);
                $credito->actualizarSaldo();

                Pago::create([
                    'credito_id' => $credito->id,
                    'monto' => $montoPago2,
                    'fecha' => Carbon::now()->subDays(3),
                ]);
            });
        }

        // VENTA 5: Orden PENDIENTE (para que el mesero pueda marcarla como entregada)
        DB::transaction(function () use ($cliente, $mesas, $productos) {
            $venta = Venta::create([
                'total' => 0,
                'fecha_hora' => Carbon::now()->subHours(2),
                'estado' => 'PENDIENTE',
                'tipo_pago' => 'Inmediato',
                'user_id' => $cliente->id,
                'mesa_id' => $mesas->random()->id,
            ]);

            $detalles = [
                ['producto' => $productos->where('nombre', 'Churrasco Completo')->first(), 'cantidad' => 1],
                ['producto' => $productos->where('nombre', 'Limonada Natural')->first(), 'cantidad' => 1],
            ];

            $total = 0;
            foreach ($detalles as $detalle) {
                if ($detalle['producto']) {
                    DetalleVenta::create([
                        'venta_id' => $venta->id,
                        'producto_id' => $detalle['producto']->id,
                        'cantidad' => $detalle['cantidad'],
                        'precio_unitario' => $detalle['producto']->precio_venta,
                    ]);
                    $total += $detalle['producto']->precio_venta * $detalle['cantidad'];
                    $detalle['producto']->decrement('stock', $detalle['cantidad']);
                }
            }

            $venta->update(['total' => $total]);
        });

        $this->command->info('✓ Creadas 5 ventas de ejemplo (3 inmediatas, 2 a crédito con pagos parciales, 1 pendiente)');
    }
}
