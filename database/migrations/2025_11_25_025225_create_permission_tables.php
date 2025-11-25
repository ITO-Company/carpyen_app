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
            CREATE TABLE IF NOT EXISTS permissions (
                id bigserial PRIMARY KEY,
                name varchar(255) NOT NULL,
                guard_name varchar(255) NOT NULL,
                created_at timestamp(0) without time zone NULL,
                updated_at timestamp(0) without time zone NULL,
                UNIQUE(name, guard_name)
            );
        ');

        DB::unprepared('
            CREATE TABLE IF NOT EXISTS roles (
                id bigserial PRIMARY KEY,
                name varchar(255) NOT NULL,
                guard_name varchar(255) NOT NULL,
                created_at timestamp(0) without time zone NULL,
                updated_at timestamp(0) without time zone NULL,
                UNIQUE(name, guard_name)
            );
        ');

        DB::unprepared('
            CREATE TABLE IF NOT EXISTS model_has_permissions (
                permission_id bigint NOT NULL REFERENCES permissions(id) ON DELETE CASCADE,
                model_type varchar(255) NOT NULL,
                model_id bigint NOT NULL,
                PRIMARY KEY (permission_id, model_id, model_type)
            );
        ');

        DB::unprepared('CREATE INDEX IF NOT EXISTS model_has_permissions_model_id_model_type_index ON model_has_permissions(model_id, model_type);');

        DB::unprepared('
            CREATE TABLE IF NOT EXISTS model_has_roles (
                role_id bigint NOT NULL REFERENCES roles(id) ON DELETE CASCADE,
                model_type varchar(255) NOT NULL,
                model_id bigint NOT NULL,
                PRIMARY KEY (role_id, model_id, model_type)
            );
        ');

        DB::unprepared('CREATE INDEX IF NOT EXISTS model_has_roles_model_id_model_type_index ON model_has_roles(model_id, model_type);');

        DB::unprepared('
            CREATE TABLE IF NOT EXISTS role_has_permissions (
                permission_id bigint NOT NULL REFERENCES permissions(id) ON DELETE CASCADE,
                role_id bigint NOT NULL REFERENCES roles(id) ON DELETE CASCADE,
                PRIMARY KEY (permission_id, role_id)
            );
        ');

        app('cache')
            ->store(config('permission.cache.store') != 'default' ? config('permission.cache.store') : null)
            ->forget(config('permission.cache.key'));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP TABLE IF EXISTS role_has_permissions CASCADE;');
        DB::unprepared('DROP TABLE IF EXISTS model_has_roles CASCADE;');
        DB::unprepared('DROP TABLE IF EXISTS model_has_permissions CASCADE;');
        DB::unprepared('DROP TABLE IF EXISTS roles CASCADE;');
        DB::unprepared('DROP TABLE IF EXISTS permissions CASCADE;');
    }
};
