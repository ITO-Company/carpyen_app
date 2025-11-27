<?php

namespace App\Http\Controllers;

use App\Models\Tarea;
use App\Models\Cronograma;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class TareaController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index(string $cronogramaId)
    {
        $cronograma = Cronograma::with('proyecto', 'usuario')->findOrFail($cronogramaId);

        // JEFE_INSTALADOR: solo puede ver tareas de sus cronogramas
        if (auth()->user()->rol === 'JEFE_INSTALADOR' && $cronograma->usuario_id !== auth()->id()) {
            return redirect()->route('dashboard');
        }

        // INSTALADOR: puede ver solo si está asignado a una tarea o es su cronograma
        if (auth()->user()->rol === 'INSTALADOR') {
            // Verificar si tiene al menos una tarea en este cronograma
            $hasTarea = Tarea::where('cronograma_id', $cronogramaId)
                ->where('user_id', auth()->id())
                ->exists();
            if (!$hasTarea) {
                return redirect()->route('dashboard');
            }
        }

        $tareas = Tarea::where('cronograma_id', $cronogramaId)
            ->with('instalador')
            ->latest()
            ->get();
        
        return Inertia::render('Tareas/Index', [
            'cronograma' => $cronograma,
            'tareas' => $tareas
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(string $cronogramaId)
    {
        $cronograma = Cronograma::with('proyecto', 'usuario')->findOrFail($cronogramaId);

        // JEFE_INSTALADOR: solo puede crear tareas en sus cronogramas
        if (auth()->user()->rol === 'JEFE_INSTALADOR' && $cronograma->usuario_id !== auth()->id()) {
            return redirect()->route('dashboard');
        }

        // INSTALADOR: no puede crear tareas
        if (auth()->user()->rol === 'INSTALADOR') {
            return redirect()->route('dashboard');
        }

        $usuarios = User::whereIn('rol', ['ADMIN', 'JEFE_INSTALADOR', 'INSTALADOR'])->get();
        
        return Inertia::render('Tareas/Create', [
            'cronograma' => $cronograma,
            'usuarios' => $usuarios
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, string $cronogramaId)
    {
        $cronograma = Cronograma::findOrFail($cronogramaId);

        // JEFE_INSTALADOR: solo puede crear tareas en sus cronogramas
        if (auth()->user()->rol === 'JEFE_INSTALADOR' && $cronograma->usuario_id !== auth()->id()) {
            return redirect()->route('dashboard');
        }

        // INSTALADOR: no puede crear tareas
        if (auth()->user()->rol === 'INSTALADOR') {
            return redirect()->route('dashboard');
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'fecha' => 'required|date|after_or_equal:' . $cronograma->fecha_inicio,
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
            'descripcion' => 'required|string|min:5',
        ], [
            'user_id.required' => 'El usuario es obligatorio',
            'user_id.exists' => 'El usuario no existe',
            'fecha.required' => 'La fecha es obligatoria',
            'fecha.date' => 'La fecha debe ser una fecha válida',
            'fecha.after_or_equal' => 'La fecha debe ser igual o posterior a la fecha de inicio del cronograma (' . $cronograma->fecha_inicio . ')',
            'hora_inicio.required' => 'La hora de inicio es obligatoria',
            'hora_inicio.date_format' => 'La hora de inicio debe estar en formato HH:MM',
            'hora_fin.required' => 'La hora de fin es obligatoria',
            'hora_fin.date_format' => 'La hora de fin debe estar en formato HH:MM',
            'hora_fin.after' => 'La hora de fin debe ser posterior a la hora de inicio',
            'descripcion.required' => 'La descripción es obligatoria',
            'descripcion.min' => 'La descripción debe tener al menos 5 caracteres',
        ]);

        Tarea::create([
            'cronograma_id' => $cronogramaId,
            'user_id' => $validated['user_id'],
            'fecha' => $validated['fecha'],
            'hora_inicio' => $validated['hora_inicio'],
            'hora_fin' => $validated['hora_fin'],
            'descripcion' => $validated['descripcion'],
            'estado' => 'pendiente',
        ]);

        return redirect()->route('tareas.index', $cronogramaId)
            ->with('success', 'Tarea creada exitosamente');
    }    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $tarea = Tarea::with('cronograma', 'instalador')->findOrFail($id);
        return Inertia::render('Tareas/Show', [
            'tarea' => $tarea
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $tarea = Tarea::with('cronograma.proyecto', 'cronograma.usuario', 'instalador')->findOrFail($id);

        // JEFE_INSTALADOR: solo puede editar tareas en sus cronogramas
        if (auth()->user()->rol === 'JEFE_INSTALADOR' && $tarea->cronograma->usuario_id !== auth()->id()) {
            return redirect()->route('dashboard');
        }

        // INSTALADOR: solo puede editar sus propias tareas
        if (auth()->user()->rol === 'INSTALADOR' && $tarea->user_id !== auth()->id()) {
            return redirect()->route('dashboard');
        }

        $usuarios = User::whereIn('rol', ['ADMIN', 'JEFE_INSTALADOR', 'INSTALADOR'])->get();
        
        return Inertia::render('Tareas/Edit', [
            'tarea' => $tarea,
            'cronograma' => $tarea->cronograma,
            'usuarios' => $usuarios
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $tarea = Tarea::findOrFail($id);
        $cronograma = $tarea->cronograma;

        // JEFE_INSTALADOR: solo puede editar tareas en sus cronogramas
        if (auth()->user()->rol === 'JEFE_INSTALADOR' && $cronograma->usuario_id !== auth()->id()) {
            return redirect()->route('dashboard');
        }

        // INSTALADOR: solo puede editar sus propias tareas
        if (auth()->user()->rol === 'INSTALADOR' && $tarea->user_id !== auth()->id()) {
            return redirect()->route('dashboard');
        }
        
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'fecha' => 'required|date|after_or_equal:' . $cronograma->fecha_inicio,
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
            'descripcion' => 'required|string|min:5',
            'estado' => 'required|in:pendiente,en_proceso,completada,cancelada',
        ], [
            'user_id.required' => 'El usuario es obligatorio',
            'user_id.exists' => 'El usuario no existe',
            'fecha.required' => 'La fecha es obligatoria',
            'fecha.date' => 'La fecha debe ser una fecha válida',
            'fecha.after_or_equal' => 'La fecha debe ser igual o posterior a la fecha de inicio del cronograma (' . $cronograma->fecha_inicio . ')',
            'hora_inicio.required' => 'La hora de inicio es obligatoria',
            'hora_inicio.date_format' => 'La hora de inicio debe estar en formato HH:MM',
            'hora_fin.required' => 'La hora de fin es obligatoria',
            'hora_fin.date_format' => 'La hora de fin debe estar en formato HH:MM',
            'hora_fin.after' => 'La hora de fin debe ser posterior a la hora de inicio',
            'descripcion.required' => 'La descripción es obligatoria',
            'descripcion.min' => 'La descripción debe tener al menos 5 caracteres',
            'estado.required' => 'El estado es obligatorio',
            'estado.in' => 'El estado debe ser uno de: pendiente, en_proceso, completada, cancelada',
        ]);

        $tarea->update($validated);

        return redirect()->route('tareas.index', $tarea->cronograma_id)
            ->with('success', 'Tarea actualizada exitosamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Solo ADMIN y JEFE_INSTALADOR pueden eliminar tareas
        if (!in_array(auth()->user()->rol, ['ADMIN', 'JEFE_INSTALADOR'])) {
            return redirect()->route('dashboard');
        }

        $tarea = Tarea::findOrFail($id);

        // JEFE_INSTALADOR: solo puede eliminar tareas de sus cronogramas
        if (auth()->user()->rol === 'JEFE_INSTALADOR' && $tarea->cronograma->usuario_id !== auth()->id()) {
            return redirect()->route('dashboard');
        }

        $cronogramaId = $tarea->cronograma_id;
        $tarea->delete();

        return redirect()->route('tareas.index', $cronogramaId)
            ->with('success', 'Tarea eliminada exitosamente');
    }
}
