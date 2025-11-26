# 🔧 Guía de Debug - Integración PagoFácil

## 📋 Resumen General

Se ha implementado **logging detallado y exhaustivo** en toda la cadena de generación de QR con PagoFácil. Todos los errores posibles se registran en:

-   🌐 **Consola del navegador** (errores del cliente y servidor)
-   💾 **Logs de Laravel** (`storage/logs/laravel.log`)

---

## 🎯 Niveles de Logging Implementados

### 1️⃣ **Navegador (Frontend)** - `resources/js/Pages/Pagos/Index.vue`

Cuando generas un QR, en la consola del navegador verás:

#### ✅ **En caso de ÉXITO:**

```
════════════════════════════════════════════════════
📱 INICIANDO GENERACIÓN DE QR CON PAGOFÁCIL
════════════════════════════════════════════════════
📤 Datos a enviar al servidor:
   - monto: 1500
   - glosa: Pago del plan Casa Nueva
   - email: usuario@ejemplo.com

🔗 URL: POST /pagos/generar-qr
⏳ Enviando solicitud al servidor...

✅ Respuesta recibida del servidor:
   - success: true
   - transaction_id: TXN20250115123456
   - qr_image: data:image/png;base64,iVBORw0KG...

🎉 ¡Éxito! QR generado correctamente.
✨ QR cargado en la interfaz correctamente.
```

#### ❌ **En caso de ERROR:**

```
════════════════════════════════════════════════════════════════
❌ ERROR AL GENERAR QR - INFORMACIÓN DETALLADA:
════════════════════════════════════════════════════════════════

📌 Tipo de Error: AxiosError
📄 Mensaje: Request failed with status code 500

🌐 RESPUESTA DEL SERVIDOR:
────────────────────────────────────────────────────────────────
📍 Status Code: 500
📍 Status Text: Internal Server Error

📦 Headers:
   - content-type: application/json
   - x-response-time: 2.5s

💾 Datos de respuesta:
   - success: false
   - message: Error de conexión con PagoFácil
   - error_type: connection_error

🔴 Error 500 - Server Error
   → Revisa los logs del servidor en storage/logs/laravel.log
   → Puede haber un problema con la API de PagoFácil
```

---

### 2️⃣ **Controlador (Backend)** - `app/Http/Controllers/PagoController.php`

Registra cada paso del proceso de validación y servicio:

```
═══════════════════════════════════════════════════
📱 INICIANDO GENERACIÓN DE QR - PAGOFÁCIL
═══════════════════════════════════════════════════
⏰ Hora: 2025-01-15 12:34:56
👤 Usuario: usuario@ejemplo.com
🌍 IP: 192.168.1.100

📥 Datos recibidos: {"monto":1500,"glosa":"Pago del plan...","email":""}

✅ Validación exitosa de datos:
   - Monto: Bs 1500
   - Glosa: Pago del plan Casa Nueva
   - Email: No proporcionado

🔗 Llamando a PagoFacilService::generarQR()...

📦 Respuesta de PagoFacilService:
   success: true
   transaction_id: "TXN20250115123456"
   qr_image: "data:image/png;base64,..."

🎉 ¡QR generado exitosamente!
   - ID Transacción: TXN20250115123456
   - Imagen QR: ✓ Base64 válida

═══════════════════════════════════════════════════
```

---

### 3️⃣ **Servicio PagoFácil** - `app/Services/PagoFacilService.php`

Es el nivel más detallado. Registra TODO lo que ocurre:

#### **Validación de Credenciales:**

```
─────────────────────────────────────────────────
🔄 PagoFacilService::generarQR() - INICIANDO
─────────────────────────────────────────────────

✅ Credenciales presentes
   - Commerce ID: XYZAB***
   - Base URL: https://serviciostigomoney.pagofacil.com.bo/api
```

#### **Preparación de Solicitud:**

```
📤 Enviando solicitud a PagoFácil:
   URL: https://serviciostigomoney.pagofacil.com.bo/api/servicio/generarqrv2
   Payload (sin credenciales):
   {
     "tcCommerceID": "***OCULTO***",
     "tnMonto": 1500,
     "tcNroPago": "PAG-123abc",
     "tnMoneda": 2,
     "tcGlosa": "Pago del plan Casa Nueva",
     "tcCorreo": "usuario@ejemplo.com",
     "tcUrlCallBack": "http://127.0.0.1:8000/pagos/callback",
     "tcUrlReturn": "http://127.0.0.1:8000/pagos/return"
   }

⏳ Realizando petición HTTP...
```

#### **Respuesta Exitosa:**

```
📥 Respuesta recibida de PagoFácil:
   - Status HTTP: 200
   - Headers: {...}
   - Body: {"error":0,"values":{"qrImage":"...","tnTransaccion":"TXN..."}}

✅ Respuesta HTTP exitosa (200-299)
🎉 ¡Éxito! error = 0 (sin errores en PagoFácil)
   - QR Image: ✓ Base64 válida (2845 bytes)
   - Transaction ID: ✓ TXN20250115123456
```

#### **Errores Comunes:**

**Error: Credenciales no configuradas**

```
❌ CREDENCIALES DE PAGOFÁCIL NO CONFIGURADAS
   - tokenService: ✗ Faltante
   - tokenSecret: ✗ Faltante
   - Verifica config/services.php
```

**Error: Conexión rechazada**

