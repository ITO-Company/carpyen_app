<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    use HasFactory;

    protected $table = 'proveedores';

    protected $fillable = [
        'nombre',
        'contacto',
        'telefono',
        'email',
        'ubicacion',
    ];

    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'producto_proveedor')
                    ->withPivot('cantidad', 'precio_unitario', 'total')
                    ->withTimestamps();
    }
}
