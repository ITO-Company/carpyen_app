<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tarea extends Model
{
    use HasFactory;

    protected $fillable = [
        'cronograma_id',
        'user_id',
        'fecha',
        'hora_inicio',
        'hora_fin',
        'descripcion',
        'estado',
    ];

    public function cronograma()
    {
        return $this->belongsTo(Cronograma::class);
    }

    public function instalador()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
