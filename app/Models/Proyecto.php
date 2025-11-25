<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proyecto extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
        'ubicacion',
        'estado',
        'cliente_id',
        'user_id',
    ];

    protected $casts = [
        'estado' => 'string',
    ];

    /**
     * Relaciones
     */
    
    // Un proyecto pertenece a un cliente
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    // Un proyecto pertenece a un vendedor (usuario)
    public function vendedor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Un proyecto puede tener muchas cotizaciones
    public function cotizaciones()
    {
        return $this->hasMany(Cotizacion::class);
    }

    // Un proyecto puede tener muchos cronogramas
    public function cronogramas()
    {
        return $this->hasMany(Cronograma::class);
    }

    // Un proyecto puede tener muchos planes de pago
    public function planPagos()
    {
        return $this->hasMany(PlanPago::class);
    }

    // Un proyecto puede usar muchos productos (relación many-to-many)
    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'proyecto_producto')
                    ->withPivot('cantidad', 'sobrante')
                    ->withTimestamps();
    }
}
