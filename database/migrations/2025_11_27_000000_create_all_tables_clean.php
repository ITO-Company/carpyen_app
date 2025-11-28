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
        // Limpiar TODOS los caches de PostgreSQL antes de crear tablas
        try {
            DB::unprepared('DEALLOCATE ALL;');
        } catch (\Exception $e) {
            // Ignorar si no hay prepared statements
        }
        
        try {
            DB::unprepared('DISCARD PLANS;');
        } catch (\Exception $e) {
            // Ignorar si falla
        }

        // Crear tabla usuarios (debe existir primero para las FK)
        DB::unprepared('
            CREATE TABLE IF NOT EXISTS usuarios (
                id BIGSERIAL PRIMARY KEY,
                nombre VARCHAR(255) NOT NULL,
                email VARCHAR(255) UNIQUE NOT NULL,
                password VARCHAR(255) NOT NULL,
                rol VARCHAR(50) DEFAULT \'VENDEDOR\',
                telefono VARCHAR(255) NULL,
                direccion VARCHAR(255) NULL,
                token VARCHAR(100) NULL,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL
            );
        ');

        // Crear tabla clientes
        DB::unprepared('
            CREATE TABLE IF NOT EXISTS clientes (
                id BIGSERIAL PRIMARY KEY,
                nombre VARCHAR(255) NOT NULL,
                email VARCHAR(255) UNIQUE NOT NULL,
                telefono VARCHAR(255) NOT NULL,
                direccion VARCHAR(255) NOT NULL,
                password VARCHAR(255) NOT NULL,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL
            );
        ');

        // Crear tabla proyectos
        DB::unprepared('
            CREATE TABLE IF NOT EXISTS proyectos (
                id BIGSERIAL PRIMARY KEY,
                nombre VARCHAR(255) NOT NULL,
                descripcion TEXT NULL,
                ubicacion VARCHAR(255) NULL,
                estado VARCHAR(50) DEFAULT \'pendiente\' CHECK (estado IN (\'pendiente\', \'en_proceso\', \'completado\', \'cancelado\')),
                cliente_id BIGINT NOT NULL REFERENCES clientes(id) ON DELETE CASCADE,
                user_id BIGINT NULL REFERENCES usuarios(id) ON DELETE SET NULL,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL
            );
        ');

        // Crear tabla cotizaciones
        DB::unprepared('
            CREATE TABLE IF NOT EXISTS cotizaciones (
                id BIGSERIAL PRIMARY KEY,
                proyecto_id BIGINT NOT NULL REFERENCES proyectos(id) ON DELETE CASCADE,
                tipo_metro VARCHAR(50) NULL,
                costo_metro DECIMAL(10, 2) NULL,
                cantidad_metro INTEGER NULL,
                costo_mueble DECIMAL(10, 2) NULL,
                mueble_numero INTEGER NULL,
                total DECIMAL(10, 2) NOT NULL,
                estado VARCHAR(50) DEFAULT \'pendiente\' CHECK (estado IN (\'pendiente\', \'aprobada\', \'rechazada\')),
                comentario TEXT NULL,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL
            );
        ');

        // Crear tabla disenos
        DB::unprepared('
            CREATE TABLE IF NOT EXISTS disenos (
                id BIGSERIAL PRIMARY KEY,
                cotizacion_id BIGINT NULL REFERENCES cotizaciones(id) ON DELETE CASCADE,
                proyecto_id BIGINT NULL REFERENCES proyectos(id) ON DELETE CASCADE,
                user_id BIGINT NULL REFERENCES usuarios(id) ON DELETE SET NULL,
                url_render VARCHAR(500) NULL,
                plano_iluminador VARCHAR(255) NULL,
                aprovado BOOLEAN DEFAULT FALSE,
                fecha_aprovacion DATE NULL,
                comentario TEXT NULL,
                descripcion TEXT NULL,
                estado VARCHAR(50) DEFAULT \'pendiente\' CHECK (estado IN (\'pendiente\', \'en_proceso\', \'completado\', \'rechazado\')),
                fecha_inicio DATE NULL,
                fecha_fin DATE NULL,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL
            );
        ');

        // Crear tabla cronogramas
        DB::unprepared('
            CREATE TABLE IF NOT EXISTS cronogramas (
                id BIGSERIAL PRIMARY KEY,
                proyecto_id BIGINT NOT NULL REFERENCES proyectos(id) ON DELETE CASCADE,
                usuario_id BIGINT NOT NULL REFERENCES usuarios(id) ON DELETE CASCADE,
                fecha_inicio DATE NOT NULL,
                fecha_fin DATE NULL,
                dias_estimados INTEGER NOT NULL,
                estado VARCHAR(50) DEFAULT \'pendiente\' CHECK (estado IN (\'pendiente\', \'en_curso\', \'completado\', \'atrasado\')),
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL
            );
        ');

        // Crear tabla tareas
        DB::unprepared('
            CREATE TABLE IF NOT EXISTS tareas (
                id BIGSERIAL PRIMARY KEY,
                cronograma_id BIGINT NOT NULL REFERENCES cronogramas(id) ON DELETE CASCADE,
                user_id BIGINT NULL REFERENCES usuarios(id) ON DELETE SET NULL,
                fecha DATE NOT NULL,
                hora_inicio TIME NULL,
                hora_fin TIME NULL,
                descripcion TEXT NULL,
                estado VARCHAR(50) DEFAULT \'pendiente\' CHECK (estado IN (\'pendiente\', \'en_proceso\', \'completada\', \'cancelada\')),
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL
            );
        ');

        // Crear tabla productos
        DB::unprepared('
            CREATE TABLE IF NOT EXISTS productos (
                id BIGSERIAL PRIMARY KEY,
                nombre VARCHAR(255) NOT NULL,
                tipo VARCHAR(255) NULL,
                unidad_medida VARCHAR(255) DEFAULT \'unidad\',
                precio_unitario DECIMAL(10, 2) NOT NULL,
                stock INTEGER DEFAULT 0,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL
            );
        ');

        // Crear tabla proveedores
        DB::unprepared('
            CREATE TABLE IF NOT EXISTS proveedores (
                id BIGSERIAL PRIMARY KEY,
                nombre VARCHAR(255) NOT NULL,
                contacto VARCHAR(255) NULL,
                telefono VARCHAR(255) NULL,
                email VARCHAR(255) NULL,
                ubicacion VARCHAR(255) NULL,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL
            );
        ');

        // Crear tabla producto_proveedor
        DB::unprepared('
            CREATE TABLE IF NOT EXISTS producto_proveedor (
                id BIGSERIAL PRIMARY KEY,
                producto_id BIGINT NOT NULL REFERENCES productos(id) ON DELETE CASCADE,
                proveedor_id BIGINT NOT NULL REFERENCES proveedores(id) ON DELETE CASCADE,
                cantidad INTEGER NOT NULL,
                precio_unitario DECIMAL(10, 2) NOT NULL,
                total DECIMAL(10, 2) NOT NULL,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL
            );
        ');

        // Crear tabla proyecto_producto
        DB::unprepared('
            CREATE TABLE IF NOT EXISTS proyecto_producto (
                id BIGSERIAL PRIMARY KEY,
                proyecto_id BIGINT NOT NULL REFERENCES proyectos(id) ON DELETE CASCADE,
                producto_id BIGINT NOT NULL REFERENCES productos(id) ON DELETE CASCADE,
                cantidad INTEGER NOT NULL,
                sobrante DECIMAL(10, 2) DEFAULT 0,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL
            );
        ');

        // Crear tabla plan_pagos
        DB::unprepared('
            CREATE TABLE IF NOT EXISTS plan_pagos (
                id BIGSERIAL PRIMARY KEY,
                proyecto_id BIGINT NOT NULL REFERENCES proyectos(id) ON DELETE CASCADE,
                deuda_total DECIMAL(10, 2) NOT NULL,
                pagado_total DECIMAL(10, 2) DEFAULT 0,
                numero_deudas INTEGER NOT NULL,
                numero_pagos INTEGER DEFAULT 0,
                estado VARCHAR(50) DEFAULT \'activo\' CHECK (estado IN (\'activo\', \'completado\', \'mora\')),
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL
            );
        ');

        // Crear tabla pagos
        DB::unprepared('
            CREATE TABLE IF NOT EXISTS pagos (
                id BIGSERIAL PRIMARY KEY,
                plan_pago_id BIGINT NOT NULL REFERENCES plan_pagos(id) ON DELETE CASCADE,
                fecha DATE NOT NULL,
                total DECIMAL(10, 2) NOT NULL,
                estado VARCHAR(50) DEFAULT \'pendiente\' CHECK (estado IN (\'pendiente\', \'completado\', \'fallido\')),
                metodo_pago VARCHAR(255) NULL,
                transaccion_id VARCHAR(255) NULL,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL
            );
        ');

        // Crear tabla menus
        DB::unprepared('
            CREATE TABLE IF NOT EXISTS menus (
                id BIGSERIAL PRIMARY KEY,
                nombre VARCHAR(255) NOT NULL,
                icono VARCHAR(255) NULL,
                ruta VARCHAR(255) NULL,
                orden INTEGER DEFAULT 0,
                parent_id BIGINT NULL REFERENCES menus(id) ON DELETE CASCADE,
                roles JSON NULL,
                activo BOOLEAN DEFAULT TRUE,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL
            );
        ');

        // Crear tabla paginas_visitadas
        DB::unprepared('
            CREATE TABLE IF NOT EXISTS paginas_visitadas (
                id BIGSERIAL PRIMARY KEY,
                nombre_pagina VARCHAR(255) NOT NULL,
                pagina_url VARCHAR(255) UNIQUE NOT NULL,
                contador_vistas BIGINT DEFAULT 0,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL
            );
        ');

        // Crear índices
        DB::unprepared('CREATE INDEX IF NOT EXISTS cronogramas_proyecto_id_index ON cronogramas(proyecto_id);');
        DB::unprepared('CREATE INDEX IF NOT EXISTS cronogramas_usuario_id_index ON cronogramas(usuario_id);');
        DB::unprepared('CREATE INDEX IF NOT EXISTS cronogramas_estado_index ON cronogramas(estado);');

        // Limpiar caches después de crear todas las tablas
        try {
            DB::unprepared('DEALLOCATE ALL;');
        } catch (\Exception $e) {
            // Ignorar
        }
        
        try {
            DB::unprepared('DISCARD PLANS;');
        } catch (\Exception $e) {
            // Ignorar
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Limpiar caches de PostgreSQL antes de borrar tablas
        try {
            DB::unprepared('DEALLOCATE ALL;');
        } catch (\Exception $e) {
            // Ignorar si no hay prepared statements
        }
        
        try {
            DB::unprepared('DISCARD PLANS;');
        } catch (\Exception $e) {
            // Ignorar si falla
        }

        DB::unprepared('DROP TABLE IF EXISTS paginas_visitadas CASCADE;');
        DB::unprepared('DROP TABLE IF EXISTS menus CASCADE;');
        DB::unprepared('DROP TABLE IF EXISTS pagos CASCADE;');
        DB::unprepared('DROP TABLE IF EXISTS plan_pagos CASCADE;');
        DB::unprepared('DROP TABLE IF EXISTS proyecto_producto CASCADE;');
        DB::unprepared('DROP TABLE IF EXISTS producto_proveedor CASCADE;');
        DB::unprepared('DROP TABLE IF EXISTS proveedores CASCADE;');
        DB::unprepared('DROP TABLE IF EXISTS productos CASCADE;');
        DB::unprepared('DROP TABLE IF EXISTS tareas CASCADE;');
        DB::unprepared('DROP TABLE IF EXISTS cronogramas CASCADE;');
        DB::unprepared('DROP TABLE IF EXISTS disenos CASCADE;');
        DB::unprepared('DROP TABLE IF EXISTS cotizaciones CASCADE;');
        DB::unprepared('DROP TABLE IF EXISTS proyectos CASCADE;');
        DB::unprepared('DROP TABLE IF EXISTS clientes CASCADE;');
        DB::unprepared('DROP TABLE IF EXISTS roles CASCADE;');
        DB::unprepared('DROP TABLE IF EXISTS usuarios CASCADE;');
    }
};

