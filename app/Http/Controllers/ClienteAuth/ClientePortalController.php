<?php

namespace App\Http\Controllers\ClienteAuth;

use App\Http\Controllers\Controller;
use App\Models\Proyecto;
use App\Models\PlanPago;
use App\Models\Pago;
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

    /**
     * Display all payment plans for the client.
     */
    public function planesPago(Request $request): Response
    {
        $cliente = Auth::guard('cliente')->user();
        
        $planesPago = PlanPago::whereHas('proyecto', function($query) use ($cliente) {
                $query->where('cliente_id', $cliente->id);
            })
            ->with(['proyecto', 'pagos'])
            ->get();

        return Inertia::render('ClienteAuth/PlanesPago', [
            'cliente' => $cliente,
            'planesPago' => $planesPago,
        ]);
    }

    /**
     * Display all payments for the client across all payment plans.
     */
    public function pagos(Request $request): Response
    {
        $cliente = Auth::guard('cliente')->user();
        
        $pagos = Pago::whereHas('planPago.proyecto', function($query) use ($cliente) {
                $query->where('cliente_id', $cliente->id);
            })
            ->with(['planPago.proyecto'])
            ->orderBy('fecha', 'desc')
            ->get();

        return Inertia::render('ClienteAuth/Pagos', [
            'cliente' => $cliente,
            'pagos' => $pagos,
        ]);
    }

    /**
     * Generate QR for payment (PagoFácil integration)
     */
    public function generarQR(Request $request)
    {
        $request->validate([
            'plan_pago_id' => 'required|exists:plan_pagos,id',
            'pago_id' => 'required|exists:pagos,id',
        ]);

        $cliente = Auth::guard('cliente')->user();

        try {
            $pago = Pago::with('planPago.proyecto')->findOrFail($request->pago_id);
            $planPago = $pago->planPago;
            $proyecto = $planPago->proyecto;

            // Verify the payment belongs to this client
            if ($proyecto->cliente_id !== $cliente->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'No autorizado para este pago'
                ], 403);
            }

            // Use the PagoController logic
            $pagoController = app(\App\Http\Controllers\PagoController::class);
            return $pagoController->generarQR($request);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al generar QR: ' . $e->getMessage()
            ], 500);
        }
    }
}
