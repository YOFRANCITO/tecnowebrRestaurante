<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Producto;
use App\Models\Venta;
use App\Models\Mesa;
use Illuminate\Http\Request;

class GlobalSearchController extends Controller
{
    /**
     * Búsqueda global en el sistema
     */
    public function search(Request $request)
    {
        try {
            $query = $request->input('q', '');
            
            // Validar longitud mínima
            if (strlen($query) < 2) {
                return response()->json(['results' => []]);
            }
            
            $user = $request->user();
            
            if (!$user) {
                return response()->json(['error' => 'Usuario no autenticado'], 401);
            }
            
            $results = [];
            
            // Buscar según rol del usuario
            if ($user->hasRole('administrador')) {
                $results = $this->searchForAdmin($query);
            } elseif ($user->hasRole('almacenero')) {
                $results = $this->searchForAlmacenero($query);
            } elseif ($user->hasRole('mesero')) {
                $results = $this->searchForMesero($query);
            } elseif ($user->hasRole('cliente')) {
                $results = $this->searchForCliente($query, $user->id);
            } else {
                // Usuario sin rol definido
                $results = [];
            }
            
            // Filtrar resultados nulos
            $results = array_filter($results, fn($r) => $r !== null);
            
            return response()->json(['results' => array_values($results)]);
            
        } catch (\Exception $e) {
            \Log::error('Error en búsqueda global: ' . $e->getMessage(), [
                'query' => $request->input('q'),
                'user_id' => $request->user()?->id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Error en la búsqueda',
                'message' => config('app.debug') ? $e->getMessage() : 'Error interno del servidor'
            ], 500);
        }
    }
    
    /**
     * Búsqueda para Administrador (acceso total)
     */
    private function searchForAdmin($query)
    {
        return [
            $this->searchNavigation($query, 'administrador'),
            $this->searchProducts($query),
            $this->searchUsers($query),
            $this->searchSales($query),
            $this->searchTables($query),
        ];
    }
    
    /**
     * Búsqueda para Almacenero
     */
    private function searchForAlmacenero($query)
    {
        return [
            $this->searchNavigation($query, 'almacenero'),
            $this->searchProducts($query),
        ];
    }
    
    /**
     * Búsqueda para Mesero
     */
    private function searchForMesero($query)
    {
        return [
            $this->searchNavigation($query, 'mesero'),
            $this->searchSales($query), // Puede ver ventas
        ];
    }
    
    /**
     * Búsqueda para Cliente (solo sus datos)
     */
    private function searchForCliente($query, $userId)
    {
        return [
            $this->searchNavigation($query, 'cliente'),
            $this->searchMySales($query, $userId),
        ];
    }
    
    /**
     * Buscar en opciones de navegación del menú
     */
    private function searchNavigation($query, $role)
    {
        $menuItems = $this->getMenuItemsByRole($role);
        
        $results = [];
        foreach ($menuItems as $item) {
            if (stripos($item['title'], $query) !== false || stripos($item['keywords'], $query) !== false) {
                $results[] = $item;
            }
        }
        
        if (empty($results)) return null;
        
        return [
            'type' => 'navegacion',
            'label' => 'Navegación',
            'icon' => '🧭',
            'items' => array_slice($results, 0, 5)
        ];
    }
    
