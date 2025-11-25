<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_pago_id')->constrained('plan_pagos')->onDelete('cascade');
            $table->date('fecha');
            $table->decimal('total', 10, 2);
            $table->enum('estado', ['pendiente', 'completado', 'fallido'])->default('pendiente');
            $table->string('metodo_pago')->nullable(); // efectivo, tarjeta, pago_facil, etc.
            $table->string('transaccion_id')->nullable(); // ID de transacción de Pago Fácil
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
