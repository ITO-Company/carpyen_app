<?php

namespace App\Http\Controllers;

use App\Models\Cronograma;
use App\Models\Proyecto;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CronogramaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cronogramas = Cronograma::with('proyecto')->latest()->paginate(15);
        
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
            'fecha_fin' => 'required|date|after:fecha_inicio',
            'descripcion' => 'nullable|string',
        ]);

        Cronograma::create($validated);

        return redirect()->route('cronogramas.index')
            ->with('success', 'Cronograma creado exitosamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $cronograma = Cronograma::with('proyecto', 'tareas')->findOrFail($id);
        return Inertia::render('Cronogramas/Show', [
            'cronograma' => $cronograma
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $cronograma = Cronograma::findOrFail($id);
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
            'fecha_fin' => 'required|date|after:fecha_inicio',
            'descripcion' => 'nullable|string',
        ]);

        $cronograma->update($validated);

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
