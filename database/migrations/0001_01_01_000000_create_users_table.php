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
            CREATE TABLE IF NOT EXISTS users (
                id bigserial PRIMARY KEY,
                name varchar(255) NOT NULL,
                email varchar(255) NOT NULL,
                email_verified_at timestamp(0) without time zone NULL,
                password varchar(255) NOT NULL,
                remember_token varchar(100) NULL,
                created_at timestamp(0) without time zone NULL,
                updated_at timestamp(0) without time zone NULL
            );
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP TABLE IF EXISTS users CASCADE;');
    }
};
