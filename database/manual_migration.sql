-- Script SQL para crear todas las tablas manualmente
-- Ejecutar este script si las migraciones de Laravel fallan

-- Eliminar tablas existentes si existen
DROP TABLE IF EXISTS page_visits CASCADE;
DROP TABLE IF EXISTS menus CASCADE;
DROP TABLE IF EXISTS pagos CASCADE;
DROP TABLE IF EXISTS plan_pagos CASCADE;
DROP TABLE IF EXISTS proyecto_producto CASCADE;
DROP TABLE IF EXISTS producto_proveedor CASCADE;
DROP TABLE IF EXISTS proveedores CASCADE;
DROP TABLE IF EXISTS productos CASCADE;
DROP TABLE IF EXISTS tareas CASCADE;
DROP TABLE IF EXISTS cronogramas CASCADE;
DROP TABLE IF EXISTS disenos CASCADE;
DROP TABLE IF EXISTS cotizaciones CASCADE;
DROP TABLE IF EXISTS proyectos CASCADE;
DROP TABLE IF EXISTS clientes CASCADE;
DROP TABLE IF EXISTS password_reset_tokens CASCADE;
DROP TABLE IF EXISTS sessions CASCADE;
DROP TABLE IF EXISTS users CASCADE;
DROP TABLE IF EXISTS migrations CASCADE;
DROP TABLE IF EXISTS permissions CASCADE;
DROP TABLE IF EXISTS roles CASCADE;
DROP TABLE IF EXISTS model_has_permissions CASCADE;
DROP TABLE IF EXISTS model_has_roles CASCADE;
DROP TABLE IF EXISTS role_has_permissions CASCADE;

-- Crear tabla migrations
CREATE TABLE migrations (
    id SERIAL PRIMARY KEY,
    migration VARCHAR(255) NOT NULL,
    batch INTEGER NOT NULL
);

