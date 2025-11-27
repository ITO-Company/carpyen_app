<?php

namespace App\Http\Controllers;

use App\Models\Cotizacion;
use App\Models\Proyecto;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CotizacionController extends Controller
{
    public function __construct()
    {
        // Solo ADMIN y VENDEDOR pueden acceder a cotizaciones
        $this->middleware(function ($request, $next) {
            if (!in_array(auth()->user()->rol, ['ADMIN', 'VENDEDOR'])) {
                abort(403, 'No tienes permisos para acceder a cotizaciones.');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $query = Cotizacion::with('proyecto.cliente');

        // VENDEDOR: solo ver cotizaciones de sus propios proyectos
        if (auth()->user()->rol === 'VENDEDOR') {
            $query->whereHas('proyecto', function ($q) {
                $q->where('user_id', auth()->id());
            });
        }

        $cotizaciones = $query->orderBy('created_at', 'desc')
            ->paginate(10);

        return Inertia::render('Cotizaciones/Index', [
            'cotizaciones' => $cotizaciones
        ]);
    }

    public function create()
    {
        $proyectos = Proyecto::with('cliente')->get();
        
        return Inertia::render('Cotizaciones/Create', [
            'proyectos' => $proyectos
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'proyecto_id' => 'required|exists:proyectos,id',
            'tipo_metro' => 'required|in:lineal,cuadrado',
            'costo_metro' => 'nullable|numeric|min:0',
            'cantidad_metro' => 'nullable|integer|min:0',
            'costo_mueble' => 'nullable|numeric|min:0',
            'mueble_numero' => 'nullable|integer|min:0',
            'total' => 'required|numeric|min:0',
            'estado' => 'required|in:pendiente,aprobada,rechazada',
            'comentario' => 'nullable|string'
        ]);

        Cotizacion::create($validated);

        return redirect()->route('cotizaciones.index')
            ->with('success', 'Cotización creada exitosamente');
    }

    public function edit(Cotizacion $cotizacion)
    {
        // VENDEDOR: solo puede editar sus propias cotizaciones
        if (auth()->user()->rol === 'VENDEDOR') {
            if ($cotizacion->proyecto->user_id !== auth()->id()) {
                abort(403, 'Solo puedes editar tus propias cotizaciones.');
            }
        }

        $proyectos = Proyecto::with('cliente')->get();
        
        return Inertia::render('Cotizaciones/Edit', [
            'cotizacion' => $cotizacion->load('proyecto'),
            'proyectos' => $proyectos
        ]);
    }

    public function update(Request $request, Cotizacion $cotizacion)
    {
        // VENDEDOR: solo puede editar sus propias cotizaciones
        if (auth()->user()->rol === 'VENDEDOR') {
            if ($cotizacion->proyecto->user_id !== auth()->id()) {
                abort(403, 'Solo puedes editar tus propias cotizaciones.');
            }
        }
        $validated = $request->validate([
            'proyecto_id' => 'required|exists:proyectos,id',
            'tipo_metro' => 'required|in:lineal,cuadrado',
            'costo_metro' => 'nullable|numeric|min:0',
            'cantidad_metro' => 'nullable|integer|min:0',
            'costo_mueble' => 'nullable|numeric|min:0',
            'mueble_numero' => 'nullable|integer|min:0',
            'total' => 'required|numeric|min:0',
            'estado' => 'required|in:pendiente,aprobada,rechazada',
            'comentario' => 'nullable|string'
        ]);

        $cotizacion->update($validated);

        return redirect()->route('cotizaciones.index')
            ->with('success', 'Cotización actualizada exitosamente');
    }

    public function destroy(Cotizacion $cotizacion)
    {
        // Solo ADMIN puede eliminar cotizaciones
        if (auth()->user()->rol !== 'ADMIN') {
            abort(403, 'Solo administradores pueden eliminar cotizaciones.');
        }

        $cotizacion->delete();

        return redirect()->route('cotizaciones.index')
            ->with('success', 'Cotización eliminada exitosamente');
    }

    /**
     * Métodos para Cotizaciones por Proyecto
     */
    
    public function byProyecto(Proyecto $proyecto)
    {
        $cotizaciones = $proyecto->cotizaciones()
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return Inertia::render('Cotizaciones/ByProyecto', [
            'proyecto' => $proyecto,
            'cotizaciones' => $cotizaciones
        ]);
    }

    public function createByProyecto(Proyecto $proyecto)
    {
        return Inertia::render('Cotizaciones/CreateByProyecto', [
            'proyecto' => $proyecto
        ]);
    }

    public function storeByProyecto(Request $request, Proyecto $proyecto)
    {
        // VENDEDOR: solo puede crear cotizaciones en sus propios proyectos
        if (auth()->user()->rol === 'VENDEDOR' && $proyecto->user_id !== auth()->id()) {
            abort(403, 'Solo puedes crear cotizaciones en tus propios proyectos.');
        }

        $validated = $request->validate([
            'tipo_metro' => 'required|in:lineal,cuadrado',
            'costo_metro' => 'nullable|numeric|min:0',
            'cantidad_metro' => 'nullable|integer|min:0',
            'costo_mueble' => 'nullable|numeric|min:0',
            'mueble_numero' => 'nullable|integer|min:0',
            'total' => 'required|numeric|min:0',
            'estado' => 'required|in:pendiente,aprobada,rechazada',
            'comentario' => 'nullable|string'
        ]);

        $validated['proyecto_id'] = $proyecto->id;
        $validated['estado'] = 'pendiente';

        Cotizacion::create($validated);

        return redirect()->route('proyectos.cotizaciones.index', $proyecto->id)
            ->with('success', 'Cotización creada exitosamente');
    }

    public function editByProyecto(Proyecto $proyecto, Cotizacion $cotizacion)
    {
        // Verificar que la cotización pertenece al proyecto
        if ($cotizacion->proyecto_id !== $proyecto->id) {
            abort(404);
        }

        return Inertia::render('Cotizaciones/EditByProyecto', [
            'proyecto' => $proyecto,
            'cotizacion' => $cotizacion
        ]);
    }

    public function updateByProyecto(Request $request, Proyecto $proyecto, Cotizacion $cotizacion)
    {
        // Verificar que la cotización pertenece al proyecto
        if ($cotizacion->proyecto_id !== $proyecto->id) {
            abort(404);
        }

        // VENDEDOR: solo puede editar sus propias cotizaciones
        if (auth()->user()->rol === 'VENDEDOR' && $proyecto->user_id !== auth()->id()) {
            abort(403, 'Solo puedes editar cotizaciones de tus propios proyectos.');
        }

        $validated = $request->validate([
            'tipo_metro' => 'required|in:lineal,cuadrado',
            'costo_metro' => 'nullable|numeric|min:0',
            'cantidad_metro' => 'nullable|integer|min:0',
            'costo_mueble' => 'nullable|numeric|min:0',
            'mueble_numero' => 'nullable|integer|min:0',
            'total' => 'required|numeric|min:0',
            'estado' => 'required|in:pendiente,aprobada,rechazada',
            'comentario' => 'nullable|string'
        ]);

        $cotizacion->update($validated);

        return redirect()->route('proyectos.cotizaciones.index', $proyecto->id)
            ->with('success', 'Cotización actualizada exitosamente');
    }

    public function destroyByProyecto(Proyecto $proyecto, Cotizacion $cotizacion)
    {
        // Verificar que la cotización pertenece al proyecto
        if ($cotizacion->proyecto_id !== $proyecto->id) {
            abort(404);
        }

        $cotizacion->delete();

        return redirect()->route('proyectos.cotizaciones.index', $proyecto->id)
            ->with('success', 'Cotización eliminada exitosamente');
    }
}