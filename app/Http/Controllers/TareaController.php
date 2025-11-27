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
    public function __construct()
    {
        // JEFE_INSTALADOR: puede crear/editar tareas en sus cronogramas
        // INSTALADOR: puede ver/editar sus propias tareas
        // VENDEDOR: puede crear tareas en sus cronogramas
        // ADMIN: acceso total
        $this->middleware(function ($request, $next) {
            $allowedRoles = ['ADMIN', 'VENDEDOR', 'JEFE_INSTALADOR', 'INSTALADOR'];
            if (!in_array(auth()->user()->rol, $allowedRoles)) {
                abort(403, 'No tienes permisos para acceder a tareas.');
            }
            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     */
    public function index(string $cronogramaId)
    {
        $cronograma = Cronograma::with('proyecto', 'usuario')->findOrFail($cronogramaId);

        // JEFE_INSTALADOR: solo puede ver tareas de sus cronogramas
        if (auth()->user()->rol === 'JEFE_INSTALADOR' && $cronograma->usuario_id !== auth()->id()) {
            abort(403, 'Solo puedes ver tareas de tus cronogramas.');
        }

        // INSTALADOR: puede ver solo si está asignado a una tarea o es su cronograma
        if (auth()->user()->rol === 'INSTALADOR') {
            // Verificar si tiene al menos una tarea en este cronograma
            $hasTarea = Tarea::where('cronograma_id', $cronogramaId)
                ->where('user_id', auth()->id())
                ->exists();
            if (!$hasTarea) {
                abort(403, 'No tienes tareas asignadas en este cronograma.');
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
            abort(403, 'Solo puedes crear tareas en tus cronogramas.');
        }

        // INSTALADOR: no puede crear tareas
        if (auth()->user()->rol === 'INSTALADOR') {
            abort(403, 'Los instaladores no pueden crear tareas.');
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
            abort(403, 'Solo puedes crear tareas en tus cronogramas.');
        }

        // INSTALADOR: no puede crear tareas
        if (auth()->user()->rol === 'INSTALADOR') {
            abort(403, 'Los instaladores no pueden crear tareas.');
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
            abort(403, 'Solo puedes editar tareas de tus cronogramas.');
        }

        // INSTALADOR: solo puede editar sus propias tareas
        if (auth()->user()->rol === 'INSTALADOR' && $tarea->user_id !== auth()->id()) {
            abort(403, 'Solo puedes editar tus propias tareas.');
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
            abort(403, 'Solo puedes editar tareas de tus cronogramas.');
        }

        // INSTALADOR: solo puede editar sus propias tareas
        if (auth()->user()->rol === 'INSTALADOR' && $tarea->user_id !== auth()->id()) {
            abort(403, 'Solo puedes editar tus propias tareas.');
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
            abort(403, 'No tienes permisos para eliminar tareas.');
        }

        $tarea = Tarea::findOrFail($id);

        // JEFE_INSTALADOR: solo puede eliminar tareas de sus cronogramas
        if (auth()->user()->rol === 'JEFE_INSTALADOR' && $tarea->cronograma->usuario_id !== auth()->id()) {
            abort(403, 'Solo puedes eliminar tareas de tus cronogramas.');
        }

        $cronogramaId = $tarea->cronograma_id;
        $tarea->delete();

        return redirect()->route('tareas.index', $cronogramaId)
            ->with('success', 'Tarea eliminada exitosamente');
    }
}
