<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductoController extends Controller
{
    public function __construct()
    {
        // JEFE_INSTALADOR: puede ver y crear/editar productos
        // INSTALADOR: puede ver productos
        // ADMIN: acceso total
        $this->middleware(function ($request, $next) {
            $allowedRoles = ['ADMIN', 'JEFE_INSTALADOR', 'INSTALADOR'];
            if (!in_array(auth()->user()->rol, $allowedRoles)) {
                abort(403, 'No tienes permisos para acceder a productos.');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $productos = Producto::latest()->paginate(15);
        
        return Inertia::render('Productos/Index', [
            'productos' => $productos
        ]);
    }

    public function create()
    {
        // INSTALADOR: no puede crear productos
        if (auth()->user()->rol === 'INSTALADOR') {
            abort(403, 'Los instaladores no pueden crear productos.');
        }

        return Inertia::render('Productos/Create');
    }

    public function store(Request $request)
    {
        // INSTALADOR: no puede crear productos
        if (auth()->user()->rol === 'INSTALADOR') {
            abort(403, 'Los instaladores no pueden crear productos.');
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
        // INSTALADOR: no puede editar productos
        if (auth()->user()->rol === 'INSTALADOR') {
            abort(403, 'Los instaladores no pueden editar productos.');
        }

        return Inertia::render('Productos/Edit', [
            'producto' => $producto
        ]);
    }

    public function update(Request $request, Producto $producto)
    {
        // INSTALADOR: no puede editar productos
        if (auth()->user()->rol === 'INSTALADOR') {
            abort(403, 'Los instaladores no pueden editar productos.');
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
        $producto->delete();

        return redirect()->route('productos.index')
            ->with('success', 'Producto eliminado exitosamente');
    }
}
