<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    use HasFactory;

    protected $fillable = [
        'plan_pago_id',
        'fecha',
        'total',
        'estado',
        'metodo_pago',
        'transaccion_id',
    ];

    protected $casts = [
        'fecha' => 'date',
        'total' => 'decimal:2',
    ];

    public function planPago()
    {
        return $this->belongsTo(PlanPago::class);
    }
}
