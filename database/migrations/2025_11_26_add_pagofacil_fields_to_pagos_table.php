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
        // Agregar campos para PagoFácil usando SQL directo para evitar problemas con Neon PostgreSQL
        DB::unprepared('
            ALTER TABLE pagos 
            ADD COLUMN IF NOT EXISTS pagofacil_transaction_id VARCHAR(255),
            ADD COLUMN IF NOT EXISTS company_transaction_id VARCHAR(255),
            ADD COLUMN IF NOT EXISTS qr_base64 TEXT,
            ADD COLUMN IF NOT EXISTS qr_status VARCHAR(50) DEFAULT \'PENDING\',
            ADD COLUMN IF NOT EXISTS qr_expiration TIMESTAMP
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('
            ALTER TABLE pagos 
            DROP COLUMN IF EXISTS pagofacil_transaction_id,
            DROP COLUMN IF EXISTS company_transaction_id,
            DROP COLUMN IF EXISTS qr_base64,
            DROP COLUMN IF EXISTS qr_status,
            DROP COLUMN IF EXISTS qr_expiration
        ');
    }
};
