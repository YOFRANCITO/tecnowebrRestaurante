<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Insumo;
use App\Models\Marca;
use App\Models\PageVisit;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InsumoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Incrementar contador de visitas
        $visitCount = PageVisit::incrementVisit('restaurant.insumos.index');

        $query = Insumo::with('marca');

        // Filtro por marca
        if ($request->filled('marca_id')) {
            $query->where('marca_id', $request->marca_id);
        }

        // Búsqueda por nombre
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('nombre', 'like', "%{$search}%");
        }

        $insumos = $query->orderBy('nombre')->paginate(10);
        $marcas = Marca::orderBy('nombre')->get();

        return Inertia::render('Restaurant/Insumos/Index', [
            'insumos' => $insumos,
            'marcas' => $marcas,
            'filters' => $request->only(['marca_id', 'search']),
            'visitCount' => $visitCount,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $visitCount = PageVisit::incrementVisit('restaurant.insumos.create');
        $marcas = Marca::orderBy('nombre')->get();

        return Inertia::render('Restaurant/Insumos/Create', [
            'marcas' => $marcas,
            'visitCount' => $visitCount,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'stock' => 'required|numeric|min:0',
            'unidad_medida' => 'required|string|max:50',
            'marca_id' => 'nullable|exists:marcas,id',
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.max' => 'El nombre no puede exceder 255 caracteres.',
            'stock.required' => 'El stock es obligatorio.',
            'stock.numeric' => 'El stock debe ser un número.',
            'stock.min' => 'El stock no puede ser negativo.',
            'unidad_medida.required' => 'La unidad de medida es obligatoria.',
            'unidad_medida.max' => 'La unidad de medida no puede exceder 50 caracteres.',
            'marca_id.exists' => 'La marca seleccionada no existe.',
        ]);

        Insumo::create($validated);

        return redirect()->route('restaurant.insumos.index')
            ->with('success', 'Insumo creado exitosamente.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $insumo = Insumo::with('marca')->findOrFail($id);
        $visitCount = PageVisit::incrementVisit('restaurant.insumos.edit');
        $marcas = Marca::orderBy('nombre')->get();

        return Inertia::render('Restaurant/Insumos/Edit', [
            'insumo' => $insumo,
            'marcas' => $marcas,
            'visitCount' => $visitCount,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $insumo = Insumo::findOrFail($id);

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'stock' => 'required|numeric|min:0',
            'unidad_medida' => 'required|string|max:50',
            'marca_id' => 'nullable|exists:marcas,id',
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.max' => 'El nombre no puede exceder 255 caracteres.',
            'stock.required' => 'El stock es obligatorio.',
            'stock.numeric' => 'El stock debe ser un número.',
            'stock.min' => 'El stock no puede ser negativo.',
            'unidad_medida.required' => 'La unidad de medida es obligatoria.',
            'unidad_medida.max' => 'La unidad de medida no puede exceder 50 caracteres.',
            'marca_id.exists' => 'La marca seleccionada no existe.',
        ]);

        $insumo->update($validated);

        return redirect()->route('restaurant.insumos.index')
            ->with('success', 'Insumo actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     * Nota: Eliminación lógica (soft delete)
     */
    public function destroy(string $id)
    {
        $insumo = Insumo::findOrFail($id);
        $insumo->delete();

        return redirect()->route('restaurant.insumos.index')
            ->with('success', 'Insumo archivado exitosamente.');
    }
}
