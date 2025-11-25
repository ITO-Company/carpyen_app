<?php

namespace App\Http\Controllers;

use App\Models\Cotizacion;
use App\Models\Proyecto;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CotizacionController extends Controller
{
    public function index()
    {
        $cotizaciones = Cotizacion::with('proyecto.cliente')
            ->orderBy('created_at', 'desc')
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
            'hipo_metro' => 'nullable|numeric|min:0',
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
        $proyectos = Proyecto::with('cliente')->get();
        
        return Inertia::render('Cotizaciones/Edit', [
            'cotizacion' => $cotizacion->load('proyecto'),
            'proyectos' => $proyectos
        ]);
    }

    public function update(Request $request, Cotizacion $cotizacion)
    {
        $validated = $request->validate([
            'proyecto_id' => 'required|exists:proyectos,id',
            'hipo_metro' => 'nullable|numeric|min:0',
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
        $cotizacion->delete();

        return redirect()->route('cotizaciones.index')
            ->with('success', 'Cotización eliminada exitosamente');
    }
}
