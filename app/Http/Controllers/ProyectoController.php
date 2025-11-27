<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
use App\Models\Cliente;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProyectoController extends Controller
{
    public function __construct()
    {
        // VENDEDOR: puede ver/crear/editar sus propios proyectos
        // JEFE_INSTALADOR: puede ver proyectos (todos)
        // ADMIN: acceso total
        $this->middleware(function ($request, $next) {
            $allowedRoles = ['ADMIN', 'VENDEDOR', 'JEFE_INSTALADOR'];
            if (!in_array(auth()->user()->rol, $allowedRoles)) {
                abort(403, 'No tienes permisos para acceder a proyectos.');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $query = Proyecto::with(['cliente', 'vendedor']);

        // VENDEDOR: ver solo sus propios proyectos
        if (auth()->user()->rol === 'VENDEDOR') {
            $query->where('user_id', auth()->id());
        }
        // JEFE_INSTALADOR: ver todos los proyectos
        // ADMIN: ver todos los proyectos

        $proyectos = $query->latest()->paginate(10);
        
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
        // Solo ADMIN y VENDEDOR pueden crear proyectos
        if (!in_array(auth()->user()->rol, ['ADMIN', 'VENDEDOR'])) {
            abort(403, 'Solo vendedores y administradores pueden crear proyectos.');
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
        $proyecto->load(['cliente', 'vendedor', 'cotizaciones', 'cronogramas', 'planPagos']);
        
        return Inertia::render('Proyectos/Show', [
            'proyecto' => $proyecto
        ]);
    }

    public function edit(Proyecto $proyecto)
    {
        // VENDEDOR: solo puede editar sus propios proyectos
        if (auth()->user()->rol === 'VENDEDOR' && $proyecto->user_id !== auth()->id()) {
            abort(403, 'Solo puedes editar tus propios proyectos.');
        }

        // JEFE_INSTALADOR: no puede editar
        if (auth()->user()->rol === 'JEFE_INSTALADOR') {
            abort(403, 'Los jefes de instalación no pueden editar proyectos.');
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
            abort(403, 'Solo puedes editar tus propios proyectos.');
        }

        // JEFE_INSTALADOR: no puede editar
        if (auth()->user()->rol === 'JEFE_INSTALADOR') {
            abort(403, 'Los jefes de instalación no pueden editar proyectos.');
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
            abort(403, 'Solo administradores pueden eliminar proyectos.');
        }

        $proyecto->delete();

        return redirect()->route('proyectos.index')
            ->with('success', 'Proyecto eliminado exitosamente');
    }
}
