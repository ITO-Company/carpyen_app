<?php

namespace App\Http\Controllers;

use App\Models\Tarea;
use App\Models\Cronograma;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InstaladorTareasController extends Controller
{
    /**
     * Display a listing of the installer's assigned tasks.
     */
    public function index(Request $request)
    {
        // Solo INSTALADOR puede acceder
        if (auth()->user()->rol !== 'INSTALADOR') {
            return redirect()->route('dashboard');
        }

        $usuario_id = auth()->id();

        // Obtener todas las tareas asignadas al instalador actual
        $tareas = Tarea::with(['cronograma' => function($query) {
            $query->with('proyecto');
        }])
        ->where('user_id', $usuario_id)
        ->orderBy('created_at', 'desc')
        ->paginate(15);

        return Inertia::render('Instalador/Tareas/Index', [
            'tareas' => $tareas,
        ]);
    }

    /**
     * Show the form for editing the specified task.
     */
    public function edit(Tarea $tarea, Request $request)
    {
        // Solo INSTALADOR puede acceder
        if (auth()->user()->rol !== 'INSTALADOR') {
            return redirect()->route('dashboard');
        }

        // Verificar que la tarea pertenezca al usuario actual
        if ($tarea->user_id !== auth()->id()) {
            return redirect()->route('dashboard');
        }

        $tarea->load('cronograma.proyecto');

        return Inertia::render('Instalador/Tareas/Edit', [
            'tarea' => $tarea,
        ]);
    }

    /**
     * Update the specified task in storage.
     */
    public function update(Tarea $tarea, Request $request)
    {
        // Solo INSTALADOR puede acceder
        if (auth()->user()->rol !== 'INSTALADOR') {
            return redirect()->route('dashboard');
        }

        // Verificar que la tarea pertenezca al usuario actual
        if ($tarea->user_id !== auth()->id()) {
            return redirect()->route('dashboard');
        }

        $validated = $request->validate([
            'descripcion' => 'required|string|max:1000',
            'estado' => 'required|in:pendiente,en_progreso,completada,bloqueada',
        ]);

        $tarea->update($validated);

        return redirect()->route('instalador.tareas.index')->with('success', 'Tarea actualizada correctamente');
    }
}
