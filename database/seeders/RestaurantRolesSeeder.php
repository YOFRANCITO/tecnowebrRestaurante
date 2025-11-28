<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RestaurantRolesSeeder extends Seeder
{
    /**
     * Run the database seeders.
     */
    public function run(): void
    {
        // Crear permisos para el sistema de restaurante
        $permissions = [
            // Dashboard
            'dashboard.view',
            
            // Marcas
            'marcas.view',
            'marcas.create',
            'marcas.edit',
            'marcas.delete',
            
            // Insumos
            'insumos.view',
            'insumos.create',
            'insumos.edit',
            'insumos.delete',
            
            // Movimientos
            'movimientos.view',
            'movimientos.create',
            
            // Proveedores
            'proveedores.view',
            'proveedores.create',
            'proveedores.edit',
            'proveedores.delete',
            
            // Compras
            'compras.view',
            'compras.create',
            'compras.edit',
            'compras.delete',
            
            // Productos
            'productos.view',
            'productos.create',
            'productos.edit',
            'productos.delete',
            
            // Planes de Crédito
            'planes-credito.view',
            'planes-credito.create',
            'planes-credito.edit',
            'planes-credito.delete',
            
            // Ventas
            'ventas.view',
            'ventas.create',
            
            // Mesas
            'mesas.view',
            'mesas.create',
            'mesas.edit',
            'mesas.delete',
            
            // Órdenes (para meseros)
            'ordenes.view',
            'ordenes.update-estado',
            
            // Créditos (para clientes)
            'creditos.view',
            'creditos.plan-pagos',
            
            // Pagos (para clientes)
            'pagos.view',
            'pagos.create',
            
            // Usuarios (para administrador)
            'usuarios.view',
            'usuarios.create',
            'usuarios.edit',
            'usuarios.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Crear rol Administrador con todos los permisos
        $administrador = Role::firstOrCreate(['name' => 'administrador']);
        $administrador->syncPermissions(Permission::all());

        // Crear rol Almacenero (acceso a inventario y compras)
        $almacenero = Role::firstOrCreate(['name' => 'almacenero']);
        $almacenero->syncPermissions([
            'dashboard.view',
            'marcas.view', 'marcas.create', 'marcas.edit', 'marcas.delete',
            'insumos.view', 'insumos.create', 'insumos.edit', 'insumos.delete',
            'movimientos.view', 'movimientos.create',
            'proveedores.view', 'proveedores.create', 'proveedores.edit', 'proveedores.delete',
            'compras.view', 'compras.create', 'compras.edit', 'compras.delete',
        ]);

        // Crear rol Mesero (solo órdenes)
        $mesero = Role::firstOrCreate(['name' => 'mesero']);
        $mesero->syncPermissions([
            'ordenes.view',
            'ordenes.update-estado',
        ]);

        // Crear rol Cliente (productos, ventas, créditos y pagos)
        $cliente = Role::firstOrCreate(['name' => 'cliente']);
        $cliente->syncPermissions([
            'productos.view',
            'ventas.create',
            'creditos.view',
            'creditos.plan-pagos',
            'pagos.view',
            'pagos.create',
        ]);

        $this->command->info('✅ Roles y permisos del restaurante creados exitosamente');
    }
}
