<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diseno extends Model
{
    use HasFactory;

    protected $table = 'disenos';

    protected $fillable = [
        'cotizacion_id',
        'user_id',
        'url_render',
        'plano_iluminador',
        'aprovado',
        'fecha_aprovacion',
        'comentario',
    ];

    protected $casts = [
        'aprovado' => 'boolean',
        'fecha_aprovacion' => 'date',
    ];

    /**
     * Relaciones
     */
    
    // Un diseño pertenece a una cotización
    public function cotizacion()
    {
        return $this->belongsTo(Cotizacion::class);
    }

    // Un diseño pertenece a un diseñador (usuario)
    public function diseñador()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
