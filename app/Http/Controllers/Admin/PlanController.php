<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\PageVisit;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PlanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $visitCount = PageVisit::incrementVisit('restaurant.planes.index');

        $query = Plan::query();

        // Búsqueda por nombre
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('nombre', 'like', "%{$search}%");
        }

        $planes = $query->orderBy('nombre')->paginate(10);

        return Inertia::render('Restaurant/Planes/Index', [
            'planes' => $planes,
            'filters' => $request->only(['search']),
            'visitCount' => $visitCount,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $visitCount = PageVisit::incrementVisit('restaurant.planes.create');

        return Inertia::render('Restaurant/Planes/Create', [
            'visitCount' => $visitCount,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:planes',
            'tasa_interes_diario' => 'required|numeric|min:0.01',
            'plazo_dias' => 'required|integer|min:1',
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.unique' => 'Ya existe un plan con este nombre.',
            'nombre.max' => 'El nombre no puede exceder 255 caracteres.',
            'tasa_interes_diario.required' => 'La tasa de interés diario es obligatoria.',
            'tasa_interes_diario.numeric' => 'La tasa de interés debe ser un número.',
            'tasa_interes_diario.min' => 'La tasa de interés debe ser mayor a 0.',
            'plazo_dias.required' => 'El plazo en días es obligatorio.',
            'plazo_dias.integer' => 'El plazo debe ser un número entero.',
            'plazo_dias.min' => 'El plazo debe ser al menos 1 día.',
        ]);

        Plan::create($validated);

        return redirect()->route('restaurant.planes.index')
            ->with('success', 'Plan creado exitosamente.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $plan = Plan::findOrFail($id);
        $visitCount = PageVisit::incrementVisit('restaurant.planes.edit');

        return Inertia::render('Restaurant/Planes/Edit', [
            'plan' => $plan,
            'visitCount' => $visitCount,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $plan = Plan::findOrFail($id);

        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:planes,nombre,' . $plan->id,
            'tasa_interes_diario' => 'required|numeric|min:0.01',
            'plazo_dias' => 'required|integer|min:1',
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.unique' => 'Ya existe un plan con este nombre.',
            'nombre.max' => 'El nombre no puede exceder 255 caracteres.',
            'tasa_interes_diario.required' => 'La tasa de interés diario es obligatoria.',
            'tasa_interes_diario.numeric' => 'La tasa de interés debe ser un número.',
            'tasa_interes_diario.min' => 'La tasa de interés debe ser mayor a 0.',
            'plazo_dias.required' => 'El plazo en días es obligatorio.',
            'plazo_dias.integer' => 'El plazo debe ser un número entero.',
            'plazo_dias.min' => 'El plazo debe ser al menos 1 día.',
        ]);

        $plan->update($validated);

        return redirect()->route('restaurant.planes.index')
            ->with('success', 'Plan actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage (soft delete).
     */
    public function destroy(string $id)
    {
        $plan = Plan::findOrFail($id);
        $plan->delete();

        return redirect()->route('restaurant.planes.index')
            ->with('success', 'Plan archivado exitosamente.');
    }
}