    /**
     * Obtener items del menú según rol
     */
    private function getMenuItemsByRole($role)
    {
        $commonItems = [
            ['id' => 'profile', 'title' => 'Mi Perfil', 'subtitle' => 'Configuración de cuenta', 'url' => route('profile.show'), 'keywords' => 'perfil cuenta configuracion ajustes'],
        ];
        
        $menuByRole = [
            'administrador' => [
                ['id' => 'dashboard', 'title' => 'Dashboard', 'subtitle' => 'Reportes y estadísticas', 'url' => route('restaurant.reportes.dashboard'), 'keywords' => 'dashboard reportes estadisticas inicio'],
                ['id' => 'users', 'title' => 'Gestión de Usuarios', 'subtitle' => 'Administrar usuarios del sistema', 'url' => route('restaurant.users.index'), 'keywords' => 'usuarios empleados personal gestion administrar'],
                ['id' => 'users-create', 'title' => 'Crear Usuario', 'subtitle' => 'Agregar nuevo usuario', 'url' => route('restaurant.users.create'), 'keywords' => 'crear nuevo usuario agregar registrar'],
                ['id' => 'productos', 'title' => 'Gestión de Productos', 'subtitle' => 'Administrar productos', 'url' => route('restaurant.productos.index'), 'keywords' => 'productos items menu gestion administrar'],
                ['id' => 'productos-create', 'title' => 'Crear Producto', 'subtitle' => 'Agregar nuevo producto', 'url' => route('restaurant.productos.create'), 'keywords' => 'crear nuevo producto agregar item'],
                ['id' => 'mesas', 'title' => 'Gestión de Mesas', 'subtitle' => 'Administrar mesas', 'url' => route('restaurant.mesas.index'), 'keywords' => 'mesas tables gestion administrar'],
                ['id' => 'mesas-create', 'title' => 'Crear Mesa', 'subtitle' => 'Agregar nueva mesa', 'url' => route('restaurant.mesas.create'), 'keywords' => 'crear nueva mesa agregar table'],
                ['id' => 'ventas', 'title' => 'Gestión de Ventas', 'subtitle' => 'Ver todas las ventas', 'url' => route('restaurant.ventas.index'), 'keywords' => 'ventas pedidos ordenes gestion'],
                ['id' => 'ventas-create', 'title' => 'Nueva Venta', 'subtitle' => 'Registrar nueva venta', 'url' => route('restaurant.ventas.create'), 'keywords' => 'crear nueva venta pedido orden'],
                ['id' => 'creditos', 'title' => 'Gestión de Créditos', 'subtitle' => 'Administrar créditos', 'url' => route('restaurant.creditos.index'), 'keywords' => 'creditos prestamos financiamiento'],
                ['id' => 'pagos', 'title' => 'Gestión de Pagos', 'subtitle' => 'Ver pagos realizados', 'url' => route('restaurant.pagos.index'), 'keywords' => 'pagos abonos cuotas'],
                ['id' => 'planes', 'title' => 'Planes de Pago', 'subtitle' => 'Administrar planes', 'url' => route('restaurant.planes.index'), 'keywords' => 'planes cuotas financiamiento'],
            ],
            'almacenero' => [
                ['id' => 'dashboard', 'title' => 'Dashboard', 'subtitle' => 'Reportes y estadísticas', 'url' => route('restaurant.reportes.dashboard'), 'keywords' => 'dashboard reportes estadisticas inicio'],
                ['id' => 'productos', 'title' => 'Gestión de Productos', 'subtitle' => 'Administrar productos', 'url' => route('restaurant.productos.index'), 'keywords' => 'productos items menu inventario stock'],
                ['id' => 'productos-create', 'title' => 'Crear Producto', 'subtitle' => 'Agregar nuevo producto', 'url' => route('restaurant.productos.create'), 'keywords' => 'crear nuevo producto agregar'],
            ],
            'mesero' => [
                ['id' => 'ventas', 'title' => 'Gestión de Ventas', 'subtitle' => 'Ver ventas', 'url' => route('restaurant.ventas.index'), 'keywords' => 'ventas pedidos ordenes'],
                ['id' => 'ventas-create', 'title' => 'Nueva Venta', 'subtitle' => 'Registrar venta', 'url' => route('restaurant.ventas.create'), 'keywords' => 'crear nueva venta pedido orden'],
                ['id' => 'mesas', 'title' => 'Ver Mesas', 'subtitle' => 'Estado de mesas', 'url' => route('restaurant.mesas.index'), 'keywords' => 'mesas tables estado'],
            ],
            'cliente' => [
                ['id' => 'ventas', 'title' => 'Mis Pedidos', 'subtitle' => 'Ver mis compras', 'url' => route('restaurant.ventas.index'), 'keywords' => 'pedidos compras ordenes mis'],
                ['id' => 'ventas-create', 'title' => 'Nuevo Pedido', 'subtitle' => 'Realizar compra', 'url' => route('restaurant.ventas.create'), 'keywords' => 'comprar pedir nuevo pedido orden'],
                ['id' => 'creditos', 'title' => 'Mis Créditos', 'subtitle' => 'Ver mis créditos', 'url' => route('restaurant.creditos.index'), 'keywords' => 'creditos prestamos financiamiento mis'],
                ['id' => 'pagos', 'title' => 'Mis Pagos', 'subtitle' => 'Ver mis pagos', 'url' => route('restaurant.pagos.index'), 'keywords' => 'pagos abonos cuotas mis'],
            ],
        ];
        
        return array_merge($commonItems, $menuByRole[$role] ?? []);
    }
    
