<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::unprepared('ALTER TABLE users ADD COLUMN IF NOT EXISTS telefono varchar(255) NULL;');
        DB::unprepared('ALTER TABLE users ADD COLUMN IF NOT EXISTS direccion varchar(255) NULL;');
        DB::unprepared('ALTER TABLE users ADD COLUMN IF NOT EXISTS contrasena varchar(255) NULL;');
        DB::unprepared('ALTER TABLE users ADD COLUMN IF NOT EXISTS rol varchar(255) DEFAULT \'INSTALADOR\';');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('ALTER TABLE users DROP COLUMN IF EXISTS rol;');
        DB::unprepared('ALTER TABLE users DROP COLUMN IF EXISTS contrasena;');
        DB::unprepared('ALTER TABLE users DROP COLUMN IF EXISTS direccion;');
        DB::unprepared('ALTER TABLE users DROP COLUMN IF EXISTS telefono;');
    }
};
