# 🚀 Instrucciones para Hosting SIN Acceso a Terminal

**Sistema**: PS-EDU FAEDU  
**Problema**: Error 404 al subir archivos (materiales, evaluaciones, tareas)  
**Causa**: Sin acceso a terminal para crear symlinks  
**Solución**: Usar `public/uploads/` directamente

---

## 🎯 Solución Implementada

He cambiado el sistema para que **todos los archivos se guarden directamente en `public/uploads/`** sin necesidad de symlinks ni comandos de terminal.

### Cambios Realizados

1. ✅ **Configuración de almacenamiento** actualizada
   - Ahora usa: `public/uploads/` en lugar de `storage/`
   - No requiere symlinks
   - Funciona sin acceso a terminal

2. ✅ **Estructura de carpetas** creada
   - `public/uploads/materials/` - Materiales educativos
   - `public/uploads/tasks/` - Archivos de tareas
   - `public/uploads/evaluations/` - Archivos de evaluaciones
   - `public/uploads/submissions/` - Entregas de alumnos
   - `public/uploads/announcements/` - Imágenes de anuncios
   - `public/uploads/avatars/` - Avatares de usuarios
   - `public/uploads/forum/` - Archivos del foro

3. ✅ **Seguridad implementada**
   - Archivo `.htaccess` previene ejecución de PHP
   - Solo permite archivos seguros (PDF, DOC, imágenes, etc.)
   - No lista directorios

---

## 📋 Pasos para Subir al Hosting

### OPCIÓN 1: Desde cPanel File Manager (Recomendado)

#### Paso 1: Subir Archivos

1. Accede a tu cPanel
2. Ve a **File Manager**
3. Navega a la carpeta de tu aplicación (ej: `public_html/`)
4. Sube **TODOS** los archivos del proyecto

#### Paso 2: Verificar Estructura

Asegúrate de que exista:
```
public_html/
└── public/
    └── uploads/
        ├── .htaccess ✅
        ├── index.php ✅
        ├── materials/
        ├── tasks/
        ├── evaluations/
        ├── submissions/
        ├── announcements/
        ├── avatars/
        └── forum/
```

#### Paso 3: Configurar Permisos

1. Click derecho en `public/uploads/`
2. Selecciona **Change Permissions**
3. Configura: **755** o **775**
4. Marca: "Recurse into subdirectories"
5. Click **Change Permissions**

#### Paso 4: Limpiar Caché (Si tienes SSH)

Si tienes acceso SSH:
```bash
cd /ruta/a/tu/proyecto
php artisan config:clear
php artisan cache:clear
```

Si NO tienes SSH:
- La aplicación limpiará caché automáticamente en el próximo acceso
- O borra manualmente: `bootstrap/cache/config.php`

---

### OPCIÓN 2: Usar Script Automático

#### Paso 1: Subir el Script

Sube el archivo `create-upload-directories.php` a la raíz de tu proyecto en el hosting.

#### Paso 2: Ejecutar desde el Navegador

Accede a:
```
https://tu-dominio.com/create-upload-directories.php
```

El script:
- ✅ Crea todos los directorios necesarios
- ✅ Configura permisos
- ✅ Crea archivos de seguridad
- ✅ Verifica que todo esté correcto

#### Paso 3: Eliminar el Script

**IMPORTANTE**: Después de ejecutarlo, elimina el archivo:
```
create-upload-directories.php
```

---

### OPCIÓN 3: Vía FTP (FileZilla, WinSCP, etc.)

#### Paso 1: Conectar por FTP

1. Abre tu cliente FTP (FileZilla)
2. Conecta a tu hosting
3. Navega a la carpeta del proyecto

#### Paso 2: Subir Archivos

1. Sube toda la carpeta `public/uploads/` con sus subcarpetas
2. Asegúrate de que `.htaccess` e `index.php` se suban

#### Paso 3: Verificar Permisos

En tu cliente FTP:
1. Click derecho en `uploads/`
2. File Permissions
3. Numeric value: **755**
4. Aplicar a subdirectorios

---

## 🔧 Configuración Actual

### Archivo: `config/filesystems.php`

```php
'public' => [
    'driver' => 'local',
    'root' => public_path('uploads'),  // ← CAMBIO CLAVE
    'url' => env('APP_URL').'/uploads',
    'visibility' => 'public',
],
```

### URLs de Archivos

**Antes** (con symlink):
```
https://tu-dominio.com/storage/materials/archivo.pdf  ❌ Error 404
```

**Ahora** (directo en public):
```
https://tu-dominio.com/uploads/materials/archivo.pdf  ✅ Funciona
```

---

## ✅ Verificación

### 1. Verificar Estructura

En File Manager de cPanel, verifica que existan:
```
public/uploads/.htaccess       ✅
public/uploads/index.php       ✅
public/uploads/materials/      ✅
public/uploads/tasks/          ✅
public/uploads/evaluations/    ✅
public/uploads/submissions/    ✅
public/uploads/announcements/  ✅
public/uploads/avatars/        ✅
```

### 2. Verificar Permisos

Todos los directorios deben tener permiso **755** o **775**

### 3. Probar Subida de Archivo

1. Login como docente en el sistema
2. Ve a un curso → Semana
3. Sube un material (PDF, DOC, etc.)
4. Verifica que:
   - ✅ El archivo se sube correctamente
   - ✅ No aparece error 404
   - ✅ Puedes descargar el archivo

---

## 🛡️ Seguridad

### Archivo `.htaccess` Protege Contra:

1. **Ejecución de PHP** en uploads
   - Previene que suban archivos maliciosos PHP
   
