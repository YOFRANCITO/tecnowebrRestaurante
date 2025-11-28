<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Venta;
use App\Models\PageVisit;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrdenController extends Controller
{
    /**
     * Display a listing of the resource (para meseros).
     * Muestra las órdenes con PENDIENTE primero
     */
    public function index(Request $request)
    {
        $visitCount = PageVisit::incrementVisit('restaurant.ordenes.index');

        $query = Venta::with(['user', 'mesa', 'detalles.producto']);

        // Ordenar por estado (PENDIENTE primero) y luego por fecha
        $query->orderByRaw("CASE WHEN estado = 'PENDIENTE' THEN 0 ELSE 1 END")
              ->orderBy('fecha_hora', 'desc');

        // Filtro por estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $ordenes = $query->paginate(10);

        return Inertia::render('Restaurant/Ordenes/Index', [
            'ordenes' => $ordenes,
            'filters' => $request->only(['estado']),
            'visitCount' => $visitCount,
        ]);
    }

    /**
     * Update the specified resource in storage.
     * Cambia el estado de PENDIENTE a ENTREGADO
     */
    public function update(Request $request, string $id)
    {
        $venta = Venta::findOrFail($id);

        // Validar que el estado actual sea PENDIENTE
        if ($venta->estado !== 'PENDIENTE') {
            return back()->withErrors(['error' => 'Solo se pueden marcar como entregadas las órdenes pendientes.']);
        }

        $venta->update(['estado' => 'ENTREGADO']);

        return redirect()->route('restaurant.ordenes.index')
            ->with('success', 'Orden marcada como entregada exitosamente.');
    }
}
