<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use App\Models\PlanPago;
use App\Models\Proyecto;
use App\Services\PagoFacilService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

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

    /**
     * Generar QR para pago con estructura correcta de PagoFácil
     */
    public function generarQR(Request $request)
    {
        \Log::info('═════════════════════════════════════════════════════════════');
        \Log::info('🔵 Iniciando generación de QR - PagoFácil');
        \Log::info('═════════════════════════════════════════════════════════════');

        $request->validate([
            'plan_pago_id' => 'required|exists:plan_pagos,id',
            'pago_id' => 'required|exists:pagos,id',
        ]);

        try {
            $pago = Pago::with('planPago.proyecto.cliente')->findOrFail($request->pago_id);
            $planPago = $pago->planPago;
            $proyecto = $planPago->proyecto;
            $cliente = $proyecto->cliente;

            \Log::info('📦 Datos encontrados:', [
                'pago_id' => $pago->id,
                'plan_pago_id' => $planPago->id,
                'proyecto' => $proyecto->nombre,
                'cliente' => $cliente->nombre,
                'monto' => $pago->total
            ]);

            // Calcular saldo pendiente del plan
            $total_pagado = $planPago->pagados()->sum('total');
            $saldo_pendiente = $planPago->deuda_total - $total_pagado;

            \Log::info('💰 Cálculo de saldo:', [
                'deuda_total' => $planPago->deuda_total,
                'total_pagado' => $total_pagado,
                'saldo_pendiente' => $saldo_pendiente
            ]);

            if ($saldo_pendiente <= 0) {
                \Log::warning('⚠️ Saldo pendiente es 0 o negativo');
                return response()->json([
                    'success' => false,
                    'message' => 'Este plan ya está completamente pagado.'
                ], 400);
            }

            // Generar ID de transacción único (como en el ejemplo)
            $companyTransactionId = 'CONF-' . $planPago->id . '-' . time();

            \Log::info('🔑 ID de transacción generado:', ['company_transaction_id' => $companyTransactionId]);

            // Preparar datos para PagoFácil (estructura correcta)
            $qrData = [
                'paymentMethod' => 4, // QR Simple
                'clientName' => $cliente->nombre,
                'documentType' => 1, // CI
                'documentId' => '123456',
                'phoneNumber' => $cliente->telefono ?? '73726149',
                'email' => $cliente->email ?? '',
                'paymentNumber' => $companyTransactionId,
                'amount' => (float)$pago->total,
                'currency' => 2, // BOB
                'clientCode' => (string)$cliente->id,
                'callbackUrl' => 'https://www.tecnoweb.org.bo/inf513/grupo03sa/payments/callback',
                'orderDetail' => [
                    [
                        'serial' => 1,
                        'product' => "Plan de Pago - Proyecto: {$proyecto->nombre}",
                        'quantity' => 1,
                        'price' => (float)$pago->total,
                        'discount' => 0,
                        'total' => (float)$pago->total,
                    ]
                ]
            ];

            \Log::info('📋 Datos preparados para PagoFácil:', [
                'paymentNumber' => $qrData['paymentNumber'],
                'amount' => $qrData['amount'],
                'clientName' => $qrData['clientName'],
            ]);

            // Generar QR a través del servicio
            \Log::info('🌐 Llamando a PagoFacilService::generateQr()...');
            $response = $this->pagoFacilService->generateQr($qrData);

            \Log::info('✅ Respuesta de PagoFacilService recibida:', [
                'success' => $response['success'],
                'has_qr' => isset($response['qrBase64']),
                'transaction_id' => $response['transactionId'] ?? null
            ]);

            if ($response['success']) {
                // Guardar datos del QR en el pago
                $pago->update([
                    'pagofacil_transaction_id' => $response['transactionId'],
                    'company_transaction_id' => $companyTransactionId,
                    'qr_base64' => $response['qrBase64'],
                    'qr_status' => 'PENDING',
                    'qr_expiration' => $response['expirationDate'] ?? now()->addHours(24),
                ]);

                \Log::info('💾 Pago actualizado en BD con datos QR:', [
                    'pago_id' => $pago->id,
                    'qr_status' => 'PENDING'
                ]);

                \Log::info('═════════════════════════════════════════════════════════════');

                return response()->json([
                    'success' => true,
                    'message' => 'QR generado exitosamente',
                    'qrBase64' => $response['qrBase64'],
                    'transactionId' => $response['transactionId'],
                    'expirationDate' => $response['expirationDate'] ?? null,
                ]);
            } else {
                \Log::error('❌ PagoFacilService retornó error:', [
                    'message' => $response['message'] ?? 'Error desconocido'
                ]);

                \Log::info('═════════════════════════════════════════════════════════════');

                return response()->json([
                    'success' => false,
                    'message' => $response['message'] ?? 'Error al generar QR'
                ], 500);
            }

        } catch (\Exception $e) {
            \Log::error('❌ EXCEPCIÓN AL GENERAR QR:', [
                'tipo' => get_class($e),
                'mensaje' => $e->getMessage(),
                'archivo' => $e->getFile(),
                'linea' => $e->getLine()
            ]);

            \Log::info('═════════════════════════════════════════════════════════════');

            return response()->json([
                'success' => false,
                'message' => 'Error al generar QR: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Callback from PagoFácil (Webhook)
     */
    public function callback(Request $request)
    {
        \Log::info('📞 Callback recibido de PagoFácil:', $request->all());

        // Validar la notificación
        $pedidoId = $request->input('PedidoID'); // Company Transaction ID
        $estado = $request->input('Estado');

        // Buscar el pago por company_transaction_id
        $pago = Pago::where('company_transaction_id', $pedidoId)->first();

        if (!$pago) {
            \Log::warning('⚠️ Pago no encontrado:', ['company_transaction_id' => $pedidoId]);
            return response()->json(['error' => 1, 'message' => 'Pago no encontrado'], 404);
        }

        // Actualizar el estado del pago si fue exitoso
        if ($estado === 'Completado' || $estado === 'PAID' || $estado === 2) {  // 2 = PAID según API
            $pago->update([
                'qr_status' => 'PAID',
                'estado' => 'completado'
            ]);

            // Actualizar estado del plan si está completamente pagado
            if ($pago->planPago) {
                $totalPagado = $pago->planPago->pagos()->where('estado', 'completado')->sum('total');
                $totalAPagar = $pago->planPago->deuda_total;

                if ($totalPagado >= $totalAPagar) {
                    $pago->planPago->update(['estado' => 'completado']);
                    \Log::info('✅ Plan de pago actualizado a completado:', ['plan_pago_id' => $pago->planPago->id]);
                }
            }

            \Log::info('✅ Pago confirmado:', ['pago_id' => $pago->id]);
        }

        // Responder conforme a la API de PagoFácil
        return response()->json([
            'error' => 0,
            'status' => 1,
            'message' => 'Notificación recibida correctamente',
            'values' => true
        ], 200);
    }

    /**
     * Consultar estado de transacción (MANUAL)
     */
    public function checkTransactionStatus($pagoId)
    {
        $pago = Pago::findOrFail($pagoId);

        if (!$pago->pagofacil_transaction_id) {
            return response()->json(['error' => 'Este pago no tiene un ID de transacción de PagoFácil'], 400);
        }

        try {
            \Log::info('🔍 Consultando estado de transacción:', [
                'pago_id' => $pagoId,
                'transaction_id' => $pago->pagofacil_transaction_id
            ]);

            // Consultar estado en PagoFácil
            $result = $this->pagoFacilService->consultarTransaccion($pago->pagofacil_transaction_id);

            \Log::info('📊 Resultado de consulta:', $result);

            // Actualizar estado según respuesta
            if (isset($result['paymentStatus'])) {
                $status = $result['paymentStatus'];
                
                if ($status == 2) {  // PAID
                    $pago->update(['qr_status' => 'PAID', 'estado' => 'completado']);

                    // Actualizar plan si está completamente pagado
                    if ($pago->planPago) {
                        $totalPagado = $pago->planPago->pagos()->where('estado', 'completado')->sum('total');
                        $totalAPagar = $pago->planPago->deuda_total;

                        if ($totalPagado >= $totalAPagar) {
                            $pago->planPago->update(['estado' => 'completado']);
                        }
                    }

                    return response()->json([
                        'success' => true,
                        'status' => 'PAID',
                        'message' => '✅ Pago confirmado exitosamente',
                        'data' => $result
                    ]);
                } elseif ($status == 1) {  // PENDING
                    return response()->json([
                        'success' => true,
                        'status' => 'PENDING',
                        'message' => '⏳ Pago pendiente de confirmación',
                        'data' => $result
                    ]);
                } elseif ($status == 3) {  // CANCELLED
                    $pago->update(['qr_status' => 'CANCELLED']);
                    return response()->json([
                        'success' => true,
                        'status' => 'CANCELLED',
                        'message' => '❌ Pago cancelado',
                        'data' => $result
                    ]);
                } elseif ($status == 4) {  // EXPIRED
                    $pago->update(['qr_status' => 'EXPIRED']);
                    return response()->json([
                        'success' => true,
                        'status' => 'EXPIRED',
                        'message' => '⏰ QR expirado',
                        'data' => $result
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Estado consultado',
                'data' => $result
            ]);

        } catch (\Exception $e) {
            \Log::error('❌ Error al consultar estado:', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al consultar estado: ' . $e->getMessage()
            ], 500);
        }
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
