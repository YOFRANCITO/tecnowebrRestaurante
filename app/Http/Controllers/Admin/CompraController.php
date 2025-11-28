<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Compra;
use App\Models\DetalleCompra;
use App\Models\Proveedor;
use App\Models\Insumo;
use App\Models\Movimiento;
use App\Models\PageVisit;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CompraController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $visitCount = PageVisit::incrementVisit('restaurant.compras.index');

        $query = Compra::with(['proveedor', 'user', 'detalles.insumo.marca'])->recent();

        // Filtro por proveedor
        if ($request->filled('proveedor_id')) {
            $query->where('proveedor_id', $request->proveedor_id);
        }

        // Filtro por rango de fechas
        if ($request->filled('fecha_desde')) {
            $query->whereDate('created_at', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('created_at', '<=', $request->fecha_hasta);
        }

        $compras = $query->paginate(15);
        $proveedores = Proveedor::orderBy('nombre')->get();

        return Inertia::render('Restaurant/Compras/Index', [
            'compras' => $compras,
            'proveedores' => $proveedores,
            'filters' => $request->only(['proveedor_id', 'fecha_desde', 'fecha_hasta']),
            'visitCount' => $visitCount,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $visitCount = PageVisit::incrementVisit('restaurant.compras.create');
        $proveedores = Proveedor::orderBy('nombre')->get();
        $insumos = Insumo::with('marca')->orderBy('nombre')->get();

        return Inertia::render('Restaurant/Compras/Create', [
            'proveedores' => $proveedores,
            'insumos' => $insumos,
            'visitCount' => $visitCount,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'proveedor_id' => 'nullable|exists:proveedores,id',
            'detalles' => 'required|array|min:1',
            'detalles.*.insumo_id' => 'required|exists:insumos,id',
            'detalles.*.cantidad' => 'required|numeric|min:0.01',
            'detalles.*.costo_unitario' => 'required|numeric|min:0',
        ], [
            'proveedor_id.exists' => 'El proveedor seleccionado no existe.',
            'detalles.required' => 'Debe agregar al menos un detalle de compra.',
            'detalles.min' => 'Debe agregar al menos un detalle de compra.',
            'detalles.*.insumo_id.required' => 'Debe seleccionar un insumo.',
            'detalles.*.insumo_id.exists' => 'El insumo seleccionado no existe.',
            'detalles.*.cantidad.required' => 'La cantidad es obligatoria.',
            'detalles.*.cantidad.numeric' => 'La cantidad debe ser un número.',
            'detalles.*.cantidad.min' => 'La cantidad debe ser mayor a 0.',
            'detalles.*.costo_unitario.required' => 'El costo unitario es obligatorio.',
            'detalles.*.costo_unitario.numeric' => 'El costo unitario debe ser un número.',
            'detalles.*.costo_unitario.min' => 'El costo unitario debe ser mayor o igual a 0.',
        ]);

        DB::transaction(function () use ($validated) {
            // Calcular total
            $total = 0;
            foreach ($validated['detalles'] as $detalle) {
                $total += $detalle['cantidad'] * $detalle['costo_unitario'];
            }

            // Crear compra
            $compra = Compra::create([
                'total' => $total,
                'proveedor_id' => $validated['proveedor_id'] ?? null,
                'user_id' => Auth::id(),
            ]);

            // Crear detalles y movimientos
            foreach ($validated['detalles'] as $detalleData) {
                $insumo = Insumo::lockForUpdate()->findOrFail($detalleData['insumo_id']);
                $stockAnterior = $insumo->stock;
                $stockNuevo = $stockAnterior + $detalleData['cantidad'];

                // Crear movimiento de Ingreso
                $movimiento = Movimiento::create([
                    'tipo' => 'Ingreso',
                    'cantidad' => $detalleData['cantidad'],
                    'motivo' => 'Compra #' . $compra->id . ' - ' . ($compra->proveedor ? $compra->proveedor->nombre : 'Sin proveedor'),
                    'insumo_id' => $detalleData['insumo_id'],
                    'stock_anterior' => $stockAnterior,
                    'stock_nuevo' => $stockNuevo,
                ]);

                // Actualizar stock del insumo
                $insumo->update(['stock' => $stockNuevo]);

                // Crear detalle de compra
                DetalleCompra::create([
                    'compra_id' => $compra->id,
                    'insumo_id' => $detalleData['insumo_id'],
                    'cantidad' => $detalleData['cantidad'],
                    'costo_unitario' => $detalleData['costo_unitario'],
                    'movimiento_id' => $movimiento->id,
                ]);
            }
        });

        return redirect()->route('restaurant.compras.index')
            ->with('success', 'Compra registrada exitosamente.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $visitCount = PageVisit::incrementVisit('restaurant.compras.edit');
        $compra = Compra::with(['proveedor', 'detalles.insumo.marca'])->findOrFail($id);
        $proveedores = Proveedor::orderBy('nombre')->get();
        $insumos = Insumo::with('marca')->orderBy('nombre')->get();

        return Inertia::render('Restaurant/Compras/Edit', [
            'compra' => $compra,
            'proveedores' => $proveedores,
            'insumos' => $insumos,
            'visitCount' => $visitCount,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $compra = Compra::with('detalles')->findOrFail($id);

        $validated = $request->validate([
            'proveedor_id' => 'nullable|exists:proveedores,id',
            'detalles' => 'required|array|min:1',
            'detalles.*.id' => 'nullable|exists:detalle_compra,id',
            'detalles.*.insumo_id' => 'required|exists:insumos,id',
            'detalles.*.cantidad' => 'required|numeric|min:0.01',
            'detalles.*.costo_unitario' => 'required|numeric|min:0',
        ], [
            'proveedor_id.exists' => 'El proveedor seleccionado no existe.',
            'detalles.required' => 'Debe agregar al menos un detalle de compra.',
            'detalles.min' => 'Debe agregar al menos un detalle de compra.',
            'detalles.*.insumo_id.required' => 'Debe seleccionar un insumo.',
            'detalles.*.insumo_id.exists' => 'El insumo seleccionado no existe.',
            'detalles.*.cantidad.required' => 'La cantidad es obligatoria.',
            'detalles.*.cantidad.numeric' => 'La cantidad debe ser un número.',
            'detalles.*.cantidad.min' => 'La cantidad debe ser mayor a 0.',
            'detalles.*.costo_unitario.required' => 'El costo unitario es obligatorio.',
            'detalles.*.costo_unitario.numeric' => 'El costo unitario debe ser un número.',
            'detalles.*.costo_unitario.min' => 'El costo unitario debe ser mayor o igual a 0.',
        ]);

        DB::transaction(function () use ($compra, $validated) {
            $detallesActuales = $compra->detalles->keyBy('id');
            $detallesNuevosIds = collect($validated['detalles'])->pluck('id')->filter();

            // Procesar detalles eliminados
            foreach ($detallesActuales as $detalleActual) {
                if (!$detallesNuevosIds->contains($detalleActual->id)) {
                    // Detalle eliminado - revertir stock
                    $insumo = Insumo::lockForUpdate()->findOrFail($detalleActual->insumo_id);
                    $stockAnterior = $insumo->stock;
                    $cantidadARestar = $detalleActual->cantidad;

                    if ($stockAnterior < $cantidadARestar) {
                        throw new \Exception("No se puede eliminar el detalle del insumo '{$insumo->nombre}'. Stock insuficiente (actual: {$stockAnterior}, necesario: {$cantidadARestar}).");
                    }

                    $stockNuevo = $stockAnterior - $cantidadARestar;

                    // Crear movimiento de Ajuste
                    Movimiento::create([
                        'tipo' => 'Ajuste',
                        'cantidad' => $stockNuevo,
                        'motivo' => 'Eliminación de detalle de Compra #' . $compra->id,
                        'insumo_id' => $detalleActual->insumo_id,
                        'stock_anterior' => $stockAnterior,
                        'stock_nuevo' => $stockNuevo,
                    ]);

                    $insumo->update(['stock' => $stockNuevo]);
                    $detalleActual->delete();
                }
            }

            // Calcular nuevo total
            $total = 0;

            // Procesar detalles nuevos y modificados
            foreach ($validated['detalles'] as $detalleData) {
                $total += $detalleData['cantidad'] * $detalleData['costo_unitario'];

                if (isset($detalleData['id'])) {
                    // Detalle existente - verificar si cambió la cantidad
                    $detalleActual = $detallesActuales->get($detalleData['id']);
                    $cantidadAnterior = $detalleActual->cantidad;
                    $cantidadNueva = $detalleData['cantidad'];

                    if ($cantidadAnterior != $cantidadNueva) {
                        $insumo = Insumo::lockForUpdate()->findOrFail($detalleData['insumo_id']);
                        $stockAnterior = $insumo->stock;
                        $diferencia = $cantidadNueva - $cantidadAnterior;

                        if ($diferencia < 0 && $stockAnterior < abs($diferencia)) {
                            throw new \Exception("No se puede reducir la cantidad del insumo '{$insumo->nombre}'. Stock insuficiente (actual: {$stockAnterior}, necesario: " . abs($diferencia) . ").");
                        }

                        $stockNuevo = $stockAnterior + $diferencia;

                        // Crear movimiento de Ajuste
                        Movimiento::create([
                            'tipo' => 'Ajuste',
                            'cantidad' => $stockNuevo,
                            'motivo' => 'Modificación de Compra #' . $compra->id . ' (cantidad: ' . $cantidadAnterior . ' → ' . $cantidadNueva . ')',
                            'insumo_id' => $detalleData['insumo_id'],
                            'stock_anterior' => $stockAnterior,
                            'stock_nuevo' => $stockNuevo,
                        ]);

                        $insumo->update(['stock' => $stockNuevo]);
                    }

                    // Actualizar detalle
                    $detalleActual->update([
                        'cantidad' => $detalleData['cantidad'],
                        'costo_unitario' => $detalleData['costo_unitario'],
                    ]);
                } else {
                    // Detalle nuevo - crear movimiento de Ingreso
                    $insumo = Insumo::lockForUpdate()->findOrFail($detalleData['insumo_id']);
                    $stockAnterior = $insumo->stock;
                    $stockNuevo = $stockAnterior + $detalleData['cantidad'];

                    $movimiento = Movimiento::create([
                        'tipo' => 'Ingreso',
                        'cantidad' => $detalleData['cantidad'],
                        'motivo' => 'Adición a Compra #' . $compra->id,
                        'insumo_id' => $detalleData['insumo_id'],
                        'stock_anterior' => $stockAnterior,
                        'stock_nuevo' => $stockNuevo,
                    ]);

                    $insumo->update(['stock' => $stockNuevo]);

                    DetalleCompra::create([
                        'compra_id' => $compra->id,
                        'insumo_id' => $detalleData['insumo_id'],
                        'cantidad' => $detalleData['cantidad'],
                        'costo_unitario' => $detalleData['costo_unitario'],
                        'movimiento_id' => $movimiento->id,
                    ]);
                }
            }

            // Actualizar compra
            $compra->update([
                'total' => $total,
                'proveedor_id' => $validated['proveedor_id'] ?? null,
            ]);
        });

        return redirect()->route('restaurant.compras.index')
            ->with('success', 'Compra actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage (soft delete).
     */
    public function destroy(string $id)
    {
        $compra = Compra::with('detalles')->findOrFail($id);

        DB::transaction(function () use ($compra) {
            // Validar que se puede revertir cada detalle
            foreach ($compra->detalles as $detalle) {
                $insumo = Insumo::findOrFail($detalle->insumo_id);
                if ($insumo->stock < $detalle->cantidad) {
                    throw new \Exception("No se puede eliminar la compra. Stock insuficiente del insumo '{$insumo->nombre}' (actual: {$insumo->stock}, necesario: {$detalle->cantidad}).");
                }
            }

            // Revertir cada detalle
            foreach ($compra->detalles as $detalle) {
                $insumo = Insumo::lockForUpdate()->findOrFail($detalle->insumo_id);
                $stockAnterior = $insumo->stock;
                $stockNuevo = $stockAnterior - $detalle->cantidad;

                // Crear movimiento de Ajuste
                Movimiento::create([
                    'tipo' => 'Ajuste',
                    'cantidad' => $stockNuevo,
                    'motivo' => 'Eliminación de Compra #' . $compra->id,
                    'insumo_id' => $detalle->insumo_id,
                    'stock_anterior' => $stockAnterior,
                    'stock_nuevo' => $stockNuevo,
                ]);

                $insumo->update(['stock' => $stockNuevo]);
            }

            // Archivar compra
            $compra->delete();
        });

        return redirect()->route('restaurant.compras.index')
            ->with('success', 'Compra eliminada exitosamente.');
    }
}
