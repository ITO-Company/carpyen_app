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
        'pagofacil_transaction_id',
        'company_transaction_id',
        'qr_base64',
        'qr_status',
        'qr_expiration',
    ];

    protected $casts = [
        'fecha' => 'date',
        'total' => 'decimal:2',
        'qr_expiration' => 'datetime',
    ];

    public function planPago()
    {
        return $this->belongsTo(PlanPago::class);
    }

    /**
     * Relación con pagos completados
     */
    public function completados()
    {
        return $this->where('estado', 'completado');
    }

    /**
     * Scope para pagos pendientes
     */
    public function scopePendiente($query)
    {
        return $query->where('estado', 'pendiente');
    }

    /**
     * Scope para pagos completados
     */
    public function scopeCompletado($query)
    {
        return $query->where('estado', 'completado');
    }
}
