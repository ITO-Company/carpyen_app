<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations
     */
    public function up(): void
    {
        // Actualizar tabla cotizaciones - reemplazar hipo_metro por tipo_metro
        DB::unprepared('
            ALTER TABLE cotizaciones
            DROP COLUMN hipo_metro,
            ADD COLUMN tipo_metro VARCHAR(50) DEFAULT \'lineal\' CHECK (tipo_metro IN (\'lineal\', \'cuadrado\'))
        ');

        // Agregar password a clientes
        DB::unprepared('
            ALTER TABLE clientes
            ADD COLUMN password VARCHAR(255) NULL
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir cambios en cotizaciones
        DB::unprepared('
            ALTER TABLE cotizaciones
            DROP COLUMN tipo_metro,
            ADD COLUMN hipo_metro DECIMAL(10, 2) NULL
        ');

        // Remover password de clientes
        DB::unprepared('
            ALTER TABLE clientes
            DROP COLUMN password
        ');
    }
};
