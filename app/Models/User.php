<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'telefono',
        'direccion',
        'contrasena',
        'rol',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Accessor para obtener el nombre del primer rol del usuario
     */
    public function getRolAttribute()
    {
        // Si tiene el atributo 'rol' en la BD, devolverlo
        if ($this->attributes['rol'] ?? null) {
            return $this->attributes['rol'];
        }
        // Si no, obtener del primer rol de Spatie
        return $this->roles()->first()?->name ?? 'Sin rol';
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

