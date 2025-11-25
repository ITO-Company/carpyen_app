<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PagoFacilService
{
    private $tokenService;
    private $tokenSecret;
    private $baseUrl;

    public function __construct()
    {
        $this->tokenService = config('services.pagofacil.token_service');
        $this->tokenSecret = config('services.pagofacil.token_secret');
        $this->baseUrl = config('services.pagofacil.base_url', 'https://serviciostigomoney.pagofacil.com.bo/api');
    }

    /**
     * Generar QR para pago
     */
    public function generarQR($monto, $glosa, $email = null)
    {
        try {
            $response = Http::post("{$this->baseUrl}/servicio/generarqrv2", [
                'tcCommerceID' => $this->tokenService,
                'tnMonto' => $monto,
                'tcNroPago' => uniqid('PAG-'),
                'tnMoneda' => 2, // 1 = USD, 2 = BOB
                'tcGlosa' => $glosa,
                'tcPrimerApellido' => '',
                'tcSegundoApellido' => '',
                'tcNombres' => '',
                'tnCiNit' => '',
                'tcNroCelular' => '',
                'tcCorreo' => $email ?? '',
                'tcUrlCallBack' => route('pagos.callback'),
                'tcUrlReturn' => route('pagos.return'),
                'taPedidoDetalle' => [],
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['error']) && $data['error'] == 0) {
                    return [
                        'success' => true,
                        'qr_image' => $data['values']['qrImage'] ?? null,
                        'transaction_id' => $data['values']['tnTransaccion'] ?? null,
                    ];
                }
            }

            Log::error('Error al generar QR de Pago Fácil', [
                'response' => $response->body()
            ]);

            return [
                'success' => false,
                'message' => 'Error al generar código QR'
            ];

        } catch (\Exception $e) {
            Log::error('Excepción al generar QR', [
                'message' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Error de conexión con Pago Fácil'
            ];
        }
    }

    /**
     * Consultar estado de transacción
     */
    public function consultarEstado($transactionId)
    {
        try {
            $response = Http::post("{$this->baseUrl}/servicio/consultartransaccion", [
                'tcCommerceID' => $this->tokenService,
                'tnTransaccion' => $transactionId,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                return [
                    'success' => true,
                    'estado' => $data['values']['messageEstado'] ?? 'Desconocido',
                    'data' => $data
                ];
            }

            return [
                'success' => false,
                'message' => 'No se pudo consultar el estado'
            ];

        } catch (\Exception $e) {
            Log::error('Error al consultar estado de transacción', [
                'message' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Error de conexión'
            ];
        }
    }
}
