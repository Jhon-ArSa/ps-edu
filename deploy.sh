#!/bin/bash

# ══════════════════════════════════════════════════════════════════════════════
# SCRIPT DE DEPLOYMENT PARA PS-EDU
# ══════════════════════════════════════════════════════════════════════════════
#
# Este script automatiza el proceso de deployment en producción.
#
# USO:
#   chmod +x deploy.sh
#   ./deploy.sh
#
# REQUISITOS:
#   - Git configurado
#   - Composer instalado
#   - Node.js y npm instalados
#   - Permisos de escritura en storage/ y bootstrap/cache/
# ══════════════════════════════════════════════════════════════════════════════

set -e  # Detener si hay algún error

echo "════════════════════════════════════════════════════════════════════════════"
echo "  🚀 DEPLOYMENT PS-EDU — $(date '+%Y-%m-%d %H:%M:%S')"
echo "════════════════════════════════════════════════════════════════════════════"
echo ""

# ── 1. MODO MANTENIMIENTO ─────────────────────────────────────────────────────
echo "📋 [1/12] Activando modo mantenimiento..."
php artisan down --retry=60 --secret="deploy-secret-token" || true
echo "✅ Modo mantenimiento activado"
echo ""

# ── 2. PULL DE CAMBIOS ────────────────────────────────────────────────────────
echo "📥 [2/12] Descargando últimos cambios desde Git..."
git pull origin main
echo "✅ Cambios descargados"
echo ""

# ── 3. INSTALAR DEPENDENCIAS PHP ──────────────────────────────────────────────
echo "📦 [3/12] Instalando dependencias de Composer..."
composer install --no-dev --optimize-autoloader --no-interaction
echo "✅ Dependencias PHP instaladas"
echo ""

# ── 4. INSTALAR DEPENDENCIAS JS ───────────────────────────────────────────────
echo "📦 [4/12] Instalando dependencias de npm..."
npm ci --production=false
echo "✅ Dependencias JS instaladas"
echo ""

# ── 5. COMPILAR ASSETS ────────────────────────────────────────────────────────
echo "🎨 [5/12] Compilando assets (CSS/JS)..."
npm run build
echo "✅ Assets compilados"
echo ""

# ── 6. LIMPIAR CACHES ANTIGUAS ────────────────────────────────────────────────
echo "🧹 [6/12] Limpiando caches antiguas..."
php artisan optimize:clear
echo "✅ Caches limpiadas"
echo ""

# ── 7. EJECUTAR MIGRACIONES ───────────────────────────────────────────────────
echo "🗄️  [7/12] Ejecutando migraciones de base de datos..."
php artisan migrate --force
echo "✅ Migraciones ejecutadas"
echo ""

# ── 8. CREAR SYMLINK DE STORAGE ───────────────────────────────────────────────
echo "🔗 [8/12] Creando symlink de storage..."
php artisan storage:link || echo "⚠️  Symlink ya existe"
echo "✅ Symlink verificado"
echo ""

# ── 9. OPTIMIZAR APLICACIÓN ───────────────────────────────────────────────────
echo "⚡ [9/12] Optimizando aplicación..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
echo "✅ Aplicación optimizada"
echo ""

# ── 10. PERMISOS ──────────────────────────────────────────────────────────────
echo "🔐 [10/12] Configurando permisos..."
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache || echo "⚠️  No se pudieron cambiar propietarios (ejecutar con sudo si es necesario)"
echo "✅ Permisos configurados"
echo ""

# ── 11. REINICIAR QUEUES ──────────────────────────────────────────────────────
echo "🔄 [11/12] Reiniciando workers de colas..."
php artisan queue:restart
echo "✅ Workers reiniciados"
echo ""

# ── 12. DESACTIVAR MODO MANTENIMIENTO ─────────────────────────────────────────
echo "🟢 [12/12] Desactivando modo mantenimiento..."
php artisan up
echo "✅ Aplicación en línea"
echo ""

echo "════════════════════════════════════════════════════════════════════════════"
echo "  ✅ DEPLOYMENT COMPLETADO EXITOSAMENTE"
echo "════════════════════════════════════════════════════════════════════════════"
echo ""
echo "📊 Verificaciones recomendadas:"
echo "   1. Verificar que el sitio carga: curl -I https://psedu.adesa.edu.pe"
echo "   2. Verificar logs: tail -f storage/logs/laravel.log"
echo "   3. Verificar queues: php artisan queue:monitor"
echo "   4. Verificar supervisor: sudo supervisorctl status"
echo ""
echo "🔗 Acceso durante mantenimiento (próximos 60 min):"
echo "   https://psedu.adesa.edu.pe?secret=deploy-secret-token"
echo ""
