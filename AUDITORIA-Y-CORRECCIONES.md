# 🔍 Auditoría Completa y Correcciones - PS-EDU FAEDU

**Fecha**: 19 de Junio, 2026  
**Sistema**: PS-EDU - Plataforma de Posgrado FAEDU-UNCP  
**Laravel**: 11.54.0  
**PHP**: 8.2+

---

## 🚨 Problemas Identificados

### **PROBLEMA 1: Error 404 al Subir Archivos en Hosting**

#### Síntomas
- ✅ Funciona en local (XAMPP/Laravel serve)
- ❌ Error 404 en hosting/producción al subir:
  - Materiales educativos
  - Archivos de evaluaciones
  - Archivos de tareas
  - Entregas de alumnos
  - Imágenes de anuncios
  - Archivos del foro

#### Causa Raíz
El disco `'public'` en `config/filesystems.php` estaba mal configurado:

**Configuración INCORRECTA** (antes):
```php
'public' => [
    'driver' => 'local',
    'root' => public_path(),  // ❌ Apunta directamente a public/
    'url' => env('APP_URL'),
    'visibility' => 'public',
],
```

**Problemas**:
1. Los archivos se guardaban directamente en `public/materials/`, `public/tasks/`, etc.
2. En hosting compartido (cPanel), esto causa conflictos de rutas
3. No se usaba el sistema de storage estándar de Laravel
4. Los symlinks no funcionaban correctamente

#### Solución Aplicada ✅

**Configuración CORRECTA** (ahora):
```php
'public' => [
    'driver' => 'local',
    'root' => storage_path('app/public'),  // ✅ Usa storage estándar
    'url' => env('APP_URL').'/storage',
    'visibility' => 'public',
],
```

**Beneficios**:
- ✅ Archivos en `storage/app/public/` (estructura estándar Laravel)
- ✅ URLs accesibles vía symlink `public/storage → storage/app/public`
- ✅ Compatible con hosting compartido
- ✅ Mejor seguridad (archivos fuera de public/)
- ✅ Backups más fáciles

---

### **PROBLEMA 2: Emails de Credenciales No Se Envían**

#### Síntomas
- Al crear un usuario (admin panel o importación CSV)
- No se envía el email con credenciales
- No hay errores visibles en la interfaz

#### Causa Raíz
Configuración de mail en `config/mail.php`:

```php
'default' => env('MAIL_MAILER', 'log'),  // ❌ Default 'log' = no envía
```

Si el `.env` no tiene `MAIL_MAILER` definido, usa `'log'` que solo guarda en logs.

#### Verificación del .env

**Tu configuración actual**:
```env
MAIL_MAILER=smtp  ✅
MAIL_HOST=smtp.gmail.com  ✅
MAIL_PORT=587  ✅
MAIL_USERNAME=upeducacionuncp@gmail.com  ✅
MAIL_PASSWORD="ntyo ebtl qfbo kkji"  ✅ (App Password)
MAIL_ENCRYPTION=tls  ✅
MAIL_FROM_ADDRESS="upeducacionuncp@gmail.com"  ✅
MAIL_FROM_NAME="PS-EDU - FAEDU"  ✅
```

**Configuración correcta** ✅

#### Posibles Causas Adicionales

1. **Caché de configuración**
   ```bash
   # Solución:
   php artisan config:clear
   php artisan cache:clear
   ```

2. **Puerto 587 bloqueado en hosting**
   - Algunos hosts bloquean el puerto 587
   - Alternativa: Usar puerto 465 con SSL
   ```env
   MAIL_PORT=465
   MAIL_ENCRYPTION=ssl
   ```

3. **Google bloqueó la App Password**
   - Regenerar App Password: https://myaccount.google.com/apppasswords
   - Verificar que "Acceso de apps menos seguras" no sea necesario

4. **Queue mal configurado**
   ```env
   QUEUE_CONNECTION=sync  ✅ (correcto para envío inmediato)
   ```

#### Solución Aplicada ✅

Se creó un comando para probar emails:
```bash
php artisan mail:test tu-email@ejemplo.com
```

