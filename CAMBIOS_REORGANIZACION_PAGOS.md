# Reorganización de la Estructura de Pagos - Fase 2

## Objetivo

Restructurar la navegación de "Pagos" en el sidebar para que muestre **planes de pago** en lugar de **pagos individuales**, mejorando la experiencia del usuario y la navegación lógica del sistema.

## Cambios Realizados

### 1. **PagoController.php** (Modificado)

**Ubicación:** `app/Http/Controllers/PagoController.php`

#### Método `index()` - Actualizado

```php
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
```

**Cambios:**

-   ✅ Retorna SOLO `$planesPago` (planes de pago paginados)
-   ✅ Carga relaciones: `proyecto.cliente` y `pagos`
-   ✅ Elimina la devolución de `$pagos` (pagos individuales)
-   ✅ Propósito: Hacer que `/pagos` sea un alias para ver planes

---

### 2. **resources/views/Pagos/Index.vue** (Simplificado)

**Ubicación:** `resources/views/Pagos/Index.vue`

#### Cambios Principales:

**❌ ELIMINADO:**

-   Tabs para cambiar entre "Planes de Pago" y "Todos los Pagos"
-   Sección completa de tabla de pagos individuales (vista === 'pagos')
-   Variable reactiva `vista` para controlar las dos vistas

**✅ AÑADIDO:**

-   Verificación de `planesPago.links` para paginación
-   Botones de navegación entre páginas
-   Función `verPagos(plan)` para abrir modal con pagos del plan
-   Función `eliminar(plan)` para eliminar un plan completo
-   Cambio de botón "Detalles" a "Editar" (redirecciona a `/planesPago/{id}/edit`)

**✅ MEJORADO:**

-   Modal de "Pagos del Plan" ahora es la forma principal de ver detalles de pagos
-   Interfaz más limpia: Solo vista de planes con tarjetas
-   Acciones: **Ver Pagos** (modal), **Editar** (plan), **Eliminar** (plan)

#### Props Esperadas:

```javascript
{
    planesPago: {
        data: [
            {
                id: 1,
                proyecto: {
                    nombre: "Proyecto X",
                    cliente: {
                        nombre: "Cliente Y"
                    }
                },
                deuda_total: "5000.00",
                pagado_total: "2000.00",
                numero_deudas: 3,
                numero_pagos: 2,
                estado: "activo",
                pagos: [
                    {
                        id: 1,
                        fecha: "2025-01-15",
                        total: "1000.00",
                        estado: "completado",
                        metodo_pago: "Transferencia",
                        created_at: "2025-01-15"
                    },
                    // ... más pagos
                ]
            },
            // ... más planes
        ],
        links: [
            { label: "&laquo; Anterior", url: "/pagos?page=1", active: false },
            { label: "1", url: "/pagos?page=1", active: true },
            { label: "2", url: "/pagos?page=2", active: false },
            { label: "Siguiente &raquo;", url: "/pagos?page=2", active: false }
        ]
    }
}
```

---

## Flujo de Navegación Actual

### Antes (Fase 1):

```
Sidebar "Pagos"
    ↓
/pagos (Index)
    ├─ Tab: Planes de Pago (mostrar planes)
    └─ Tab: Todos los Pagos (mostrar pagos individuales)
```

### Ahora (Fase 2):

```
Sidebar "Pagos"
    ↓
/pagos (Index)
    └─ Mostrar SOLO planes de pago
        ├─ Botón "Ver Pagos" → Modal con detalles de pagos
        ├─ Botón "Editar" → /planesPago/{id}/edit (editar plan)
        └─ Botón "Eliminar" → Eliminar plan
```

---

## Acciones Principales Disponibles

### Desde Sidebar "Pagos":

1. **Ver Lista de Planes**

    - Muestra todos los planes paginados (10 por página)
    - Cada tarjeta muestra: Proyecto, Cliente, Estado, Deuda, Pagado, Progreso

2. **Ver Pagos de un Plan**

    - Click en "Ver Pagos" → Abre modal
    - Muestra: Resumen financiero + Tabla de pagos + Estadísticas

3. **Editar Plan**

    - Click en "Editar" → Redirecciona a `/planesPago/{id}/edit`
    - Mismo flujo que desde PlanesPago/Edit

4. **Eliminar Plan**
    - Click en "Eliminar" → Solicita confirmación
    - Deletes plan y todos sus pagos en cascada

---

## Rutas Involucradas

```php
// GET /pagos → Mostrar lista de planes (CAMBIÓ)
GET /pagos (PagoController@index) → Pagos/Index.vue

// Otras rutas de PlanPago (sin cambios):
GET /planesPago → PlanesPago/Index (duplicate view)
POST /planesPago → PlanesPago/Store
GET /planesPago/create → PlanesPago/Create
GET /planesPago/{id} → PlanesPago/Show
GET /planesPago/{id}/edit → PlanesPago/Edit
PUT /planesPago/{id} → PlanesPago/Update
DELETE /planesPago/{id} → PlanesPago/Destroy

// Rutas de Pago (reducidas):
GET /pagos (PagoController@index) → Mostrar planes (CAMBIÓ)
POST /pagos → PagoController@store (crear pago)
GET /pagos/{pago}/qr → Generar QR
```

---

## Archivos Modificados

| Archivo                                   | Tipo        | Cambios                       |
| ----------------------------------------- | ----------- | ----------------------------- |
| `app/Http/Controllers/PagoController.php` | Controlador | Método `index()` actualizado  |
| `resources/views/Pagos/Index.vue`         | Vista       | Simplificada, eliminadas tabs |

---

## Archivos NO Modificados (pero afectados conceptualmente)

| Archivo                                       | Razón                                            |
| --------------------------------------------- | ------------------------------------------------ |
| `resources/views/Pagos/Show.vue`              | Ya no es accesible desde navegación, pero existe |
| `resources/views/Pagos/Edit.vue`              | Ya no es accesible desde navegación, pero existe |
| `routes/web.php`                              | Las rutas siguen siendo las mismas               |
| `app/Http/Controllers/PlanPagoController.php` | Sin cambios, funciona como antes                 |

---

## Consideraciones Futuras

1. **Eliminar archivos innecesarios:**

    - `resources/views/Pagos/Show.vue` - No se usa
    - `resources/views/Pagos/Edit.vue` - No se usa
    - Métodos `show()`, `edit()`, `update()` en PagoController

2. **Agregar validaciones:**

    - Confirmar antes de eliminar plan con pagos
    - Mostrar advertencia si hay pagos pendientes

3. **Mejorar UX:**
    - Agregar filtros en lista de planes (por estado, proyecto)
    - Búsqueda rápida de planes
    - Ordenamiento personalizado

---

## Verificación de Cambios

✅ Sintaxis PHP verificada (sin errores)
✅ Sintaxis Vue verificada (compilación OK)
✅ Rutas registradas correctamente
✅ Controlador retorna datos esperados
✅ Relaciones de modelos cargadas correctamente

---

## Cómo Probar

1. Navegar a `http://localhost/pagos` (o tu dominio)
2. Verificar que se muestren todos los planes con información del proyecto y cliente
3. Hacer click en "Ver Pagos" para abrir modal con detalles
4. Hacer click en "Editar" para modificar el plan
5. Hacer click en "Eliminar" para borrar el plan

---

**Última actualización:** Enero 2025
**Estado:** ✅ COMPLETADO
