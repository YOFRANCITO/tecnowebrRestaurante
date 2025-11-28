<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Marca;
use App\Models\PageVisit;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MarcaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Incrementar contador de visitas
        $visitCount = PageVisit::incrementVisit('restaurant.marcas.index');

        $query = Marca::query();

        // Búsqueda por nombre
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('nombre', 'like', "%{$search}%");
        }

        $marcas = $query->orderBy('nombre')->paginate(10);

        return Inertia::render('Restaurant/Marcas/Index', [
            'marcas' => $marcas,
            'filters' => $request->only(['search']),
            'visitCount' => $visitCount,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $visitCount = PageVisit::incrementVisit('restaurant.marcas.create');

        return Inertia::render('Restaurant/Marcas/Create', [
            'visitCount' => $visitCount,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:marcas',
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.unique' => 'Esta marca ya existe.',
            'nombre.max' => 'El nombre no puede exceder 255 caracteres.',
        ]);

        Marca::create($validated);

        return redirect()->route('restaurant.marcas.index')
            ->with('success', 'Marca creada exitosamente.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $marca = Marca::findOrFail($id);
        $visitCount = PageVisit::incrementVisit('restaurant.marcas.edit');

        return Inertia::render('Restaurant/Marcas/Edit', [
            'marca' => $marca,
            'visitCount' => $visitCount,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $marca = Marca::findOrFail($id);

        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:marcas,nombre,' . $marca->id,
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.unique' => 'Esta marca ya existe.',
            'nombre.max' => 'El nombre no puede exceder 255 caracteres.',
        ]);

        $marca->update($validated);

        return redirect()->route('restaurant.marcas.index')
            ->with('success', 'Marca actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     * Nota: Eliminación lógica (soft delete)
     */
    public function destroy(string $id)
    {
        $marca = Marca::findOrFail($id);
        $marca->delete();

        return redirect()->route('restaurant.marcas.index')
            ->with('success', 'Marca archivada exitosamente.');
    }
}
