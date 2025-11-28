<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mesa;
use App\Models\PageVisit;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MesaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $visitCount = PageVisit::incrementVisit('restaurant.mesas.index');

        $query = Mesa::query();

        // Búsqueda por código
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('codigo', 'like', "%{$search}%");
        }

        $mesas = $query->orderBy('codigo')->paginate(10);

        return Inertia::render('Restaurant/Mesas/Index', [
            'mesas' => $mesas,
            'filters' => $request->only(['search']),
            'visitCount' => $visitCount,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $visitCount = PageVisit::incrementVisit('restaurant.mesas.create');

        return Inertia::render('Restaurant/Mesas/Create', [
            'visitCount' => $visitCount,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'codigo' => 'required|string|max:255|unique:mesas',
            'capacidad' => 'required|integer|min:1',
        ], [
            'codigo.required' => 'El código es obligatorio.',
            'codigo.unique' => 'Ya existe una mesa con este código.',
            'codigo.max' => 'El código no puede exceder 255 caracteres.',
            'capacidad.required' => 'La capacidad es obligatoria.',
            'capacidad.integer' => 'La capacidad debe ser un número entero.',
            'capacidad.min' => 'La capacidad debe ser al menos 1.',
        ]);

        Mesa::create($validated);

        return redirect()->route('restaurant.mesas.index')
            ->with('success', 'Mesa creada exitosamente.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $mesa = Mesa::findOrFail($id);
        $visitCount = PageVisit::incrementVisit('restaurant.mesas.edit');

        return Inertia::render('Restaurant/Mesas/Edit', [
            'mesa' => $mesa,
            'visitCount' => $visitCount,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $mesa = Mesa::findOrFail($id);

        $validated = $request->validate([
            'codigo' => 'required|string|max:255|unique:mesas,codigo,' . $mesa->id,
            'capacidad' => 'required|integer|min:1',
        ], [
            'codigo.required' => 'El código es obligatorio.',
            'codigo.unique' => 'Ya existe una mesa con este código.',
            'codigo.max' => 'El código no puede exceder 255 caracteres.',
            'capacidad.required' => 'La capacidad es obligatoria.',
            'capacidad.integer' => 'La capacidad debe ser un número entero.',
            'capacidad.min' => 'La capacidad debe ser al menos 1.',
        ]);

        $mesa->update($validated);

        return redirect()->route('restaurant.mesas.index')
            ->with('success', 'Mesa actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage (soft delete).
     */
    public function destroy(string $id)
    {
        $mesa = Mesa::findOrFail($id);
        $mesa->delete();

        return redirect()->route('restaurant.mesas.index')
            ->with('success', 'Mesa archivada exitosamente.');
    }
}
