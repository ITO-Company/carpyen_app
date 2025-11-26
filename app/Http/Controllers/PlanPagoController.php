<?php

namespace App\Http\Controllers;

use App\Models\PlanPago;
use App\Models\Pago;
use App\Models\Proyecto;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PlanPagoController extends Controller
{
    /**
     * Mostrar lista de todos los planes de pago con información del proyecto
     */
    public function index()
    {
        $planesPago = PlanPago::with('proyecto.cliente')
            ->latest()
            ->paginate(10);
        
        return Inertia::render('PlanesPago/Index', [
            'planesPago' => $planesPago
        ]);
    }

    /**
     * Mostrar formulario para crear un nuevo plan de pago
     */
    public function create()
    {
        $proyectos = Proyecto::with('cliente')
            ->where('estado', '!=', 'cancelado')
            ->get();
        
        return Inertia::render('PlanesPago/Create', [
            'proyectos' => $proyectos
        ]);
    }

    /**
     * Guardar un nuevo plan de pago con sus pagos asociados
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'proyecto_id' => 'required|exists:proyectos,id',
            'deuda_total' => 'required|numeric|min:0.01',
            'numero_deudas' => 'required|integer|min:1',
            'pagos' => 'required|array|min:1',
            'pagos.*.fecha' => 'required|date|after_or_equal:today',
            'pagos.*.total' => 'required|numeric|min:0.01',
            'pagos.*.metodo_pago' => 'nullable|string|max:255',
        ], [
            'proyecto_id.required' => 'Debe seleccionar un proyecto',
            'deuda_total.required' => 'La deuda total es obligatoria',
            'deuda_total.min' => 'La deuda total debe ser mayor a 0',
            'numero_deudas.required' => 'El número de deudas es obligatorio',
            'pagos.required' => 'Debe agregar al menos un pago',
            'pagos.*.fecha.required' => 'Todas las fechas de pago son obligatorias',
            'pagos.*.total.required' => 'Todos los montos de pago son obligatorios',
        ]);

        // Validar que la suma de pagos coincida con la deuda total
        $totalPagos = array_sum(array_column($validated['pagos'], 'total'));
        
        if (abs($totalPagos - $validated['deuda_total']) > 0.01) {
            return back()->withErrors([
                'pagos' => 'La suma de los pagos (' . $totalPagos . ') debe ser igual a la deuda total (' . $validated['deuda_total'] . ')'
            ])->withInput();
        }

        // Crear el plan de pago
        $planPago = PlanPago::create([
            'proyecto_id' => $validated['proyecto_id'],
            'deuda_total' => $validated['deuda_total'],
            'pagado_total' => 0,
            'numero_deudas' => $validated['numero_deudas'],
            'numero_pagos' => 0,
            'estado' => 'activo',
        ]);

        // Crear los pagos asociados
        foreach ($validated['pagos'] as $pagoData) {
            Pago::create([
                'plan_pago_id' => $planPago->id,
                'fecha' => $pagoData['fecha'],
                'total' => $pagoData['total'],
                'estado' => 'pendiente',
                'metodo_pago' => $pagoData['metodo_pago'] ?? null,
            ]);
        }

        return redirect()->route('planesPago.show', $planPago->id)
            ->with('success', 'Plan de pago creado exitosamente con ' . count($validated['pagos']) . ' pagos');
    }

    /**
     * Mostrar detalles de un plan de pago específico
     */
    public function show(PlanPago $planPago)
    {
        $planPago->load('proyecto.cliente', 'pagos');
        
        return Inertia::render('PlanesPago/Show', [
            'planPago' => $planPago
        ]);
    }

    /**
     * Mostrar formulario para editar un plan de pago
     */
    public function edit(PlanPago $planPago)
    {
        $planPago->load('proyecto', 'pagos');
        $proyectos = Proyecto::with('cliente')->get();
        
        return Inertia::render('PlanesPago/Edit', [
            'planPago' => $planPago,
            'proyectos' => $proyectos
        ]);
    }

    /**
     * Actualizar un plan de pago
     */
    public function update(Request $request, PlanPago $planPago)
    {
        $validated = $request->validate([
            'proyecto_id' => 'required|exists:proyectos,id',
            'deuda_total' => 'required|numeric|min:0.01',
            'numero_deudas' => 'required|integer|min:1',
            'pagos' => 'required|array|min:1',
            'pagos.*.id' => 'nullable|exists:pagos,id',
            'pagos.*.fecha' => 'required|date|after_or_equal:today',
            'pagos.*.total' => 'required|numeric|min:0.01',
            'pagos.*.estado' => 'required|in:pendiente,completado,fallido',
            'pagos.*.metodo_pago' => 'nullable|string|max:255',
        ]);

        // Validar suma de pagos
        $totalPagos = array_sum(array_column($validated['pagos'], 'total'));
        if (abs($totalPagos - $validated['deuda_total']) > 0.01) {
            return back()->withErrors([
                'pagos' => 'La suma de los pagos debe ser igual a la deuda total'
            ])->withInput();
        }

        // Actualizar plan de pago
        $planPago->update([
            'proyecto_id' => $validated['proyecto_id'],
            'deuda_total' => $validated['deuda_total'],
            'numero_deudas' => $validated['numero_deudas'],
        ]);

        // Recalcular pagado_total y numero_pagos
        $pagadoTotal = 0;
        $numeroPagos = 0;

        // Actualizar pagos existentes y crear nuevos
        $pagoIds = [];
        foreach ($validated['pagos'] as $pagoData) {
            if (isset($pagoData['id']) && $pagoData['id']) {
                // Actualizar pago existente
                $pago = Pago::find($pagoData['id']);
                $pago->update([
                    'fecha' => $pagoData['fecha'],
                    'total' => $pagoData['total'],
                    'estado' => $pagoData['estado'],
                    'metodo_pago' => $pagoData['metodo_pago'] ?? null,
                ]);
                $pagoIds[] = $pago->id;
            } else {
                // Crear nuevo pago
                $pago = Pago::create([
                    'plan_pago_id' => $planPago->id,
                    'fecha' => $pagoData['fecha'],
                    'total' => $pagoData['total'],
                    'estado' => $pagoData['estado'],
                    'metodo_pago' => $pagoData['metodo_pago'] ?? null,
                ]);
                $pagoIds[] = $pago->id;
            }

            if ($pagoData['estado'] === 'completado') {
                $pagadoTotal += $pagoData['total'];
                $numeroPagos += 1;
            }
        }

        // Eliminar pagos que no están en la request
        Pago::where('plan_pago_id', $planPago->id)
            ->whereNotIn('id', $pagoIds)
            ->delete();

        // Actualizar totales en plan de pago
        $planPago->update([
            'pagado_total' => $pagadoTotal,
            'numero_pagos' => $numeroPagos,
            'estado' => $pagadoTotal >= $validated['deuda_total'] ? 'completado' : 'activo',
        ]);

        return redirect()->route('planesPago.show', $planPago->id)
            ->with('success', 'Plan de pago actualizado exitosamente');
    }

    /**
     * Eliminar un plan de pago
     */
    public function destroy(PlanPago $planPago)
    {
        $planPago->pagos()->delete();
        $planPago->delete();

        return redirect()->route('planesPago.index')
            ->with('success', 'Plan de pago eliminado exitosamente');
    }

    /**
     * Obtener los pagos de un plan de pago (para mostrar cuando se hace click en "Pagos")
     */
    public function getPagos(PlanPago $planPago)
    {
        $planPago->load('pagos', 'proyecto.cliente');
        
        return response()->json([
            'planPago' => $planPago,
            'pagos' => $planPago->pagos
        ]);
    }
}
