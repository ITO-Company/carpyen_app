<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cotizaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proyecto_id')->constrained('proyectos')->onDelete('cascade');
            $table->decimal('hipo_metro', 10, 2)->nullable(); // Hipoteca por metro
            $table->decimal('costo_metro', 10, 2)->nullable();
            $table->integer('cantidad_metro')->nullable();
            $table->decimal('costo_mueble', 10, 2)->nullable();
            $table->integer('mueble_numero')->nullable();
            $table->decimal('total', 10, 2);
            $table->enum('estado', ['pendiente', 'aprobada', 'rechazada'])->default('pendiente');
            $table->text('comentario')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cotizaciones');
    }
};
