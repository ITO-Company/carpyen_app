<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ProyectoController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\CotizacionController;
use App\Http\Controllers\DisenoController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\CronogramaController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\PlanPagoController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\UsuarioController;
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

    // Gestión de Usuarios (CU1)
    Route::resource('usuarios', UsuarioController::class);

    // Gestión de Clientes y Proyectos (CU2)
    Route::resource('clientes', ClienteController::class);
    Route::resource('proyectos', ProyectoController::class);

    // Gestión de Productos (CU3)
    Route::resource('productos', ProductoController::class);
    Route::post('/productos/{producto}/agregar-stock', [ProductoController::class, 'agregarStock'])->name('productos.agregarStock');
    Route::post('/productos/{producto}/disminuir-stock', [ProductoController::class, 'disminuirStock'])->name('productos.disminuirStock');

    // Gestión de Proveedores
    Route::get('/proveedores', [ProveedorController::class, 'index'])->name('proveedores.index');
    Route::get('/proveedores/create', [ProveedorController::class, 'create'])->name('proveedores.create');
    Route::post('/proveedores', [ProveedorController::class, 'store'])->name('proveedores.store');
    Route::get('/proveedores/{id}', [ProveedorController::class, 'show'])->name('proveedores.show');
    Route::get('/proveedores/{id}/edit', [ProveedorController::class, 'edit'])->name('proveedores.edit');
    Route::put('/proveedores/{id}', [ProveedorController::class, 'update'])->name('proveedores.update');
    Route::delete('/proveedores/{id}', [ProveedorController::class, 'destroy'])->name('proveedores.destroy');
    Route::get('/proveedores/{id}/agregar-productos', [ProveedorController::class, 'agregarProductos'])->name('proveedores.agregarProductos');
    Route::post('/proveedores/{id}/guardar-productos', [ProveedorController::class, 'guardarProductos'])->name('proveedores.guardarProductos');

    // Gestión de Inventario (redirige a Productos)
    Route::get('/inventario', function() {
        return redirect()->route('productos.index');
    })->name('inventario.index');

    // Gestión de Cotizaciones por Proyecto (CU4)
    Route::get('/proyectos/{proyecto}/cotizaciones', [CotizacionController::class, 'byProyecto'])->name('proyectos.cotizaciones.index');
    Route::get('/proyectos/{proyecto}/cotizaciones/crear', [CotizacionController::class, 'createByProyecto'])->name('proyectos.cotizaciones.create');
    Route::post('/proyectos/{proyecto}/cotizaciones', [CotizacionController::class, 'storeByProyecto'])->name('proyectos.cotizaciones.store');
    Route::get('/proyectos/{proyecto}/cotizaciones/{cotizacion}/editar', [CotizacionController::class, 'editByProyecto'])->name('proyectos.cotizaciones.edit');
    Route::put('/proyectos/{proyecto}/cotizaciones/{cotizacion}', [CotizacionController::class, 'updateByProyecto'])->name('proyectos.cotizaciones.update');
    Route::delete('/proyectos/{proyecto}/cotizaciones/{cotizacion}', [CotizacionController::class, 'destroyByProyecto'])->name('proyectos.cotizaciones.destroy');

    // Gestión de Diseños por Cotización (CU5)
    Route::get('/cotizaciones/{cotizacion}/disenos', [DisenoController::class, 'byCotizacion'])->name('cotizaciones.disenos.index');
    Route::get('/cotizaciones/{cotizacion}/disenos/crear', [DisenoController::class, 'createByCotizacion'])->name('cotizaciones.disenos.create');
    Route::post('/cotizaciones/{cotizacion}/disenos', [DisenoController::class, 'storeByCotizacion'])->name('cotizaciones.disenos.store');
    Route::get('/cotizaciones/{cotizacion}/disenos/{diseno}/editar', [DisenoController::class, 'editByCotizacion'])->name('cotizaciones.disenos.edit');
    Route::put('/cotizaciones/{cotizacion}/disenos/{diseno}', [DisenoController::class, 'updateByCotizacion'])->name('cotizaciones.disenos.update');
    Route::delete('/cotizaciones/{cotizacion}/disenos/{diseno}', [DisenoController::class, 'destroyByCotizacion'])->name('cotizaciones.disenos.destroy');

    // Gestión de Diseños (CU5 - Admin)
    Route::resource('disenos', DisenoController::class);

    // Gestión de Cronogramas y Tareas (CU6)

    // Gestión de Planes de Pago (CU6)
    Route::resource('planesPago', PlanPagoController::class);
    Route::get('/planesPago/{planPago}/pagos', [PlanPagoController::class, 'getPagos'])->name('planesPago.getPagos');

    // Gestión de Pagos (CU7)
    Route::resource('pagos', PagoController::class);
    Route::post('/pagos/generar-qr', [PagoController::class, 'generarQR'])->name('pagos.generarQR');
    Route::get('/pagos/{pago}/check-transaction-status', [PagoController::class, 'checkTransactionStatus'])->name('pagos.checkTransactionStatus');
    Route::post('/pagos/callback', [PagoController::class, 'callback'])->name('pagos.callback');
    Route::get('/pagos/return', [PagoController::class, 'return'])->name('pagos.return');

    // Reportes y Estadísticas (CU8)
    Route::get('/reportes/estadisticas', [ReporteController::class, 'estadisticas'])->name('reportes.estadisticas');
    Route::get('/reportes/ventas', [ReporteController::class, 'ventas'])->name('reportes.ventas');
    Route::get('/reportes/inventario', [ReporteController::class, 'inventario'])->name('reportes.inventario');

    // Perfil de usuario
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// API Routes (sin autenticación para callbacks)
Route::post('/api/page-visit', function(Request $request) {
    $visit = \App\Models\PageVisit::incrementVisit(
        $request->page_name,
        $request->page_url
    );
    return response()->json(['visit_count' => $visit->visit_count]);
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
