#!/bin/bash

# =========================================
# Script de Migración de Base de Datos
# PS-EDU FAEDU - AWS RDS a MySQL Local
# =========================================

echo "🔄 Iniciando migración de base de datos..."
echo ""

# Colores para output
GREEN='\033[0;32m'
BLUE='\033[0;34m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Configuración AWS RDS (origen)
AWS_HOST="cpapcentro.cr40yws4ssdu.us-east-2.rds.amazonaws.com"
AWS_PORT="3306"
AWS_DB="ps_edu"
AWS_USER="cpapcentro"
AWS_PASS="cpapcentro2026"

# Configuración MySQL Local (destino)
LOCAL_HOST="127.0.0.1"
LOCAL_PORT="3306"
LOCAL_DB="posgrado_intranet"
LOCAL_USER="root"
LOCAL_PASS="admin"

# Archivo de backup
BACKUP_DIR="storage/app/backups"
BACKUP_FILE="database_backup.sql"
BACKUP_PATH="${BACKUP_DIR}/${BACKUP_FILE}"

# Crear directorio de backups
mkdir -p "$BACKUP_DIR"

echo -e "${BLUE}📊 Configuración:${NC}"
echo "  Origen: ${AWS_HOST} (${AWS_DB})"
echo "  Destino: ${LOCAL_HOST} (${LOCAL_DB})"
echo ""

# =========================================
# PASO 1: Exportar desde AWS RDS
# =========================================
echo -e "${BLUE}🔵 PASO 1: Exportando desde AWS RDS...${NC}"
echo ""

if command -v mysqldump &> /dev/null; then
    echo "  ✅ mysqldump encontrado"
    
    mysqldump --host="$AWS_HOST" \
              --port="$AWS_PORT" \
              --user="$AWS_USER" \
              --password="$AWS_PASS" \
              --single-transaction \
              --routines \
              --triggers \
              --events \
              --add-drop-table \
              --databases "$AWS_DB" > "$BACKUP_PATH" 2>&1
    
    if [ $? -eq 0 ]; then
        FILE_SIZE=$(du -h "$BACKUP_PATH" | cut -f1)
        echo -e "  ${GREEN}✅ Exportación exitosa: ${BACKUP_FILE} (${FILE_SIZE})${NC}"
    else
        echo -e "  ${RED}❌ Error al exportar la base de datos${NC}"
        exit 1
    fi
else
    echo -e "  ${RED}❌ mysqldump no encontrado${NC}"
    echo -e "  ${YELLOW}💡 Usa: php artisan db:export-laravel${NC}"
    exit 1
fi

echo ""

# =========================================
# PASO 2: Verificar MySQL local
# =========================================
echo -e "${BLUE}🔵 PASO 2: Verificando MySQL local...${NC}"
echo ""

if command -v mysql &> /dev/null; then
    echo "  ✅ mysql encontrado"
    
    # Verificar conexión
    mysql -h"$LOCAL_HOST" \
          -P"$LOCAL_PORT" \
          -u"$LOCAL_USER" \
          -p"$LOCAL_PASS" \
          -e "SELECT VERSION();" &> /dev/null
    
    if [ $? -eq 0 ]; then
        MYSQL_VERSION=$(mysql -h"$LOCAL_HOST" -P"$LOCAL_PORT" -u"$LOCAL_USER" -p"$LOCAL_PASS" -e "SELECT VERSION();" -s -N 2>/dev/null)
        echo -e "  ${GREEN}✅ Conexión exitosa a MySQL ${MYSQL_VERSION}${NC}"
    else
        echo -e "  ${RED}❌ No se pudo conectar a MySQL local${NC}"
        echo -e "  ${YELLOW}💡 Verifica que MySQL esté corriendo y las credenciales sean correctas${NC}"
        exit 1
    fi
else
    echo -e "  ${RED}❌ mysql client no encontrado${NC}"
    exit 1
fi

echo ""

# =========================================
# PASO 3: Crear base de datos local
# =========================================
echo -e "${BLUE}🔵 PASO 3: Creando base de datos local...${NC}"
echo ""

mysql -h"$LOCAL_HOST" \
      -P"$LOCAL_PORT" \
      -u"$LOCAL_USER" \
      -p"$LOCAL_PASS" \
      -e "CREATE DATABASE IF NOT EXISTS \`${LOCAL_DB}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>&1

if [ $? -eq 0 ]; then
    echo -e "  ${GREEN}✅ Base de datos '${LOCAL_DB}' creada${NC}"
else
    echo -e "  ${RED}❌ Error al crear la base de datos${NC}"
    exit 1
fi

echo ""

# =========================================
# PASO 4: Importar datos
# =========================================
echo -e "${BLUE}🔵 PASO 4: Importando datos...${NC}"
echo ""

