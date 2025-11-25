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
            CREATE TABLE IF NOT EXISTS jobs (
                id bigserial PRIMARY KEY,
                queue varchar(255) NOT NULL,
                payload text NOT NULL,
                attempts smallint NOT NULL,
                reserved_at integer NULL,
                available_at integer NOT NULL,
                created_at integer NOT NULL
            );
        ');

        DB::unprepared('CREATE INDEX IF NOT EXISTS jobs_queue_index ON jobs(queue);');

        DB::unprepared('
            CREATE TABLE IF NOT EXISTS job_batches (
                id varchar(255) PRIMARY KEY,
                name varchar(255) NOT NULL,
                total_jobs integer NOT NULL,
                pending_jobs integer NOT NULL,
                failed_jobs integer NOT NULL,
                failed_job_ids text NOT NULL,
                options text NULL,
                cancelled_at integer NULL,
                created_at integer NOT NULL,
                finished_at integer NULL
            );
        ');

        DB::unprepared('
            CREATE TABLE IF NOT EXISTS failed_jobs (
                id bigserial PRIMARY KEY,
                uuid varchar(255) NOT NULL UNIQUE,
                connection text NOT NULL,
                queue text NOT NULL,
                payload text NOT NULL,
                exception text NOT NULL,
                failed_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP
            );
        ');

        DB::unprepared('
            CREATE TABLE IF NOT EXISTS sessions (
                id varchar(255) PRIMARY KEY,
                user_id bigint NULL,
                ip_address varchar(45) NULL,
                user_agent text NULL,
                payload text NOT NULL,
                last_activity integer NOT NULL
            );
        ');

        DB::unprepared('CREATE INDEX IF NOT EXISTS sessions_user_id_index ON sessions(user_id);');
        DB::unprepared('CREATE INDEX IF NOT EXISTS sessions_last_activity_index ON sessions(last_activity);');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('job_batches');
        Schema::dropIfExists('failed_jobs');
    }
};
