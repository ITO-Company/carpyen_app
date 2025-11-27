<?php

namespace App\Http\Controllers;

use App\Models\Cronograma;
use App\Models\Proyecto;
use App\Models\User;
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
        $query = Cronograma::with('proyecto', 'usuario');

        // JEFE_INSTALADOR: ver solo cronogramas asignados a él
        if (auth()->user()->rol === 'JEFE_INSTALADOR') {
            $query->where('usuario_id', auth()->id());
        }
        // VENDEDOR: ver todos los cronogramas (acceso a ver)
        // ADMIN: ver todos

        $cronogramas = $query->latest()->paginate(15);
        
        return Inertia::render('Cronogramas/Index', [
            'cronogramas' => $cronogramas
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Solo ADMIN y VENDEDOR pueden crear cronogramas
        if (!in_array(auth()->user()->rol, ['ADMIN', 'VENDEDOR'])) {
            return redirect()->route('dashboard');
        }

        $proyectos = Proyecto::all();
        $usuarios = User::whereIn('rol', ['ADMIN', 'JEFE_INSTALADOR'])->get();
        
        return Inertia::render('Cronogramas/Create', [
            'proyectos' => $proyectos,
            'usuarios' => $usuarios
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'proyecto_id' => 'required|exists:proyectos,id',
            'usuario_id' => 'required|exists:users,id',
            'fecha_inicio' => 'required|date',
            'dias_estimados' => 'required|integer|min:1',
        ], [
            'proyecto_id.required' => 'El proyecto es obligatorio',
            'proyecto_id.exists' => 'El proyecto no existe',
            'usuario_id.required' => 'El usuario es obligatorio',
            'usuario_id.exists' => 'El usuario no existe',
            'fecha_inicio.required' => 'La fecha de inicio es obligatoria',
            'fecha_inicio.date' => 'La fecha de inicio debe ser una fecha válida',
            'dias_estimados.required' => 'Los días estimados son obligatorios',
            'dias_estimados.integer' => 'Los días estimados deben ser un número entero',
            'dias_estimados.min' => 'Los días estimados deben ser al menos 1',
        ]);

        Cronograma::create([
            'proyecto_id' => $validated['proyecto_id'],
            'usuario_id' => $validated['usuario_id'],
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

        // JEFE_INSTALADOR: solo puede editar cronogramas asignados a él
        if (auth()->user()->rol === 'JEFE_INSTALADOR' && $cronograma->usuario_id !== auth()->id()) {
            return redirect()->route('dashboard');
        }

        // VENDEDOR: puede editar cronogramas del proyecto (se le asigna como creador)
        // Asumiendo que VENDEDOR que lo creó puede editarlo
        // ADMIN: puede editar cualquiera

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

        // JEFE_INSTALADOR: solo puede editar cronogramas asignados a él
        if (auth()->user()->rol === 'JEFE_INSTALADOR' && $cronograma->usuario_id !== auth()->id()) {
            return redirect()->route('dashboard');
        }

        // VENDEDOR: no puede editar cronogramas (solo ver)
        if (auth()->user()->rol === 'VENDEDOR') {
            return redirect()->route('dashboard');
        }
        
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
        // Solo ADMIN puede eliminar cronogramas
        if (auth()->user()->rol !== 'ADMIN') {
            return redirect()->route('dashboard');
        }

        $cronograma = Cronograma::findOrFail($id);
        $cronograma->delete();

        return redirect()->route('cronogramas.index')
            ->with('success', 'Cronograma eliminado exitosamente');
    }
}

