<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
use App\Models\Producto;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProyectoProductoController extends Controller
{
    public function __construct()
    {
        // VENDEDOR: puede ver/crear/editar productos de sus propios proyectos
        // JEFE_INSTALADOR: puede ver/crear/editar productos
        // INSTALADOR: puede ver/crear/editar productos
        // ADMIN: acceso total
        $this->middleware(function ($request, $next) {
            $allowedRoles = ['ADMIN', 'VENDEDOR', 'JEFE_INSTALADOR', 'INSTALADOR'];
            if (!in_array(auth()->user()->rol, $allowedRoles)) {
                abort(403, 'No tienes permisos para acceder a productos de proyecto.');
            }
            return $next($request);
        });
    }

    /**
     * Display a listing of productos for a proyecto
     */
    public function index(string $proyectoId)
    {
        $proyecto = Proyecto::with('cliente', 'vendedor')->findOrFail($proyectoId);

        // VENDEDOR: solo puede ver productos de sus propios proyectos
        if (auth()->user()->rol === 'VENDEDOR' && $proyecto->user_id !== auth()->id()) {
            abort(403, 'Solo puedes ver productos de tus propios proyectos.');
        }
        
        $productosProyecto = $proyecto->productos()
            ->with('proveedores')
            ->get()
            ->map(function ($producto) {
                return [
                    'id' => $producto->id,
                    'nombre' => $producto->nombre,
                    'tipo' => $producto->tipo,
                    'unidad_medida' => $producto->unidad_medida,
                    'precio_unitario' => $producto->precio_unitario,
                    'stock' => $producto->stock,
                    'cantidad_proyecto' => $producto->pivot->cantidad,
                    'sobrante' => $producto->pivot->sobrante,
                ];
            });

        return Inertia::render('ProyectoProductos/Index', [
            'proyecto' => $proyecto,
            'productosProyecto' => $productosProyecto
        ]);
    }

    /**
     * Show the form for creating a new producto for a proyecto
     */
    public function create(string $proyectoId)
    {
        $proyecto = Proyecto::with('cliente', 'vendedor')->findOrFail($proyectoId);

        // VENDEDOR: solo puede crear productos en sus propios proyectos
        if (auth()->user()->rol === 'VENDEDOR' && $proyecto->user_id !== auth()->id()) {
            abort(403, 'Solo puedes agregar productos a tus propios proyectos.');
        }
        
        // Obtener productos que NO están ya asociados al proyecto
        $productosDisponibles = Producto::whereNotIn('id', function ($query) use ($proyectoId) {
            $query->select('producto_id')
                ->from('proyecto_producto')
                ->where('proyecto_id', $proyectoId);
        })->get();

        return Inertia::render('ProyectoProductos/Create', [
            'proyecto' => $proyecto,
            'productosDisponibles' => $productosDisponibles
        ]);
    }

    /**
     * Store a newly created producto in proyecto
     */
    public function store(Request $request, string $proyectoId)
    {
        $proyecto = Proyecto::findOrFail($proyectoId);

        $validated = $request->validate([
            'producto_id' => 'required|exists:productos,id|unique:proyecto_producto,producto_id,null,id,proyecto_id,' . $proyectoId,
            'cantidad' => 'required|integer|min:1',
        ], [
            'producto_id.required' => 'Debe seleccionar un producto',
            'producto_id.exists' => 'El producto no existe',
            'producto_id.unique' => 'Este producto ya está asociado al proyecto',
            'cantidad.required' => 'La cantidad es obligatoria',
            'cantidad.integer' => 'La cantidad debe ser un número entero',
            'cantidad.min' => 'La cantidad debe ser al menos 1',
        ]);

        $producto = Producto::findOrFail($validated['producto_id']);

        // Validar que hay stock disponible
        if ($producto->stock < $validated['cantidad']) {
            return redirect()->back()
                ->with('error', "Stock insuficiente. Stock disponible: {$producto->stock}, cantidad solicitada: {$validated['cantidad']}");
        }

        // Restar stock del producto
        $producto->decrement('stock', $validated['cantidad']);

        // Asociar producto al proyecto
        $proyecto->productos()->attach($producto->id, [
            'cantidad' => $validated['cantidad'],
            'sobrante' => 0,
        ]);

        return redirect()->route('proyectos.productos.index', $proyectoId)
            ->with('success', 'Producto agregado al proyecto exitosamente');
    }

    /**
     * Show the form for editing a producto in proyecto
     */
    public function edit(string $proyectoId, string $productoId)
    {
        $proyecto = Proyecto::with('cliente', 'vendedor')->findOrFail($proyectoId);
        $producto = Producto::findOrFail($productoId);

        // Verificar que el producto está asociado al proyecto
        $relacion = $proyecto->productos()->where('producto_id', $productoId)->first();
        if (!$relacion) {
            abort(404);
        }

        return Inertia::render('ProyectoProductos/Edit', [
            'proyecto' => $proyecto,
            'producto' => $producto,
            'cantidadActual' => $relacion->pivot->cantidad,
            'sobrante' => $relacion->pivot->sobrante,
        ]);
    }

    /**
     * Update a producto in proyecto
     */
    public function update(Request $request, string $proyectoId, string $productoId)
    {
        $proyecto = Proyecto::findOrFail($proyectoId);
        $producto = Producto::findOrFail($productoId);

        // Verificar que el producto está asociado al proyecto
        $relacion = $proyecto->productos()->where('producto_id', $productoId)->first();
        if (!$relacion) {
            abort(404);
        }

        $cantidadActual = $relacion->pivot->cantidad;

        $validated = $request->validate([
            'cantidad' => 'required|integer|min:1',
        ], [
            'cantidad.required' => 'La cantidad es obligatoria',
            'cantidad.integer' => 'La cantidad debe ser un número entero',
            'cantidad.min' => 'La cantidad debe ser al menos 1',
        ]);

        $nuevaCantidad = $validated['cantidad'];
        $diferencia = $nuevaCantidad - $cantidadActual;

        // Si aumenta cantidad, validar que hay stock
        if ($diferencia > 0) {
            if ($producto->stock < $diferencia) {
                return redirect()->back()
                    ->with('error', "Stock insuficiente para aumentar. Stock disponible: {$producto->stock}, cantidad adicional requerida: {$diferencia}");
            }
            // Restar stock
            $producto->decrement('stock', $diferencia);
        } elseif ($diferencia < 0) {
            // Si disminuye cantidad, devolver stock
            $producto->increment('stock', abs($diferencia));
        }

        // Actualizar relación
        $proyecto->productos()->updateExistingPivot($productoId, [
            'cantidad' => $nuevaCantidad,
            'sobrante' => 0,
        ]);

        return redirect()->route('proyectos.productos.index', $proyectoId)
            ->with('success', 'Producto actualizado exitosamente');
    }

    /**
     * Remove a producto from proyecto
     */
    public function destroy(string $proyectoId, string $productoId)
    {
        $proyecto = Proyecto::findOrFail($proyectoId);
        $producto = Producto::findOrFail($productoId);

        // Verificar que el producto está asociado al proyecto
        $relacion = $proyecto->productos()->where('producto_id', $productoId)->first();
        if (!$relacion) {
            abort(404);
        }

        $cantidadUsada = $relacion->pivot->cantidad;

        // Devolver stock al producto
        $producto->increment('stock', $cantidadUsada);

        // Desasociar producto del proyecto
        $proyecto->productos()->detach($productoId);

        return redirect()->route('proyectos.productos.index', $proyectoId)
            ->with('success', 'Producto removido del proyecto exitosamente');
    }
}
