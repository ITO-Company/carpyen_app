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
        Schema::table('users', function (Blueprint $table) {
            $table->string('telefono')->nullable()->after('email');
            $table->string('direccion')->nullable()->after('telefono');
            $table->string('contrasena')->nullable()->after('password'); // Alias para password
            $table->string('rol')->default('INSTALADOR')->after('contrasena'); // ADMIN, VENDEDOR, JEFE_INSTALADOR, DISEÑADOR, INSTALADOR
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['telefono', 'direccion', 'contrasena', 'rol']);
        });
    }
};
