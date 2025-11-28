<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Venta;
use App\Models\Compra;
use App\Models\Credito;
use App\Models\Pago;
use App\Models\Producto;
use App\Models\Insumo;
use App\Models\DetalleVenta;
use App\Models\PageVisit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\VentasExport;
use App\Exports\ComprasExport;
use App\Exports\CreditosExport;
use Barryvdh\DomPDF\Facade\Pdf;

class ReporteController extends Controller
{
    /**
     * Dashboard principal con métricas generales
     */
    public function dashboard()
    {
        $visitCount = PageVisit::incrementVisit('restaurant.reportes.dashboard');

        // Métricas del mes actual
        $ventasMes = Venta::whereMonth('fecha_hora', Carbon::now()->month)
            ->whereYear('fecha_hora', Carbon::now()->year)
            ->sum('total');

        $comprasMes = Compra::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('total');

        $creditosPendientes = Credito::where('saldo_final', '>', 0)->sum('saldo_final');

        $ordenesPendientes = Venta::where('estado', 'PENDIENTE')->count();

        // Ventas por tipo de pago (mes actual)
        $ventasPorTipo = Venta::whereMonth('fecha_hora', Carbon::now()->month)
            ->whereYear('fecha_hora', Carbon::now()->year)
            ->select('tipo_pago', DB::raw('SUM(total) as total'))
            ->groupBy('tipo_pago')
            ->get();

        // Ventas por día (últimos 30 días)
        $ventasPorDia = Venta::where('fecha_hora', '>=', Carbon::now()->subDays(30))
            ->select(DB::raw('DATE(fecha_hora) as fecha'), DB::raw('SUM(total) as total'))
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();

        // Productos más vendidos (top 10)
        $productosMasVendidos = DetalleVenta::select(
                'productos.nombre',
                DB::raw('SUM(detalle_venta.cantidad) as total_vendido'),
                DB::raw('SUM(detalle_venta.cantidad * detalle_venta.precio_unitario) as total_ingresos')
            )
            ->join('productos', 'detalle_venta.producto_id', '=', 'productos.id')
            ->groupBy('productos.id', 'productos.nombre')
            ->orderBy('total_vendido', 'desc')
            ->limit(10)
            ->get();

        // Insumos con stock bajo (menos de 20)
        $insumosStockBajo = Insumo::where('stock', '<', 20)
            ->orderBy('stock', 'asc')
            ->limit(10)
            ->get();

        // Estadísticas de créditos
        $totalPrestado = Credito::sum('saldo_inicial');
        $totalRecuperado = Pago::sum('monto');
        $saldoPendiente = Credito::sum('saldo_final');
        $tasaRecuperacion = $totalPrestado > 0 ? ($totalRecuperado / $totalPrestado) * 100 : 0;

        // Créditos en mora (más de plazo_dias sin pagar completamente)
        $creditosEnMora = Credito::join('planes', 'creditos.plan_id', '=', 'planes.id')
            ->where('creditos.saldo_final', '>', 0)
            ->whereRaw('CURRENT_DATE - creditos.fecha > planes.plazo_dias')
            ->count();

        return Inertia::render('Restaurant/Reportes/Dashboard', [
            'metricas' => [
                'ventasMes' => round($ventasMes, 2),
                'comprasMes' => round($comprasMes, 2),
                'creditosPendientes' => round($creditosPendientes, 2),
                'ordenesPendientes' => $ordenesPendientes,
            ],
            'ventasPorTipo' => $ventasPorTipo,
            'ventasPorDia' => $ventasPorDia,
            'productosMasVendidos' => $productosMasVendidos,
            'insumosStockBajo' => $insumosStockBajo,
            'estadisticasCreditos' => [
                'totalPrestado' => round($totalPrestado, 2),
                'totalRecuperado' => round($totalRecuperado, 2),
                'saldoPendiente' => round($saldoPendiente, 2),
                'tasaRecuperacion' => round($tasaRecuperacion, 2),
                'creditosEnMora' => $creditosEnMora,
            ],
            'visitCount' => $visitCount,
        ]);
    }

