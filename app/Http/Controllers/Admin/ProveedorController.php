<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Proveedor;
use App\Models\PageVisit;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProveedorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $visitCount = PageVisit::incrementVisit('restaurant.proveedores.index');

        $query = Proveedor::query();

        // Búsqueda por nombre
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('nombre', 'like', "%{$search}%");
        }

        $proveedores = $query->orderBy('nombre')->paginate(10);

        return Inertia::render('Restaurant/Proveedores/Index', [
            'proveedores' => $proveedores,
            'filters' => $request->only(['search']),
            'visitCount' => $visitCount,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $visitCount = PageVisit::incrementVisit('restaurant.proveedores.create');

        return Inertia::render('Restaurant/Proveedores/Create', [
            'visitCount' => $visitCount,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:proveedores',
            'detalles' => 'nullable|string|max:1000',
            'correo' => 'nullable|email|max:255',
            'celular' => 'nullable|string|max:20',
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.unique' => 'Ya existe un proveedor con este nombre.',
            'nombre.max' => 'El nombre no puede exceder 255 caracteres.',
            'detalles.max' => 'Los detalles no pueden exceder 1000 caracteres.',
            'correo.email' => 'El correo debe ser una dirección válida.',
            'correo.max' => 'El correo no puede exceder 255 caracteres.',
            'celular.max' => 'El celular no puede exceder 20 caracteres.',
        ]);

        Proveedor::create($validated);

        return redirect()->route('restaurant.proveedores.index')
            ->with('success', 'Proveedor creado exitosamente.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $visitCount = PageVisit::incrementVisit('restaurant.proveedores.edit');
        $proveedor = Proveedor::findOrFail($id);

        return Inertia::render('Restaurant/Proveedores/Edit', [
            'proveedor' => $proveedor,
            'visitCount' => $visitCount,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $proveedor = Proveedor::findOrFail($id);

        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:proveedores,nombre,' . $proveedor->id,
            'detalles' => 'nullable|string|max:1000',
            'correo' => 'nullable|email|max:255',
            'celular' => 'nullable|string|max:20',
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.unique' => 'Ya existe un proveedor con este nombre.',
            'nombre.max' => 'El nombre no puede exceder 255 caracteres.',
            'detalles.max' => 'Los detalles no pueden exceder 1000 caracteres.',
            'correo.email' => 'El correo debe ser una dirección válida.',
            'correo.max' => 'El correo no puede exceder 255 caracteres.',
            'celular.max' => 'El celular no puede exceder 20 caracteres.',
        ]);

        $proveedor->update($validated);

        return redirect()->route('restaurant.proveedores.index')
            ->with('success', 'Proveedor actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage (soft delete).
     */
    public function destroy(string $id)
    {
        $proveedor = Proveedor::findOrFail($id);
        $proveedor->delete();

        return redirect()->route('restaurant.proveedores.index')
            ->with('success', 'Proveedor archivado exitosamente.');
    }
}