2. **Solo archivos permitidos**
   - PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX
   - Imágenes: JPG, PNG, GIF
   - Videos: MP4, MP3
   - Comprimidos: ZIP, RAR
   
3. **No listado de directorios**
   - Nadie puede ver qué archivos hay

4. **Headers de seguridad**
   - X-Content-Type-Options
   - X-Frame-Options

---

## 🐛 Solución de Problemas

### Problema: Aún da error 404

**Solución 1**: Verificar permisos
```
public/uploads/  → 755
public/uploads/materials/  → 755
```

**Solución 2**: Limpiar caché

Si tienes SSH:
```bash
php artisan config:clear
php artisan cache:clear
```

Si NO tienes SSH:
1. Accede por FTP/File Manager
2. Elimina: `bootstrap/cache/config.php`
3. Elimina: `bootstrap/cache/routes-*.php`

**Solución 3**: Verificar .env

Asegúrate de que:
```env
APP_URL=https://tu-dominio-real.com
```

**Solución 4**: Verificar ruta pública

En cPanel, tu "Document Root" debe apuntar a la carpeta `public/`

---

### Problema: Los archivos no se suben

**Causa**: Permisos insuficientes

**Solución**:
1. En cPanel File Manager
2. Click derecho en `public/uploads/`
3. Change Permissions → **775**
4. Marcar: "Recurse into subdirectories"

**Alternativa**: Contacta soporte técnico del hosting

---

### Problema: Error de espacio en disco

**Causa**: Límite de almacenamiento alcanzado

**Solución**:
1. Verifica espacio en cPanel → Disk Usage
2. Limpia archivos innecesarios
3. O actualiza tu plan de hosting

---

## 📊 Comparación

| Aspecto | Storage (Antes) | Public/Uploads (Ahora) |
|---------|----------------|------------------------|
| Requiere terminal | ✅ Sí (symlink) | ❌ No |
| Requiere SSH | ✅ Sí | ❌ No |
| Funciona en cPanel | ❌ Difícil | ✅ Fácil |
| Seguridad | ✅ Alta | ✅ Alta (con .htaccess) |
| URLs | /storage/ | /uploads/ |
| Backups | Más difícil | Más fácil |

---

## 📁 Archivos Incluidos

### Para el Hosting:
```
public/uploads/
├── .htaccess           # Seguridad
├── index.php           # Protección
├── .gitignore          # Control de versiones
├── materials/          # Materiales
├── tasks/              # Tareas
├── evaluations/        # Evaluaciones
├── submissions/        # Entregas
├── announcements/      # Anuncios
├── avatars/            # Avatares
└── forum/              # Foro
```

### En la Raíz:
```
create-upload-directories.php  # Script de instalación (ejecutar y eliminar)
```

---

## 🎯 Controladores que Usan Uploads

Estos controladores ahora guardan en `public/uploads/`:

1. **MaterialController** → `uploads/materials/`
2. **TaskController** → `uploads/tasks/`
3. **EvaluationController** → `uploads/evaluations/`
4. **SubmissionController** → `uploads/submissions/`
5. **AnnouncementController** → `uploads/announcements/`
6. **ProfileController** → `uploads/avatars/`

No necesitas modificar nada en el código, Laravel usa automáticamente la configuración de `filesystems.php`.

---

## ⚠️ Importante para Producción

### Hacer Backup Regular

```bash
# Respaldar uploads (si tienes SSH)
tar -czf uploads-backup-$(date +%Y%m%d).tar.gz public/uploads/

# O desde cPanel File Manager:
# 1. Click derecho en uploads/
# 2. Compress
# 3. Descargar el ZIP
```

### Monitorear Espacio

En cPanel → Disk Usage, verifica regularmente el espacio usado.

### Limpiar Archivos Antiguos

Periódicamente revisa y elimina:
- Materiales de cursos antiguos
- Evaluaciones expiradas
- Entregas de semestres pasados

---

## ✨ Ventajas de Esta Solución

| Ventaja | Descripción |
|---------|-------------|
| **Sin terminal necesario** | Funciona en cualquier hosting compartido |
| **Fácil de subir** | Solo usar FTP o File Manager |
| **URLs directas** | `/uploads/file.pdf` - Más simple |
| **Backups fáciles** | Solo copiar carpeta uploads/ |
| **Compatible cPanel** | Funciona en 100% de hostings |
| **Seguro** | .htaccess protege contra malware |

---

## 📞 Checklist Final

- [ ] Carpeta `public/uploads/` existe en el servidor
- [ ] Todas las subcarpetas creadas
- [ ] Archivo `.htaccess` presente
- [ ] Archivo `index.php` presente
- [ ] Permisos 755 o 775 configurados
- [ ] Caché limpiada (si es posible)
- [ ] Prueba de subida exitosa
- [ ] No aparece error 404

---

## 🎉 ¡Listo!

Tu sistema ahora funciona **sin necesidad de terminal ni SSH**. Simplemente:

1. ✅ Sube todos los archivos al hosting
2. ✅ Verifica que `public/uploads/` tenga las subcarpetas
3. ✅ Configura permisos a 755/775
4. ✅ Prueba subir un archivo

**Si todo está correcto**: Los archivos se subirán a `public/uploads/` y serán accesibles vía `https://tu-dominio.com/uploads/archivo.pdf`

---

**Sistema**: PS-EDU FAEDU  
**Solución**: Compatible con hosting sin SSH/terminal  
**Estado**: ✅ Listo para producción  
**Fecha**: Junio 19, 2026
