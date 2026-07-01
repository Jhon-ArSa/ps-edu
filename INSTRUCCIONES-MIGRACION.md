# 🚀 Instrucciones Rápidas de Migración

## Migración de Base de Datos: AWS RDS → MySQL Local

---

## ⚡ Opción 1: Comando Artisan (MÁS FÁCIL - RECOMENDADO)

```bash
php artisan db:migrate-to-local
```

**¡Eso es todo!** El comando hará todo automáticamente:
- ✅ Exporta desde AWS RDS
- ✅ Crea la base de datos local
- ✅ Importa todos los datos
- ✅ Verifica que todo esté correcto
- ✅ Te pregunta si quiere actualizar el .env

Después ejecuta:
```bash
php artisan config:clear
php artisan cache:clear
php artisan serve
```

---

## 🔧 Opción 2: Script PowerShell (Windows)

```powershell
.\migrate-database.ps1
```

---

## 🔧 Opción 3: Script Bash (Linux/Mac)

```bash
chmod +x migrate-database.sh
./migrate-database.sh
```

---

## 📝 Opción 4: Laravel Export (Si no tienes mysqldump)

```bash
# Paso 1: Exportar usando Laravel
php artisan db:export-laravel

# Paso 2: Crear base de datos local
mysql -u root -p
CREATE DATABASE posgrado_intranet CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
exit

# Paso 3: Importar
mysql -h 127.0.0.1 -u root -padmin posgrado_intranet < storage/app/backups/database_backup.sql

# Paso 4: Actualizar .env manualmente (ver abajo)
```

---

## 📋 Actualizar .env Manualmente

Si no usaste el comando Artisan, edita tu `.env` y cambia estas líneas:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=posgrado_intranet
DB_USERNAME=root
DB_PASSWORD=admin
```

Luego:
```bash
php artisan config:clear
php artisan cache:clear
```

---

## ✅ Verificar que Todo Funcione

```bash
# Ver información de la base de datos
php artisan db:show

# Probar conexión con tinker
php artisan tinker
>>> DB::table('users')->count()
>>> exit

# Iniciar servidor
php artisan serve
```

Accede a: http://127.0.0.1:8000

Inicia sesión con:
- **Email**: upeducacionuncp@gmail.com
- **Password**: Admin2024!

---

## 📊 Datos que se Migrarán

✅ **Todos los usuarios** (docentes, alumnos, admin)  
✅ **Todos los cursos** y programas  
✅ **Todas las matrículas** (enrollments)  
✅ **Todas las evaluaciones** y sus intentos  
✅ **Todas las tareas** y entregas (submissions)  
✅ **Todos los temas del foro** y respuestas  
✅ **Todos los anuncios** institucionales  
✅ **Todas las configuraciones** del sistema  
✅ **Todas las notificaciones**  
✅ **Todo el contenido educativo** (materiales, semanas, etc.)

**Total**: ~50 tablas con todos sus datos

---

## 🔍 ¿Qué Hace Cada Herramienta?

### `php artisan db:migrate-to-local`
- **Más fácil y rápido**
- Todo automatizado en un comando
- Verifica conexiones antes de empezar
- Muestra progreso en tiempo real
- Crea backups automáticamente
- Maneja errores inteligentemente

### `migrate-database.ps1` (PowerShell)
- Para Windows
- Script interactivo con colores
- Verifica cada paso
- Muestra estadísticas al final

### `migrate-database.sh` (Bash)
- Para Linux/Mac
- Script shell tradicional
- Compatible con servidores

### `php artisan db:export-laravel`
- No requiere mysqldump
- Usa solo PHP y Laravel
- Más lento pero más compatible
- Útil si no tienes herramientas MySQL instaladas

---

## ⚠️ Requisitos Previos

Antes de migrar, asegúrate de tener:

1. ✅ **MySQL instalado y corriendo** en tu máquina local
   ```bash
   # Verificar
   mysql --version
   ```

2. ✅ **PHP 8.2+** instalado
   ```bash
   # Verificar
   php --version
   ```

3. ✅ **Conexión a internet** (para exportar desde AWS RDS)

4. ✅ **Espacio en disco** suficiente (~100-500 MB para la base de datos)

---

## 🐛 Solución Rápida de Problemas

### "mysqldump: command not found"
```bash
# Solución: Usa Laravel export
php artisan db:export-laravel
```

### "Access denied for user 'root'@'localhost'"
```bash
# Solución: Verifica tu password de MySQL
mysql -u root -p
# Ingresa: admin
```

### "Can't connect to MySQL server"
```bash
# Solución: Inicia MySQL
# En Windows (XAMPP): Abre el panel y da Start a MySQL
# En Windows (servicio): net start MySQL
# En Linux: sudo service mysql start
```

### El archivo SQL está vacío o muy pequeño
```bash
# Solución: Verifica tu conexión a AWS RDS
ping cpapcentro.cr40yws4ssdu.us-east-2.rds.amazonaws.com
```

---

## 📞 ¿Necesitas Ayuda?

Si algo no funciona:

1. Revisa los logs: `storage/logs/laravel.log`
2. Verifica el archivo de backup: `storage/app/backups/database_backup.sql`
3. Intenta con una opción diferente (Artisan, PowerShell, Bash, Laravel)

---

## 🎯 Recomendación Final

**Usa la Opción 1** (Comando Artisan):

```bash
php artisan db:migrate-to-local
```

Es la más fácil, rápida y confiable. Maneja todo automáticamente y te guía paso a paso.

---

**¡Listo para migrar!** 🚀
