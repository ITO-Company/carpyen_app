<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InventarioController extends Controller
{
    /**
     * Display a listing of the inventory.
     */
    public function index()
    {
        $productos = Producto::latest()->paginate(15);
        
        return Inertia::render('Inventario/Index', [
            'productos' => $productos
        ]);
    }
}
