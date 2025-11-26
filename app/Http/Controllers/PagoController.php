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
        // Mostrar solo planes de pago con información del cliente
        $planesPago = PlanPago::with('proyecto.cliente', 'pagos')
            ->latest()
            ->paginate(10);
        
        return Inertia::render('Pagos/Index', [
            'planesPago' => $planesPago
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

    public function show(Pago $pago)
    {
        $pago->load('planPago.proyecto.cliente');
        
        return Inertia::render('Pagos/Show', [
            'pago' => $pago
        ]);
    }

    public function edit(Pago $pago)
    {
        $pago->load('planPago');
        $planesPago = PlanPago::with('proyecto')
            ->where('estado', '!=', 'completado')
            ->get();
        
        return Inertia::render('Pagos/Edit', [
            'pago' => $pago,
            'planesPago' => $planesPago
        ]);
    }

    public function update(Request $request, Pago $pago)
    {
        $validated = $request->validate([
            'plan_pago_id' => 'required|exists:plan_pagos,id',
            'fecha' => 'required|date',
            'total' => 'required|numeric|min:0',
            'estado' => 'required|in:pendiente,completado,fallido',
            'metodo_pago' => 'nullable|string',
        ]);

        // Si el pago cambia de estado de pendiente a completado
        $estabaCompletado = $pago->estado === 'completado';
        $estaCompletado = $validated['estado'] === 'completado';

        if (!$estabaCompletado && $estaCompletado) {
            // Cambiar a completado
            $planPago = $pago->planPago;
            $planPago->pagado_total += $pago->total;
            $planPago->numero_pagos += 1;
            if ($planPago->pagado_total >= $planPago->deuda_total) {
                $planPago->estado = 'completado';
            }
            $planPago->save();
        } elseif ($estabaCompletado && !$estaCompletado) {
            // Cambiar desde completado
            $planPago = $pago->planPago;
            $planPago->pagado_total -= $pago->total;
            $planPago->numero_pagos -= 1;
            if ($planPago->pagado_total < $planPago->deuda_total) {
                $planPago->estado = 'activo';
            }
            $planPago->save();
        }

        $pago->update($validated);

        return redirect()->route('pagos.show', $pago->id)
            ->with('success', 'Pago actualizado exitosamente');
    }

    public function destroy(Pago $pago)
    {
        $planPago = $pago->planPago;
        
        // Si el pago estaba completado, restar de los totales
        if ($pago->estado === 'completado') {
            $planPago->pagado_total -= $pago->total;
            $planPago->numero_pagos -= 1;
            if ($planPago->pagado_total < $planPago->deuda_total) {
                $planPago->estado = 'activo';
            }
            $planPago->save();
        }

        $pago->delete();

        return redirect()->route('pagos.index')
            ->with('success', 'Pago eliminado exitosamente');
    }

    public function return(Request $request)
    {
        return redirect()->route('pagos.index')
            ->with('success', 'Pago procesado correctamente');
    }
}