<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Pago;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
    public function estadisticas()
    {
        $stats = [
            'total_proyectos' => Proyecto::count(),
            'proyectos_activos' => Proyecto::where('estado', 'en_proceso')->count(),
            'proyectos_completados' => Proyecto::where('estado', 'completado')->count(),
            'total_clientes' => Cliente::count(),
            'total_productos' => Producto::count(),
            'stock_bajo' => Producto::where('stock', '<', 10)->count(),
            'pagos_pendientes' => Pago::where('estado', 'pendiente')->count(),
            'ingresos_mes' => Pago::where('estado', 'completado')
                ->whereMonth('fecha', now()->month)
                ->sum('total'),
        ];

        // Proyectos por estado
        $proyectosPorEstado = Proyecto::select('estado', DB::raw('count(*) as total'))
            ->groupBy('estado')
            ->get();

        // Ingresos por mes (últimos 6 meses)
        $ingresosPorMes = Pago::select(
                DB::raw('DATE_FORMAT(fecha, "%Y-%m") as mes'),
                DB::raw('SUM(total) as total')
            )
            ->where('estado', 'completado')
            ->where('fecha', '>=', now()->subMonths(6))
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();

        return Inertia::render('Reportes/Estadisticas', [
            'stats' => $stats,
            'proyectosPorEstado' => $proyectosPorEstado,
            'ingresosPorMes' => $ingresosPorMes,
        ]);
    }

    public function ventas()
    {
        $ventas = Pago::with('planPago.proyecto.cliente')
            ->where('estado', 'completado')
            ->latest('fecha')
            ->paginate(20);

        $totalVentas = Pago::where('estado', 'completado')->sum('total');

        return Inertia::render('Reportes/Ventas', [
            'ventas' => $ventas,
            'totalVentas' => $totalVentas,
        ]);
    }

    public function inventario()
    {
        $productos = Producto::all();
        
        $stockTotal = $productos->sum('stock');
        $valorInventario = $productos->sum(function($producto) {
            return $producto->stock * $producto->precio_unitario;
        });

        return Inertia::render('Reportes/Inventario', [
            'productos' => $productos,
            'stockTotal' => $stockTotal,
            'valorInventario' => $valorInventario,
        ]);
    }
}