    /**
     * Buscar Usuarios
     */
    private function searchUsers($query)
    {
        try {
            $users = User::where('name', 'like', "%{$query}%")
                ->orWhere('email', 'like', "%{$query}%")
                ->limit(5)
                ->get();
            
            if ($users->isEmpty()) return null;
            
            return [
                'type' => 'usuarios',
                'label' => 'Usuarios',
                'icon' => '👥',
                'items' => $users->map(fn($u) => [
                    'id' => $u->id,
                    'title' => $u->name,
                    'subtitle' => $u->email,
                    'url' => route('restaurant.users.edit', $u->id),
                ])
            ];
        } catch (\Exception $e) {
            \Log::error('Error en searchUsers: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Buscar Productos
     */
    private function searchProducts($query, $readOnly = false)
    {
        try {
            // Búsqueda insensible a acentos usando ILIKE y unaccent si está disponible
            $products = Producto::whereRaw("unaccent(nombre) ILIKE unaccent(?)", ["%{$query}%"])
                ->limit(5)
                ->get();
            
            // Si no hay resultados con unaccent, intentar búsqueda normal
            if ($products->isEmpty()) {
                $products = Producto::where('nombre', 'ILIKE', "%{$query}%")
                    ->limit(5)
                    ->get();
            }
            
            if ($products->isEmpty()) return null;
            
            return [
                'type' => 'productos',
                'label' => 'Productos',
                'icon' => '📦',
                'items' => $products->map(fn($p) => [
                    'id' => $p->id,
                    'title' => $p->nombre,
                    'subtitle' => $readOnly 
                        ? "Precio: \$" . number_format($p->precio_venta, 2) . " • Ver catálogo"
                        : "Stock: {$p->stock} • Precio: \$" . number_format($p->precio_venta, 2),
                    'url' => route('restaurant.productos.index') . ($readOnly ? '' : "?highlight={$p->id}"),
                ])
            ];
        } catch (\Exception $e) {
            \Log::error('Error en searchProducts: ' . $e->getMessage());
            
            // Fallback: búsqueda simple si unaccent no está disponible
            try {
                $products = Producto::where('nombre', 'ILIKE', "%{$query}%")
                    ->limit(5)
                    ->get();
                
                if ($products->isEmpty()) return null;
                
                return [
                    'type' => 'productos',
                    'label' => 'Productos',
                    'icon' => '📦',
                    'items' => $products->map(fn($p) => [
                        'id' => $p->id,
                        'title' => $p->nombre,
                        'subtitle' => $readOnly 
                            ? "Precio: \$" . number_format($p->precio_venta, 2) . " • Ver catálogo"
                            : "Stock: {$p->stock} • Precio: \$" . number_format($p->precio_venta, 2),
                        'url' => route('restaurant.productos.index') . ($readOnly ? '' : "?highlight={$p->id}"),
                    ])
                ];
            } catch (\Exception $e2) {
                \Log::error('Error en searchProducts fallback: ' . $e2->getMessage());
                return null;
            }
        }
    }
    
    /**
     * Buscar Ventas
     */
    private function searchSales($query)
    {
        try {
            // Buscar por ID numérico
            $sales = Venta::where('id', 'like', "%{$query}%")
                ->limit(5)
                ->get();
            
            if ($sales->isEmpty()) return null;
            
            return [
                'type' => 'ventas',
                'label' => 'Ventas',
                'icon' => '💰',
                'items' => $sales->map(fn($v) => [
                    'id' => $v->id,
                    'title' => "Venta #{$v->id}",
                    'subtitle' => "Total: \$" . number_format($v->total, 2),
                    'url' => route('restaurant.ventas.show', $v->id),
                ])
            ];
        } catch (\Exception $e) {
            \Log::error('Error en searchSales: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Buscar Mesas
     */
    private function searchTables($query)
    {
        try {
            // Buscar por código
            $tables = Mesa::where('codigo', 'like', "%{$query}%")
                ->limit(5)
                ->get();
            
            if ($tables->isEmpty()) return null;
            
            return [
                'type' => 'mesas',
                'label' => 'Mesas',
                'icon' => '🪑',
                'items' => $tables->map(fn($m) => [
                    'id' => $m->id,
                    'title' => "Mesa {$m->codigo}",
                    'subtitle' => "Capacidad: {$m->capacidad} personas",
                    'url' => route('restaurant.mesas.edit', $m->id),
                ])
            ];
        } catch (\Exception $e) {
            \Log::error('Error en searchTables: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Buscar Mis Ventas (Cliente)
     */
    private function searchMySales($query, $userId)
    {
        try {
            $sales = Venta::where('user_id', $userId)
                ->where('id', 'like', "%{$query}%")
                ->limit(5)
                ->get();
            
            if ($sales->isEmpty()) return null;
            
            return [
                'type' => 'mis-ventas',
                'label' => 'Mis Pedidos',
                'icon' => '🛍️',
                'items' => $sales->map(fn($v) => [
                    'id' => $v->id,
                    'title' => "Pedido #{$v->id}",
                    'subtitle' => "Total: \$" . number_format($v->total, 2),
                    'url' => route('restaurant.ventas.show', $v->id),
                ])
            ];
        } catch (\Exception $e) {
            \Log::error('Error en searchMySales: ' . $e->getMessage());
            return null;
        }
    }
}
