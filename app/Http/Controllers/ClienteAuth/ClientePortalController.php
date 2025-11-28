<?php

namespace App\Http\Controllers\ClienteAuth;

use App\Http\Controllers\Controller;
use App\Models\Proyecto;
use App\Models\PlanPago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ClientePortalController extends Controller
{
    /**
     * Display the client dashboard with their projects.
     */
    public function dashboard(): Response
    {
        $cliente = Auth::guard('cliente')->user();
        
        $proyectos = Proyecto::where('cliente_id', $cliente->id)
            ->with('vendedor:id,nombre')
            ->get();

        return Inertia::render('ClienteAuth/Dashboard', [
            'cliente' => $cliente,
            'proyectos' => $proyectos,
        ]);
    }

    /**
     * Display payment plans for a specific project.
     */
    public function showProyecto(Request $request, $proyectoId): Response
    {
        $cliente = Auth::guard('cliente')->user();
        
        $proyecto = Proyecto::where('id', $proyectoId)
            ->where('cliente_id', $cliente->id)
            ->with(['planPagos'])
            ->firstOrFail();

        return Inertia::render('ClienteAuth/ProyectoPlanes', [
            'cliente' => $cliente,
            'proyecto' => $proyecto,
            'planPagos' => $proyecto->planPagos,
        ]);
    }

    /**
     * Display individual payments for a payment plan.
     */
    public function showPlanPago(Request $request, $planPagoId): Response
    {
        $cliente = Auth::guard('cliente')->user();
        
        $planPago = PlanPago::where('id', $planPagoId)
            ->whereHas('proyecto', function($query) use ($cliente) {
                $query->where('cliente_id', $cliente->id);
            })
            ->with(['proyecto', 'pagos'])
            ->firstOrFail();

        return Inertia::render('ClienteAuth/PlanPagos', [
            'cliente' => $cliente,
            'planPago' => $planPago,
            'proyecto' => $planPago->proyecto,
            'pagos' => $planPago->pagos,
        ]);
    }
}
