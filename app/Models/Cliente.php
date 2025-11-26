<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'email',
        'telefono',
        'direccion',
        'password',
    ];

    protected $hidden = [
        'password',
    ];

    /**
     * Relaciones
     */
    
    // Un cliente puede tener muchos proyectos
    public function proyectos()
    {
        return $this->hasMany(Proyecto::class);
    }
}
