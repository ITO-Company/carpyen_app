<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ProyectoController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\DisenoController;
use App\Http\Controllers\CronogramaController;
use App\Http\Controllers\PagoController;
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

    // Gestión de Inventario
    Route::get('/inventario', [InventarioController::class, 'index'])->name('inventario.index');

    // Gestión de Diseños (CU4)
    Route::resource('disenos', DisenoController::class);

    // Gestión de Cronogramas y Tareas (CU5)
    Route::resource('cronogramas', CronogramaController::class);

    // Gestión de Pagos (CU7)
    Route::resource('pagos', PagoController::class);
    Route::post('/pagos/generar-qr', [PagoController::class, 'generarQR'])->name('pagos.generar-qr');
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
