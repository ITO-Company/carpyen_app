<?php

namespace App\Http\Controllers;

use App\Models\Diseno;
use App\Models\Proyecto;
use App\Models\Cotizacion;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DisenoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $disenos = Diseno::with('proyecto')->latest()->paginate(15);
        
        return Inertia::render('Disenos/Index', [
            'disenos' => $disenos
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $proyectos = Proyecto::all();
        
        return Inertia::render('Disenos/Create', [
            'proyectos' => $proyectos
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'proyecto_id' => 'required|exists:proyectos,id',
            'descripcion' => 'nullable|string',
            'estado' => 'required|in:pendiente,en_proceso,completado,rechazado',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date',
        ]);

        Diseno::create($validated);

        return redirect()->route('disenos.index')
            ->with('success', 'Diseño creado exitosamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $diseno = Diseno::with('proyecto')->findOrFail($id);
        return Inertia::render('Disenos/Show', [
            'diseno' => $diseno
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $diseno = Diseno::findOrFail($id);
        $proyectos = Proyecto::all();
        
        return Inertia::render('Disenos/Edit', [
            'diseno' => $diseno,
            'proyectos' => $proyectos
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $diseno = Diseno::findOrFail($id);
        
        $validated = $request->validate([
            'proyecto_id' => 'required|exists:proyectos,id',
            'descripcion' => 'nullable|string',
            'estado' => 'required|in:pendiente,en_proceso,completado,rechazado',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date',
        ]);

        $diseno->update($validated);

        return redirect()->route('disenos.index')
            ->with('success', 'Diseño actualizado exitosamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $diseno = Diseno::findOrFail($id);
        $diseno->delete();

        return redirect()->route('disenos.index')
            ->with('success', 'Diseño eliminado exitosamente');
    }

    /**
     * Métodos para Diseños por Cotización
     */

    public function byCotizacion(Cotizacion $cotizacion)
    {
        $disenos = $cotizacion->disenos()
            ->with('diseñador')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return Inertia::render('Disenos/ByCotizacion', [
            'cotizacion' => $cotizacion,
            'proyecto' => $cotizacion->proyecto,
            'disenos' => $disenos
        ]);
    }

    public function createByCotizacion(Cotizacion $cotizacion)
    {
        $diseñadores = \App\Models\User::where('rol', 'DISEÑADOR')
            ->orWhere('rol', 'ADMIN')
            ->get();

        return Inertia::render('Disenos/CreateByCotizacion', [
            'cotizacion' => $cotizacion,
            'proyecto' => $cotizacion->proyecto,
            'diseñadores' => $diseñadores
        ]);
    }

    public function storeByCotizacion(Request $request, Cotizacion $cotizacion)
    {
        $validated = $request->validate([
            'url_render' => 'nullable|string|max:500',
            'plano_iluminador' => 'nullable|string',
            'user_id' => 'nullable|exists:users,id',
            'descripcion' => 'nullable|string',
            'estado' => 'required|in:pendiente,en_proceso,completado,rechazado',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date',
        ]);

        $validated['cotizacion_id'] = $cotizacion->id;
        $validated['proyecto_id'] = $cotizacion->proyecto_id;

        Diseno::create($validated);

        return redirect()->route('cotizaciones.disenos.index', $cotizacion->id)
            ->with('success', 'Diseño creado exitosamente');
    }

    public function editByCotizacion(Cotizacion $cotizacion, Diseno $diseno)
    {
        // Verificar que el diseño pertenece a la cotización
        if ($diseno->cotizacion_id !== $cotizacion->id) {
            abort(404);
        }

        $diseñadores = \App\Models\User::where('rol', 'DISEÑADOR')
            ->orWhere('rol', 'ADMIN')
            ->get();

        return Inertia::render('Disenos/EditByCotizacion', [
            'cotizacion' => $cotizacion,
            'proyecto' => $cotizacion->proyecto,
            'diseno' => $diseno,
            'diseñadores' => $diseñadores
        ]);
    }

    public function updateByCotizacion(Request $request, Cotizacion $cotizacion, Diseno $diseno)
    {
        // Verificar que el diseño pertenece a la cotización
        if ($diseno->cotizacion_id !== $cotizacion->id) {
            abort(404);
        }

        $validated = $request->validate([
            'url_render' => 'nullable|string|max:500',
            'plano_iluminador' => 'nullable|string',
            'user_id' => 'nullable|exists:users,id',
            'descripcion' => 'nullable|string',
            'estado' => 'required|in:pendiente,en_proceso,completado,rechazado',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date',
        ]);

        $diseno->update($validated);

        return redirect()->route('cotizaciones.disenos.index', $cotizacion->id)
            ->with('success', 'Diseño actualizado exitosamente');
    }

    public function destroyByCotizacion(Cotizacion $cotizacion, Diseno $diseno)
    {
        // Verificar que el diseño pertenece a la cotización
        if ($diseno->cotizacion_id !== $cotizacion->id) {
            abort(404);
        }

        $diseno->delete();

        return redirect()->route('cotizaciones.disenos.index', $cotizacion->id)
            ->with('success', 'Diseño eliminado exitosamente');
    }
}