# Script para configurar el archivo .env con PostgreSQL
$envPath = "c:\Users\USUARIO\Desktop\UAGRM\TECNO\Exposición\proyecto\carpyen_project\carpyen_app\.env"

# Leer el contenido actual
$envContent = Get-Content $envPath -Raw

# Configurar las variables de base de datos PostgreSQL
$envContent = $envContent -replace 'DB_CONNECTION=sqlite', 'DB_CONNECTION=pgsql'
$envContent = $envContent -replace '# DB_HOST=127.0.0.1', 'DB_HOST=ep-green-tooth-a4cs3zjo-pooler.us-east-1.aws.neon.tech'
$envContent = $envContent -replace '# DB_PORT=3306', 'DB_PORT=5432'
$envContent = $envContent -replace '# DB_DATABASE=laravel', 'DB_DATABASE=neondb'
$envContent = $envContent -replace '# DB_USERNAME=root', 'DB_USERNAME=neondb_owner'
$envContent = $envContent -replace '# DB_PASSWORD=', 'DB_PASSWORD=npg_WJwGy8ig7EQR'

# Agregar SSL mode si no existe
if ($envContent -notmatch 'DB_SSLMODE') {
    $envContent += "`nDB_SSLMODE=require`n"
}

# Cambiar APP_NAME
$envContent = $envContent -replace 'APP_NAME=Laravel', 'APP_NAME=Carpyen'

# Cambiar APP_LOCALE a español
$envContent = $envContent -replace 'APP_LOCALE=en', 'APP_LOCALE=es'
$envContent = $envContent -replace 'APP_FALLBACK_LOCALE=en', 'APP_FALLBACK_LOCALE=es'

# Guardar los cambios
Set-Content -Path $envPath -Value $envContent

Write-Host "✓ Archivo .env configurado correctamente con PostgreSQL" -ForegroundColor Green
Write-Host "  - Base de datos: neondb" -ForegroundColor Gray
Write-Host "  - Host: ep-green-tooth-a4cs3zjo-pooler.us-east-1.aws.neon.tech" -ForegroundColor Gray
Write-Host "  - Puerto: 5432" -ForegroundColor Gray
