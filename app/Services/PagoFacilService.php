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
        
        // Debug de configuración
        Log::debug('🔧 PagoFacilService CONSTRUCTOR');
        Log::debug('   - tokenService está configurado: ' . ($this->tokenService ? 'SÍ' : 'NO'));
        Log::debug('   - tokenSecret está configurado: ' . ($this->tokenSecret ? 'SÍ' : 'NO'));
        Log::debug('   - baseUrl: ' . $this->baseUrl);
        Log::debug('   - APP_ENV: ' . env('APP_ENV'));
    }

    /**
     * Generar QR para pago
     */
    public function generarQR($monto, $glosa, $email = null)
    {
        Log::info("─────────────────────────────────────────────────");
        Log::info("🔄 PagoFacilService::generarQR() - INICIANDO");
        Log::info("─────────────────────────────────────────────────");

        // Verificar credenciales
        if (!$this->tokenService || !$this->tokenSecret) {
            Log::error("❌ CREDENCIALES DE PAGOFÁCIL NO CONFIGURADAS");
            Log::error("   - tokenService: " . ($this->tokenService ? "✓ Presente" : "✗ Faltante"));
            Log::error("   - tokenSecret: " . ($this->tokenSecret ? "✓ Presente" : "✗ Faltante"));
            Log::error("   - Verifica config/services.php");

            return [
                'success' => false,
                'message' => 'Credenciales de PagoFácil no configuradas'
            ];
        }

        Log::info("✅ Credenciales presentes");
        Log::info("   - Commerce ID: " . substr($this->tokenService, 0, 5) . "***");
        Log::info("   - Base URL: " . $this->baseUrl);

        try {
            $nroPago = uniqid('PAG-');
            $payload = [
                'tcCommerceID' => $this->tokenService,
                'tnMonto' => $monto,
                'tcNroPago' => $nroPago,
                'tnMoneda' => 2, // 1 = USD, 2 = BOB
                'tcGlosa' => $glosa,
                'tcPrimerApellido' => '',
                'tcSegundoApellido' => '',
                'tcNombres' => '',
                'tnCiNit' => '',
                'tcNroCelular' => '73726149',
                'tcCorreo' => $email ?? '',
                'tcUrlCallBack' => route('pagos.callback'),
                'tcUrlReturn' => route('pagos.return'),
                'taPedidoDetalle' => [],
            ];

            Log::info("📤 Enviando solicitud a PagoFácil:");
            Log::info("   URL: {$this->baseUrl}/servicio/generarqrv2");
            Log::info("   Payload (sin credenciales):");
            $payloadLog = $payload;
            $payloadLog['tcCommerceID'] = '***OCULTO***';
            Log::info(json_encode($payloadLog, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            Log::info("⏳ Realizando petición HTTP...");
            $response = Http::timeout(30)->post("{$this->baseUrl}/servicio/generarqrv2", $payload);

            Log::info("📥 Respuesta recibida de PagoFácil:");
            Log::info("   - Status HTTP: " . $response->status());
            Log::info("   - Headers: " . json_encode($response->headers(), JSON_PRETTY_PRINT));

            $responseBody = $response->json();
            Log::info("   - Body: " . json_encode($responseBody, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            // Verificar si la respuesta es exitosa
            if ($response->successful()) {
                Log::info("✅ Respuesta HTTP exitosa (200-299)");

                $data = $responseBody;

                if (isset($data['error'])) {
                    Log::info("   - Campo 'error': " . $data['error']);

                    if ($data['error'] == 0) {
                        Log::info("🎉 ¡Éxito! error = 0 (sin errores en PagoFácil)");

                        $qrImage = $data['values']['qrImage'] ?? null;
                        $transaccionId = $data['values']['tnTransaccion'] ?? null;

                        Log::info("   - QR Image: " . (strlen($qrImage) > 0 ? "✓ Base64 válida (" . strlen($qrImage) . " bytes)" : "✗ Vacía"));
                        Log::info("   - Transaction ID: " . ($transaccionId ? "✓ " . $transaccionId : "✗ No disponible"));

                        Log::info("─────────────────────────────────────────────────");

                        return [
                            'success' => true,
                            'qr_image' => $qrImage,
                            'transaction_id' => $transaccionId,
                        ];
                    } else {
                        Log::error("❌ PagoFácil retornó error: " . $data['error']);
                        Log::error("   - Mensaje: " . ($data['message'] ?? "No disponible"));
                        Log::error("   - Detalles: " . json_encode($data['values'] ?? [], JSON_PRETTY_PRINT));
                    }
                } else {
                    Log::warning("⚠️  Campo 'error' no presente en respuesta");
                    Log::warning("   - Response: " . json_encode($data, JSON_PRETTY_PRINT));
                }
            } else {
                Log::error("❌ Respuesta HTTP no exitosa");
                Log::error("   - Status: " . $response->status());
                Log::error("   - Reason: " . $response->reason());
                Log::error("   - Body: " . $response->body());
            }

            Log::error("❌ No se pudo generar QR");
            Log::info("─────────────────────────────────────────────────");

            return [
                'success' => false,
                'message' => 'Error al generar código QR'
            ];

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error("❌ ERROR DE CONEXIÓN CON PAGOFÁCIL");
            Log::error("   - Tipo: ConnectionException");
            Log::error("   - Mensaje: " . $e->getMessage());
            Log::error("   - Causa: Posiblemente el servidor de PagoFácil está inactivo");
            Log::error("   - Stack: " . $e->getTraceAsString());
            Log::info("─────────────────────────────────────────────────");

            return [
                'success' => false,
                'message' => 'Servicio de PagoFácil no disponible. Intenta más tarde.'
            ];

        } catch (\Illuminate\Http\Client\RequestException $e) {
            Log::error("❌ ERROR EN LA SOLICITUD HTTP");
            Log::error("   - Tipo: RequestException");
            Log::error("   - Mensaje: " . $e->getMessage());
            if ($e->response) {
                Log::error("   - Status: " . $e->response->status());
                Log::error("   - Body: " . $e->response->body());
            }
            Log::error("   - Stack: " . $e->getTraceAsString());
            Log::info("─────────────────────────────────────────────────");

            return [
                'success' => false,
                'message' => 'Error en la solicitud a PagoFácil'
            ];

        } catch (\Exception $e) {
            Log::error("❌ EXCEPCIÓN GENERAL");
            Log::error("   - Tipo: " . get_class($e));
            Log::error("   - Mensaje: " . $e->getMessage());
            Log::error("   - Archivo: " . $e->getFile() . " (Línea: " . $e->getLine() . ")");
            Log::error("   - Stack: " . $e->getTraceAsString());
            Log::info("─────────────────────────────────────────────────");

            return [
                'success' => false,
                'message' => 'Error inesperado: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Generar QR con estructura correcta de PagoFácil (nuevo formato)
     */
    public function generateQr($qrData)
    {
        Log::info("═════════════════════════════════════════════════════");
        Log::info("🔵 PagoFacilService::generateQr() - INICIANDO (NEW)");
        Log::info("═════════════════════════════════════════════════════");

        if (!$this->tokenService || !$this->tokenSecret) {
            Log::error("❌ CREDENCIALES DE PAGOFÁCIL NO CONFIGURADAS");
            return [
                'success' => false,
                'message' => 'Credenciales de PagoFácil no configuradas'
            ];
        }

        try {
            // Estructura correcta de PagoFácil
            $payload = [
                'tcCommerceID' => $this->tokenService,
                'tcSecretKey' => $this->tokenSecret,
                'paymentMethod' => $qrData['paymentMethod'] ?? 4,
                'clientName' => $qrData['clientName'] ?? '',
                'documentType' => $qrData['documentType'] ?? 1,
                'documentId' => $qrData['documentId'] ?? '',
                'phoneNumber' => $qrData['phoneNumber'] ?? '73726149',
                'email' => $qrData['email'] ?? '',
                'paymentNumber' => $qrData['paymentNumber'] ?? uniqid('CONF-'),
                'amount' => (float)($qrData['amount'] ?? 0),
                'currency' => $qrData['currency'] ?? 2, // BOB
                'clientCode' => (string)($qrData['clientCode'] ?? '0'),
                'callbackUrl' => $qrData['callbackUrl'] ?? route('pagos.callback'),
                'orderDetail' => $qrData['orderDetail'] ?? [],
            ];

            Log::info("📤 Enviando solicitud a PagoFácil:");
            Log::info("   URL: {$this->baseUrl}/servicio/generarqrv2");
            $payloadLog = $payload;
            $payloadLog['tcCommerceID'] = '***OCULTO***';
            $payloadLog['tcSecretKey'] = '***OCULTO***';
            Log::info("   Payload: " . json_encode($payloadLog, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            $response = Http::timeout(30)->post("{$this->baseUrl}/servicio/generarqrv2", $payload);

            Log::info("📥 Respuesta HTTP recibida:");
            Log::info("   - Status: " . $response->status());
            
            $responseBody = $response->json();
            Log::info("   - Body: " . json_encode($responseBody, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            if ($response->successful() && $responseBody) {
                if (isset($responseBody['error']) && $responseBody['error'] == 0) {
                    Log::info("🎉 ¡Éxito! PagoFácil retornó error = 0");

                    $qrImage = $responseBody['values']['qrImage'] ?? null;
                    $transactionId = $responseBody['values']['tnTransaccion'] ?? null;
                    $expirationDate = $responseBody['values']['expirationDate'] ?? null;

                    Log::info("   ✅ QR Image: " . (strlen($qrImage) > 0 ? "Base64 válida (" . strlen($qrImage) . " bytes)" : "Vacía"));
                    Log::info("   ✅ Transaction ID: " . ($transactionId ?? "No disponible"));
                    Log::info("   ✅ Expiration: " . ($expirationDate ?? "No disponible"));

                    Log::info("═════════════════════════════════════════════════════");

                    return [
                        'success' => true,
                        'qrBase64' => $qrImage,
                        'transactionId' => $transactionId,
                        'expirationDate' => $expirationDate,
                    ];
                } else {
                    Log::error("❌ PagoFácil retornó error: " . ($responseBody['error'] ?? 'desconocido'));
                    Log::error("   Mensaje: " . ($responseBody['message'] ?? 'No disponible'));
                }
            } else {
                Log::error("❌ Respuesta HTTP no exitosa");
                Log::error("   Status: " . $response->status());
            }

            Log::error("═════════════════════════════════════════════════════");
            return [
                'success' => false,
                'message' => 'Error al generar QR'
            ];

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error("❌ ERROR DE CONEXIÓN");
            Log::error("   Mensaje: " . $e->getMessage());
            Log::error("═════════════════════════════════════════════════════");

            return [
                'success' => false,
                'message' => 'Servicio de PagoFácil no disponible. Intenta más tarde.'
            ];

        } catch (\Exception $e) {
            Log::error("❌ EXCEPCIÓN GENERAL");
            Log::error("   Tipo: " . get_class($e));
            Log::error("   Mensaje: " . $e->getMessage());
            Log::error("═════════════════════════════════════════════════════");

            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Consultar estado de transacción
     */
    public function consultarTransaccion($transactionId)
    {
        Log::info("🔍 Consultando estado de transacción: " . $transactionId);

        try {
            $response = Http::post("{$this->baseUrl}/servicio/consultartransaccion", [
                'tcCommerceID' => $this->tokenService,
                'tcSecretKey' => $this->tokenSecret,
                'tnTransaccion' => $transactionId,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                Log::info("✅ Consulta exitosa: " . json_encode($data));
                
                return [
                    'success' => true,
                    'paymentStatus' => $data['values']['paymentStatus'] ?? null,
                    'messageEstado' => $data['values']['messageEstado'] ?? 'Desconocido',
                    'data' => $data
                ];
            }

            Log::error("❌ Consulta fallida: " . $response->status());
            return [
                'success' => false,
                'message' => 'No se pudo consultar el estado'
            ];

        } catch (\Exception $e) {
            Log::error('❌ Error al consultar transacción: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error de conexión'
            ];
        }
    }

    /**
     * Consultar estado de transacción (antiguo, mantener compatibilidad)
     */
    public function consultarEstado($transactionId)
    {
        return $this->consultarTransaccion($transactionId);
    }
}
