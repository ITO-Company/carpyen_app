<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class PagoFacilService
{
    private $tokenService;
    private $tokenSecret;
    private $baseUrl;
    private $client;

    public function __construct()
    {
        $this->tokenService = config('services.pagofacil.token_service');
        $this->tokenSecret = config('services.pagofacil.token_secret');
        $this->baseUrl = 'https://masterqr.pagofacil.com.bo/api/services/v2';
        $this->client = new Client();
        
        Log::debug('🔧 PagoFacilService CONSTRUCTOR');
        Log::debug('   - tokenService está configurado: ' . ($this->tokenService ? 'SÍ' : 'NO'));
        Log::debug('   - tokenSecret está configurado: ' . ($this->tokenSecret ? 'SÍ' : 'NO'));
        Log::debug('   - baseUrl: ' . $this->baseUrl);
        Log::debug('   - APP_ENV: ' . env('APP_ENV'));
    }

    /**
     * 🔑 OBTENER TOKEN DE ACCESO - PASO 1
     */
    private function obtenerToken()
    {
        Log::info('🔑 Obteniendo token de acceso para MasterQR...');

        try {
            $response = $this->client->post("{$this->baseUrl}/login", [
                'headers' => [
                    'tcTokenService' => $this->tokenService,
                    'tcTokenSecret'  => $this->tokenSecret,
                    'Accept'         => 'application/json',
                ],
                'verify' => env('APP_ENV') === 'production', // Desabilitar SSL en desarrollo
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            Log::info('📥 Respuesta del login:', [
                'status' => $response->getStatusCode(),
                'has_accessToken' => isset($data['values']['accessToken']),
            ]);

            if (!isset($data['values']['accessToken'])) {
                Log::error('❌ No se recibió accessToken en login');
                Log::error('Response: ' . json_encode($data));
                throw new \Exception("No se recibió accessToken. Respuesta: " . json_encode($data));
            }

            $token = $data['values']['accessToken'];
            Log::info('✅ Token obtenido exitosamente');
            
            return $token;

        } catch (\Exception $e) {
            Log::error('❌ Error al obtener token:', [
                'mensaje' => $e->getMessage(),
                'archivo' => $e->getFile(),
                'linea' => $e->getLine()
            ]);
            throw $e;
        }
    }

    /**
     * 📱 GENERAR QR - PASO 2
     */
    public function generateQr($qrData)
    {
        Log::info("═════════════════════════════════════════════════════");
        Log::info("📱 PagoFacilService::generateQr() - INICIANDO");
        Log::info("═════════════════════════════════════════════════════");

        if (!$this->tokenService || !$this->tokenSecret) {
            Log::error("❌ CREDENCIALES DE PAGOFÁCIL NO CONFIGURADAS");
            return [
                'success' => false,
                'message' => 'Credenciales de PagoFácil no configuradas'
            ];
        }

        try {
            // 🔑 PASO 1: Obtener token
            Log::info('🔑 Paso 1: Obtener Bearer Token');
            $token = $this->obtenerToken();

            // 📝 PASO 2: Preparar payload
            Log::info('📝 Paso 2: Preparar payload para QR');
            
            $payload = [
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

            Log::info("📤 Datos principales del QR:");
            Log::info("   - paymentNumber: " . $payload['paymentNumber']);
            Log::info("   - amount: " . $payload['amount']);
            Log::info("   - clientName: " . $payload['clientName']);
            Log::info("   - currency: " . $payload['currency']);

            // 📤 PASO 3: Enviar solicitud con Bearer token
            Log::info('📤 Paso 3: Enviar solicitud a generate-qr');
            
            $response = $this->client->post(
                "{$this->baseUrl}/generate-qr",
                [
                    'headers' => [
                        'Content-Type'  => 'application/json',
                        'Authorization' => 'Bearer ' . $token,
                        'Accept' => 'application/json',
                    ],
                    'json' => $payload,
                    'timeout' => 30,
                    'verify' => env('APP_ENV') === 'production', // Desabilitar SSL en desarrollo
                ]
            );

            Log::info("📥 Paso 4: Respuesta recibida");
            Log::info("   - HTTP Status: " . $response->getStatusCode());
            
            $responseBody = json_decode($response->getBody()->getContents(), true);

            // ✅ PASO 5: Procesar respuesta
            Log::info('✅ Paso 5: Procesar respuesta');

            if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
                if (isset($responseBody['values']['qrBase64']) && isset($responseBody['values']['transactionId'])) {
                    Log::info("🎉 ¡Éxito! QR generado correctamente");

                    $qrImage = $responseBody['values']['qrBase64'];
                    $transactionId = $responseBody['values']['transactionId'];
                    $expirationDate = $responseBody['values']['expirationDate'] ?? null;

                    Log::info("   ✅ QR Base64: " . (strlen($qrImage) > 0 ? "OK (" . strlen($qrImage) . " bytes)" : "VACÍO"));
                    Log::info("   ✅ Transaction ID: " . $transactionId);
                    Log::info("   ✅ Expiration: " . ($expirationDate ?? "N/A"));

                    Log::info("═════════════════════════════════════════════════════");

                    return [
                        'success' => true,
                        'qrBase64' => $qrImage,
                        'transactionId' => $transactionId,
                        'expirationDate' => $expirationDate,
                    ];
                } else {
                    Log::error("❌ Respuesta exitosa pero faltan campos");
                    Log::error("   - qrBase64 presente: " . (isset($responseBody['values']['qrBase64']) ? 'SÍ' : 'NO'));
                    Log::error("   - transactionId presente: " . (isset($responseBody['values']['transactionId']) ? 'SÍ' : 'NO'));
                    Log::error("   - Response: " . json_encode($responseBody, JSON_PRETTY_PRINT));
                }
            } else {
                Log::error("❌ Respuesta HTTP no exitosa");
                Log::error("   - Status: " . $response->getStatusCode());
                Log::error("   - Body: " . json_encode($responseBody, JSON_PRETTY_PRINT));
            }

            Log::error("═════════════════════════════════════════════════════");
            
            return [
                'success' => false,
                'message' => 'Error al generar QR: ' . ($responseBody['message'] ?? 'Respuesta inválida')
            ];

        } catch (\GuzzleHttp\Exception\ConnectException $e) {
            Log::error("❌ ERROR DE CONEXIÓN CON PAGOFÁCIL");
            Log::error("   - Tipo: ConnectException");
            Log::error("   - Mensaje: " . $e->getMessage());
            Log::error("   - Base URL: " . $this->baseUrl);
            Log::error("═════════════════════════════════════════════════════");

            return [
                'success' => false,
                'message' => 'No se puede conectar con PagoFácil. Verifica tu conexión.'
            ];

        } catch (\GuzzleHttp\Exception\RequestException $e) {
            Log::error("❌ ERROR EN LA SOLICITUD HTTP");
            Log::error("   - Tipo: RequestException");
            Log::error("   - Mensaje: " . $e->getMessage());
            if ($e->hasResponse()) {
                Log::error("   - Status: " . $e->getResponse()->getStatusCode());
                Log::error("   - Body: " . $e->getResponse()->getBody());
            }
            Log::error("═════════════════════════════════════════════════════");

            return [
                'success' => false,
                'message' => 'Error en la solicitud: ' . $e->getMessage()
            ];

        } catch (\Exception $e) {
            Log::error("❌ EXCEPCIÓN GENERAL");
            Log::error("   - Tipo: " . get_class($e));
            Log::error("   - Mensaje: " . $e->getMessage());
            Log::error("   - Archivo: " . $e->getFile() . " (Línea: " . $e->getLine() . ")");
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
            $token = $this->obtenerToken();

            $response = $this->client->post(
                "{$this->baseUrl}/query-transaction",
                [
                    'headers' => [
                        'Content-Type'  => 'application/json',
                        'Authorization' => 'Bearer ' . $token
                    ],
                    'json' => [
                        'pagofacilTransactionId' => $transactionId
                    ],
                    'timeout' => 30,
                    'verify' => env('APP_ENV') === 'production', // Desabilitar SSL en desarrollo
                ]
            );

            if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
                $data = json_decode($response->getBody()->getContents(), true);
                Log::info("✅ Consulta exitosa");
                
                return [
                    'success' => true,
                    'paymentStatus' => $data['values']['paymentStatus'] ?? null,
                    'messageEstado' => $data['values']['messageEstado'] ?? 'Desconocido',
                    'data' => $data
                ];
            }

            Log::error("❌ Consulta fallida: " . $response->getStatusCode());
            return [
                'success' => false,
                'message' => 'No se pudo consultar el estado'
            ];

        } catch (\Exception $e) {
            Log::error('❌ Error al consultar transacción: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error de conexión: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Consultar estado de transacción (alias)
     */
    public function consultarEstado($transactionId)
    {
        return $this->consultarTransaccion($transactionId);
    }

    /**
     * Generar QR compatibilidad (antiguo método)
     */
    public function generarQR($monto, $glosa, $email = null)
    {
        Log::warning("⚠️  generarQR() es antiguo, usa generateQr() en su lugar");
        
        $qrData = [
            'paymentMethod' => 4,
            'clientName' => 'Cliente',
            'documentType' => 1,
            'documentId' => '0',
            'phoneNumber' => '73726149',
            'email' => $email ?? '',
            'paymentNumber' => uniqid('PAG-'),
            'amount' => (float)$monto,
            'currency' => 2,
            'clientCode' => '0',
            'callbackUrl' => route('pagos.callback'),
            'orderDetail' => [
                [
                    'serial' => 1,
                    'product' => $glosa,
                    'quantity' => 1,
                    'price' => (float)$monto,
                    'discount' => 0,
                    'total' => (float)$monto,
                ]
            ]
        ];

        return $this->generateQr($qrData);
    }
}

