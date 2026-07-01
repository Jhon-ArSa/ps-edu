# ✅ Checklist de Migración - Base de Datos PS-EDU

## Pre-Migración

### Requisitos del Sistema
- [ ] MySQL instalado en local (verificar con `mysql --version`)
- [ ] MySQL está corriendo (XAMPP/WAMP iniciado o servicio activo)
- [ ] PHP 8.2+ instalado (verificar con `php --version`)
- [ ] Laravel funcionando (verificar con `php artisan --version`)
- [ ] Conexión a internet estable
- [ ] Espacio en disco disponible (~500 MB mínimo)

### Verificación de Acceso
- [ ] Puedo conectarme a MySQL local:
  ```bash
  mysql -u root -p
  # Password: admin
  ```
- [ ] Tengo acceso al proyecto Laravel (estoy en el directorio correcto)
- [ ] Tengo acceso a internet (para conectar a AWS RDS)

### Preparación
- [ ] He leído la documentación: `INSTRUCCIONES-MIGRACION.md`
- [ ] Entiendo qué se va a migrar (~50 tablas con todos los datos)
- [ ] Tengo tiempo disponible (5-15 minutos)
- [ ] Nadie más está usando el sistema en este momento

---

## Durante la Migración

### Paso 1: Ejecutar Comando
- [ ] He abierto la terminal en el directorio del proyecto
- [ ] He ejecutado: `php artisan db:migrate-to-local`
- [ ] Estoy viendo el progreso en pantalla

### Paso 2: Monitorear
- [ ] La exportación desde AWS RDS está en progreso
- [ ] Se está creando el archivo en `storage/app/backups/`
- [ ] La importación a MySQL local está en progreso
- [ ] Veo mensajes de éxito (✅) sin errores (❌)

### Paso 3: Actualizar .env
- [ ] El comando preguntó si quiero actualizar el .env
- [ ] He respondido "sí" o "no" según mi preferencia
- [ ] Si respondí "sí", se creó un backup del .env anterior

---

## Post-Migración

### Verificación Básica
- [ ] El comando finalizó con "🎉 MIGRACIÓN COMPLETADA EXITOSA"
- [ ] Se creó el archivo: `storage/app/backups/database_backup.sql`
- [ ] El archivo SQL tiene buen tamaño (varios MB, no está vacío)
- [ ] Se creó backup del .env: `.env.backup.YYYYMMDD_HHMMSS`

### Limpieza de Caché
- [ ] Ejecuté: `php artisan config:clear`
- [ ] Ejecuté: `php artisan cache:clear`
- [ ] Ejecuté: `php artisan view:clear`

### Verificación de Conexión
- [ ] Ejecuté: `php artisan db:show`
- [ ] Muestra conexión a MySQL (no AWS RDS)
- [ ] Database: posgrado_intranet
- [ ] Host: 127.0.0.1

### Verificación de Datos

#### Usando Tinker
```bash
php artisan tinker
```

- [ ] `DB::table('users')->count()` → Devuelve número > 0
- [ ] `DB::table('courses')->count()` → Devuelve número > 0
- [ ] `DB::table('enrollments')->count()` → Devuelve número > 0
- [ ] `DB::table('evaluations')->count()` → Devuelve número > 0
- [ ] `exit`

#### Verificar Usuario Admin
```bash
php artisan tinker
```

- [ ] `User::where('email', 'upeducacionuncp@gmail.com')->first()` → Devuelve el usuario admin
- [ ] `exit`

### Verificación del Sistema

#### Iniciar Servidor
- [ ] Ejecuté: `php artisan serve`
- [ ] El servidor inició correctamente
- [ ] Puedo acceder a: http://127.0.0.1:8000

#### Probar Login
- [ ] La página de login carga correctamente
- [ ] Los estilos se ven bien (diseño moderno)
- [ ] Puedo iniciar sesión con:
  - Email: upeducacionuncp@gmail.com
  - Password: Admin2024!
- [ ] El dashboard del admin carga correctamente

#### Verificar Funcionalidades Principales

**Panel de Administración**
- [ ] Puedo ver la lista de usuarios
- [ ] Puedo ver la lista de cursos
- [ ] Puedo ver las matrículas
- [ ] Puedo ver los programas

**Sistema General**
- [ ] Los anuncios se muestran
- [ ] Las notificaciones funcionan
- [ ] El menú de navegación funciona
- [ ] No hay errores en la consola del navegador

**Si eres Docente o Alumno** (opcional)
- [ ] Puedo ver mis cursos
- [ ] Puedo acceder al foro
- [ ] Las evaluaciones cargan
- [ ] Puedo ver las tareas

