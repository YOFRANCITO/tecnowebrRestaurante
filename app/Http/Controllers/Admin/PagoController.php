<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pago;
use App\Models\Credito;
use App\Models\PageVisit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Carbon\Carbon;

class PagoController extends Controller
{
    /**
     * Display a listing of the resource (mis pagos).
     */
    public function index(Request $request)
    {
        $visitCount = PageVisit::incrementVisit('restaurant.pagos.index');

        $query = Pago::with('credito.venta.user', 'credito.user');
        
        // Si es cliente, solo ver sus pagos
        if (auth()->user()->hasRole('cliente')) {
            $query->whereHas('credito', function ($q) {
                $q->where('user_id', auth()->id());
            });
        }
        // Si es administrador, ver todos los pagos
        
        $pagos = $query->orderBy('fecha', 'desc')
            ->paginate(10);

        return Inertia::render('Restaurant/Pagos/Index', [
            'pagos' => $pagos,
            'visitCount' => $visitCount,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $visitCount = PageVisit::incrementVisit('restaurant.pagos.create');

        // Obtener créditos pendientes del usuario
        $creditos = Credito::with('venta')
            ->where('user_id', auth()->id())
            ->pendientes()
            ->get();

        // Si se especifica un crédito, obtener sus detalles
        $creditoSeleccionado = null;
        if ($request->filled('credito_id')) {
            $creditoSeleccionado = Credito::with(['venta', 'plan'])
                ->where('user_id', auth()->id())
                ->findOrFail($request->credito_id);
            
            // Calcular intereses acumulados
            $creditoSeleccionado->intereses_acumulados = $creditoSeleccionado->calcularInteresesDiarios();
        }

        return Inertia::render('Restaurant/Pagos/Create', [
            'creditos' => $creditos,
            'creditoSeleccionado' => $creditoSeleccionado,
            'visitCount' => $visitCount,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * Lógica de amortización: pago - intereses = capital
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'credito_id' => 'required|exists:creditos,id',
            'monto' => 'required|numeric|min:0.01',
        ], [
            'credito_id.required' => 'Debe seleccionar un crédito.',
            'credito_id.exists' => 'El crédito seleccionado no existe.',
            'monto.required' => 'El monto es obligatorio.',
            'monto.numeric' => 'El monto debe ser un número.',
            'monto.min' => 'El monto debe ser mayor a 0.',
        ]);

        DB::beginTransaction();

        try {
            $credito = Credito::where('user_id', auth()->id())
                ->findOrFail($validated['credito_id']);

            // Validar que el crédito tenga saldo pendiente
            if ($credito->saldo_final <= 0) {
                DB::rollBack();
                return back()->withErrors(['error' => 'Este crédito ya está pagado.']);
            }

            // Calcular intereses acumulados desde el último pago
            $interesesAcumulados = $credito->calcularInteresesDiarios();
            $credito->interes += $interesesAcumulados;

            // Amortización: primero se pagan los intereses, luego el capital
            $monto = $validated['monto'];
            $pagoIntereses = min($monto, $credito->interes);
            $pagoCapital = $monto - $pagoIntereses;

            // Actualizar crédito
            $credito->interes = max(0, $credito->interes - $pagoIntereses);
            $credito->capital = max(0, $credito->capital - $pagoCapital);
            $credito->actualizarSaldo();

            // Registrar pago
            Pago::create([
                'credito_id' => $credito->id,
                'monto' => $monto,
                'fecha' => Carbon::now(),
            ]);

            DB::commit();

            return redirect()->route('restaurant.pagos.index')
                ->with('success', 'Pago registrado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al registrar el pago: ' . $e->getMessage()]);
        }
    }
}