# Modificar el archivo SQL para cambiar el nombre de la base de datos
sed -i.bak "s/\`${AWS_DB}\`/\`${LOCAL_DB}\`/g" "$BACKUP_PATH"

echo "  ⏳ Importando (esto puede tomar varios minutos)..."
mysql -h"$LOCAL_HOST" \
      -P"$LOCAL_PORT" \
      -u"$LOCAL_USER" \
      -p"$LOCAL_PASS" \
      < "$BACKUP_PATH" 2>&1

if [ $? -eq 0 ]; then
    echo -e "  ${GREEN}✅ Importación exitosa${NC}"
    
    # Contar tablas
    TABLE_COUNT=$(mysql -h"$LOCAL_HOST" -P"$LOCAL_PORT" -u"$LOCAL_USER" -p"$LOCAL_PASS" -e "USE ${LOCAL_DB}; SHOW TABLES;" -s -N 2>/dev/null | wc -l)
    echo -e "  ${GREEN}📊 Total de tablas: ${TABLE_COUNT}${NC}"
    
    # Mostrar algunos registros
    echo ""
    echo -e "  ${GREEN}✅ Verificando datos:${NC}"
    
    USER_COUNT=$(mysql -h"$LOCAL_HOST" -P"$LOCAL_PORT" -u"$LOCAL_USER" -p"$LOCAL_PASS" -D"$LOCAL_DB" -e "SELECT COUNT(*) FROM users;" -s -N 2>/dev/null)
    echo "    • Usuarios: ${USER_COUNT}"
    
    COURSE_COUNT=$(mysql -h"$LOCAL_HOST" -P"$LOCAL_PORT" -u"$LOCAL_USER" -p"$LOCAL_PASS" -D"$LOCAL_DB" -e "SELECT COUNT(*) FROM courses;" -s -N 2>/dev/null)
    echo "    • Cursos: ${COURSE_COUNT}"
    
    ENROLLMENT_COUNT=$(mysql -h"$LOCAL_HOST" -P"$LOCAL_PORT" -u"$LOCAL_USER" -p"$LOCAL_PASS" -D"$LOCAL_DB" -e "SELECT COUNT(*) FROM enrollments;" -s -N 2>/dev/null)
    echo "    • Matrículas: ${ENROLLMENT_COUNT}"
    
else
    echo -e "  ${RED}❌ Error al importar los datos${NC}"
    exit 1
fi

echo ""

# =========================================
# PASO 5: Actualizar .env
# =========================================
echo -e "${BLUE}🔵 PASO 5: Actualizar archivo .env${NC}"
echo ""

read -p "¿Deseas actualizar el archivo .env con las credenciales locales? (s/n): " UPDATE_ENV

if [[ $UPDATE_ENV == "s" || $UPDATE_ENV == "S" ]]; then
    # Hacer backup del .env
    TIMESTAMP=$(date +%Y%m%d_%H%M%S)
    cp .env ".env.backup.${TIMESTAMP}"
    echo -e "  ${GREEN}✅ Backup creado: .env.backup.${TIMESTAMP}${NC}"
    
    # Actualizar .env
    sed -i.tmp "s/DB_HOST=.*/DB_HOST=127.0.0.1/" .env
    sed -i.tmp "s/DB_PORT=.*/DB_PORT=3306/" .env
    sed -i.tmp "s/DB_DATABASE=.*/DB_DATABASE=posgrado_intranet/" .env
    sed -i.tmp "s/DB_USERNAME=.*/DB_USERNAME=root/" .env
    sed -i.tmp "s/DB_PASSWORD=.*/DB_PASSWORD=admin/" .env
    rm -f .env.tmp
    
    echo -e "  ${GREEN}✅ Archivo .env actualizado${NC}"
    echo ""
    echo -e "  ${YELLOW}⚠️  Ejecuta estos comandos:${NC}"
    echo "    php artisan config:clear"
    echo "    php artisan cache:clear"
else
    echo -e "  ${YELLOW}⏭️  .env no actualizado${NC}"
fi

echo ""

# =========================================
# RESUMEN
# =========================================
echo -e "${GREEN}╔════════════════════════════════════════╗${NC}"
echo -e "${GREEN}║   🎉 MIGRACIÓN COMPLETADA EXITOSA     ║${NC}"
echo -e "${GREEN}╚════════════════════════════════════════╝${NC}"
echo ""
echo -e "${BLUE}📋 Siguientes pasos:${NC}"
echo "  1. php artisan config:clear"
echo "  2. php artisan cache:clear"
echo "  3. php artisan db:show"
echo "  4. php artisan serve"
echo ""
echo -e "${BLUE}📁 Archivos generados:${NC}"
echo "  • ${BACKUP_PATH}"
echo "  • .env.backup.${TIMESTAMP}"
echo ""
echo -e "${GREEN}✅ ¡Todo listo para trabajar en local!${NC}"
echo ""
