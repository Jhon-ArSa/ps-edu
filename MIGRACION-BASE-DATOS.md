# 🔄 Guía de Migración de Base de Datos

## AWS RDS MySQL → MySQL Local

**Fecha**: 19 de Junio, 2026  
**Sistema**: PS-EDU FAEDU  
**Estado**: Preparado para migración

---

## 📊 Información de Migración

### Base de Datos Origen (AWS RDS)
- **Host**: cpapcentro.cr40yws4ssdu.us-east-2.rds.amazonaws.com
- **Puerto**: 3306
- **Base de datos**: ps_edu
- **Usuario**: cpapcentro
- **Password**: cpapcentro2026

### Base de Datos Destino (MySQL Local)
- **Host**: 127.0.0.1
- **Puerto**: 3306
- **Base de datos**: posgrado_intranet
- **Usuario**: root
- **Password**: admin

---

## 🚀 Método 1: Comando Artisan (RECOMENDADO)

Este método automatiza todo el proceso de migración.

### Paso 1: Ejecutar el comando de migración completa

```bash
php artisan db:migrate-to-local
```

Este comando hará:
1. ✅ Exportar toda la base de datos desde AWS RDS
2. ✅ Crear la base de datos local `posgrado_intranet`
3. ✅ Importar todos los datos y estructura
4. ✅ Verificar que las tablas fueron importadas correctamente
5. ✅ Ofrecer actualizar el archivo .env automáticamente

### Opciones adicionales disponibles:

```bash
# Solo exportar (no importar)
php artisan db:migrate-to-local --export-only

# Solo importar desde archivo existente
php artisan db:migrate-to-local --import-only

# Usar nombre de archivo personalizado
php artisan db:migrate-to-local --file=mi_backup.sql
```

### Paso 2: Limpiar configuración

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

### Paso 3: Verificar conexión

```bash
php artisan db:show
```

---

## 🛠️ Método 2: Manual (Si no tienes mysqldump)

### Paso 1: Exportar usando Laravel

```bash
php artisan db:export-laravel
```

Este comando alternativo exporta datos usando Laravel sin necesidad de mysqldump.

### Paso 2: Crear la base de datos local

```sql
CREATE DATABASE posgrado_intranet CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Paso 3: Importar el SQL

```bash
mysql -h 127.0.0.1 -u root -padmin posgrado_intranet < storage/app/backups/database_backup.sql
```

---

## 📋 Estructura de la Base de Datos

El sistema PS-EDU incluye las siguientes tablas principales:

### Tablas de Usuarios y Perfiles
- `users` - Usuarios del sistema
- `docente_profiles` - Perfiles de docentes
- `alumno_profiles` - Perfiles de alumnos

### Tablas Académicas
- `programs` - Programas de posgrado
- `courses` - Cursos
- `semesters` - Semestres
- `enrollments` - Matrículas
- `weeks` - Semanas de curso
- `materials` - Materiales educativos
- `curriculum_items` - Items curriculares
- `mentions` - Menciones

### Tablas de Evaluación
- `evaluations` - Evaluaciones
- `evaluation_questions` - Preguntas
- `evaluation_options` - Opciones de respuesta
- `evaluation_attempts` - Intentos de evaluación
- `attempt_answers` - Respuestas de intentos

### Tablas de Tareas
- `tasks` - Tareas
- `task_files` - Archivos de tareas
- `submissions` - Entregas de tareas
- `submission_files` - Archivos de entregas

### Tablas de Foro
- `forum_topics` - Temas del foro
- `forum_replies` - Respuestas del foro
- `forum_reports` - Reportes del foro

### Tablas de Sistema
- `announcements` - Anuncios institucionales
- `announcement_programs` - Relación anuncios-programas
- `announcement_courses` - Relación anuncios-cursos
- `support_tickets` - Tickets de soporte
- `settings` - Configuraciones
- `notifications` - Notificaciones
- `cache` - Caché de Laravel
- `sessions` - Sesiones
- `jobs` - Trabajos en cola
- `migrations` - Migraciones ejecutadas

**Total**: ~50 tablas

---

## 🔐 Actualizar Archivo .env

Después de la migración, actualiza tu archivo `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=posgrado_intranet
DB_USERNAME=root
DB_PASSWORD=admin
```

**IMPORTANTE**: El comando de migración puede hacer esto automáticamente y creará un backup del `.env` anterior.

---

## ✅ Verificación Post-Migración

### 1. Verificar tablas importadas

```bash
php artisan tinker
```

```php
DB::table('users')->count();
DB::table('courses')->count();
DB::table('enrollments')->count();
DB::table('evaluations')->count();
```

### 2. Verificar usuario administrador

```bash
php artisan tinker
```

```php
User::where('email', 'upeducacionuncp@gmail.com')->first();
```

Debe existir el usuario administrador con email: `upeducacionuncp@gmail.com`

### 3. Probar el sistema

```bash
php artisan serve
```

Accede a: http://127.0.0.1:8000 e inicia sesión con:
- **Email**: upeducacionuncp@gmail.com
- **Password**: Admin2024!

---

## 🗂️ Ubicación de Backups

Todos los backups se guardan en:
```
storage/app/backups/
├── database_backup.sql
└── .env.backup.YYYYMMDD_HHMMSS
```

---

## ⚠️ Requisitos Previos

### En tu máquina local necesitas:

1. **MySQL instalado y corriendo**
   ```bash
   # Verificar
   mysql --version
   
   # En Windows con XAMPP/WAMP
   # Iniciar MySQL desde el panel de control
   ```

2. **mysqldump instalado** (para Método 1)
   ```bash
   # Verificar
   mysqldump --version
   ```

3. **Conexión a AWS RDS** (para exportar)
   - Firewall debe permitir conexión saliente
   - AWS RDS debe permitir tu IP

---

## 🐛 Solución de Problemas

### Error: "mysqldump: command not found"
**Solución**: Usa el Método 2 (Laravel export) o instala MySQL client.

### Error: "Access denied for user 'root'@'localhost'"
**Solución**: Verifica las credenciales de MySQL local:
```bash
mysql -u root -p
# Ingresa password: admin
```

### Error: "Can't connect to MySQL server on 'cpapcentro.cr40yws4ssdu.us-east-2.rds.amazonaws.com'"
**Solución**: 
- Verifica tu conexión a internet
- Verifica que AWS RDS esté activo
- Verifica que tu IP esté permitida en AWS Security Group

### Tablas vacías después de importar
**Solución**: Verifica que el archivo SQL tiene datos:
```bash
ls -lh storage/app/backups/database_backup.sql
# Debe tener varios MB de tamaño
```

---

## 📞 Soporte

Si encuentras problemas durante la migración:

1. Revisa los logs: `storage/logs/laravel.log`
2. Verifica conexión MySQL: `php artisan db:show`
3. Consulta el archivo de backup: `storage/app/backups/`

---

## ✨ Ventajas de la Base de Datos Local

✅ **Mayor velocidad** - Sin latencia de red  
✅ **Desarrollo offline** - No necesitas internet  
✅ **Control total** - Acceso directo a la base de datos  
✅ **Sin costos** - No pagas por AWS RDS  
✅ **Depuración fácil** - Herramientas locales como phpMyAdmin

---

**¡Listo para migrar!** 🚀

Ejecuta: `php artisan db:migrate-to-local`
