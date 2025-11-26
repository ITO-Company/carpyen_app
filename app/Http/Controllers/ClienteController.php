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
        $clientes = Cliente::latest()->paginate(10);
        
        return Inertia::render('Clientes/Index', [
            'clientes' => $clientes
        ]);
    }

    public function create()
    {
        return Inertia::render('Clientes/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|unique:clientes',
            'telefono' => 'required|string',
            'direccion' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'nombre.required' => 'El nombre es obligatorio',
            'email.required' => 'El correo electrónico es obligatorio',
            'email.email' => 'El correo electrónico debe ser válido',
            'email.unique' => 'Este correo ya está registrado',
            'telefono.required' => 'El teléfono es obligatorio',
            'direccion.required' => 'La dirección es obligatoria',
            'password.required' => 'La contraseña es obligatoria',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres',
            'password.confirmed' => 'Las contraseñas no coinciden',
        ]);

        // Hashear la contraseña
        $validated['password'] = Hash::make($validated['password']);

        Cliente::create($validated);

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente creado exitosamente');
    }

    public function edit(Cliente $cliente)
    {
        return Inertia::render('Clientes/Edit', [
            'cliente' => $cliente
        ]);
    }

    public function update(Request $request, Cliente $cliente)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|unique:clientes,email,' . $cliente->id,
            'telefono' => 'required|string',
            'direccion' => 'required|string',
            'password' => 'nullable|string|min:8',
        ], [
            'nombre.required' => 'El nombre es obligatorio',
            'email.required' => 'El correo electrónico es obligatorio',
            'email.email' => 'El correo electrónico debe ser válido',
            'email.unique' => 'Este correo ya está registrado',
            'telefono.required' => 'El teléfono es obligatorio',
            'direccion.required' => 'La dirección es obligatoria',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres',
        ]);

        // Si se proporciona contraseña, hashearla
        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            // Si no se proporciona, no actualizar este campo
            unset($validated['password']);
        }

        $cliente->update($validated);

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente actualizado exitosamente');
    }

    public function destroy(Cliente $cliente)
    {
        $cliente->delete();

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente eliminado exitosamente');
    }
}
