<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductoController extends Controller
{

    public function index()
    {
        // ADMIN, JEFE_INSTALADOR e INSTALADOR pueden ver productos
        if (!in_array(auth()->user()->rol, ['ADMIN', 'JEFE_INSTALADOR', 'INSTALADOR'])) {
            return redirect()->route('dashboard');
        }

        $productos = Producto::latest()->paginate(15);
        
        return Inertia::render('Productos/Index', [
            'productos' => $productos
        ]);
    }

    public function create()
    {
        // ADMIN y JEFE_INSTALADOR pueden crear. INSTALADOR: no puede
        if (!in_array(auth()->user()->rol, ['ADMIN', 'JEFE_INSTALADOR'])) {
            return redirect()->route('dashboard');
        }

        return Inertia::render('Productos/Create');
    }

    public function store(Request $request)
    {
        // ADMIN y JEFE_INSTALADOR pueden crear. INSTALADOR: no puede
        if (!in_array(auth()->user()->rol, ['ADMIN', 'JEFE_INSTALADOR'])) {
            return redirect()->route('dashboard');
        }

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'tipo' => 'nullable|string',
            'unidad_medida' => 'required|string',
            'precio_unitario' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ], [
            'nombre.required' => 'El nombre del producto es obligatorio',
            'unidad_medida.required' => 'La unidad de medida es obligatoria',
            'precio_unitario.required' => 'El precio unitario es obligatorio',
            'precio_unitario.numeric' => 'El precio debe ser un número',
            'stock.required' => 'El stock es obligatorio',
            'stock.integer' => 'El stock debe ser un número entero',
        ]);

        Producto::create($validated);

        return redirect()->route('productos.index')
            ->with('success', 'Producto creado exitosamente');
    }

    public function show(Producto $producto)
    {
        return Inertia::render('Productos/Show', [
            'producto' => $producto
        ]);
    }

    public function edit(Producto $producto)
    {
        // ADMIN y JEFE_INSTALADOR pueden editar. INSTALADOR: no puede
        if (!in_array(auth()->user()->rol, ['ADMIN', 'JEFE_INSTALADOR'])) {
            return redirect()->route('dashboard');
        }

        return Inertia::render('Productos/Edit', [
            'producto' => $producto
        ]);
    }

    public function update(Request $request, Producto $producto)
    {
        // ADMIN y JEFE_INSTALADOR pueden editar. INSTALADOR: no puede
        if (!in_array(auth()->user()->rol, ['ADMIN', 'JEFE_INSTALADOR'])) {
            return redirect()->route('dashboard');
        }

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'tipo' => 'nullable|string',
            'unidad_medida' => 'required|string',
            'precio_unitario' => 'required|numeric|min:0',
        ]);

        $producto->update($validated);

        return redirect()->route('productos.index')
            ->with('success', 'Producto actualizado exitosamente');
    }

    /**
     * Agregar stock a un producto
     */
    public function agregarStock(Request $request, Producto $producto)
    {
        $validated = $request->validate([
            'cantidad' => 'required|integer|min:1',
        ], [
            'cantidad.required' => 'La cantidad es obligatoria',
            'cantidad.integer' => 'La cantidad debe ser un número entero',
            'cantidad.min' => 'La cantidad debe ser al menos 1',
        ]);

        $producto->update([
            'stock' => $producto->stock + $validated['cantidad']
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Se agregaron {$validated['cantidad']} unidades al stock",
                'stock' => $producto->stock
            ]);
        }

        return redirect()->route('productos.index')
            ->with('success', "Se agregaron {$validated['cantidad']} unidades al stock");
    }

    /**
     * Disminuir stock de un producto
     */
    public function disminuirStock(Request $request, Producto $producto)
    {
        $validated = $request->validate([
            'cantidad' => 'required|integer|min:1',
        ], [
            'cantidad.required' => 'La cantidad es obligatoria',
            'cantidad.integer' => 'La cantidad debe ser un número entero',
            'cantidad.min' => 'La cantidad debe ser al menos 1',
        ]);

        $nuevoStock = $producto->stock - $validated['cantidad'];
        
        if ($nuevoStock < 0) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => "No hay suficiente stock. Stock actual: {$producto->stock}"
                ], 422);
            }
            return redirect()->route('productos.index')
                ->with('error', "No hay suficiente stock para disminuir. Stock actual: {$producto->stock}");
        }

        $producto->update([
            'stock' => $nuevoStock
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Se disminuyeron {$validated['cantidad']} unidades del stock",
                'stock' => $producto->stock
            ]);
        }

        return redirect()->route('productos.index')
            ->with('success', "Se disminuyeron {$validated['cantidad']} unidades del stock");
    }

    public function destroy(Producto $producto)
    {
        // Solo ADMIN puede eliminar productos
        if (auth()->user()->rol !== 'ADMIN') {
            return redirect()->route('dashboard');
        }

        $producto->delete();

        return redirect()->route('productos.index')
            ->with('success', 'Producto eliminado exitosamente');
    }
}
