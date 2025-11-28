<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class ClienteController extends Controller
{

    public function index()
    {
        // ADMIN y VENDEDOR pueden ver la lista
        if (!in_array(auth()->user()->rol, ['ADMIN', 'VENDEDOR'])) {
            return redirect()->route('dashboard');
        }

        $clientes = Cliente::latest()->paginate(10);
        
        return Inertia::render('Clientes/Index', [
            'clientes' => $clientes
        ]);
    }

    public function create()
    {
        // ADMIN y VENDEDOR pueden crear
        if (!in_array(auth()->user()->rol, ['ADMIN', 'VENDEDOR'])) {
            return redirect()->route('dashboard');
        }

        return Inertia::render('Clientes/Create');
    }

    public function store(Request $request)
    {
        // ADMIN y VENDEDOR pueden crear
        if (!in_array(auth()->user()->rol, ['ADMIN', 'VENDEDOR'])) {
            return redirect()->route('dashboard');
        }

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|unique:clientes',
            'telefono' => 'required|string',
            'direccion' => 'required|string',
            'contrasena' => 'required|string|min:8|confirmed',
        ], [
            'nombre.required' => 'El nombre es obligatorio',
            'email.required' => 'El correo electrónico es obligatorio',
            'email.email' => 'El correo electrónico debe ser válido',
            'email.unique' => 'Este correo ya está registrado',
            'telefono.required' => 'El teléfono es obligatorio',
            'direccion.required' => 'La dirección es obligatoria',
            'contrasena.required' => 'La contraseña es obligatoria',
            'contrasena.min' => 'La contraseña debe tener al menos 8 caracteres',
            'contrasena.confirmed' => 'Las contraseñas no coinciden',
        ]);

        // Hashear la contraseña
        $validated['contrasena'] = Hash::make($validated['contrasena']);

        Cliente::create($validated);

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente creado exitosamente');
    }

    public function edit(Cliente $cliente)
    {
        // Solo ADMIN puede editar clientes
        if (auth()->user()->rol !== 'ADMIN') {
            return redirect()->route('dashboard');
        }

        return Inertia::render('Clientes/Edit', [
            'cliente' => $cliente
        ]);
    }

    public function update(Request $request, Cliente $cliente)
    {
        // Solo ADMIN puede editar clientes
        if (auth()->user()->rol !== 'ADMIN') {
            return redirect()->route('dashboard');
        }
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|unique:clientes,email,' . $cliente->id,
            'telefono' => 'required|string',
            'direccion' => 'required|string',
            'contrasena' => 'nullable|string|min:8',
        ], [
            'nombre.required' => 'El nombre es obligatorio',
            'email.required' => 'El correo electrónico es obligatorio',
            'email.email' => 'El correo electrónico debe ser válido',
            'email.unique' => 'Este correo ya está registrado',
            'telefono.required' => 'El teléfono es obligatorio',
            'direccion.required' => 'La dirección es obligatoria',
            'contrasena.min' => 'La contraseña debe tener al menos 8 caracteres',
        ]);

        // Si se proporciona contraseña, hashearla
        if (!empty($validated['contrasena'])) {
            $validated['contrasena'] = Hash::make($validated['contrasena']);
        } else {
            // Si no se proporciona, no actualizar este campo
            unset($validated['contrasena']);
        }

        $cliente->update($validated);

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente actualizado exitosamente');
    }

    public function destroy(Cliente $cliente)
    {
        // Solo ADMIN puede eliminar clientes
        if (auth()->user()->rol !== 'ADMIN') {
            return redirect()->route('dashboard');
        }

        $cliente->delete();

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente eliminado exitosamente');
    }
}
