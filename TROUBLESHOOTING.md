# Guía Rápida de Solución de Problemas - Carpyen

## Error: Maximum execution time exceeded

### Causa
El error ocurre cuando las consultas a la base de datos tardan más de 30 segundos.

### Solución Aplicada

1. **Dashboard con Try-Catch**: Se agregó manejo de errores en el dashboard para evitar fallos si las tablas no existen.

2. **Cachés Limpiados**:
```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

3. **Verificar Conexión a Base de Datos**:
```bash
php artisan tinker
DB::connection()->getPdo();
exit
```

### Si el Error Persiste

#### Opción 1: Aumentar Tiempo de Ejecución
Editar `php.ini`:
```ini
max_execution_time = 300
```

#### Opción 2: Verificar Tablas
```bash
php artisan tinker
Schema::hasTable('users')
Schema::hasTable('clientes')
exit
```

#### Opción 3: Re-ejecutar Migraciones
```bash
# Eliminar todas las tablas y recrear
php artisan migrate:fresh

# O usar el script SQL manual
php artisan tinker
DB::unprepared(file_get_contents('database/manual_migration.sql'));
exit
```

### Acceso Temporal sin Autenticación

Si necesitas acceder sin login, puedes usar la ruta raíz:
```
http://localhost:8000/
```

### Comandos Útiles

```bash
# Ver estado de migraciones
php artisan migrate:status

# Ver rutas
php artisan route:list

# Verificar conexión DB
php artisan db:show

# Limpiar todo
php artisan optimize:clear
```

### Solución Rápida

Si el servidor sigue dando error 500, reinicia los servicios:

```bash
# Detener servidor
Ctrl + C (en la terminal de php artisan serve)

# Limpiar cachés
php artisan optimize:clear

# Reiniciar servidor
php artisan serve
```

### Acceso al Sistema

Una vez resuelto:
```
URL: http://localhost:8000
Email: admin@carpyen.com
Password: password
```

### Nota Importante

El dashboard intenta cargar estadísticas de la base de datos. Si las tablas están vacías o no existen, ahora mostrará valores en 0 en lugar de dar error.
