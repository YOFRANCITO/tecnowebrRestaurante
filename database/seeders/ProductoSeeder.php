<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Producto;

class ProductoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $productos = [
            // PLATOS PRINCIPALES
            [
                'nombre' => 'Pique Macho',
                'costo' => 25.00,
                'precio_venta' => 45.00,
                'stock' => 50,
                'imagen_url' => 'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?w=800&h=800&fit=crop',
            ],
            [
                'nombre' => 'Silpancho',
                'costo' => 18.00,
                'precio_venta' => 32.00,
                'stock' => 50,
                'imagen_url' => 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=800&h=800&fit=crop',
            ],
            [
                'nombre' => 'Sajta de Pollo',
                'costo' => 20.00,
                'precio_venta' => 35.00,
                'stock' => 40,
                'imagen_url' => 'https://images.unsplash.com/photo-1598103442097-8b74394b95c6?w=800&h=800&fit=crop',
            ],
            [
                'nombre' => 'Fricasé',
                'costo' => 22.00,
                'precio_venta' => 38.00,
                'stock' => 35,
                'imagen_url' => 'https://images.unsplash.com/photo-1547592166-23ac45744acd?w=800&h=800&fit=crop',
            ],
            [
                'nombre' => 'Chairo Paceño',
                'costo' => 15.00,
                'precio_venta' => 28.00,
                'stock' => 40,
                'imagen_url' => 'https://images.unsplash.com/photo-1547592180-85f173990554?w=800&h=800&fit=crop',
            ],
            [
                'nombre' => 'Lechón al Horno',
                'costo' => 30.00,
                'precio_venta' => 55.00,
                'stock' => 25,
                'imagen_url' => 'https://images.unsplash.com/photo-1544025162-d76694265947?w=800&h=800&fit=crop',
            ],
            [
                'nombre' => 'Trucha a la Plancha',
                'costo' => 28.00,
                'precio_venta' => 50.00,
                'stock' => 30,
                'imagen_url' => 'https://images.unsplash.com/photo-1519708227418-c8fd9a32b7a2?w=800&h=800&fit=crop',
            ],
            [
                'nombre' => 'Milanesa de Pollo',
                'costo' => 16.00,
                'precio_venta' => 30.00,
                'stock' => 45,
                'imagen_url' => 'https://images.unsplash.com/photo-1562967914-608f82629710?w=800&h=800&fit=crop',
            ],
            [
                'nombre' => 'Churrasco Completo',
                'costo' => 24.00,
                'precio_venta' => 42.00,
                'stock' => 40,
                'imagen_url' => 'https://images.unsplash.com/photo-1558030006-450675393462?w=800&h=800&fit=crop',
            ],
            [
                'nombre' => 'Plato Paceño',
                'costo' => 26.00,
                'precio_venta' => 48.00,
                'stock' => 35,
                'imagen_url' => 'https://images.unsplash.com/photo-1567620905732-2d1ec7ab7445?w=800&h=800&fit=crop',
            ],

            // ENTRADAS Y PICADAS
            [
                'nombre' => 'Salteñas (2 unidades)',
                'costo' => 4.00,
                'precio_venta' => 10.00,
                'stock' => 100,
                'imagen_url' => 'https://images.unsplash.com/photo-1601050690597-df0568f70950?w=800&h=800&fit=crop',
            ],
            [
                'nombre' => 'Empanadas de Queso (3 unidades)',
                'costo' => 3.50,
                'precio_venta' => 8.00,
                'stock' => 100,
                'imagen_url' => 'https://images.unsplash.com/photo-1599599810769-bcde5a160d32?w=800&h=800&fit=crop',
            ],
            [
                'nombre' => 'Anticuchos (4 unidades)',
                'costo' => 8.00,
                'precio_venta' => 18.00,
                'stock' => 60,
                'imagen_url' => 'https://images.unsplash.com/photo-1529042410759-befb1204b468?w=800&h=800&fit=crop',
            ],
            [
                'nombre' => 'Chorizo Chuquisaqueño',
                'costo' => 10.00,
                'precio_venta' => 22.00,
                'stock' => 50,
                'imagen_url' => 'https://images.unsplash.com/photo-1612392062798-2dbae2d8d6f9?w=800&h=800&fit=crop',
            ],
            [
                'nombre' => 'Papas Fritas',
                'costo' => 5.00,
                'precio_venta' => 12.00,
                'stock' => 80,
                'imagen_url' => 'https://images.unsplash.com/photo-1573080496219-bb080dd4f877?w=800&h=800&fit=crop',
            ],

            // SOPAS
            [
                'nombre' => 'Sopa de Maní',
                'costo' => 8.00,
                'precio_venta' => 18.00,
                'stock' => 40,
                'imagen_url' => 'https://images.unsplash.com/photo-1547592166-23ac45744acd?w=800&h=800&fit=crop',
            ],
            [
                'nombre' => 'Sopa de Quinua',
                'costo' => 7.00,
                'precio_venta' => 16.00,
                'stock' => 40,
                'imagen_url' => 'https://images.unsplash.com/photo-1547592180-85f173990554?w=800&h=800&fit=crop',
            ],
            [
                'nombre' => 'Caldo de Pollo',
                'costo' => 6.00,
                'precio_venta' => 14.00,
                'stock' => 50,
                'imagen_url' => 'https://images.unsplash.com/photo-1604908176997-125f25cc6f3d?w=800&h=800&fit=crop',
            ],

            // POSTRES
            [
                'nombre' => 'Helado de Canela',
                'costo' => 4.00,
                'precio_venta' => 10.00,
                'stock' => 60,
                'imagen_url' => 'https://images.unsplash.com/photo-1563805042-7684c019e1cb?w=800&h=800&fit=crop',
            ],
            [
                'nombre' => 'Flan Casero',
                'costo' => 3.50,
                'precio_venta' => 8.00,
                'stock' => 50,
                'imagen_url' => 'https://images.unsplash.com/photo-1587314168485-3236d6710814?w=800&h=800&fit=crop',
            ],
            [
                'nombre' => 'Torta de Chocolate',
                'costo' => 5.00,
                'precio_venta' => 12.00,
                'stock' => 40,
                'imagen_url' => 'https://images.unsplash.com/photo-1578985545062-69928b1d9587?w=800&h=800&fit=crop',
            ],
            [
                'nombre' => 'Buñuelos (6 unidades)',
                'costo' => 3.00,
                'precio_venta' => 7.00,
                'stock' => 70,
                'imagen_url' => 'https://images.unsplash.com/photo-1612182062631-4b6c3c0e5e3f?w=800&h=800&fit=crop',
            ],

            // BEBIDAS CALIENTES
            [
                'nombre' => 'Café Americano',
                'costo' => 2.00,
                'precio_venta' => 6.00,
                'stock' => 100,
                'imagen_url' => 'https://images.unsplash.com/photo-1509042239860-f550ce710b93?w=800&h=800&fit=crop',
            ],
            [
                'nombre' => 'Café con Leche',
                'costo' => 2.50,
                'precio_venta' => 7.00,
                'stock' => 100,
                'imagen_url' => 'https://images.unsplash.com/photo-1461023058943-07fcbe16d735?w=800&h=800&fit=crop',
            ],
            [
                'nombre' => 'Té de Coca',
                'costo' => 1.50,
                'precio_venta' => 5.00,
                'stock' => 100,
                'imagen_url' => 'https://images.unsplash.com/photo-1556679343-c7306c1976bc?w=800&h=800&fit=crop',
            ],
            [
                'nombre' => 'Api Morado',
                'costo' => 2.00,
                'precio_venta' => 6.00,
                'stock' => 80,
                'imagen_url' => 'https://images.unsplash.com/photo-1544145945-f90425340c7e?w=800&h=800&fit=crop',
            ],

            // BEBIDAS FRÍAS
            [
                'nombre' => 'Coca Cola 500ml',
                'costo' => 3.00,
                'precio_venta' => 8.00,
                'stock' => 150,
                'imagen_url' => 'https://images.unsplash.com/photo-1554866585-cd94860890b7?w=800&h=800&fit=crop',
            ],
            [
                'nombre' => 'Coca Cola 2L',
                'costo' => 8.00,
                'precio_venta' => 18.00,
                'stock' => 80,
                'imagen_url' => 'https://images.unsplash.com/photo-1629203851122-3726ecdf080e?w=800&h=800&fit=crop',
            ],
            [
                'nombre' => 'Sprite 500ml',
                'costo' => 3.00,
                'precio_venta' => 8.00,
                'stock' => 120,
                'imagen_url' => 'https://images.unsplash.com/photo-1625772452859-1c03d5bf1137?w=800&h=800&fit=crop',
            ],
            [
                'nombre' => 'Fanta 500ml',
                'costo' => 3.00,
                'precio_venta' => 8.00,
                'stock' => 120,
                'imagen_url' => 'https://images.unsplash.com/photo-1624517452488-04869289c4ca?w=800&h=800&fit=crop',
            ],
            [
                'nombre' => 'Agua Mineral 500ml',
                'costo' => 2.00,
                'precio_venta' => 5.00,
                'stock' => 200,
                'imagen_url' => 'https://images.unsplash.com/photo-1548839140-29a749e1cf4d?w=800&h=800&fit=crop',
            ],
            [
                'nombre' => 'Jugo Natural de Naranja',
                'costo' => 3.50,
                'precio_venta' => 10.00,
                'stock' => 60,
                'imagen_url' => 'https://images.unsplash.com/photo-1600271886742-f049cd451bba?w=800&h=800&fit=crop',
            ],
            [
                'nombre' => 'Jugo Natural de Papaya',
                'costo' => 3.50,
                'precio_venta' => 10.00,
                'stock' => 60,
                'imagen_url' => 'https://images.unsplash.com/photo-1610970881699-44a5587cabec?w=800&h=800&fit=crop',
            ],
            [
                'nombre' => 'Limonada Natural',
                'costo' => 2.50,
                'precio_venta' => 8.00,
                'stock' => 80,
                'imagen_url' => 'https://images.unsplash.com/photo-1523677011781-c91d1bbe2f9d?w=800&h=800&fit=crop',
            ],

            // CERVEZAS
            [
                'nombre' => 'Cerveza Paceña 330ml',
                'costo' => 4.00,
                'precio_venta' => 12.00,
                'stock' => 200,
                'imagen_url' => 'https://images.unsplash.com/photo-1608270586620-248524c67de9?w=800&h=800&fit=crop',
            ],
            [
                'nombre' => 'Cerveza Huari 330ml',
                'costo' => 4.00,
                'precio_venta' => 12.00,
                'stock' => 150,
                'imagen_url' => 'https://images.unsplash.com/photo-1535958636474-b021ee887b13?w=800&h=800&fit=crop',
            ],
            [
                'nombre' => 'Cerveza Taquiña 330ml',
                'costo' => 4.00,
                'precio_venta' => 12.00,
                'stock' => 150,
                'imagen_url' => 'https://images.unsplash.com/photo-1618885472179-5e474019f2a9?w=800&h=800&fit=crop',
            ],
            [
                'nombre' => 'Cerveza Paceña 650ml',
                'costo' => 7.00,
                'precio_venta' => 18.00,
                'stock' => 120,
                'imagen_url' => 'https://images.unsplash.com/photo-1532634922-8fe0b757fb13?w=800&h=800&fit=crop',
            ],

            // MENÚ EJECUTIVO
            [
                'nombre' => 'Menú Ejecutivo del Día',
                'costo' => 12.00,
                'precio_venta' => 25.00,
                'stock' => 50,
                'imagen_url' => 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=800&h=800&fit=crop',
            ],
            [
                'nombre' => 'Menú Vegetariano',
                'costo' => 10.00,
                'precio_venta' => 22.00,
                'stock' => 30,
                'imagen_url' => 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=800&h=800&fit=crop',
            ],
        ];

        foreach ($productos as $producto) {
            Producto::create($producto);
        }

        $this->command->info('✓ Creados ' . count($productos) . ' productos con imágenes de Unsplash');
    }
}
