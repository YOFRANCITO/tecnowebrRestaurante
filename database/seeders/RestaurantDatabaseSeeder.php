<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Marca;
use App\Models\Insumo;
use App\Models\Proveedor;
use App\Models\Compra;
use App\Models\DetalleCompra;
use App\Models\Movimiento;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class RestaurantDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Primero ejecutar el seeder de roles
        $this->call(RestaurantRolesSeeder::class);

        // Crear usuarios de prueba
        $this->createUsers();

        // Crear marcas
        $marcas = $this->createMarcas();

        // Crear insumos
        $insumos = $this->createInsumos($marcas);

        // Crear proveedores
        $proveedores = $this->createProveedores();

        // Crear compras con detalles y movimientos
        $this->createCompras($proveedores, $insumos);

        // Crear algunos movimientos adicionales
        $this->createMovimientos($insumos);

        // Crear mesas (CU6)
        $this->call(MesaSeeder::class);

        // Crear planes de crédito (CU7)
        $this->call(PlanSeeder::class);

        // Crear productos (platos y bebidas)
        $this->call(ProductoSeeder::class);

        // Crear ventas de ejemplo (con créditos y pagos)
        $this->call(VentaSeeder::class);

        $this->command->info('✅ Base de datos del restaurante poblada exitosamente!');
    }

    private function createUsers()
    {
        $this->command->info('Creando usuarios...');

        $admin = User::create([
            'name' => 'Administrador Sistema',
            'email' => 'admin@santopecatto.com',
            'password' => Hash::make('password123'),
            'profile_photo_path' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=400&h=400&fit=crop&crop=face',
        ]);
        $admin->assignRole('administrador');

        $almacenero = User::create([
            'name' => 'Juan Pérez',
            'email' => 'almacenero@santopecatto.com',
            'password' => Hash::make('password123'),
            'profile_photo_path' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=400&h=400&fit=crop&crop=face',
        ]);
        $almacenero->assignRole('almacenero');

        $mesero = User::create([
            'name' => 'María García',
            'email' => 'mesero@santopecatto.com',
            'password' => Hash::make('password123'),
            'profile_photo_path' => 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=400&h=400&fit=crop&crop=face',
        ]);
        $mesero->assignRole('mesero');

        $cliente1 = User::create([
            'name' => 'Carlos Rodríguez',
            'email' => 'cliente1@example.com',
            'password' => Hash::make('password123'),
            'profile_photo_path' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&h=400&fit=crop&crop=face',
        ]);
        $cliente1->assignRole('cliente');

        $cliente2 = User::create([
            'name' => 'Ana Martínez',
            'email' => 'cliente2@example.com',
            'password' => Hash::make('password123'),
            'profile_photo_path' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=400&h=400&fit=crop&crop=face',
        ]);
        $cliente2->assignRole('cliente');

        $this->command->info('✓ Creados 5 usuarios con fotos de perfil (1 admin, 1 almacenero, 1 mesero, 2 clientes)');
    }

    private function createMarcas()
    {
        $this->command->info('Creando marcas...');

        $marcas = [
            ['nombre' => 'PIL'],
            ['nombre' => 'Coca Cola'],
            ['nombre' => 'Paceña'],
            ['nombre' => 'Fino'],
            ['nombre' => 'Baldom'],
            ['nombre' => 'La Italiana'],
            ['nombre' => 'Ricapollo'],
            ['nombre' => 'Maggi'],
        ];

        $createdMarcas = [];
        foreach ($marcas as $marca) {
            $createdMarcas[] = Marca::create($marca);
        }

        $this->command->info('✓ ' . count($createdMarcas) . ' marcas creadas');
        return $createdMarcas;
    }

    private function createInsumos($marcas)
    {
        $this->command->info('Creando insumos...');

        $insumos = [
            ['nombre' => 'Leche entera', 'stock' => 50, 'unidad_medida' => 'litros', 'marca_id' => $marcas[0]->id],
            ['nombre' => 'Coca Cola 2L', 'stock' => 100, 'unidad_medida' => 'unidades', 'marca_id' => $marcas[1]->id],
            ['nombre' => 'Cerveza Paceña', 'stock' => 200, 'unidad_medida' => 'unidades', 'marca_id' => $marcas[2]->id],
            ['nombre' => 'Aceite vegetal', 'stock' => 30, 'unidad_medida' => 'litros', 'marca_id' => $marcas[3]->id],
            ['nombre' => 'Arroz', 'stock' => 100, 'unidad_medida' => 'kg', 'marca_id' => $marcas[4]->id],
            ['nombre' => 'Fideos', 'stock' => 80, 'unidad_medida' => 'kg', 'marca_id' => $marcas[5]->id],
            ['nombre' => 'Pollo entero', 'stock' => 25, 'unidad_medida' => 'unidades', 'marca_id' => $marcas[6]->id],
            ['nombre' => 'Caldo de pollo', 'stock' => 150, 'unidad_medida' => 'unidades', 'marca_id' => $marcas[7]->id],
            ['nombre' => 'Tomate', 'stock' => 40, 'unidad_medida' => 'kg', 'marca_id' => null],
            ['nombre' => 'Cebolla', 'stock' => 35, 'unidad_medida' => 'kg', 'marca_id' => null],
            ['nombre' => 'Papa', 'stock' => 120, 'unidad_medida' => 'kg', 'marca_id' => null],
            ['nombre' => 'Sal', 'stock' => 20, 'unidad_medida' => 'kg', 'marca_id' => null],
        ];

        $createdInsumos = [];
        foreach ($insumos as $insumo) {
            $createdInsumos[] = Insumo::create($insumo);
        }

        $this->command->info('✓ ' . count($createdInsumos) . ' insumos creados');
        return $createdInsumos;
    }

    private function createProveedores()
    {
        $this->command->info('Creando proveedores...');

        $proveedores = [
            [
                'nombre' => 'Distribuidora PIL',
                'detalles' => 'Proveedor de lácteos y bebidas',
                'correo' => 'ventas@pil.com.bo',
                'celular' => '70123456',
            ],
            [
                'nombre' => 'Mercado Central',
                'detalles' => 'Proveedor de verduras y frutas frescas',
                'correo' => 'mercadocentral@gmail.com',
                'celular' => '71234567',
            ],
            [
                'nombre' => 'Distribuidora La Paz',
                'detalles' => 'Proveedor de abarrotes y productos secos',
                'correo' => 'info@distlapaz.com',
                'celular' => '72345678',
            ],
            [
                'nombre' => 'Avícola San José',
                'detalles' => 'Proveedor de carnes de pollo',
                'correo' => 'ventas@avicolasj.com',
                'celular' => '73456789',
            ],
        ];

        $createdProveedores = [];
        foreach ($proveedores as $proveedor) {
            $createdProveedores[] = Proveedor::create($proveedor);
        }

        $this->command->info('✓ ' . count($createdProveedores) . ' proveedores creados');
        return $createdProveedores;
    }

    private function createCompras($proveedores, $insumos)
    {
        $this->command->info('Creando compras...');

        $admin = User::where('email', 'admin@santopecatto.com')->first();

        // Compra 1: Lácteos y bebidas
        DB::transaction(function () use ($proveedores, $insumos, $admin) {
            $compra1 = Compra::create([
                'total' => 0,
                'proveedor_id' => $proveedores[0]->id,
                'user_id' => $admin->id,
            ]);

            $detalles = [
                ['insumo_id' => $insumos[0]->id, 'cantidad' => 20, 'costo_unitario' => 7.50], // Leche
                ['insumo_id' => $insumos[1]->id, 'cantidad' => 50, 'costo_unitario' => 12.00], // Coca Cola
            ];

            $total = 0;
            foreach ($detalles as $detalle) {
                $insumo = Insumo::lockForUpdate()->find($detalle['insumo_id']);
                $stockAnterior = $insumo->stock;
                $stockNuevo = $stockAnterior + $detalle['cantidad'];

                $movimiento = Movimiento::create([
                    'tipo' => 'Ingreso',
                    'cantidad' => $detalle['cantidad'],
                    'motivo' => 'Compra #' . $compra1->id . ' - ' . $proveedores[0]->nombre,
                    'insumo_id' => $detalle['insumo_id'],
                    'stock_anterior' => $stockAnterior,
                    'stock_nuevo' => $stockNuevo,
                ]);

                $insumo->update(['stock' => $stockNuevo]);

                DetalleCompra::create([
                    'compra_id' => $compra1->id,
                    'insumo_id' => $detalle['insumo_id'],
                    'cantidad' => $detalle['cantidad'],
                    'costo_unitario' => $detalle['costo_unitario'],
                    'movimiento_id' => $movimiento->id,
                ]);

                $total += $detalle['cantidad'] * $detalle['costo_unitario'];
            }

            $compra1->update(['total' => $total]);
        });

        // Compra 2: Verduras
        DB::transaction(function () use ($proveedores, $insumos, $admin) {
            $compra2 = Compra::create([
                'total' => 0,
                'proveedor_id' => $proveedores[1]->id,
                'user_id' => $admin->id,
            ]);

            $detalles = [
                ['insumo_id' => $insumos[8]->id, 'cantidad' => 15, 'costo_unitario' => 8.00], // Tomate
                ['insumo_id' => $insumos[9]->id, 'cantidad' => 12, 'costo_unitario' => 6.00], // Cebolla
                ['insumo_id' => $insumos[10]->id, 'cantidad' => 50, 'costo_unitario' => 4.50], // Papa
            ];

            $total = 0;
            foreach ($detalles as $detalle) {
                $insumo = Insumo::lockForUpdate()->find($detalle['insumo_id']);
                $stockAnterior = $insumo->stock;
                $stockNuevo = $stockAnterior + $detalle['cantidad'];

                $movimiento = Movimiento::create([
                    'tipo' => 'Ingreso',
                    'cantidad' => $detalle['cantidad'],
                    'motivo' => 'Compra #' . $compra2->id . ' - ' . $proveedores[1]->nombre,
                    'insumo_id' => $detalle['insumo_id'],
                    'stock_anterior' => $stockAnterior,
                    'stock_nuevo' => $stockNuevo,
                ]);

                $insumo->update(['stock' => $stockNuevo]);

                DetalleCompra::create([
                    'compra_id' => $compra2->id,
                    'insumo_id' => $detalle['insumo_id'],
                    'cantidad' => $detalle['cantidad'],
                    'costo_unitario' => $detalle['costo_unitario'],
                    'movimiento_id' => $movimiento->id,
                ]);

                $total += $detalle['cantidad'] * $detalle['costo_unitario'];
            }

            $compra2->update(['total' => $total]);
        });

        $this->command->info('✓ 2 compras creadas con sus detalles y movimientos');
    }

    private function createMovimientos($insumos)
    {
        $this->command->info('Creando movimientos adicionales...');

        // Salida de arroz
        $arroz = $insumos[4];
        $stockAnterior = $arroz->stock;
        $cantidad = 10;
        $stockNuevo = $stockAnterior - $cantidad;

        Movimiento::create([
            'tipo' => 'Salida',
            'cantidad' => $cantidad,
            'motivo' => 'Uso en cocina - Preparación de platos',
            'insumo_id' => $arroz->id,
            'stock_anterior' => $stockAnterior,
            'stock_nuevo' => $stockNuevo,
        ]);

        $arroz->update(['stock' => $stockNuevo]);

        // Ajuste de inventario de pollo
        $pollo = $insumos[6];
        $stockAnterior = $pollo->stock;
        $stockNuevo = 30; // Ajuste a 30 unidades

        Movimiento::create([
            'tipo' => 'Ajuste',
            'cantidad' => $stockNuevo,
            'motivo' => 'Ajuste por inventario físico',
            'insumo_id' => $pollo->id,
            'stock_anterior' => $stockAnterior,
            'stock_nuevo' => $stockNuevo,
        ]);

        $pollo->update(['stock' => $stockNuevo]);

        $this->command->info('✓ Movimientos adicionales creados');
    }
}
