<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Pago;
use App\Models\Cotizacion;
use App\Models\Diseno;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
    /**
     * Vista principal de reportes
     */
    public function index()
    {
        return Inertia::render('Reportes/Index');
    }

    /**
     * Reporte de estadísticas generales
     */
    public function estadisticas(Request $request)
    {
        $fechaInicio = $request->input('fecha_inicio', now()->subMonths(6)->format('Y-m-d'));
        $fechaFin = $request->input('fecha_fin', now()->format('Y-m-d'));

        $stats = [
            'total_proyectos' => Proyecto::count(),
            'proyectos_activos' => Proyecto::where('estado', 'en_proceso')->count(),
            'proyectos_completados' => Proyecto::where('estado', 'completado')->count(),
            'total_clientes' => Cliente::count(),
            'total_productos' => Producto::count(),
            'stock_bajo' => Producto::where('stock', '<', 10)->count(),
            'pagos_pendientes' => Pago::where('estado', 'pendiente')->count(),
            'ingresos_totales' => Pago::where('estado', 'completado')->sum('total'),
            'ingresos_periodo' => Pago::where('estado', 'completado')
                ->whereBetween('fecha', [$fechaInicio, $fechaFin])
                ->sum('total'),
        ];

        // Proyectos por estado
        $proyectosPorEstado = Proyecto::select('estado', DB::raw('count(*) as total'))
            ->groupBy('estado')
            ->get();

        // Ingresos por mes
        $ingresosPorMes = Pago::select(
                DB::raw('EXTRACT(MONTH FROM fecha) as mes'),
                DB::raw('EXTRACT(YEAR FROM fecha) as año'),
                DB::raw('SUM(total) as total')
            )
            ->where('estado', 'completado')
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->groupBy('año', 'mes')
            ->orderBy('año')
            ->orderBy('mes')
            ->get();

        // Top clientes
        $topClientes = Cliente::withCount('proyectos')
            ->orderBy('proyectos_count', 'desc')
            ->take(10)
            ->get();

        return Inertia::render('Reportes/Estadisticas', [
            'stats' => $stats,
            'proyectosPorEstado' => $proyectosPorEstado,
            'ingresosPorMes' => $ingresosPorMes,
            'topClientes' => $topClientes,
            'filtros' => [
                'fecha_inicio' => $fechaInicio,
                'fecha_fin' => $fechaFin,
            ],
        ]);
    }

    /**
     * Reporte de proyectos
     */
    public function proyectos(Request $request)
    {
        $query = Proyecto::with(['cliente', 'vendedor']);

        // Filtros
        if ($request->has('estado') && $request->estado != 'todos') {
            $query->where('estado', $request->estado);
        }

        if ($request->has('fecha_inicio') && $request->has('fecha_fin')) {
            $query->whereBetween('created_at', [$request->fecha_inicio, $request->fecha_fin]);
        }

        $proyectos = $query->latest()->paginate(20);

        $estadisticas = [
            'total' => Proyecto::count(),
            'pendientes' => Proyecto::where('estado', 'pendiente')->count(),
            'en_proceso' => Proyecto::where('estado', 'en_proceso')->count(),
            'completados' => Proyecto::where('estado', 'completado')->count(),
            'cancelados' => Proyecto::where('estado', 'cancelado')->count(),
        ];

        return Inertia::render('Reportes/Proyectos', [
            'proyectos' => $proyectos,
            'estadisticas' => $estadisticas,
            'filtros' => $request->only(['estado', 'fecha_inicio', 'fecha_fin']),
        ]);
    }

    /**
     * Reporte de clientes
     */
    public function clientes()
    {
        $clientes = Cliente::withCount(['proyectos'])
            ->with(['proyectos' => function($query) {
                $query->latest()->take(3);
            }])
            ->paginate(20);

        $stats = [
            'total_clientes' => Cliente::count(),
            'con_proyectos' => Cliente::has('proyectos')->count(),
            'sin_proyectos' => Cliente::doesntHave('proyectos')->count(),
        ];

        return Inertia::render('Reportes/Clientes', [
            'clientes' => $clientes,
            'stats' => $stats,
        ]);
    }

    /**
     * Reporte de ventas/pagos
     */
    public function ventas(Request $request)
    {
        $fechaInicio = $request->input('fecha_inicio', now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->input('fecha_fin', now()->format('Y-m-d'));

        $ventas = Pago::with('planPago.proyecto.cliente')
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->latest('fecha')
            ->paginate(20);

        $stats = [
            'total_ventas' => Pago::where('estado', 'completado')
                ->whereBetween('fecha', [$fechaInicio, $fechaFin])
                ->sum('total'),
            'pagos_completados' => Pago::where('estado', 'completado')
                ->whereBetween('fecha', [$fechaInicio, $fechaFin])
                ->count(),
            'pagos_pendientes' => Pago::where('estado', 'pendiente')
                ->whereBetween('fecha', [$fechaInicio, $fechaFin])
                ->count(),
            'total_pendiente' => Pago::where('estado', 'pendiente')
                ->whereBetween('fecha', [$fechaInicio, $fechaFin])
                ->sum('total'),
        ];

        // Ventas por día
        $ventasPorDia = Pago::select(
                DB::raw('DATE(fecha) as dia'),
                DB::raw('SUM(total) as total'),
                DB::raw('COUNT(*) as cantidad')
            )
            ->where('estado', 'completado')
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->groupBy('dia')
            ->orderBy('dia')
            ->get();

        return Inertia::render('Reportes/Ventas', [
            'ventas' => $ventas,
            'stats' => $stats,
            'ventasPorDia' => $ventasPorDia,
            'filtros' => [
                'fecha_inicio' => $fechaInicio,
                'fecha_fin' => $fechaFin,
            ],
        ]);
    }

    /**
     * Reporte de inventario
     */
    public function inventario()
    {
        $productos = Producto::withCount('proyectos')
            ->orderBy('stock', 'asc')
            ->get();
        
        $stockTotal = $productos->sum('stock');
        $valorInventario = $productos->sum(function($producto) {
            return $producto->stock * $producto->precio_unitario;
        });

        $stats = [
            'total_productos' => $productos->count(),
            'stock_total' => $stockTotal,
            'valor_inventario' => $valorInventario,
            'stock_bajo' => $productos->where('stock', '<', 10)->count(),
            'sin_stock' => $productos->where('stock', '<=', 0)->count(),
        ];

        // Productos más usados
        $productosPopulares = $productos->sortByDesc('proyectos_count')->take(10)->values();

        return Inertia::render('Reportes/Inventario', [
            'productos' => $productos,
            'stats' => $stats,
            'productosPopulares' => $productosPopulares,
        ]);
    }
}