    /**
     * Reporte de ventas con filtros
     */
    public function ventas(Request $request)
    {
        $visitCount = PageVisit::incrementVisit('restaurant.reportes.ventas');

        $query = Venta::with(['user', 'mesa', 'detalles.producto']);

        // Filtros
        if ($request->filled('fecha_desde')) {
            $query->where('fecha_hora', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->where('fecha_hora', '<=', $request->fecha_hasta . ' 23:59:59');
        }

        if ($request->filled('tipo_pago')) {
            $query->where('tipo_pago', $request->tipo_pago);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $ventas = $query->orderBy('fecha_hora', 'desc')->paginate(20);

        $totalGeneral = $query->sum('total');

        return Inertia::render('Restaurant/Reportes/Ventas', [
            'ventas' => $ventas,
            'totalGeneral' => round($totalGeneral, 2),
            'filters' => $request->only(['fecha_desde', 'fecha_hasta', 'tipo_pago', 'estado']),
            'visitCount' => $visitCount,
        ]);
    }

    /**
     * Reporte de compras con filtros
     */
    public function compras(Request $request)
    {
        $visitCount = PageVisit::incrementVisit('restaurant.reportes.compras');

        $query = Compra::with(['proveedor', 'user', 'detalles.insumo']);

        // Filtros
        if ($request->filled('fecha_desde')) {
            $query->where('created_at', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->where('created_at', '<=', $request->fecha_hasta . ' 23:59:59');
        }

        if ($request->filled('proveedor_id')) {
            $query->where('proveedor_id', $request->proveedor_id);
        }

        $compras = $query->orderBy('created_at', 'desc')->paginate(20);

        $totalGeneral = $query->sum('total');

        // Obtener proveedores para el filtro
        $proveedores = \App\Models\Proveedor::orderBy('nombre')->get();

        return Inertia::render('Restaurant/Reportes/Compras', [
            'compras' => $compras,
            'totalGeneral' => round($totalGeneral, 2),
            'proveedores' => $proveedores,
            'filters' => $request->only(['fecha_desde', 'fecha_hasta', 'proveedor_id']),
            'visitCount' => $visitCount,
        ]);
    }

    /**
     * Estadísticas detalladas de créditos
     */
    public function creditos(Request $request)
    {
        $visitCount = PageVisit::incrementVisit('restaurant.reportes.creditos');

        // Estadísticas generales
        $totalPrestado = Credito::sum('saldo_inicial');
        $totalRecuperado = Pago::sum('monto');
        $saldoPendiente = Credito::sum('saldo_final');
        $tasaRecuperacion = $totalPrestado > 0 ? ($totalRecuperado / $totalPrestado) * 100 : 0;

        // Créditos por estado
        $creditosPagados = Credito::where('saldo_final', '<=', 0)->count();
        $creditosPendientes = Credito::where('saldo_final', '>', 0)->count();

        // Créditos en mora
        $creditosEnMora = Credito::join('planes', 'creditos.plan_id', '=', 'planes.id')
            ->where('creditos.saldo_final', '>', 0)
            ->whereRaw('CURRENT_DATE - creditos.fecha > planes.plazo_dias')
            ->count();

        // Evolución de créditos por mes (últimos 6 meses)
        $evolucionCreditos = Credito::where('fecha', '>=', Carbon::now()->subMonths(6))
            ->select(
                DB::raw("TO_CHAR(fecha, 'YYYY-MM') as mes"),
                DB::raw('SUM(saldo_inicial) as total')
            )
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();

        // Créditos por plan
        $creditosPorPlan = Credito::join('planes', 'creditos.plan_id', '=', 'planes.id')
            ->select('planes.nombre', DB::raw('COUNT(*) as cantidad'), DB::raw('SUM(creditos.saldo_inicial) as total'))
            ->groupBy('planes.id', 'planes.nombre')
            ->get();

        // Lista de créditos pendientes
        $creditosPendientesDetalle = Credito::with(['user', 'plan', 'venta'])
            ->where('saldo_final', '>', 0)
            ->orderBy('fecha', 'desc')
            ->paginate(20);

        return Inertia::render('Restaurant/Reportes/Creditos', [
            'estadisticas' => [
                'totalPrestado' => round($totalPrestado, 2),
                'totalRecuperado' => round($totalRecuperado, 2),
                'saldoPendiente' => round($saldoPendiente, 2),
                'tasaRecuperacion' => round($tasaRecuperacion, 2),
                'creditosPagados' => $creditosPagados,
                'creditosPendientes' => $creditosPendientes,
                'creditosEnMora' => $creditosEnMora,
            ],
            'evolucionCreditos' => $evolucionCreditos,
            'creditosPorPlan' => $creditosPorPlan,
            'creditosPendientesDetalle' => $creditosPendientesDetalle,
            'visitCount' => $visitCount,
        ]);
    }

    /**
     * Exportar ventas a Excel
     */
    public function exportVentasExcel(Request $request)
    {
        $filters = $request->only(['fecha_desde', 'fecha_hasta', 'tipo_pago', 'estado']);
        return Excel::download(new VentasExport($filters), 'reporte_ventas_' . date('Y-m-d') . '.xlsx');
    }

    /**
     * Exportar ventas a PDF
     */
    public function exportVentasPdf(Request $request)
    {
        $query = Venta::with(['user', 'mesa']);

        if ($request->filled('fecha_desde')) {
            $query->where('fecha_hora', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->where('fecha_hora', '<=', $request->fecha_hasta . ' 23:59:59');
        }
        if ($request->filled('tipo_pago')) {
            $query->where('tipo_pago', $request->tipo_pago);
        }
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $ventas = $query->orderBy('fecha_hora', 'desc')->get();
        $totalGeneral = $ventas->sum('total');

        $pdf = Pdf::loadView('reportes.ventas-pdf', compact('ventas', 'totalGeneral'));
        return $pdf->download('reporte_ventas_' . date('Y-m-d') . '.pdf');
    }

    /**
     * Exportar compras a Excel
     */
    public function exportComprasExcel(Request $request)
    {
        $filters = $request->only(['fecha_desde', 'fecha_hasta', 'proveedor_id']);
        return Excel::download(new ComprasExport($filters), 'reporte_compras_' . date('Y-m-d') . '.xlsx');
    }

    /**
     * Exportar compras a PDF
     */
    public function exportComprasPdf(Request $request)
    {
        $query = Compra::with(['proveedor', 'user']);

        if ($request->filled('fecha_desde')) {
            $query->where('created_at', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->where('created_at', '<=', $request->fecha_hasta . ' 23:59:59');
        }
        if ($request->filled('proveedor_id')) {
            $query->where('proveedor_id', $request->proveedor_id);
        }

        $compras = $query->orderBy('created_at', 'desc')->get();
        $totalGeneral = $compras->sum('total');

        $pdf = Pdf::loadView('reportes.compras-pdf', compact('compras', 'totalGeneral'));
        return $pdf->download('reporte_compras_' . date('Y-m-d') . '.pdf');
    }

    /**
     * Exportar créditos a Excel
     */
    public function exportCreditosExcel()
    {
        return Excel::download(new CreditosExport(), 'reporte_creditos_' . date('Y-m-d') . '.xlsx');
    }

    /**
     * Exportar créditos a PDF
     */
    public function exportCreditosPdf()
    {
        $creditos = Credito::with(['user', 'plan', 'venta'])
            ->where('saldo_final', '>', 0)
            ->orderBy('fecha', 'desc')
            ->get();

        $totalPrestado = Credito::sum('saldo_inicial');
        $totalRecuperado = \App\Models\Pago::sum('monto');
        $saldoPendiente = Credito::sum('saldo_final');

        $pdf = Pdf::loadView('reportes.creditos-pdf', compact('creditos', 'totalPrestado', 'totalRecuperado', 'saldoPendiente'));
        return $pdf->download('reporte_creditos_' . date('Y-m-d') . '.pdf');
    }
}
