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
        // ADMIN, VENDEDOR, JEFE_INSTALADOR e INSTALADOR pueden ver proyectos
        if (!in_array(auth()->user()->rol, ['ADMIN', 'VENDEDOR', 'JEFE_INSTALADOR', 'INSTALADOR'])) {
            return redirect()->route('dashboard');
        }

        $query = Proyecto::with(['cliente', 'vendedor']);

        // VENDEDOR: ver solo sus propios proyectos
        if (auth()->user()->rol === 'VENDEDOR') {
            $query->where('user_id', auth()->id());
        }
        // JEFE_INSTALADOR e INSTALADOR: ver todos los proyectos
        // ADMIN: ver todos los proyectos

        $proyectos = $query->latest()->paginate(10);
        
        return Inertia::render('Proyectos/Index', [
            'proyectos' => $proyectos
        ]);
    }

    public function create()
    {
        // ADMIN y VENDEDOR pueden crear
        if (!in_array(auth()->user()->rol, ['ADMIN', 'VENDEDOR'])) {
            return redirect()->route('dashboard');
        }

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
        // Solo ADMIN y VENDEDOR pueden crear proyectos
        if (!in_array(auth()->user()->rol, ['ADMIN', 'VENDEDOR'])) {
            return redirect()->route('dashboard');
        }

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

        // Asignar estado por defecto y usuario actual si no se especifica
        $validated['estado'] = 'pendiente';
        if (auth()->user()->rol === 'VENDEDOR') {
            $validated['user_id'] = auth()->id();
        }

        Proyecto::create($validated);

        return redirect()->route('proyectos.index')
            ->with('success', 'Proyecto creado exitosamente');
    }

    public function show(Proyecto $proyecto)
    {
        // Solo ADMIN y VENDEDOR pueden ver detalles del proyecto
        if (!in_array(auth()->user()->rol, ['ADMIN', 'VENDEDOR'])) {
            return redirect()->route('dashboard');
        }

        // VENDEDOR: solo puede ver sus propios proyectos
        if (auth()->user()->rol === 'VENDEDOR' && $proyecto->user_id !== auth()->id()) {
            return redirect()->route('dashboard');
        }

        $proyecto->load(['cliente', 'vendedor', 'cotizaciones', 'cronogramas', 'planPagos']);
        
        return Inertia::render('Proyectos/Show', [
            'proyecto' => $proyecto
        ]);
    }

    public function edit(Proyecto $proyecto)
    {
        // VENDEDOR: solo puede editar sus propios proyectos
        if (auth()->user()->rol === 'VENDEDOR' && $proyecto->user_id !== auth()->id()) {
            return redirect()->route('dashboard');
        }

        // JEFE_INSTALADOR: no puede editar
        if (auth()->user()->rol === 'JEFE_INSTALADOR') {
            return redirect()->route('dashboard');
        }

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
        // VENDEDOR: solo puede editar sus propios proyectos
        if (auth()->user()->rol === 'VENDEDOR' && $proyecto->user_id !== auth()->id()) {
            return redirect()->route('dashboard');
        }

        // JEFE_INSTALADOR: no puede editar
        if (auth()->user()->rol === 'JEFE_INSTALADOR') {
            return redirect()->route('dashboard');
        }
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
        // Solo ADMIN puede eliminar proyectos
        if (auth()->user()->rol !== 'ADMIN') {
            return redirect()->route('dashboard');
        }

        $proyecto->delete();

        return redirect()->route('proyectos.index')
            ->with('success', 'Proyecto eliminado exitosamente');
    }
}
