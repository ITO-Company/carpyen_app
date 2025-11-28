<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'usuarios';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nombre',
        'email',
        'contrasena',
        'telefono',
        'direccion',
        'rol',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'contrasena',
        'token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [];
    }

    /**
     * Set the password/contrasena attribute.
     *
     * @param string $value
     * @return void
     */
    public function setContrasenaAttribute($value)
    {
        $this->attributes['contrasena'] = \Illuminate\Support\Facades\Hash::make($value);
    }

    /**
     * Get the password key for the model.
     *
     * @return string
     */
    public function getAuthPasswordName()
    {
        return 'contrasena';
    }

    /**
     * Get the name of the "password" column for the model.
     *
     * @return string
     */
    public function getPasswordColumnName()
    {
        return 'contrasena';
    }

    /**
     * Relaciones
     */
    
    // Proyectos asignados (como vendedor)
    public function proyectos()
    {
        return $this->hasMany(Proyecto::class);
    }

    // Diseños asignados (como diseñador)
    public function disenos()
    {
        return $this->hasMany(Diseno::class);
    }

    // Tareas asignadas (como instalador)
    public function tareas()
    {
        return $this->hasMany(Tarea::class);
    }
}

