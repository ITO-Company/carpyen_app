<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
use App\Models\Cliente;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProyectoController extends Controller
{
    public function index()
    {
        $proyectos = Proyecto::with(['cliente', 'vendedor'])
            ->latest()
            ->paginate(10);
        
        return Inertia::render('Proyectos/Index', [
            'proyectos' => $proyectos
        ]);
    }

    public function create()
    {
        $clientes = Cliente::all();
        $vendedores = User::where('rol', 'VENDEDOR')
            ->orWhere('rol', 'ADMIN')
            ->get();
        
        return Inertia::render('Proyectos/Create', [
            'clientes' => $clientes,
            'vendedores' => $vendedores
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'ubicacion' => 'nullable|string',
            'cliente_id' => 'required|exists:clientes,id',
            'user_id' => 'nullable|exists:users,id',
        ], [
            'nombre.required' => 'El nombre del proyecto es obligatorio',
            'cliente_id.required' => 'Debe seleccionar un cliente',
            'cliente_id.exists' => 'El cliente seleccionado no existe',
        ]);

        // Asignar estado por defecto
        $validated['estado'] = 'pendiente';

        Proyecto::create($validated);

        return redirect()->route('proyectos.index')
            ->with('success', 'Proyecto creado exitosamente');
    }

    public function show(Proyecto $proyecto)
    {
        $proyecto->load(['cliente', 'vendedor', 'cotizaciones', 'cronogramas', 'planPagos']);
        
        return Inertia::render('Proyectos/Show', [
            'proyecto' => $proyecto
        ]);
    }

    public function edit(Proyecto $proyecto)
    {
        $clientes = Cliente::all();
        $vendedores = User::where('rol', 'VENDEDOR')
            ->orWhere('rol', 'ADMIN')
            ->get();
        
        return Inertia::render('Proyectos/Edit', [
            'proyecto' => $proyecto,
            'clientes' => $clientes,
            'vendedores' => $vendedores
        ]);
    }

    public function update(Request $request, Proyecto $proyecto)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'ubicacion' => 'nullable|string',
            'estado' => 'required|in:pendiente,en_proceso,completado,cancelado',
            'cliente_id' => 'required|exists:clientes,id',
            'user_id' => 'nullable|exists:users,id',
        ]);

        $proyecto->update($validated);

        return redirect()->route('proyectos.index')
            ->with('success', 'Proyecto actualizado exitosamente');
    }

    public function destroy(Proyecto $proyecto)
    {
        $proyecto->delete();

        return redirect()->route('proyectos.index')
            ->with('success', 'Proyecto eliminado exitosamente');
    }
}
