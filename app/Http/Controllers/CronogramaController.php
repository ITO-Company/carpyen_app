<?php

namespace App\Http\Controllers;

use App\Models\Cronograma;
use App\Models\Proyecto;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class CronogramaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cronogramas = Cronograma::with('proyecto', 'usuario')->latest()->paginate(15);
        
        return Inertia::render('Cronogramas/Index', [
            'cronogramas' => $cronogramas
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $proyectos = Proyecto::all();
        
        return Inertia::render('Cronogramas/Create', [
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
            'fecha_inicio' => 'required|date',
            'dias_estimados' => 'required|integer|min:1',
        ], [
            'proyecto_id.required' => 'El proyecto es obligatorio',
            'proyecto_id.exists' => 'El proyecto no existe',
            'fecha_inicio.required' => 'La fecha de inicio es obligatoria',
            'fecha_inicio.date' => 'La fecha de inicio debe ser una fecha válida',
            'dias_estimados.required' => 'Los días estimados son obligatorios',
            'dias_estimados.integer' => 'Los días estimados deben ser un número entero',
            'dias_estimados.min' => 'Los días estimados deben ser al menos 1',
        ]);

        Cronograma::create([
            'proyecto_id' => $validated['proyecto_id'],
            'usuario_id' => auth()->id(),
            'fecha_inicio' => $validated['fecha_inicio'],
            'fecha_fin' => null,
            'dias_estimados' => $validated['dias_estimados'],
            'estado' => 'pendiente',
        ]);

        return redirect()->route('cronogramas.index')
            ->with('success', 'Cronograma creado exitosamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $cronograma = Cronograma::with('proyecto', 'usuario', 'tareas')->findOrFail($id);
        return Inertia::render('Cronogramas/Show', [
            'cronograma' => $cronograma
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $cronograma = Cronograma::with('usuario')->findOrFail($id);
        $proyectos = Proyecto::all();
        
        return Inertia::render('Cronogramas/Edit', [
            'cronograma' => $cronograma,
            'proyectos' => $proyectos
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $cronograma = Cronograma::findOrFail($id);
        
        $validated = $request->validate([
            'proyecto_id' => 'required|exists:proyectos,id',
            'fecha_inicio' => 'required|date',
            'dias_estimados' => 'required|integer|min:1',
            'estado' => 'required|in:pendiente,en_curso,completado,atrasado',
        ], [
            'estado.required' => 'El estado es obligatorio',
            'estado.in' => 'El estado debe ser uno de: pendiente, en_curso, completado, atrasado',
        ]);

        $data = $validated;

        // Si el estado es completado y la fecha_fin es null, asignar la fecha actual
        if ($validated['estado'] === 'completado' && is_null($cronograma->fecha_fin)) {
            $data['fecha_fin'] = Carbon::now()->toDateString();
        }

        $cronograma->update($data);

        return redirect()->route('cronogramas.index')
            ->with('success', 'Cronograma actualizado exitosamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $cronograma = Cronograma::findOrFail($id);
        $cronograma->delete();

        return redirect()->route('cronogramas.index')
            ->with('success', 'Cronograma eliminado exitosamente');
    }
}