-- Crear tabla users
CREATE TABLE users (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    telefono VARCHAR(255) NULL,
    direccion VARCHAR(255) NULL,
    contrasena VARCHAR(255) NULL,
    rol VARCHAR(255) DEFAULT 'INSTALADOR',
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

-- Crear tabla password_reset_tokens
CREATE TABLE password_reset_tokens (
    email VARCHAR(255) PRIMARY KEY,
    token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL
);

-- Crear tabla sessions
CREATE TABLE sessions (
    id VARCHAR(255) PRIMARY KEY,
    user_id BIGINT NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    payload TEXT NOT NULL,
    last_activity INTEGER NOT NULL
);

CREATE INDEX sessions_user_id_index ON sessions(user_id);
CREATE INDEX sessions_last_activity_index ON sessions(last_activity);

-- Crear tabla clientes
CREATE TABLE clientes (
    id BIGSERIAL PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    telefono VARCHAR(255) NOT NULL,
    direccion VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

-- Crear tabla proyectos
CREATE TABLE proyectos (
    id BIGSERIAL PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT NULL,
    ubicacion VARCHAR(255) NULL,
    estado VARCHAR(50) DEFAULT 'pendiente' CHECK (estado IN ('pendiente', 'en_proceso', 'completado', 'cancelado')),
    cliente_id BIGINT NOT NULL REFERENCES clientes(id) ON DELETE CASCADE,
    user_id BIGINT NULL REFERENCES users(id) ON DELETE SET NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

-- Crear tabla cotizaciones
CREATE TABLE cotizaciones (
    id BIGSERIAL PRIMARY KEY,
    proyecto_id BIGINT NOT NULL REFERENCES proyectos(id) ON DELETE CASCADE,
    hipo_metro DECIMAL(10, 2) NULL,
    costo_metro DECIMAL(10, 2) NULL,
    cantidad_metro INTEGER NULL,
    costo_mueble DECIMAL(10, 2) NULL,
    mueble_numero INTEGER NULL,
    total DECIMAL(10, 2) NOT NULL,
    estado VARCHAR(50) DEFAULT 'pendiente' CHECK (estado IN ('pendiente', 'aprobada', 'rechazada')),
    comentario TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

-- Crear tabla disenos
CREATE TABLE disenos (
    id BIGSERIAL PRIMARY KEY,
    cotizacion_id BIGINT NOT NULL REFERENCES cotizaciones(id) ON DELETE CASCADE,
    user_id BIGINT NULL REFERENCES users(id) ON DELETE SET NULL,
    url_render VARCHAR(255) NULL,
    plano_iluminador VARCHAR(255) NULL,
    aprovado BOOLEAN DEFAULT FALSE,
    fecha_aprovacion DATE NULL,
    comentario TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

-- Crear tabla cronogramas
CREATE TABLE cronogramas (
    id BIGSERIAL PRIMARY KEY,
    proyecto_id BIGINT NOT NULL REFERENCES proyectos(id) ON DELETE CASCADE,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NOT NULL,
    dias_estimados INTEGER NOT NULL,
    estado VARCHAR(50) DEFAULT 'pendiente' CHECK (estado IN ('pendiente', 'en_curso', 'completado', 'atrasado')),
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

-- Crear tabla tareas
CREATE TABLE tareas (
    id BIGSERIAL PRIMARY KEY,
    cronograma_id BIGINT NOT NULL REFERENCES cronogramas(id) ON DELETE CASCADE,
    user_id BIGINT NULL REFERENCES users(id) ON DELETE SET NULL,
    hora_inicio TIME NULL,
    hora_fin TIME NULL,
    descripcion TEXT NULL,
    estado VARCHAR(50) DEFAULT 'pendiente' CHECK (estado IN ('pendiente', 'en_proceso', 'completada', 'cancelada')),
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

-- Crear tabla productos
CREATE TABLE productos (
    id BIGSERIAL PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    tipo VARCHAR(255) NULL,
    unidad_medida VARCHAR(255) DEFAULT 'unidad',
    precio_unitario DECIMAL(10, 2) NOT NULL,
    stock INTEGER DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

-- Crear tabla proveedores
CREATE TABLE proveedores (
    id BIGSERIAL PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    contacto VARCHAR(255) NULL,
    telefono VARCHAR(255) NULL,
    email VARCHAR(255) NULL,
    ubicacion VARCHAR(255) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

-- Crear tabla producto_proveedor
CREATE TABLE producto_proveedor (
    id BIGSERIAL PRIMARY KEY,
    producto_id BIGINT NOT NULL REFERENCES productos(id) ON DELETE CASCADE,
    proveedor_id BIGINT NOT NULL REFERENCES proveedores(id) ON DELETE CASCADE,
    cantidad INTEGER NOT NULL,
    precio_unitario DECIMAL(10, 2) NOT NULL,
    total DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

-- Crear tabla proyecto_producto
CREATE TABLE proyecto_producto (
    id BIGSERIAL PRIMARY KEY,
    proyecto_id BIGINT NOT NULL REFERENCES proyectos(id) ON DELETE CASCADE,
    producto_id BIGINT NOT NULL REFERENCES productos(id) ON DELETE CASCADE,
    cantidad INTEGER NOT NULL,
    sobrante DECIMAL(10, 2) DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

-- Crear tabla plan_pagos
CREATE TABLE plan_pagos (
    id BIGSERIAL PRIMARY KEY,
    proyecto_id BIGINT NOT NULL REFERENCES proyectos(id) ON DELETE CASCADE,
    deuda_total DECIMAL(10, 2) NOT NULL,
    pagado_total DECIMAL(10, 2) DEFAULT 0,
    numero_deudas INTEGER NOT NULL,
    numero_pagos INTEGER DEFAULT 0,
    estado VARCHAR(50) DEFAULT 'activo' CHECK (estado IN ('activo', 'completado', 'mora')),
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

-- Crear tabla pagos
CREATE TABLE pagos (
    id BIGSERIAL PRIMARY KEY,
    plan_pago_id BIGINT NOT NULL REFERENCES plan_pagos(id) ON DELETE CASCADE,
    fecha DATE NOT NULL,
    total DECIMAL(10, 2) NOT NULL,
    estado VARCHAR(50) DEFAULT 'pendiente' CHECK (estado IN ('pendiente', 'completado', 'fallido')),
    metodo_pago VARCHAR(255) NULL,
    transaccion_id VARCHAR(255) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

-- Crear tabla menus
CREATE TABLE menus (
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

-- Crear tabla page_visits
CREATE TABLE page_visits (
    id BIGSERIAL PRIMARY KEY,
    page_name VARCHAR(255) NOT NULL,
    page_url VARCHAR(255) UNIQUE NOT NULL,
    visit_count BIGINT DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

-- Tablas de Spatie Permission
CREATE TABLE permissions (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    guard_name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    UNIQUE(name, guard_name)
);

CREATE TABLE roles (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    guard_name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    UNIQUE(name, guard_name)
);

CREATE TABLE model_has_permissions (
    permission_id BIGINT NOT NULL REFERENCES permissions(id) ON DELETE CASCADE,
    model_type VARCHAR(255) NOT NULL,
    model_id BIGINT NOT NULL,
    PRIMARY KEY (permission_id, model_id, model_type)
);

CREATE INDEX model_has_permissions_model_id_model_type_index ON model_has_permissions(model_id, model_type);

CREATE TABLE model_has_roles (
    role_id BIGINT NOT NULL REFERENCES roles(id) ON DELETE CASCADE,
    model_type VARCHAR(255) NOT NULL,
    model_id BIGINT NOT NULL,
    PRIMARY KEY (role_id, model_id, model_type)
);

CREATE INDEX model_has_roles_model_id_model_type_index ON model_has_roles(model_id, model_type);

CREATE TABLE role_has_permissions (
    permission_id BIGINT NOT NULL REFERENCES permissions(id) ON DELETE CASCADE,
    role_id BIGINT NOT NULL REFERENCES roles(id) ON DELETE CASCADE,
    PRIMARY KEY (permission_id, role_id)
);

-- Insertar registros en la tabla migrations
INSERT INTO migrations (migration, batch) VALUES
('0001_01_01_000000_create_users_table', 1),
('0001_01_01_000001_create_cache_table', 1),
('0001_01_01_000002_create_jobs_table', 1),
('2025_11_25_025225_create_permission_tables', 1),
('2025_11_25_025343_create_clientes_table', 1),
('2025_11_25_025348_create_proyectos_table', 1),
('2025_11_25_025356_create_cotizaciones_table', 1),
('2025_11_25_025358_create_disenos_table', 1),
('2025_11_25_025423_create_cronogramas_table', 1),
('2025_11_25_025425_create_tareas_table', 1),
('2025_11_25_025427_create_productos_table', 1),
('2025_11_25_025445_create_proveedores_table', 1),
('2025_11_25_025457_create_producto_proveedor_table', 1),
('2025_11_25_025459_create_proyecto_producto_table', 1),
('2025_11_25_025518_create_pagos_table', 1),
('2025_11_25_025520_create_plan_pagos_table', 1),
('2025_11_25_025521_create_menus_table', 1),
('2025_11_25_025523_create_page_visits_table', 1),
('2025_11_25_999999_modify_users_table_for_carpyen', 1);

-- Mensaje de éxito
SELECT 'Base de datos creada exitosamente!' AS mensaje;
