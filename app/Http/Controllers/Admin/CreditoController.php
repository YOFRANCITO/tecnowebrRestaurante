<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Credito;
use App\Models\PageVisit;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CreditoController extends Controller
{
    /**
     * Display a listing of the resource (mis créditos pendientes).
     */
    public function index(Request $request)
    {
        $visitCount = PageVisit::incrementVisit('restaurant.creditos.index');

        $query = Credito::with(['venta.user', 'plan', 'user']);
        
        // Si es cliente, solo ver sus créditos
        if (auth()->user()->hasRole('cliente')) {
            $query->where('user_id', auth()->id());
        }
        // Si es administrador, ver todos los créditos
        
        $creditos = $query->pendientes()
            ->orderBy('fecha', 'desc')
            ->paginate(10);

        return Inertia::render('Restaurant/Creditos/Index', [
            'creditos' => $creditos,
            'visitCount' => $visitCount,
        ]);
    }

    /**
     * Display the specified resource (detalle del crédito con cuotas y pagos).
     */
    public function show(string $id)
    {
        $visitCount = PageVisit::incrementVisit('restaurant.creditos.show');

        $query = Credito::with(['venta.user', 'plan', 'cuotas', 'pagos', 'user']);
        
        // Si es cliente, solo ver sus créditos
        if (auth()->user()->hasRole('cliente')) {
            $query->where('user_id', auth()->id());
        }
        // Si es administrador, puede ver cualquier crédito
        
        $credito = $query->findOrFail($id);

        // Calcular intereses acumulados hasta hoy
        $interesesAcumulados = $credito->calcularInteresesDiarios();

        return Inertia::render('Restaurant/Creditos/Show', [
            'credito' => $credito,
            'interesesAcumulados' => $interesesAcumulados,
            'visitCount' => $visitCount,
        ]);
    }
}
