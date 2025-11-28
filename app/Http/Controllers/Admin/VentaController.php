<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Venta;
use App\Models\Mesa;
use App\Models\Producto;
use App\Models\Plan;
use App\Models\Credito;
use App\Models\DetalleVenta;
use App\Models\PageVisit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Carbon\Carbon;

class VentaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $visitCount = PageVisit::incrementVisit('restaurant.ventas.index');

        $query = Venta::with(['user', 'mesa'])->recent();

        // Filtro por estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // Filtro por tipo de pago
        if ($request->filled('tipo_pago')) {
            $query->where('tipo_pago', $request->tipo_pago);
        }

        $ventas = $query->paginate(10);

        return Inertia::render('Restaurant/Ventas/Index', [
            'ventas' => $ventas,
            'filters' => $request->only(['estado', 'tipo_pago']),
            'visitCount' => $visitCount,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $visitCount = PageVisit::incrementVisit('restaurant.ventas.create');
        
        $mesas = Mesa::orderBy('codigo')->get();
        $productos = Producto::orderBy('nombre')->get();
        
        // Solo clientes pueden acceder a planes de crédito
        $planes = null;
        if (auth()->user()->hasRole('cliente')) {
            $planes = Plan::orderBy('nombre')->get();
        }

        return Inertia::render('Restaurant/Ventas/Create', [
            'mesas' => $mesas,
            'productos' => $productos,
            'planes' => $planes,
            'visitCount' => $visitCount,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validaciones básicas
        $validated = $request->validate([
            'mesa_id' => 'required|exists:mesas,id',
            'tipo_pago' => 'required|in:Inmediato,Crédito',
            'plan_id' => 'required_if:tipo_pago,Crédito|exists:planes,id',
            'detalles' => 'required|array|min:1',
            'detalles.*.producto_id' => 'required|exists:productos,id',
            'detalles.*.cantidad' => 'required|numeric|min:0.01',
        ], [
            'mesa_id.required' => 'Debe seleccionar una mesa.',
            'mesa_id.exists' => 'La mesa seleccionada no existe.',
            'tipo_pago.required' => 'Debe seleccionar un tipo de pago.',
            'tipo_pago.in' => 'El tipo de pago no es válido.',
            'plan_id.required_if' => 'Debe seleccionar un plan de crédito.',
            'plan_id.exists' => 'El plan seleccionado no existe.',
            'detalles.required' => 'Debe agregar al menos un producto.',
            'detalles.min' => 'Debe agregar al menos un producto.',
            'detalles.*.producto_id.required' => 'El producto es obligatorio.',
            'detalles.*.producto_id.exists' => 'El producto no existe.',
            'detalles.*.cantidad.required' => 'La cantidad es obligatoria.',
            'detalles.*.cantidad.numeric' => 'La cantidad debe ser un número.',
            'detalles.*.cantidad.min' => 'La cantidad debe ser mayor a 0.',
        ]);

        // Validar que solo clientes puedan pagar a crédito
        if ($validated['tipo_pago'] === 'Crédito' && !auth()->user()->hasRole('cliente')) {
            return back()->withErrors(['tipo_pago' => 'Solo los clientes pueden pagar a crédito.']);
        }

        DB::beginTransaction();

        try {
            // Validar stock y calcular total
            $total = 0;
            foreach ($validated['detalles'] as $detalle) {
                $producto = Producto::findOrFail($detalle['producto_id']);
                
                // Validar stock disponible
                if ($producto->stock < $detalle['cantidad']) {
                    DB::rollBack();
                    return back()->withErrors([
                        'stock' => "Stock insuficiente para {$producto->nombre}. Disponible: {$producto->stock}"
                    ]);
                }

                $total += $producto->precio_venta * $detalle['cantidad'];
            }

            // Crear venta
            $venta = Venta::create([
                'total' => $total,
                'fecha_hora' => Carbon::now(),
                'estado' => 'PENDIENTE',
                'tipo_pago' => $validated['tipo_pago'],
                'user_id' => auth()->id(),
                'mesa_id' => $validated['mesa_id'],
            ]);

            // Crear detalles de venta y reducir stock
            foreach ($validated['detalles'] as $detalle) {
                $producto = Producto::findOrFail($detalle['producto_id']);

                DetalleVenta::create([
                    'venta_id' => $venta->id,
                    'producto_id' => $producto->id,
                    'cantidad' => $detalle['cantidad'],
                    'precio_unitario' => $producto->precio_venta,
                ]);

                // Reducir stock del producto
                $producto->decrement('stock', $detalle['cantidad']);
            }

            // Si es pago a crédito, crear crédito y cuotas
            if ($validated['tipo_pago'] === 'Crédito') {
                $plan = Plan::findOrFail($validated['plan_id']);
                
                $credito = Credito::create([
                    'nro' => Credito::generarNumeroCredito(),
                    'fecha' => Carbon::now()->toDateString(),
                    'saldo_inicial' => $total,
                    'interes' => 0,
                    'capital' => $total,
                    'cuota' => $total / ceil($plan->plazo_dias / 7), // Cuota semanal sugerida
                    'saldo_final' => $total,
                    'venta_id' => $venta->id,
                    'plan_id' => $plan->id,
                    'user_id' => auth()->id(),
                ]);

                // Generar cuotas sugeridas
                $credito->generarCuotas();
            }

            DB::commit();

            return redirect()->route('restaurant.ventas.index')
                ->with('success', 'Venta registrada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al registrar la venta: ' . $e->getMessage()]);
        }
    }
}
