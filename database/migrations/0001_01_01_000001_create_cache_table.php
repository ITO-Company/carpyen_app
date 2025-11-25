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
        DB::unprepared('
            CREATE TABLE IF NOT EXISTS cache (
                key varchar(255) PRIMARY KEY,
                value text NOT NULL,
                expiration integer NOT NULL
            );
        ');

        DB::unprepared('
            CREATE TABLE IF NOT EXISTS cache_locks (
                key varchar(255) PRIMARY KEY,
                owner varchar(255) NOT NULL,
                expiration integer NOT NULL
            );
        ');

        DB::unprepared('
            CREATE TABLE IF NOT EXISTS password_reset_tokens (
                email varchar(255) PRIMARY KEY,
                token varchar(255) NOT NULL,
                created_at timestamp(0) without time zone NULL
            );
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('cache');
        Schema::dropIfExists('cache_locks');
    }
};
