<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Redirect;

// =====================================================
// CONTROLADORES DEL SISTEMA DE RESTAURANTE
// =====================================================

use App\Http\Controllers\Admin\RestaurantUserController;
use App\Http\Controllers\Admin\MarcaController;
use App\Http\Controllers\Admin\InsumoController;
use App\Http\Controllers\Admin\MovimientoController;
use App\Http\Controllers\Admin\ProveedorController;
use App\Http\Controllers\Admin\CompraController;
use App\Http\Controllers\Admin\ProductoController;
use App\Http\Controllers\Admin\MesaController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Admin\VentaController;
use App\Http\Controllers\Admin\OrdenController;
use App\Http\Controllers\Admin\CreditoController;
use App\Http\Controllers\Admin\PagoController;
use App\Http\Controllers\Admin\ReporteController;
use App\Http\Controllers\GlobalSearchController;

// =====================================================
// RUTA RAÍZ - REDIRIGE AL LOGIN
// =====================================================

Route::get('/', function () {
    return Redirect::route('login');
});

// =====================================================
// API DE BÚSQUEDA GLOBAL
// =====================================================

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified', 'throttle:60,1'])
    ->get('/api/global-search', [GlobalSearchController::class, 'search'])
    ->name('global.search');

// =====================================================
// RUTAS DEL SISTEMA DE RESTAURANTE
// =====================================================

// Rutas de gestión de usuarios del restaurante (solo para administradores)
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified', 'restaurant.role:administrador'])
    ->prefix('restaurant')
    ->name('restaurant.')
    ->group(function () {
        Route::resource('users', RestaurantUserController::class);
        Route::resource('mesas', MesaController::class)->except(['show']);
        Route::resource('planes', PlanController::class)->except(['show']);
        
        // Rutas de reportes
        Route::get('reportes/dashboard', [ReporteController::class, 'dashboard'])->name('reportes.dashboard')->withoutMiddleware('restaurant.role:administrador')->middleware('restaurant.role:administrador,almacenero');
        Route::get('reportes/ventas', [ReporteController::class, 'ventas'])->name('reportes.ventas');
        Route::get('reportes/compras', [ReporteController::class, 'compras'])->name('reportes.compras');
        Route::get('reportes/creditos', [ReporteController::class, 'creditos'])->name('reportes.creditos');
        
        // Rutas de exportación
        Route::get('reportes/ventas/export/excel', [ReporteController::class, 'exportVentasExcel'])->name('reportes.ventas.export.excel');
        Route::get('reportes/ventas/export/pdf', [ReporteController::class, 'exportVentasPdf'])->name('reportes.ventas.export.pdf');
        Route::get('reportes/compras/export/excel', [ReporteController::class, 'exportComprasExcel'])->name('reportes.compras.export.excel');
        Route::get('reportes/compras/export/pdf', [ReporteController::class, 'exportComprasPdf'])->name('reportes.compras.export.pdf');
        Route::get('reportes/creditos/export/excel', [ReporteController::class, 'exportCreditosExcel'])->name('reportes.creditos.export.excel');
        Route::get('reportes/creditos/export/pdf', [ReporteController::class, 'exportCreditosPdf'])->name('reportes.creditos.export.pdf');
    });

// Rutas de gestión de inventario (administrador y almacenero)
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified', 'restaurant.role:administrador,almacenero'])
    ->prefix('restaurant')
    ->name('restaurant.')
    ->group(function () {
        Route::resource('marcas', MarcaController::class)->except(['show']);
        Route::resource('insumos', InsumoController::class)->except(['show']);
        
        // Movimientos - rutas especiales primero
        Route::get('movimientos/archived', [MovimientoController::class, 'archived'])->name('movimientos.archived');
        Route::post('movimientos/{id}/restore', [MovimientoController::class, 'restore'])->name('movimientos.restore');
        Route::resource('movimientos', MovimientoController::class)->only(['index', 'create', 'store', 'destroy']);
        
        // Proveedores y Compras
        Route::resource('proveedores', ProveedorController::class)->except(['show']);
        Route::resource('compras', CompraController::class)->except(['show']);
        
        // Productos
        Route::resource('productos', ProductoController::class)->except(['show']);
    });

// Rutas de ventas (administrador y cliente)
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified', 'restaurant.role:administrador,cliente'])
    ->prefix('restaurant')
    ->name('restaurant.')
    ->group(function () {
        Route::get('ventas', [VentaController::class, 'index'])->name('ventas.index');
        Route::get('ventas/create', [VentaController::class, 'create'])->name('ventas.create');
        Route::post('ventas', [VentaController::class, 'store'])->name('ventas.store');
    });

// Rutas de órdenes (solo mesero)
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified', 'restaurant.role:mesero'])
    ->prefix('restaurant')
    ->name('restaurant.')
    ->group(function () {
        Route::get('ordenes', [OrdenController::class, 'index'])->name('ordenes.index');
        Route::put('ordenes/{id}', [OrdenController::class, 'update'])->name('ordenes.update');
    });

// Rutas de créditos (administrador y cliente)
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified', 'restaurant.role:administrador,cliente'])
    ->prefix('restaurant')
    ->name('restaurant.')
    ->group(function () {
        Route::get('creditos', [CreditoController::class, 'index'])->name('creditos.index');
        Route::get('creditos/{id}', [CreditoController::class, 'show'])->name('creditos.show');
    });

// Rutas de pagos (administrador y cliente)
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified', 'restaurant.role:administrador,cliente'])
    ->prefix('restaurant')
    ->name('restaurant.')
    ->group(function () {
        Route::get('pagos', [PagoController::class, 'index'])->name('pagos.index');
    });

// Rutas de creación de pagos (solo cliente)
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified', 'restaurant.role:cliente'])
    ->prefix('restaurant')
    ->name('restaurant.')
    ->group(function () {
        Route::get('pagos/create', [PagoController::class, 'create'])->name('pagos.create');
        Route::post('pagos', [PagoController::class, 'store'])->name('pagos.store');
    });
