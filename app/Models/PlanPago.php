<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanPago extends Model
{
    use HasFactory;

    protected $table = 'plan_pagos';

    protected $fillable = [
        'proyecto_id',
        'deuda_total',
        'pagado_total',
        'numero_deudas',
        'numero_pagos',
        'estado',
    ];

    protected $casts = [
        'deuda_total' => 'decimal:2',
        'pagado_total' => 'decimal:2',
    ];

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class);
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class);
    }
}