```
❌ ERROR DE CONEXIÓN CON PAGOFÁCIL
   - Tipo: ConnectionException
   - Mensaje: Connection refused
   - Causa: Posiblemente el servidor de PagoFácil está inactivo
   - Stack: [trace completo de excepción]
```

**Error: API retorna error**

```
❌ PagoFácil retornó error: 1
   - Mensaje: Monto inválido
   - Detalles: {"codigo":"ERR001","descripcion":"..."}
```

---

## 🔍 Cómo Usar para Debugging

### **Paso 1: Abre la consola del navegador**

-   Presiona `F12` → Pestaña "Console"
-   Verás logs con emojis que indican cada paso

### **Paso 2: Revisa los logs de Laravel**

```bash
# Terminal (en la carpeta del proyecto)
tail -f storage/logs/laravel.log
```

### **Paso 3: Intenta generar un QR y observa:**

| Resultado      | Consola Navegador | Laravel Logs |
| -------------- | ----------------- | ------------ |
| ✅ Éxito       | Verde ✓           | Verde ✓      |
| ❌ Error       | Rojo ✗            | Rojo ✗       |
| ⚠️ Advertencia | Amarillo          | Amarillo     |

---

## 🚨 Códigos de Error HTTP Esperados

### **Error 200 (OK) - La solicitud fue exitosa**

-   ✅ Se espera que PagoFácil retorne `{"error":0,...}`
-   ❌ Si retorna `{"error":[1-999],...}` = Error de PagoFácil

### **Error 400 (Bad Request)**

-   ❌ Datos inválidos enviados (monto negativo, glosa vacía, etc.)
-   🔧 Revisa la validación en `PagoController@generarQR()`

### **Error 401 (Unauthorized)**

-   ❌ Problemas de autenticación
-   🔧 Verifica que `auth()->user()` esté disponible

### **Error 422 (Unprocessable Entity)**

-   ❌ Errores de validación de Laravel
-   🔧 Verifica los campos requeridos: `monto`, `glosa`

### **Error 500 (Internal Server Error)**

-   ❌ Error grave en el servidor
-   🔧 Revisa `storage/logs/laravel.log` para detalles completos
-   🔧 Posiblemente conexión con PagoFácil rechazada

### **Error 419 (Token Mismatch) - YA RESUELTO**

-   ❌ Token CSRF inválido (esto ya fue corregido)
-   ✅ Ahora usamos `window.axios` que maneja CSRF automáticamente

---

## 📊 Estructura de Respuesta Exitosa

```json
{
    "success": true,
    "qr_image": "data:image/png;base64,iVBORw0KGgoAAAANSU...",
    "transaction_id": "TXN20250115123456"
}
```

## 📊 Estructura de Respuesta con Error

```json
{
    "success": false,
    "message": "Error al generar código QR",
    "error_type": "connection_error"
}
```

---

## 🎨 Cómo Leer los Logs en Consola

```
════════════════════════════════════════════════════
  ↑ SEPARADOR DE SECCIONES (fácil de localizar)

📱 📤 📥 🌐 💾 📍 📄 🎉 ❌ ✅ ✨
  ↑ EMOJIS DE CONTEXTO (muestran qué está pasando)

════════════════════════════════════════════════════
  ↑ OTRA SECCIÓN (búsqueda visual)
```

**Tipos de líneas:**

-   `console.log()` = ℹ️ Información general (azul)
-   `console.warn()` = ⚠️ Advertencia (amarillo)
-   `console.error()` = ❌ Error (rojo)
-   `console.table()` = 📊 Tabla de datos (tabular)

---

## 🛠️ Archivos Modificados

1. **`app/Services/PagoFacilService.php`**

    - ✅ Logging exhaustivo en cada paso
    - ✅ Validación de credenciales
    - ✅ Captura de respuesta completa
    - ✅ Manejo de excepciones detallado

2. **`app/Http/Controllers/PagoController.php`**

    - ✅ Logging de validación
    - ✅ Logging del servicio
    - ✅ Logging de respuesta

3. **`resources/js/Pages/Pagos/Index.vue`**
    - ✅ Logging en navegador (cliente)
    - ✅ Manejo de todos los tipos de error
    - ✅ Información estructurada en consola

---

## 💡 Tips de Debugging

### **Si no ves logs:**

1. ✅ Abre F12 → Console
2. ✅ Filtra por "Error" o "Warning"
3. ✅ Busca por emojis (🔴, ⚠️, ❌)

### **Si Laravel logs están vacíos:**

1. ✅ Verifica que `config/logging.php` esté configurado
2. ✅ Asegúrate que `storage/logs/` tenga permisos de escritura
3. ✅ Reinicia el servidor Laravel

### **Si PagoFácil retorna error:**

1. ✅ Revisa el código de error en la respuesta
2. ✅ Verifica credenciales en `config/services.php`
3. ✅ Confirma que el monto sea positivo
4. ✅ Asegúrate de URLs de callback/return correctas

---

## 📞 Contacto con PagoFácil

Si el servicio está inactivo o tienes errores persistentes:

-   🌐 API PagoFácil: `https://serviciostigomoney.pagofacil.com.bo`
-   📧 Contacto: (Verifica configuración en config/services.php)
-   🔐 Commerce ID: (Oculto en logs por seguridad)

---

✅ **Sistema listo para debugging profesional**
