<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ProyectoController;
use App\Http\Controllers\ProyectoProductoController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\CotizacionController;
use App\Http\Controllers\DisenoController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\CronogramaController;
use App\Http\Controllers\TareaController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\PlanPagoController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\InstaladorTareasController;
use App\Http\Controllers\DiseñadorDisenosController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        try {
            $stats = [
                'total_proyectos' => \App\Models\Proyecto::count(),
                'proyectos_activos' => \App\Models\Proyecto::where('estado', 'en_proceso')->count(),
                'total_clientes' => \App\Models\Cliente::count(),
                'productos_stock_bajo' => \App\Models\Producto::where('stock', '<', 10)->count(),
            ];
        } catch (\Exception $e) {
            // Si las tablas no existen, usar valores por defecto
            $stats = [
                'total_proyectos' => 0,
                'proyectos_activos' => 0,
                'total_clientes' => 0,
                'productos_stock_bajo' => 0,
            ];
        }
        
        return Inertia::render('Dashboard', ['stats' => $stats]);
    })->name('dashboard');

    // ============================================
    // CLIENTES - ADMIN y VENDEDOR
    // ============================================
    Route::get('/clientes', [ClienteController::class, 'index'])->name('clientes.index');
    Route::get('/clientes/create', [ClienteController::class, 'create'])->name('clientes.create');
    Route::post('/clientes', [ClienteController::class, 'store'])->name('clientes.store');
    Route::get('/clientes/{cliente}/edit', [ClienteController::class, 'edit'])->name('clientes.edit');
    Route::put('/clientes/{cliente}', [ClienteController::class, 'update'])->name('clientes.update');
    Route::delete('/clientes/{cliente}', [ClienteController::class, 'destroy'])->name('clientes.destroy');

    // ============================================
    // PROYECTOS - ADMIN y VENDEDOR
    // ============================================
    Route::resource('proyectos', ProyectoController::class);

    // ============================================
    // COTIZACIONES
    // ============================================
    Route::get('/proyectos/{proyecto}/cotizaciones', [CotizacionController::class, 'byProyecto'])->name('proyectos.cotizaciones.index');
    Route::get('/proyectos/{proyecto}/cotizaciones/crear', [CotizacionController::class, 'createByProyecto'])->name('proyectos.cotizaciones.create');
    Route::post('/proyectos/{proyecto}/cotizaciones', [CotizacionController::class, 'storeByProyecto'])->name('proyectos.cotizaciones.store');
    Route::get('/proyectos/{proyecto}/cotizaciones/{cotizacion}/editar', [CotizacionController::class, 'editByProyecto'])->name('proyectos.cotizaciones.edit');
    Route::put('/proyectos/{proyecto}/cotizaciones/{cotizacion}', [CotizacionController::class, 'updateByProyecto'])->name('proyectos.cotizaciones.update');
    Route::delete('/proyectos/{proyecto}/cotizaciones/{cotizacion}', [CotizacionController::class, 'destroyByProyecto'])->name('proyectos.cotizaciones.destroy');

    // ============================================
    // DISEÑOS
    // ============================================
    Route::get('/cotizaciones/{cotizacion}/disenos', [DisenoController::class, 'byCotizacion'])->name('cotizaciones.disenos.index');
    Route::get('/cotizaciones/{cotizacion}/disenos/crear', [DisenoController::class, 'createByCotizacion'])->name('cotizaciones.disenos.create');
    Route::post('/cotizaciones/{cotizacion}/disenos', [DisenoController::class, 'storeByCotizacion'])->name('cotizaciones.disenos.store');
    Route::resource('disenos', DisenoController::class);

    // ============================================
    // PLANES DE PAGO
    // ============================================
    Route::resource('planesPago', PlanPagoController::class);
    Route::get('/planesPago/{planPago}/pagos', [PlanPagoController::class, 'getPagos'])->name('planesPago.getPagos');

    // ============================================
    // CRONOGRAMAS
    // ============================================
    Route::get('/cronogramas', [CronogramaController::class, 'index'])->name('cronogramas.index');
    Route::get('/cronogramas/create', [CronogramaController::class, 'create'])->name('cronogramas.create');
    Route::post('/cronogramas', [CronogramaController::class, 'store'])->name('cronogramas.store');
    Route::get('/cronogramas/{cronograma}', [CronogramaController::class, 'show'])->name('cronogramas.show');
    Route::get('/cronogramas/{cronograma}/edit', [CronogramaController::class, 'edit'])->name('cronogramas.edit');
    Route::put('/cronogramas/{cronograma}', [CronogramaController::class, 'update'])->name('cronogramas.update');
    Route::delete('/cronogramas/{cronograma}', [CronogramaController::class, 'destroy'])->name('cronogramas.destroy');

    // ============================================
    // TAREAS
    // ============================================
    Route::get('/cronogramas/{cronogramaId}/tareas', [TareaController::class, 'index'])->name('tareas.index');
    Route::get('/cronogramas/{cronogramaId}/tareas/crear', [TareaController::class, 'create'])->name('tareas.create');
    Route::post('/cronogramas/{cronogramaId}/tareas', [TareaController::class, 'store'])->name('tareas.store');
    Route::get('/tareas/{id}/editar', [TareaController::class, 'edit'])->name('tareas.edit');
    Route::put('/tareas/{id}', [TareaController::class, 'update'])->name('tareas.update');
    Route::delete('/tareas/{id}', [TareaController::class, 'destroy'])->name('tareas.destroy');

    // ============================================
    // MIS TAREAS - INSTALADOR
    // ============================================
    Route::get('/mis-tareas', [InstaladorTareasController::class, 'index'])->name('instalador.tareas.index');
    Route::get('/mis-tareas/{tarea}/editar', [InstaladorTareasController::class, 'edit'])->name('instalador.tareas.edit');
    Route::put('/mis-tareas/{tarea}', [InstaladorTareasController::class, 'update'])->name('instalador.tareas.update');

    // ============================================
    // PRODUCTOS
    // ============================================
    Route::resource('productos', ProductoController::class);
    Route::post('/productos/{producto}/agregar-stock', [ProductoController::class, 'agregarStock'])->name('productos.agregarStock');
    Route::post('/productos/{producto}/disminuir-stock', [ProductoController::class, 'disminuirStock'])->name('productos.disminuirStock');

    // ============================================
    // PROVEEDORES
    // ============================================
    Route::get('/proveedores', [ProveedorController::class, 'index'])->name('proveedores.index');
    Route::get('/proveedores/create', [ProveedorController::class, 'create'])->name('proveedores.create');
    Route::post('/proveedores', [ProveedorController::class, 'store'])->name('proveedores.store');
    Route::get('/proveedores/{id}', [ProveedorController::class, 'show'])->name('proveedores.show');
    Route::get('/proveedores/{id}/edit', [ProveedorController::class, 'edit'])->name('proveedores.edit');
    Route::put('/proveedores/{id}', [ProveedorController::class, 'update'])->name('proveedores.update');
    Route::delete('/proveedores/{id}', [ProveedorController::class, 'destroy'])->name('proveedores.destroy');
    Route::get('/proveedores/{id}/agregar-productos', [ProveedorController::class, 'agregarProductos'])->name('proveedores.agregarProductos');
    Route::post('/proveedores/{id}/guardar-productos', [ProveedorController::class, 'guardarProductos'])->name('proveedores.guardarProductos');

    // ============================================
    // PRODUCTOS POR PROYECTO
    // ============================================
    Route::get('/proyectos/{proyectoId}/productos', [ProyectoProductoController::class, 'index'])->name('proyectos.productos.index');
    Route::get('/proyectos/{proyectoId}/productos/crear', [ProyectoProductoController::class, 'create'])->name('proyectos.productos.create');
    Route::post('/proyectos/{proyectoId}/productos', [ProyectoProductoController::class, 'store'])->name('proyectos.productos.store');
    Route::get('/proyectos/{proyectoId}/productos/{productoId}/editar', [ProyectoProductoController::class, 'edit'])->name('proyectos.productos.edit');
    Route::put('/proyectos/{proyectoId}/productos/{productoId}', [ProyectoProductoController::class, 'update'])->name('proyectos.productos.update');
    Route::delete('/proyectos/{proyectoId}/productos/{productoId}', [ProyectoProductoController::class, 'destroy'])->name('proyectos.productos.destroy');

    // ============================================
    // USUARIOS (ADMIN ONLY)
    // ============================================
    Route::resource('usuarios', UsuarioController::class);

    // ============================================
    // REPORTES (ADMIN ONLY)
    // ============================================
    Route::get('/reportes/estadisticas', [ReporteController::class, 'estadisticas'])->name('reportes.estadisticas');
    Route::get('/reportes/ventas', [ReporteController::class, 'ventas'])->name('reportes.ventas');
    Route::get('/reportes/inventario', [ReporteController::class, 'inventario'])->name('reportes.inventario');

    // ============================================
    // MIS DISEÑOS - DISEÑADOR
    // ============================================
    Route::get('/mis-disenos', [DiseñadorDisenosController::class, 'index'])->name('diseñador.disenos.index');
    Route::get('/mis-disenos/{diseno}/editar', [DiseñadorDisenosController::class, 'edit'])->name('diseñador.disenos.edit');
    Route::put('/mis-disenos/{diseno}', [DiseñadorDisenosController::class, 'update'])->name('diseñador.disenos.update');

    // ============================================
    // PAGOS
    // ============================================
    Route::get('/pagos', [PagoController::class, 'index'])->name('pagos.index');
    Route::get('/pagos/create', [PagoController::class, 'create'])->name('pagos.create');
    Route::post('/pagos', [PagoController::class, 'store'])->name('pagos.store');
    Route::get('/pagos/{pago}', [PagoController::class, 'show'])->name('pagos.show');
    Route::get('/pagos/{pago}/edit', [PagoController::class, 'edit'])->name('pagos.edit');
    Route::put('/pagos/{pago}', [PagoController::class, 'update'])->name('pagos.update');
    Route::delete('/pagos/{pago}', [PagoController::class, 'destroy'])->name('pagos.destroy');
    Route::post('/pagos/generar-qr', [PagoController::class, 'generarQR'])->name('pagos.generarQR');
    Route::get('/pagos/{pago}/check-transaction-status', [PagoController::class, 'checkTransactionStatus'])->name('pagos.checkTransactionStatus');

    // Gestión de Inventario (redirige a Productos)
    Route::get('/inventario', function() {
        return redirect()->route('productos.index');
    })->name('inventario.index');

    // Perfil de usuario
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// API Routes (sin autenticación para callbacks)
Route::post('/api/page-visit', function(Request $request) {
    $visit = \App\Models\PageVisit::incrementVisit(
        $request->nombre_pagina,
        $request->pagina_url
    );
    return response()->json(['contador_vistas' => $visit->contador_vistas]);
});

// Búsqueda global
Route::middleware(['auth'])->get('/api/search', function(Request $request) {
    $query = $request->input('q');
    
    if (strlen($query) < 3) {
        return response()->json([]);
    }
    
    $results = [
        'clientes' => \App\Models\Cliente::where('nombre', 'LIKE', "%{$query}%")
            ->orWhere('email', 'LIKE', "%{$query}%")
            ->limit(5)->get(),
        'proyectos' => \App\Models\Proyecto::where('nombre', 'LIKE', "%{$query}%")
            ->limit(5)->get(),
        'productos' => \App\Models\Producto::where('nombre', 'LIKE', "%{$query}%")
            ->limit(5)->get(),
    ];
    
    return response()->json($results);
});

require __DIR__.'/auth.php';
