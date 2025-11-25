<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plan_pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proyecto_id')->constrained('proyectos')->onDelete('cascade');
            $table->decimal('deuda_total', 10, 2);
            $table->decimal('pagado_total', 10, 2)->default(0);
            $table->integer('numero_deudas'); // Número de cuotas
            $table->integer('numero_pagos')->default(0); // Pagos realizados
            $table->enum('estado', ['activo', 'completado', 'mora'])->default('activo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plan_pagos');
    }
};