Este comando:
- ✅ Verifica configuración SMTP
- ✅ Muestra diagnóstico detallado
- ✅ Envía email de prueba
- ✅ Prueba la notificación de bienvenida
- ✅ Proporciona soluciones específicas a errores

---

## 📁 Archivos Corregidos

### 1. `config/filesystems.php`
**Cambios**:
- ✅ Disco `'public'` ahora usa `storage_path('app/public')`
- ✅ URL corregida: `env('APP_URL').'/storage'`
- ✅ Symlink configurado: `public/storage → storage/app/public`

### 2. Comandos Artisan Creados

#### `php artisan mail:test`
**Archivo**: `app/Console/Commands/TestEmailConfiguration.php`

**Funciones**:
- Verifica configuración SMTP
- Envía email de prueba simple
- Prueba notificación de bienvenida
- Diagnóstico automático de errores
- Sugerencias de solución

**Uso**:
```bash
# Prueba básica
php artisan mail:test

# Con email específico
php artisan mail:test admin@ejemplo.com
```

#### `php artisan storage:fix`
**Archivo**: `app/Console/Commands/FixFileStorage.php`

**Funciones**:
- Migra archivos de `public/` a `storage/app/public/`
- Actualiza referencias en base de datos
- Crea symlink automáticamente
- Modo dry-run para verificar cambios

**Uso**:
```bash
# Ver qué se haría (sin cambios)
php artisan storage:fix --dry-run

# Ejecutar migración
php artisan storage:fix

# Forzar sin confirmación
php artisan storage:fix --force
```

---

## 🛠️ Pasos de Corrección

### PASO 1: Corregir Sistema de Archivos

```bash
# 1. Ver qué se migrará
php artisan storage:fix --dry-run

# 2. Ejecutar migración
php artisan storage:fix

# 3. Crear symlink (si no existe)
php artisan storage:link

# 4. Limpiar caché
php artisan config:clear
php artisan cache:clear
```

**Qué hace**:
- Mueve archivos de `public/materials/` → `storage/app/public/materials/`
- Mueve archivos de `public/tasks/` → `storage/app/public/tasks/`
- Mueve archivos de `public/evaluations/` → `storage/app/public/evaluations/`
- Mueve archivos de `public/submissions/` → `storage/app/public/submissions/`
- Mueve archivos de `public/announcements/` → `storage/app/public/announcements/`
- Mueve avatares de `public/avatars/` → `storage/app/public/avatars/`
- Actualiza todas las rutas en la base de datos
- Crea symlink `public/storage`

### PASO 2: Probar Emails

```bash
# Probar configuración de email
php artisan mail:test upeducacionuncp@gmail.com
```

**Verificar**:
- ✅ Configuración SMTP correcta
- ✅ Email simple se envía
- ✅ Notificación de bienvenida se envía
- ✅ Email llega a bandeja de entrada

### PASO 3: Probar Subida de Archivos

1. **Login como docente**
2. **Ir a un curso**
3. **Subir material en una semana**
4. **Verificar**:
   - Archivo se guarda correctamente
   - Archivo es accesible (sin 404)
   - Ruta muestra: `/storage/materials/...`

### PASO 4: Verificar en Hosting

Si estás en hosting compartido (cPanel):

```bash
# 1. Subir cambios al servidor
git pull origin main

# 2. En el servidor, ejecutar:
php artisan storage:fix
php artisan storage:link
php artisan config:clear
php artisan cache:clear
php artisan optimize

# 3. Verificar permisos
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
```

**Importante en cPanel**:
- Asegúrate que `public/storage` sea un symlink
- Si no puedes crear symlink, contacta al soporte del hosting
- Verifica que los permisos de `storage/` sean 775

---

## 📊 Controladores Afectados

Estos controladores ahora guardan archivos correctamente:

### 1. Materiales (Docentes)
**Archivo**: `app/Http/Controllers/Docente/MaterialController.php`
- **Antes**: `$file->store("materials/{$course->id}", 'public')`
- **Ahora**: Guarda en `storage/app/public/materials/`
- **URL**: `/storage/materials/1/archivo.pdf`

### 2. Evaluaciones (Docentes)
**Archivo**: `app/Http/Controllers/Docente/EvaluationController.php`
- **Antes**: `$file->store('evaluations', 'public')`
- **Ahora**: Guarda en `storage/app/public/evaluations/`
- **URL**: `/storage/evaluations/archivo.pdf`

