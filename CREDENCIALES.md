# Carpyen - Credenciales de Acceso

## 🔐 Usuarios del Sistema

Todos los usuarios tienen la contraseña: **12345**

### 1. Administrador (Acceso Total)
- **Email:** admin@carpyen.com
- **Rol:** ADMIN
- **Permisos:** Acceso completo a todas las funcionalidades

### 2. Vendedor
- **Email:** vendedor@carpyen.com
- **Rol:** VENDEDOR
- **Permisos:** Gestión de clientes, proyectos, cotizaciones

### 3. Jefe Instalador
- **Email:** jefe@carpyen.com
- **Rol:** JEFE_INSTALADOR
- **Permisos:** Gestión de cronogramas, asignación de tareas

### 4. Diseñador
- **Email:** disenador@carpyen.com
- **Rol:** DISEÑADOR
- **Permisos:** Gestión de diseños y cotizaciones

### 5. Instalador
- **Email:** instalador@carpyen.com
- **Rol:** INSTALADOR
- **Permisos:** Visualización de tareas asignadas

## 🚀 Acceso a la Aplicación

**URL:** http://localhost:8000

## 🎨 Características del Sistema

- **3 Temas:** Azul, Verde, Violeta
- **Modo Día/Noche:** Automático según hora
- **Accesibilidad:** Tamaño de fuente y contraste ajustables
- **Dashboard:** Estadísticas en tiempo real
- **Gestión Completa:** Clientes, Proyectos, Productos, Pagos

## 📝 Comandos Útiles

```bash
# Iniciar servidor
php artisan serve

# Compilar assets (desarrollo)
npm run dev

# Compilar assets (producción)
npm run build

# Limpiar cachés
php artisan optimize:clear

# Ver usuarios
php artisan tinker
User::all();
exit
```

## 🔄 Resetear Usuarios

Si necesitas recrear los usuarios:

```bash
php artisan db:seed --class=UserSeeder
```

## 💡 Notas Importantes

- La contraseña de todos los usuarios es **12345** (para desarrollo)
- Cambia las contraseñas en producción
- El sistema usa PostgreSQL (Neon.tech)
- Los assets están compilados y listos para usar
