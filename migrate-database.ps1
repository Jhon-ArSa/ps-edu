# =========================================
# Script de Migración de Base de Datos
# PS-EDU FAEDU - AWS RDS a MySQL Local
# PowerShell Version
# =========================================

Write-Host "🔄 Iniciando migración de base de datos..." -ForegroundColor Cyan
Write-Host ""

# Configuración AWS RDS (origen)
$AWS_HOST = "cpapcentro.cr40yws4ssdu.us-east-2.rds.amazonaws.com"
$AWS_PORT = "3306"
$AWS_DB = "ps_edu"
$AWS_USER = "cpapcentro"
$AWS_PASS = "cpapcentro2026"

# Configuración MySQL Local (destino)
$LOCAL_HOST = "127.0.0.1"
$LOCAL_PORT = "3306"
$LOCAL_DB = "posgrado_intranet"
$LOCAL_USER = "root"
$LOCAL_PASS = "admin"

# Archivo de backup
$BACKUP_DIR = "storage\app\backups"
$BACKUP_FILE = "database_backup.sql"
$BACKUP_PATH = "$BACKUP_DIR\$BACKUP_FILE"

# Crear directorio de backups
if (!(Test-Path $BACKUP_DIR)) {
    New-Item -ItemType Directory -Path $BACKUP_DIR -Force | Out-Null
}

Write-Host "📊 Configuración:" -ForegroundColor Blue
Write-Host "  Origen: $AWS_HOST ($AWS_DB)"
Write-Host "  Destino: $LOCAL_HOST ($LOCAL_DB)"
Write-Host ""

# =========================================
# PASO 1: Exportar desde AWS RDS
# =========================================
Write-Host "🔵 PASO 1: Exportando desde AWS RDS..." -ForegroundColor Blue
Write-Host ""

$mysqldumpPath = Get-Command mysqldump -ErrorAction SilentlyContinue

if ($mysqldumpPath) {
    Write-Host "  ✅ mysqldump encontrado" -ForegroundColor Green
    
    $mysqldumpCmd = "mysqldump --host=$AWS_HOST --port=$AWS_PORT --user=$AWS_USER --password=$AWS_PASS --single-transaction --routines --triggers --events --add-drop-table --databases $AWS_DB"
    
    Invoke-Expression "$mysqldumpCmd > $BACKUP_PATH 2>&1"
    
    if ($LASTEXITCODE -eq 0) {
        $fileSize = (Get-Item $BACKUP_PATH).Length / 1MB
        $fileSizeRounded = [math]::Round($fileSize, 2)
        Write-Host "  ✅ Exportación exitosa: $BACKUP_FILE ($fileSizeRounded MB)" -ForegroundColor Green
    } else {
        Write-Host "  ❌ Error al exportar la base de datos" -ForegroundColor Red
        exit 1
    }
} else {
    Write-Host "  ❌ mysqldump no encontrado" -ForegroundColor Red
    Write-Host "  💡 Usa: php artisan db:export-laravel" -ForegroundColor Yellow
    exit 1
}

Write-Host ""

# =========================================
# PASO 2: Verificar MySQL local
# =========================================
Write-Host "🔵 PASO 2: Verificando MySQL local..." -ForegroundColor Blue
Write-Host ""

$mysqlPath = Get-Command mysql -ErrorAction SilentlyContinue

