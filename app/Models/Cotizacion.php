<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cotizacion extends Model
{
    use HasFactory;

    protected $table = 'cotizaciones';

    protected $fillable = [
        'proyecto_id',
        'tipo_metro', // 'lineal' o 'cuadrado'
        'costo_metro',
        'cantidad_metro',
        'costo_mueble',
        'mueble_numero',
        'total',
        'estado',
        'comentario',
    ];

    protected $casts = [
        'costo_metro' => 'decimal:2',
        'costo_mueble' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    /**
     * Relaciones
     */
    
    // Una cotización pertenece a un proyecto
    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class);
    }

    // Una cotización puede tener muchos diseños
    public function disenos()
    {
        return $this->hasMany(Diseno::class);
    }
}
