<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class Cliente extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'email',
        'telefono',
        'direccion',
        'contrasena',
    ];

    protected $hidden = [
        'contrasena',
    ];

    /**
     * Set the contrasena attribute.
     *
     * @param string $value
     * @return void
     */
    public function setContrasenaAttribute($value)
    {
        $this->attributes['contrasena'] = Hash::make($value);
    }

    /**
     * Relaciones
     */
    
    // Un cliente puede tener muchos proyectos
    public function proyectos()
    {
        return $this->hasMany(Proyecto::class);
    }
}
