<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Movimiento;
use App\Models\Insumo;
use App\Models\PageVisit;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class MovimientoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Incrementar contador de visitas
        $visitCount = PageVisit::incrementVisit('restaurant.movimientos.index');

        $query = Movimiento::with('insumo.marca')->recent();

        // Filtro por tipo
        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        // Filtro por insumo
        if ($request->filled('insumo_id')) {
            $query->where('insumo_id', $request->insumo_id);
        }

        // Filtro por rango de fechas
        if ($request->filled('fecha_desde')) {
            $query->whereDate('created_at', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('created_at', '<=', $request->fecha_hasta);
        }

        $movimientos = $query->paginate(15);
        $insumos = Insumo::orderBy('nombre')->get();

        return Inertia::render('Restaurant/Movimientos/Index', [
            'movimientos' => $movimientos,
            'insumos' => $insumos,
            'filters' => $request->only(['tipo', 'insumo_id', 'fecha_desde', 'fecha_hasta']),
            'visitCount' => $visitCount,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $visitCount = PageVisit::incrementVisit('restaurant.movimientos.create');
        $insumos = Insumo::with('marca')->orderBy('nombre')->get();

        return Inertia::render('Restaurant/Movimientos/Create', [
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
            'tipo' => 'required|in:Ingreso,Salida,Ajuste',
            'cantidad' => 'required|numeric|min:0.01',
            'motivo' => 'required|string|max:500',
            'insumo_id' => 'required|exists:insumos,id',
        ], [
            'tipo.required' => 'El tipo de movimiento es obligatorio.',
            'tipo.in' => 'El tipo de movimiento debe ser Ingreso, Salida o Ajuste.',
            'cantidad.required' => 'La cantidad es obligatoria.',
            'cantidad.numeric' => 'La cantidad debe ser un número.',
            'cantidad.min' => 'La cantidad debe ser mayor a 0.',
            'motivo.required' => 'El motivo es obligatorio.',
            'motivo.max' => 'El motivo no puede exceder 500 caracteres.',
            'insumo_id.required' => 'Debe seleccionar un insumo.',
            'insumo_id.exists' => 'El insumo seleccionado no existe.',
        ]);

        // Usar transacción para garantizar consistencia
        DB::transaction(function () use ($validated) {
            $insumo = Insumo::lockForUpdate()->findOrFail($validated['insumo_id']);
            $stockAnterior = $insumo->stock;
            $stockNuevo = 0;

            // Calcular nuevo stock según el tipo de movimiento
            switch ($validated['tipo']) {
                case 'Ingreso':
                    $stockNuevo = $stockAnterior + $validated['cantidad'];
                    break;
                
                case 'Salida':
                    // Validar que hay stock suficiente
                    if ($stockAnterior < $validated['cantidad']) {
                        throw new \Exception('Stock insuficiente. Stock actual: ' . $stockAnterior);
                    }
                    $stockNuevo = $stockAnterior - $validated['cantidad'];
                    break;
                
                case 'Ajuste':
                    // En ajuste, la cantidad ES el nuevo stock
                    $stockNuevo = $validated['cantidad'];
                    break;
            }

            // Crear el movimiento
            Movimiento::create([
                'tipo' => $validated['tipo'],
                'cantidad' => $validated['cantidad'],
                'motivo' => $validated['motivo'],
                'insumo_id' => $validated['insumo_id'],
                'stock_anterior' => $stockAnterior,
                'stock_nuevo' => $stockNuevo,
            ]);

            // Actualizar el stock del insumo
            $insumo->update(['stock' => $stockNuevo]);
        });

        return redirect()->route('restaurant.movimientos.index')
            ->with('success', 'Movimiento registrado exitosamente.');
    }

    /**
     * Remove the specified resource from storage (soft delete).
     */
    public function destroy(string $id)
    {
        $movimiento = Movimiento::findOrFail($id);
        $movimiento->delete();

        return redirect()->route('restaurant.movimientos.index')
            ->with('success', 'Movimiento archivado exitosamente.');
    }

    /**
     * Display archived movimientos.
     */
    public function archived(Request $request)
    {
        $visitCount = PageVisit::incrementVisit('restaurant.movimientos.archived');

        $movimientos = Movimiento::onlyTrashed()
            ->with('insumo.marca')
            ->recent()
            ->paginate(15);

        return Inertia::render('Restaurant/Movimientos/Archived', [
            'movimientos' => $movimientos,
            'visitCount' => $visitCount,
        ]);
    }

    /**
     * Restore an archived movimiento.
     */
    public function restore(string $id)
    {
        $movimiento = Movimiento::onlyTrashed()->findOrFail($id);
        $movimiento->restore();

        return redirect()->route('restaurant.movimientos.archived')
            ->with('success', 'Movimiento desarchivado exitosamente.');
    }
}
