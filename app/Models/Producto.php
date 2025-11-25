<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'tipo',
        'unidad_medida',
        'precio_unitario',
        'stock',
    ];

    protected $casts = [
        'precio_unitario' => 'decimal:2',
    ];

    public function proveedores()
    {
        return $this->belongsToMany(Proveedor::class, 'producto_proveedor')
                    ->withPivot('cantidad', 'precio_unitario', 'total')
                    ->withTimestamps();
    }

    public function proyectos()
    {
        return $this->belongsToMany(Proyecto::class, 'proyecto_producto')
                    ->withPivot('cantidad', 'sobrante')
                    ->withTimestamps();
    }
}
