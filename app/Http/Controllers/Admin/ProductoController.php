<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use App\Models\PageVisit;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Cloudinary\Cloudinary;

class ProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $visitCount = PageVisit::incrementVisit('restaurant.productos.index');

        $query = Producto::query();

        // Búsqueda por nombre
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('nombre', 'like', "%{$search}%");
        }

        $productos = $query->orderBy('nombre')->paginate(10);

        return Inertia::render('Restaurant/Productos/Index', [
            'productos' => $productos,
            'filters' => $request->only(['search']),
            'visitCount' => $visitCount,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $visitCount = PageVisit::incrementVisit('restaurant.productos.create');

        return Inertia::render('Restaurant/Productos/Create', [
            'visitCount' => $visitCount,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:productos',
            'costo' => 'required|numeric|min:0',
            'precio_venta' => 'required|numeric|min:0|gt:costo',
            'stock' => 'required|numeric|min:0',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.unique' => 'Ya existe un producto con este nombre.',
            'nombre.max' => 'El nombre no puede exceder 255 caracteres.',
            'costo.required' => 'El costo es obligatorio.',
            'costo.numeric' => 'El costo debe ser un número.',
            'costo.min' => 'El costo no puede ser negativo.',
            'precio_venta.required' => 'El precio de venta es obligatorio.',
            'precio_venta.numeric' => 'El precio de venta debe ser un número.',
            'precio_venta.min' => 'El precio de venta no puede ser negativo.',
            'precio_venta.gt' => 'El precio de venta debe ser mayor que el costo.',
            'stock.required' => 'El stock es obligatorio.',
            'stock.numeric' => 'El stock debe ser un número.',
            'stock.min' => 'El stock no puede ser negativo.',
            'imagen.image' => 'El archivo debe ser una imagen.',
            'imagen.mimes' => 'La imagen debe ser de tipo: jpeg, png, jpg, gif o webp.',
            'imagen.max' => 'La imagen no puede ser mayor a 2MB.',
        ]);

        $imagenUrl = null;
        
        // Subir imagen a Cloudinary si existe
        if ($request->hasFile('imagen')) {
            try {
                $cloudinary = new Cloudinary([
                    'cloud' => [
                        'cloud_name' => config('cloudinary.cloud_name'),
                        'api_key' => config('cloudinary.api_key'),
                        'api_secret' => config('cloudinary.api_secret'),
                    ],
                ]);

                $uploadedFile = $cloudinary->uploadApi()->upload(
                    $request->file('imagen')->getRealPath(),
                    [
                        'folder' => 'productos',
                        'transformation' => [
                            'width' => 800,
                            'height' => 800,
                            'crop' => 'limit',
                            'quality' => 'auto',
                        ],
                    ]
                );

                $imagenUrl = $uploadedFile['secure_url'];
            } catch (\Exception $e) {
                return back()->withErrors(['imagen' => 'Error al subir la imagen: ' . $e->getMessage()]);
            }
        }

        Producto::create([
            'nombre' => $validated['nombre'],
            'costo' => $validated['costo'],
            'precio_venta' => $validated['precio_venta'],
            'stock' => $validated['stock'],
            'imagen_url' => $imagenUrl,
        ]);

        return redirect()->route('restaurant.productos.index')
            ->with('success', 'Producto creado exitosamente.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $visitCount = PageVisit::incrementVisit('restaurant.productos.edit');
        $producto = Producto::findOrFail($id);

        return Inertia::render('Restaurant/Productos/Edit', [
            'producto' => $producto,
            'visitCount' => $visitCount,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $producto = Producto::findOrFail($id);

        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:productos,nombre,' . $producto->id,
            'costo' => 'required|numeric|min:0',
            'precio_venta' => 'required|numeric|min:0|gt:costo',
            'stock' => 'required|numeric|min:0',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.unique' => 'Ya existe un producto con este nombre.',
            'nombre.max' => 'El nombre no puede exceder 255 caracteres.',
            'costo.required' => 'El costo es obligatorio.',
            'costo.numeric' => 'El costo debe ser un número.',
            'costo.min' => 'El costo no puede ser negativo.',
            'precio_venta.required' => 'El precio de venta es obligatorio.',
            'precio_venta.numeric' => 'El precio de venta debe ser un número.',
            'precio_venta.min' => 'El precio de venta no puede ser negativo.',
            'precio_venta.gt' => 'El precio de venta debe ser mayor que el costo.',
            'stock.required' => 'El stock es obligatorio.',
            'stock.numeric' => 'El stock debe ser un número.',
            'stock.min' => 'El stock no puede ser negativo.',
            'imagen.image' => 'El archivo debe ser una imagen.',
            'imagen.mimes' => 'La imagen debe ser de tipo: jpeg, png, jpg, gif o webp.',
            'imagen.max' => 'La imagen no puede ser mayor a 2MB.',
        ]);

        // Subir nueva imagen si existe
        if ($request->hasFile('imagen')) {
            try {
                $cloudinary = new Cloudinary([
                    'cloud' => [
                        'cloud_name' => config('cloudinary.cloud_name'),
                        'api_key' => config('cloudinary.api_key'),
                        'api_secret' => config('cloudinary.api_secret'),
                    ],
                ]);

                $uploadedFile = $cloudinary->uploadApi()->upload(
                    $request->file('imagen')->getRealPath(),
                    [
                        'folder' => 'productos',
                        'transformation' => [
                            'width' => 800,
                            'height' => 800,
                            'crop' => 'limit',
                            'quality' => 'auto',
                        ],
                    ]
                );

                $validated['imagen_url'] = $uploadedFile['secure_url'];
            } catch (\Exception $e) {
                return back()->withErrors(['imagen' => 'Error al subir la imagen: ' . $e->getMessage()]);
            }
        }

        $producto->update($validated);

        return redirect()->route('restaurant.productos.index')
            ->with('success', 'Producto actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage (soft delete).
     */
    public function destroy(string $id)
    {
        $producto = Producto::findOrFail($id);
        $producto->delete();

        return redirect()->route('restaurant.productos.index')
            ->with('success', 'Producto archivado exitosamente.');
    }
}