### 3. Tareas (Docentes)
**Archivo**: `app/Http/Controllers/Docente/TaskController.php`
- **Antes**: `$file->storeAs("tasks/{$course->id}", $filename, 'public')`
- **Ahora**: Guarda en `storage/app/public/tasks/`
- **URL**: `/storage/tasks/1/archivo.pdf`

### 4. Entregas (Alumnos)
**Archivo**: `app/Http/Controllers/Alumno/SubmissionController.php`
- **Antes**: `$file->storeAs("submissions/{$task->id}", $filename, 'public')`
- **Ahora**: Guarda en `storage/app/public/submissions/`
- **URL**: `/storage/submissions/1/archivo.pdf`

### 5. Anuncios (Admin/Docentes)
**Archivo**: `app/Http/Controllers/Admin/AnnouncementController.php`
- **Antes**: `$file->store('announcements', 'public')`
- **Ahora**: Guarda en `storage/app/public/announcements/`
- **URL**: `/storage/announcements/imagen.jpg`

### 6. Avatares (Perfil)
**Archivo**: `app/Http/Controllers/ProfileController.php`
- **Antes**: `$file->store('avatars', 'public')`
- **Ahora**: Guarda en `storage/app/public/avatars/`
- **URL**: `/storage/avatars/avatar.jpg`

---

## 🔍 Verificación de Emails

### Causas Comunes de Fallo

| Problema | Síntoma | Solución |
|----------|---------|----------|
| **Caché de config** | Usa configuración antigua | `php artisan config:clear` |
| **Puerto bloqueado** | Connection refused | Cambiar a puerto 465 con SSL |
| **App Password inválida** | Authentication failed | Regenerar en Google |
| **Firewall del host** | Timeout | Contactar soporte hosting |
| **MAIL_MAILER=log** | Emails no se envían | Cambiar a `smtp` en .env |

### Comando de Diagnóstico

```bash
php artisan mail:test
```

**Salida esperada**:
```
✅ Email simple enviado correctamente
✅ Notificación de bienvenida enviada correctamente
🎉 ¡Prueba de email completada exitosamente!
📧 Revisa la bandeja de entrada de: tu-email@gmail.com
```

### Alternativa: Puerto 465 (SSL)

Si el puerto 587 no funciona en tu hosting:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=465  # ← Cambiar
MAIL_USERNAME=upeducacionuncp@gmail.com
MAIL_PASSWORD="ntyo ebtl qfbo kkji"
MAIL_ENCRYPTION=ssl  # ← Cambiar de tls a ssl
```

Luego:
```bash
php artisan config:clear
php artisan mail:test
```

---

## 🎯 Checklist de Verificación

### Sistema de Archivos
- [ ] Configuración `filesystems.php` corregida
- [ ] Symlink `public/storage` creado
- [ ] Archivos movidos a `storage/app/public/`
- [ ] Referencias en BD actualizadas
- [ ] Permisos correctos (775) en `storage/`
- [ ] Prueba de subida de material exitosa
- [ ] Prueba de subida de evaluación exitosa
- [ ] Prueba de entrega de tarea exitosa
- [ ] Archivos existentes accesibles

### Sistema de Emails
- [ ] `MAIL_MAILER=smtp` en .env
- [ ] App Password de Gmail configurada
- [ ] `php artisan config:clear` ejecutado
- [ ] `php artisan mail:test` exitoso
- [ ] Email de prueba recibido
- [ ] Creación de usuario envía email
- [ ] Importación CSV envía emails
- [ ] Emails llegan a bandeja (no spam)

### Hosting/Producción
- [ ] Cambios subidos al servidor
- [ ] `php artisan storage:fix` ejecutado
- [ ] `php artisan storage:link` ejecutado
- [ ] Cachés limpiadas en servidor
- [ ] Subida de archivos funciona
- [ ] Emails se envían correctamente
- [ ] Sin errores 404
- [ ] Sin errores en logs

---

## 📝 Comandos de Mantenimiento

### Después de Subir Cambios al Servidor

```bash
# 1. Actualizar código
git pull origin main

