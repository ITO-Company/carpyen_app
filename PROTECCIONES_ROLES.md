# Protecciones por Roles - Sistema Carpyen

## Descripción General
Este documento describe las protecciones implementadas por roles en la aplicación. Cada controlador valida los permisos del usuario antes de permitir acciones.

---

## Roles y Permisos

### 1. **ADMIN**
Tiene acceso completo a todas las funciones del sistema.

**Acceso:**
- ✅ Usuarios: Ver, crear, editar, eliminar
- ✅ Clientes: Ver, crear, editar, eliminar
- ✅ Proyectos: Ver todos, crear, editar, eliminar
- ✅ Cotizaciones: Ver todas, crear, editar, eliminar
- ✅ Diseños: Ver todos, crear, editar, eliminar
- ✅ Cronogramas: Ver todos, crear, editar, eliminar
- ✅ Tareas: Ver todas, crear, editar, eliminar
- ✅ Productos: Ver, crear, editar, gestionar stock
- ✅ Proveedores: Ver, crear, editar, eliminar
- ✅ Productos por Proyecto: Ver, crear, editar, eliminar
- ✅ Pagos: Ver todos, crear, editar
- ✅ Reportes: Ver todos

---

### 2. **VENDEDOR**
Gestiona clientes, proyectos propios, cotizaciones y diseños.

**Acceso:**
- ✅ Clientes: Ver, crear (editar/eliminar: SOLO ADMIN)
- ✅ Proyectos: Ver **SOLO SUS PROPIOS**, crear, editar
- ✅ Cotizaciones: Crear en sus proyectos, ver **SOLO LAS SUYAS**, editar
- ✅ Diseños: Crear (NO puede editar ni eliminar)
- ✅ Cronogramas: Ver todos (puede crear, NO puede editar)
- ✅ Planes de Pago: Crear y generar cronogramas
- ✅ Productos por Proyecto: Ver/crear/editar en sus proyectos
- ✅ Pagos: Ver, crear
- ✅ Reportes: Ver

**Restricciones:**
- No puede ver proyectos de otros vendedores
- No puede editar cotizaciones de otros vendedores
- No puede editar cronogramas
- No puede editar diseños (solo crear)

---

### 3. **JEFE_INSTALADOR**
Gestiona cronogramas asignados, tareas y products.

**Acceso:**
- ✅ Cronogramas: Ver **SOLO SUS CRONOGRAMAS**, editar
- ✅ Tareas: Ver/crear/editar en sus cronogramas
- ✅ Productos: Ver, crear, editar
- ✅ Proveedores: Ver, crear, editar
- ✅ Productos por Proyecto: Ver, crear, editar
- ✅ Proyectos: Ver todos

**Restricciones:**
- Solo puede ver cronogramas asignados a él
- Solo puede crear/editar tareas en sus cronogramas
- No puede editar proyectos
- No puede gestionar usuarios

---

### 4. **INSTALADOR**
Trabaja en tareas asignadas y puede ver productos.

**Acceso:**
- ✅ Proyectos: Ver (a través de sus tareas)
- ✅ Cronogramas: Ver (a través de sus tareas)
- ✅ Tareas: Ver **SOLO ASIGNADAS A ÉL**, editar
- ✅ Productos: Ver, crear, editar

**Restricciones:**
- Solo puede ver tareas asignadas a él
- No puede crear tareas
- No puede editar otros proyectos
- No puede gestionar cronogramas
- No puede gestionar proveedores

---

## Protecciones Implementadas por Controlador

### ClienteController
```php
public function __construct() {
    // Solo ADMIN y VENDEDOR
    // VENDEDOR: Solo puede crear, no editar ni eliminar
    // ADMIN: Acceso completo
}
```

### ProyectoController
```php
public function index() {
    // VENDEDOR: Ver solo sus propios proyectos
    // JEFE_INSTALADOR: Ver todos
    // ADMIN: Ver todos
}

public function store() {
    // Solo ADMIN y VENDEDOR
    // VENDEDOR: Auto-asignado como user_id
}

public function edit/update() {
    // VENDEDOR: Solo sus propios proyectos
    // JEFE_INSTALADOR: Bloqueado (abort 403)
}

public function destroy() {
    // Solo ADMIN
}
```