if ($mysqlPath) {
    Write-Host "  ✅ mysql encontrado" -ForegroundColor Green
    
    # Verificar conexión
    $testCmd = "mysql -h$LOCAL_HOST -P$LOCAL_PORT -u$LOCAL_USER -p$LOCAL_PASS -e `"SELECT VERSION();`" 2>&1"
    $result = Invoke-Expression $testCmd
    
    if ($LASTEXITCODE -eq 0) {
        Write-Host "  ✅ Conexión exitosa a MySQL local" -ForegroundColor Green
    } else {
        Write-Host "  ❌ No se pudo conectar a MySQL local" -ForegroundColor Red
        Write-Host "  💡 Verifica que MySQL esté corriendo y las credenciales sean correctas" -ForegroundColor Yellow
        exit 1
    }
} else {
    Write-Host "  ❌ mysql client no encontrado" -ForegroundColor Red
    Write-Host "  💡 Instala MySQL o agrega mysql.exe al PATH" -ForegroundColor Yellow
    exit 1
}

Write-Host ""

# =========================================
# PASO 3: Crear base de datos local
# =========================================
Write-Host "🔵 PASO 3: Creando base de datos local..." -ForegroundColor Blue
Write-Host ""

$createDbCmd = "mysql -h$LOCAL_HOST -P$LOCAL_PORT -u$LOCAL_USER -p$LOCAL_PASS -e `"CREATE DATABASE IF NOT EXISTS \``$LOCAL_DB\`` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;`" 2>&1"
Invoke-Expression $createDbCmd

if ($LASTEXITCODE -eq 0) {
    Write-Host "  ✅ Base de datos '$LOCAL_DB' creada" -ForegroundColor Green
} else {
    Write-Host "  ❌ Error al crear la base de datos" -ForegroundColor Red
    exit 1
}

Write-Host ""

# =========================================
# PASO 4: Importar datos
# =========================================
Write-Host "🔵 PASO 4: Importando datos..." -ForegroundColor Blue
Write-Host ""

# Modificar el archivo SQL para cambiar el nombre de la base de datos
$content = Get-Content $BACKUP_PATH -Raw
$content = $content -replace "\``$AWS_DB\``", "\``$LOCAL_DB\``"
Set-Content -Path $BACKUP_PATH -Value $content

Write-Host "  ⏳ Importando (esto puede tomar varios minutos)..." -ForegroundColor Yellow

$importCmd = "mysql -h$LOCAL_HOST -P$LOCAL_PORT -u$LOCAL_USER -p$LOCAL_PASS < $BACKUP_PATH 2>&1"
Invoke-Expression $importCmd

if ($LASTEXITCODE -eq 0) {
    Write-Host "  ✅ Importación exitosa" -ForegroundColor Green
    
    # Contar tablas
    $tableCountCmd = "mysql -h$LOCAL_HOST -P$LOCAL_PORT -u$LOCAL_USER -p$LOCAL_PASS -e `"USE $LOCAL_DB; SHOW TABLES;`" -s -N 2>&1"
    $tables = Invoke-Expression $tableCountCmd
    $tableCount = ($tables -split "`n").Count
    Write-Host "  📊 Total de tablas: $tableCount" -ForegroundColor Green
    
    Write-Host ""
    Write-Host "  ✅ Verificando datos:" -ForegroundColor Green
    
    # Contar usuarios
    $userCountCmd = "mysql -h$LOCAL_HOST -P$LOCAL_PORT -u$LOCAL_USER -p$LOCAL_PASS -D$LOCAL_DB -e `"SELECT COUNT(*) FROM users;`" -s -N 2>&1"
    $userCount = Invoke-Expression $userCountCmd
    Write-Host "    • Usuarios: $userCount"
    
    # Contar cursos
    $courseCountCmd = "mysql -h$LOCAL_HOST -P$LOCAL_PORT -u$LOCAL_USER -p$LOCAL_PASS -D$LOCAL_DB -e `"SELECT COUNT(*) FROM courses;`" -s -N 2>&1"
    $courseCount = Invoke-Expression $courseCountCmd
    Write-Host "    • Cursos: $courseCount"
    
    # Contar matrículas
    $enrollmentCountCmd = "mysql -h$LOCAL_HOST -P$LOCAL_PORT -u$LOCAL_USER -p$LOCAL_PASS -D$LOCAL_DB -e `"SELECT COUNT(*) FROM enrollments;`" -s -N 2>&1"
    $enrollmentCount = Invoke-Expression $enrollmentCountCmd
    Write-Host "    • Matrículas: $enrollmentCount"
    
} else {
    Write-Host "  ❌ Error al importar los datos" -ForegroundColor Red
    exit 1
}

Write-Host ""

# =========================================
# PASO 5: Actualizar .env
# =========================================
Write-Host "🔵 PASO 5: Actualizar archivo .env" -ForegroundColor Blue
Write-Host ""

$response = Read-Host "¿Deseas actualizar el archivo .env con las credenciales locales? (s/n)"

if ($response -eq "s" -or $response -eq "S") {
    # Hacer backup del .env
    $timestamp = Get-Date -Format "yyyyMMdd_HHmmss"
    $backupEnvPath = ".env.backup.$timestamp"
    Copy-Item .env $backupEnvPath
    Write-Host "  ✅ Backup creado: $backupEnvPath" -ForegroundColor Green
    
    # Actualizar .env
    $envContent = Get-Content .env -Raw
    $envContent = $envContent -replace "DB_HOST=.*", "DB_HOST=127.0.0.1"
    $envContent = $envContent -replace "DB_PORT=.*", "DB_PORT=3306"
    $envContent = $envContent -replace "DB_DATABASE=.*", "DB_DATABASE=posgrado_intranet"
    $envContent = $envContent -replace "DB_USERNAME=.*", "DB_USERNAME=root"
    $envContent = $envContent -replace "DB_PASSWORD=.*", "DB_PASSWORD=admin"
    Set-Content -Path .env -Value $envContent
    
    Write-Host "  ✅ Archivo .env actualizado" -ForegroundColor Green
    Write-Host ""
    Write-Host "  ⚠️  Ejecuta estos comandos:" -ForegroundColor Yellow
    Write-Host "    php artisan config:clear"
    Write-Host "    php artisan cache:clear"
} else {
    Write-Host "  ⏭️  .env no actualizado" -ForegroundColor Yellow
}

Write-Host ""

# =========================================
# RESUMEN
# =========================================
Write-Host "╔════════════════════════════════════════╗" -ForegroundColor Green
Write-Host "║   🎉 MIGRACIÓN COMPLETADA EXITOSA     ║" -ForegroundColor Green
Write-Host "╚════════════════════════════════════════╝" -ForegroundColor Green
Write-Host ""
Write-Host "📋 Siguientes pasos:" -ForegroundColor Blue
Write-Host "  1. php artisan config:clear"
Write-Host "  2. php artisan cache:clear"
Write-Host "  3. php artisan db:show"
Write-Host "  4. php artisan serve"
Write-Host ""
Write-Host "📁 Archivos generados:" -ForegroundColor Blue
Write-Host "  • $BACKUP_PATH"
if ($response -eq "s" -or $response -eq "S") {
    Write-Host "  • $backupEnvPath"
}
Write-Host ""
Write-Host "✅ ¡Todo listo para trabajar en local!" -ForegroundColor Green
Write-Host ""
