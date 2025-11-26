<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Resetear el caché de PostgreSQL completamente
        try {
            DB::unprepared('DISCARD PLANS');
        } catch (\Exception $e) {
            // Ignorar errores si no hay planes cacheados
        }
        
        try {
            DB::unprepared('DISCARD ALL');
        } catch (\Exception $e) {
            // Ignorar errores si no hay nada que descartar
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No hay nada que deshacer
    }
};