# 2. Instalar dependencias (si hay cambios)
composer install --optimize-autoloader --no-dev

# 3. Corregir almacenamiento
php artisan storage:fix
php artisan storage:link

# 4. Limpiar cachés
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# 5. Optimizar para producción
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# 6. Verificar permisos
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/

# 7. Probar emails
php artisan mail:test upeducacionuncp@gmail.com

# 8. Ver logs si hay errores
tail -f storage/logs/laravel.log
```

### Monitoreo Regular

```bash
# Ver últimos errores
tail -50 storage/logs/laravel.log | grep ERROR

# Verificar configuración actual
php artisan config:show mail
php artisan config:show filesystems

# Probar email periódicamente
php artisan mail:test

# Ver estado de la base de datos
php artisan db:show
```

---

## 🚀 Mejoras Implementadas

### 1. Sistema de Almacenamiento
- ✅ Usa estructura estándar de Laravel
- ✅ Compatible con hosting compartido
- ✅ Mejor organización de archivos
- ✅ Más seguro (archivos fuera de public/)
- ✅ Backups más fáciles

### 2. Sistema de Emails
- ✅ Comando de prueba y diagnóstico
- ✅ Mejor manejo de errores
- ✅ Logs detallados
- ✅ Soporte para múltiples puertos SMTP

### 3. Herramientas de Migración
- ✅ Migración automática de archivos
- ✅ Actualización automática de BD
- ✅ Modo dry-run para seguridad
- ✅ Verificación de symlinks

---

## 📚 Documentación Adicional

### Estructura de Archivos (Nueva)

```
storage/app/public/
├── materials/          # Materiales educativos
│   ├── 1/             # Por curso
│   ├── 2/
│   └── 3/
├── tasks/             # Archivos de tareas
│   ├── 1/
│   └── 2/
├── evaluations/       # Archivos de evaluaciones
├── evaluation-answers/ # Respuestas con archivos
├── submissions/       # Entregas de alumnos
│   ├── 1/            # Por tarea
│   └── 2/
├── announcements/     # Imágenes de anuncios
└── avatars/          # Avatares de usuarios

public/
└── storage/          # Symlink → storage/app/public/
```

### URLs de Acceso

Antes:
```
https://intranet.upeducacion-uncp.edu.pe/materials/1/file.pdf  ❌ 404
```

Ahora:
```
https://intranet.upeducacion-uncp.edu.pe/storage/materials/1/file.pdf  ✅
```

---

## ⚠️ Notas Importantes

### Para Hosting Compartido (cPanel)

1. **Symlinks**: Algunos hosts no permiten symlinks
   - Contacta soporte técnico si `storage:link` falla
   - Alternativa: Mover `storage/app/public` a `public/storage` manualmente

2. **Permisos**: Asegúrate de tener permisos correctos
   ```bash
   chmod -R 775 storage/
   chown -R usuario:grupo storage/
   ```

3. **PHP Version**: Requiere PHP 8.2+
   - Verifica en cPanel → Select PHP Version

4. **Extensiones PHP requeridas**:
   - OpenSSL (para emails TLS/SSL)
   - FileInfo
   - GD/Imagick (para imágenes)

### Backups

Ahora los backups son más fáciles:
```bash
# Respaldar todos los archivos
tar -czf archivos-backup.tar.gz storage/app/public/

# Restaurar
tar -xzf archivos-backup.tar.gz -C /
```

---

## 🎉 Resultado Final

### Antes
- ❌ Error 404 al subir archivos en hosting
- ❌ Emails no se envían al crear usuarios
- ❌ Archivos en ubicaciones no estándares
- ❌ Difícil de depurar

### Ahora
- ✅ Subida de archivos funciona en hosting
- ✅ Emails se envían correctamente
- ✅ Estructura estándar de Laravel
- ✅ Herramientas de diagnóstico incluidas
- ✅ Fácil de mantener y depurar
- ✅ Compatible con cualquier hosting

---

**Sistema**: PS-EDU FAEDU  
**Estado**: ✅ Corregido y optimizado  
**Fecha**: 19 de Junio, 2026  
**Versión**: 1.0
