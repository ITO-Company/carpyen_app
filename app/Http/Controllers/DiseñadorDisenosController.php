<?php

namespace App\Http\Controllers;

use App\Models\Diseno;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DiseñadorDisenosController extends Controller
{
    /**
     * Display a listing of the designer's assigned designs.
     */
    public function index(Request $request)
    {
        // Solo DISEÑADOR puede acceder
        if (auth()->user()->rol !== 'DISEÑADOR') {
            return redirect()->route('dashboard');
        }

        $usuario_id = auth()->id();

        // Obtener todos los diseños asignados al diseñador actual
        $disenos = Diseno::with([
            'cotizacion' => function($query) {
                $query->with('proyecto');
            }
        ])
        ->where('user_id', $usuario_id)
        ->orderBy('created_at', 'desc')
        ->paginate(15);

        return Inertia::render('Diseñador/Disenos/Index', [
            'disenos' => $disenos,
        ]);
    }

    /**
     * Show the form for editing the specified design.
     */
    public function edit(Diseno $diseno, Request $request)
    {
        // Solo DISEÑADOR puede acceder
        if (auth()->user()->rol !== 'DISEÑADOR') {
            return redirect()->route('dashboard');
        }

        // Verificar que el diseño pertenezca al usuario actual
        if ($diseno->user_id !== auth()->id()) {
            return redirect()->route('dashboard');
        }

        $diseno->load('cotizacion.proyecto');

        return Inertia::render('Diseñador/Disenos/Edit', [
            'diseno' => $diseno,
        ]);
    }

    /**
     * Update the specified design in storage.
     */
    public function update(Diseno $diseno, Request $request)
    {
        // Solo DISEÑADOR puede acceder
        if (auth()->user()->rol !== 'DISEÑADOR') {
            return redirect()->route('dashboard');
        }

        // Verificar que el diseño pertenezca al usuario actual
        if ($diseno->user_id !== auth()->id()) {
            return redirect()->route('dashboard');
        }

        $validated = $request->validate([
            'descripcion' => 'required|string|max:1000',
            'estado' => 'required|in:pendiente,en_proceso,completado,rechazado',
            'url_render' => 'nullable|url',
            'plano_iluminador' => 'nullable|url',
            'comentario' => 'nullable|string|max:1000',
        ]);

        $diseno->update($validated);

        return redirect()->route('diseñador.disenos.index')->with('success', 'Diseño actualizado correctamente');
    }
}
