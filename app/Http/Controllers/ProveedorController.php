<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use App\Models\Producto;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProveedorController extends Controller
{
    public function __construct()
    {
        // JEFE_INSTALADOR: puede ver y crear proveedores
        // ADMIN: acceso total
        $this->middleware(function ($request, $next) {
            $allowedRoles = ['ADMIN', 'JEFE_INSTALADOR'];
            if (!in_array(auth()->user()->rol, $allowedRoles)) {
                abort(403, 'No tienes permisos para acceder a proveedores.');
            }
            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $proveedores = Proveedor::latest()->paginate(15);
        
        return Inertia::render('Proveedores/Index', [
            'proveedores' => $proveedores
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Proveedores/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:proveedores,nombre',
            'contacto' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'ubicacion' => 'nullable|string|max:255',
        ], [
            'nombre.required' => 'El nombre del proveedor es obligatorio',
            'nombre.unique' => 'El nombre del proveedor ya existe',
            'email.email' => 'El email debe ser válido',
        ]);

        Proveedor::create($validated);

        return redirect()->route('proveedores.index')
            ->with('success', 'Proveedor creado exitosamente');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $proveedor = Proveedor::findOrFail($id);
        $proveedor->load('productos');
        
        return Inertia::render('Proveedores/Show', [
            'proveedor' => $proveedor,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $proveedor = Proveedor::findOrFail($id);
        return Inertia::render('Proveedores/Edit', [
            'proveedor' => $proveedor
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $proveedor = Proveedor::findOrFail($id);
        
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:proveedores,nombre,' . $proveedor->id,
            'contacto' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'ubicacion' => 'nullable|string|max:255',
        ], [
            'nombre.required' => 'El nombre del proveedor es obligatorio',
            'nombre.unique' => 'El nombre del proveedor ya existe',
            'email.email' => 'El email debe ser válido',
        ]);

        $proveedor->update($validated);

        return redirect()->route('proveedores.index')
            ->with('success', 'Proveedor actualizado exitosamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $proveedor = Proveedor::findOrFail($id);
        $proveedor->delete();

        return redirect()->route('proveedores.index')
            ->with('success', 'Proveedor eliminado exitosamente');
    }

    /**
     * Mostrar formulario para agregar productos al proveedor
     */
    public function agregarProductos($id)
    {
        $proveedor = Proveedor::findOrFail($id);
        $productosExistentes = $proveedor->productos()->pluck('producto_id')->toArray();
        $productos = Producto::whereNotIn('id', $productosExistentes)->get();

        return Inertia::render('Proveedores/AgregarProductos', [
            'proveedor' => $proveedor,
            'productos' => $productos
        ]);
    }

    /**
     * Guardar productos agregados o stock actualizado
     */
    public function guardarProductos(Request $request, $id)
    {
        $proveedor = Proveedor::findOrFail($id);
        
        $rules = [
            'items' => 'required|array|min:1',
            'items.*.cantidad' => 'required|integer|min:1',
            'items.*.precio_unitario' => 'required|numeric|min:0',
            'items.*.es_nuevo' => 'required|boolean',
            'items.*.nombre_nuevo' => 'nullable|string|max:255',
            'items.*.tipo_nuevo' => 'nullable|string',
            'items.*.unidad_medida_nueva' => 'nullable|string',
        ];

        $messages = [
            'items.required' => 'Debes agregar al menos un producto',
            'items.min' => 'Debes agregar al menos un producto',
            'items.*.cantidad.required' => 'La cantidad es obligatoria',
            'items.*.cantidad.min' => 'La cantidad debe ser al menos 1',
            'items.*.precio_unitario.required' => 'El precio unitario es obligatorio',
            'items.*.precio_unitario.min' => 'El precio no puede ser negativo',
            'items.*.nombre_nuevo.required' => 'El nombre del producto es obligatorio',
        ];

        // Validación condicional: si es nuevo, necesita nombre. Si no es nuevo, necesita producto_id
        $items = $request->input('items', []);
        foreach ($items as $index => $item) {
            if ($item['es_nuevo']) {
                $rules["items.$index.nombre_nuevo"] = 'required|string|max:255';
            } else {
                $rules["items.$index.producto_id"] = 'required|integer|exists:productos,id';
            }
        }

        $validated = $request->validate($rules, $messages);

        foreach ($validated['items'] as $item) {
            $productoId = null;

            // Si es un producto nuevo, crearlo primero
            if ($item['es_nuevo'] && $item['nombre_nuevo']) {
                $nuevoProducto = Producto::create([
                    'nombre' => $item['nombre_nuevo'],
                    'tipo' => $item['tipo_nuevo'] ?? null,
                    'unidad_medida' => $item['unidad_medida_nueva'] ?? 'unidad',
                    'precio_unitario' => $item['precio_unitario'],
                    'stock' => $item['cantidad']
                ]);
                $productoId = $nuevoProducto->id;
            } else {
                // Si no es nuevo, obtener el ID del producto existente
                $productoId = $item['producto_id'] ?? null;
            }

            // Calcular total
            $total = $item['cantidad'] * $item['precio_unitario'];

            // Verificar si ya existe la relación
            $relacion = $proveedor->productos()->where('producto_id', $productoId)->first();

            if ($relacion) {
                // Actualizar stock existente
                $relacion->pivot->update([
                    'cantidad' => $relacion->pivot->cantidad + $item['cantidad'],
                    'precio_unitario' => $item['precio_unitario'],
                    'total' => ($relacion->pivot->cantidad + $item['cantidad']) * $item['precio_unitario']
                ]);
            } else {
                // Crear nueva relación
                $proveedor->productos()->attach($productoId, [
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio_unitario'],
                    'total' => $total
                ]);
            }
        }

        return redirect()->route('proveedores.show', ['id' => $proveedor->id])
            ->with('success', 'Productos agregados exitosamente');
    }
}
