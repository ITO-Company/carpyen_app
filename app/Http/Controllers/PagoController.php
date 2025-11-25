<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use App\Models\PlanPago;
use App\Services\PagoFacilService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PagoController extends Controller
{
    protected $pagoFacilService;

    public function __construct(PagoFacilService $pagoFacilService)
    {
        $this->pagoFacilService = $pagoFacilService;
    }

    public function index()
    {
        $pagos = Pago::with('planPago.proyecto')
            ->latest()
            ->paginate(15);
        
        return Inertia::render('Pagos/Index', [
            'pagos' => $pagos
        ]);
    }

    public function create()
    {
        $planesPago = PlanPago::with('proyecto')
            ->where('estado', '!=', 'completado')
            ->get();
        
        return Inertia::render('Pagos/Create', [
            'planesPago' => $planesPago
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'plan_pago_id' => 'required|exists:plan_pagos,id',
            'fecha' => 'required|date',
            'total' => 'required|numeric|min:0',
            'metodo_pago' => 'required|string',
        ], [
            'plan_pago_id.required' => 'Debe seleccionar un plan de pago',
            'fecha.required' => 'La fecha es obligatoria',
            'total.required' => 'El monto es obligatorio',
            'metodo_pago.required' => 'El método de pago es obligatorio',
        ]);

        $validated['estado'] = 'pendiente';

        $pago = Pago::create($validated);

        // Actualizar plan de pago
        $planPago = PlanPago::find($validated['plan_pago_id']);
        $planPago->pagado_total += $validated['total'];
        $planPago->numero_pagos += 1;
        
        if ($planPago->pagado_total >= $planPago->deuda_total) {
            $planPago->estado = 'completado';
        }
        
        $planPago->save();

        return redirect()->route('pagos.index')
            ->with('success', 'Pago registrado exitosamente');
    }

    public function generarQR(Request $request)
    {
        $validated = $request->validate([
            'monto' => 'required|numeric|min:0',
            'glosa' => 'required|string',
            'email' => 'nullable|email',
        ]);

        $resultado = $this->pagoFacilService->generarQR(
            $validated['monto'],
            $validated['glosa'],
            $validated['email'] ?? null
        );

        return response()->json($resultado);
    }

    public function callback(Request $request)
    {
        // Procesar callback de Pago Fácil
        $transactionId = $request->input('tnTransaccion');
        
        // Actualizar estado del pago
        $pago = Pago::where('transaccion_id', $transactionId)->first();
        
        if ($pago) {
            $pago->estado = 'completado';
            $pago->save();
        }

        return response()->json(['success' => true]);
    }

    public function return(Request $request)
    {
        return redirect()->route('pagos.index')
            ->with('success', 'Pago procesado correctamente');
    }
}