### CotizacionController
```php
public function index() {
    // VENDEDOR: Solo sus propias cotizaciones (proyectos propios)
    // ADMIN: Ver todas
}

public function store/update() {
    // Solo ADMIN y VENDEDOR en sus proyectos
}

public function edit() {
    // VENDEDOR: Solo sus propias cotizaciones
}

public function destroy() {
    // Solo ADMIN
}
```

### DisenoController
```php
public function __construct() {
    // Solo ADMIN y VENDEDOR
}

public function create/store() {
    // Solo ADMIN y VENDEDOR
}

public function edit/update() {
    // VENDEDOR: Bloqueado (abort 403)
    // Solo ADMIN puede editar
}

public function destroy() {
    // Solo ADMIN
}
```

### CronogramaController
```php
public function index() {
    // JEFE_INSTALADOR: Ver solo sus cronogramas (usuario_id)
    // VENDEDOR: Ver todos (lectura)
    // ADMIN: Ver todos
}

public function create/store() {
    // Solo ADMIN y VENDEDOR
}

public function edit/update() {
    // JEFE_INSTALADOR: Solo sus cronogramas
    // VENDEDOR: Bloqueado (abort 403)
    // ADMIN: Todos
}

public function destroy() {
    // Solo ADMIN
}
```

### TareaController
```php
public function index() {
    // JEFE_INSTALADOR: Ver solo sus cronogramas
    // INSTALADOR: Ver solo si tiene tareas asignadas
}

public function create/store() {
    // JEFE_INSTALADOR: En sus cronogramas
    // INSTALADOR: Bloqueado (abort 403)
}

public function edit/update() {
    // JEFE_INSTALADOR: En sus cronogramas
    // INSTALADOR: Solo sus propias tareas
}

public function destroy() {
    // ADMIN y JEFE_INSTALADOR (en sus cronogramas)
}
```

### ProductoController
```php
public function __construct() {
    // ADMIN, JEFE_INSTALADOR, INSTALADOR
}

public function create/store/edit/update() {
    // INSTALADOR: Bloqueado para crear/editar
    // ADMIN, JEFE_INSTALADOR: Permitido
}
```

### ProveedorController
```php
public function __construct() {
    // Solo ADMIN y JEFE_INSTALADOR
}
```

### ProyectoProductoController
```php
public function index/create/store/edit/update() {
    // VENDEDOR: Solo sus propios proyectos
    // Otros roles: Acceso según permisos
}
```

---

## Respuestas de Error

Cuando un usuario intenta acceder sin permisos:

**403 Forbidden:**
```
No tienes permisos para acceder a [recurso].
Solo [rol específico] pueden [acción].
```

Ejemplos:
- "No tienes permisos para acceder a usuarios."
- "Solo vendedores pueden crear proyectos."
- "Solo puedes ver tus propios proyectos."
- "Los instaladores no pueden crear tareas."

---

## Middleware Personalizado

Se creó un middleware `CheckRole` en:
```
app/Http/Middleware/CheckRole.php
```

Puede usarse en rutas de la siguiente manera:
```php
Route::resource('usuarios', UsuarioController::class)
    ->middleware('role:ADMIN');
```

---

## Validación en Frontend

El menú lateral (`AuthenticatedLayout.vue`) filtra opciones por rol:
- ADMIN: Ve todos los elementos del menú
- VENDEDOR: Ve Clientes, Proyectos, Diseños, Cotizaciones, Pagos, Reportes
- JEFE_INSTALADOR: Ve Proyectos, Productos, Proveedores, Cronogramas
- INSTALADOR: Ve Productos, Proyectos (limitado)

---

## Pruebas Recomendadas

1. **VENDEDOR:** Intentar acceder a proyectos de otro vendedor
2. **JEFE_INSTALADOR:** Intentar editar cronograma que no le pertenece
3. **INSTALADOR:** Intentar crear una nueva tarea
4. **Todos:** Navegar y verificar que solo ven opciones permitidas

---

## Notas Importantes

- Las protecciones se aplican tanto en rutas como en lógica de controladores
- El frontend filtra el menú, pero la verdadera protección está en backend
- Todas las acciones retornan `abort(403)` si no hay permisos
- Los usuarios deben tener el rol correcto en la tabla `users`
- La tabla de Spatie Permission (`roles`, `model_has_roles`) complementa el sistema