---

## Verificación de Archivo .env

### Credenciales Actualizadas
- [ ] Abrí el archivo `.env`
- [ ] Verifico que dice:
  ```
  DB_CONNECTION=mysql
  DB_HOST=127.0.0.1
  DB_PORT=3306
  DB_DATABASE=posgrado_intranet
  DB_USERNAME=root
  DB_PASSWORD=admin
  ```

---

## Archivos Generados

### Backups
- [ ] Existe: `storage/app/backups/database_backup.sql`
- [ ] Existe: `.env.backup.YYYYMMDD_HHMMSS`

### Logs
- [ ] Revisé: `storage/logs/laravel.log`
- [ ] No hay errores críticos recientes

---

## Solución de Problemas

### Si algo falló:

#### Error en Exportación
- [ ] Verifico conexión a internet
- [ ] Verifico que AWS RDS esté accesible
- [ ] Intento de nuevo con: `php artisan db:migrate-to-local --export-only`
- [ ] O uso método alternativo: `php artisan db:export-laravel`

#### Error en Importación
- [ ] Verifico que MySQL local esté corriendo
- [ ] Verifico credenciales (root/admin)
- [ ] Verifico que existe el archivo SQL
- [ ] Intento manual:
  ```bash
  mysql -u root -padmin
  CREATE DATABASE posgrado_intranet;
  exit
  mysql -u root -padmin posgrado_intranet < storage/app/backups/database_backup.sql
  ```

#### Error en Login
- [ ] Ejecuto: `php artisan config:clear`
- [ ] Ejecuto: `php artisan cache:clear`
- [ ] Verifico que el .env tenga las credenciales locales
- [ ] Reinicio el servidor: `php artisan serve`

#### Tablas Vacías
- [ ] Verifico el tamaño del archivo SQL (debe ser varios MB)
- [ ] Re-exporto usando: `php artisan db:migrate-to-local --export-only`
- [ ] Re-importo usando: `php artisan db:migrate-to-local --import-only`

---

## Reversión (Si es necesario)

### Para volver a AWS RDS:
- [ ] Restauro el archivo .env desde el backup:
  ```bash
  cp .env.backup.YYYYMMDD_HHMMSS .env
  ```
- [ ] Limpio caché:
  ```bash
  php artisan config:clear
  php artisan cache:clear
  ```
- [ ] Verifico conexión:
  ```bash
  php artisan db:show
  ```

---

## Estado Final

### Confirmación de Migración Exitosa
- [ ] ✅ Datos migrados correctamente
- [ ] ✅ Sistema funcionando en local
- [ ] ✅ Login funciona
- [ ] ✅ Dashboard carga
- [ ] ✅ No hay errores
- [ ] ✅ Backups creados
- [ ] ✅ Documentación revisada

### Próximos Pasos (Opcional)
- [ ] Desactivar AWS RDS (si ya no lo necesito)
- [ ] Configurar backups automáticos locales
- [ ] Instalar phpMyAdmin para gestión visual
- [ ] Configurar MySQL Workbench para queries avanzados

---

## 📊 Resumen de Verificación

| Aspecto | Estado | Notas |
|---------|--------|-------|
| Exportación | ⬜ | |
| Importación | ⬜ | |
| .env actualizado | ⬜ | |
| Caché limpiada | ⬜ | |
| Conexión verificada | ⬜ | |
| Login funciona | ⬜ | |
| Dashboard carga | ⬜ | |
| Datos presentes | ⬜ | |
| Sin errores | ⬜ | |

**Leyenda**: ⬜ Pendiente | ✅ Completado | ❌ Falló

---

## 📝 Notas Personales

```
Fecha de migración: ___________________
Hora de inicio: ___________________
Hora de finalización: ___________________
Tamaño del backup: ___________________
Total de tablas migradas: ___________________
Total de usuarios: ___________________
Total de cursos: ___________________
Problemas encontrados: 




Soluciones aplicadas:




```

---

## 🎯 Migración Completada

Si has marcado todos los checkboxes principales, ¡felicidades! 🎉

Tu base de datos ha sido migrada exitosamente de AWS RDS a MySQL Local.

**Ahora puedes**:
- ✨ Trabajar más rápido (sin latencia de red)
- 🔌 Desarrollar sin conexión a internet
- 💰 Ahorrar en costos de AWS
- 🛠️ Depurar más fácilmente
- 🧪 Probar cambios sin riesgo

---

**Archivo**: CHECKLIST-MIGRACION.md  
**Sistema**: PS-EDU FAEDU  
**Versión**: 1.0  
**Fecha**: Junio 19, 2026
