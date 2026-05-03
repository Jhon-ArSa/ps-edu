# ✅ CHECKLIST DE SUBIDA - Sistema PS-EDU

**Usa este checklist mientras subes el sistema al servidor**

---

## 📦 FASE 1: PREPARACIÓN LOCAL

### Verificar Archivos
- [ ] Proyecto está en: `C:\laragon\www\psedu-plataforma\`
- [ ] Laravel 11.51.0 funcionando
- [ ] Carpeta `vendor/` existe y tiene contenido
- [ ] Carpeta `public/` existe con `index.php`
- [ ] Archivo `.env` configurado para producción

### Comprimir Proyecto
- [ ] Seleccionar TODOS los archivos del proyecto
- [ ] Crear archivo .zip (nombre: `psedu-sistema.zip`)
- [ ] Verificar que el .zip incluye carpeta `vendor/`
- [ ] Tamaño del .zip es mayor a 50 MB (indica que vendor/ está incluido)

---

## 🌐 FASE 2: ACCESO AL SERVIDOR

### Acceder a cPanel
- [ ] Abrir: https://paul.ihost1001.com:2083/
- [ ] Iniciar sesión correctamente
- [ ] cPanel carga sin errores

### Abrir File Manager
- [ ] Click en "File Manager" en cPanel
- [ ] File Manager abre correctamente
- [ ] Puedes ver la carpeta `/home/upeducac/`

---

## 📁 FASE 3: PREPARAR CARPETA EN SERVIDOR

### Navegar a Ubicación Correcta
- [ ] Estás en: `/home/upeducac/`
- [ ] Ves la carpeta: `intranet.upeducacion-uncp.edu.pe`

### Limpiar Carpeta (si existe contenido viejo)
- [ ] Entrar a `intranet.upeducacion-uncp.edu.pe`
- [ ] Seleccionar TODO el contenido viejo
- [ ] Eliminar contenido viejo
- [ ] Carpeta está vacía

### Crear Carpeta (si no existe)
- [ ] Click en "+ Folder"
- [ ] Nombre: `intranet.upeducacion-uncp.edu.pe`
- [ ] Carpeta creada exitosamente

---

## ⬆️ FASE 4: SUBIR ARCHIVOS

### Subir ZIP
- [ ] Estás dentro de: `intranet.upeducacion-uncp.edu.pe`
- [ ] Click en "Upload"
- [ ] Seleccionar `psedu-sistema.zip`
- [ ] Subida completada al 100%
- [ ] Cerrar ventana de upload

### Extraer Archivos
- [ ] Ver archivo `psedu-sistema.zip` en File Manager
- [ ] Click derecho → "Extract"
- [ ] Ruta es: `/home/upeducac/intranet.upeducacion-uncp.edu.pe/`
- [ ] Click en "Extract File(s)"
- [ ] Extracción completada
- [ ] Eliminar `psedu-sistema.zip`

### Verificar Estructura
- [ ] Carpeta `app/` existe
- [ ] Carpeta `bootstrap/` existe
- [ ] Carpeta `config/` existe
- [ ] Carpeta `database/` existe
- [ ] Carpeta `public/` existe
- [ ] Carpeta `resources/` existe
- [ ] Carpeta `routes/` existe
- [ ] Carpeta `storage/` existe
- [ ] Carpeta `vendor/` existe ⚠️ CRÍTICO
- [ ] Archivo `.env` existe
- [ ] Archivo `artisan` existe
- [ ] Archivo `composer.json` existe

### Verificar Public
- [ ] Entrar a carpeta `public/`
- [ ] Archivo `index.php` existe ⚠️ CRÍTICO
- [ ] Archivo `.htaccess` existe
- [ ] Carpeta `logo/` existe
- [ ] Carpeta `build/` existe

---

## ⚙️ FASE 5: CONFIGURAR DOCUMENT ROOT

### Ir a Configuración de Dominios
- [ ] Volver al inicio de cPanel
- [ ] Buscar "Domains"
- [ ] Click en "Domains"
- [ ] Panel de dominios carga

### Encontrar Subdominio
- [ ] Ver dominio: `intranet.upeducacion-uncp.edu.pe`
- [ ] Click en "Manage" del dominio
- [ ] Panel de configuración abre

### Cambiar Document Root
- [ ] Encontrar campo "Document Root"
- [ ] Borrar contenido actual
- [ ] Escribir: `intranet.upeducacion-uncp.edu.pe/public`
- [ ] Verificar que NO empiece con `/home/upeducac/`
- [ ] Verificar que termine en `/public`
- [ ] Click en "Save" o "Guardar"
- [ ] Mensaje de confirmación aparece

**Document Root debe ser exactamente:**
```
intranet.upeducacion-uncp.edu.pe/public
```

---

## 🔐 FASE 6: CONFIGURAR PERMISOS

### Permisos de Storage
- [ ] Volver a File Manager
- [ ] Ir a: `/home/upeducac/intranet.upeducacion-uncp.edu.pe/`
- [ ] Click derecho en carpeta `storage/`
- [ ] Click en "Change Permissions"
- [ ] Establecer: `755`
- [ ] ✅ Marcar "Recurse into subdirectories"
- [ ] Click en "Change Permissions"
- [ ] Mensaje de confirmación

### Permisos de Bootstrap Cache
- [ ] Entrar a carpeta `bootstrap/`
- [ ] Click derecho en carpeta `cache/`
- [ ] Click en "Change Permissions"
- [ ] Establecer: `755`
- [ ] ✅ Marcar "Recurse into subdirectories"
- [ ] Click en "Change Permissions"
- [ ] Mensaje de confirmación

---

## ✅ FASE 7: VERIFICACIÓN FINAL

### Acceder al Sistema
- [ ] Abrir navegador
- [ ] Ir a: https://intranet.upeducacion-uncp.edu.pe
- [ ] Página carga (sin Error 500)
- [ ] Aparece página de login

### Probar Login
- [ ] Email: `upeducacionuncp@gmail.com`
- [ ] Password: `Admin2024!`
- [ ] Click en "Iniciar Sesión"
- [ ] Login exitoso
- [ ] Redirige a dashboard

### Verificar Dashboard
- [ ] Dashboard carga correctamente
- [ ] Estadísticas aparecen
- [ ] Menú lateral funciona
- [ ] Logo aparece
- [ ] Sin errores en consola del navegador

### Verificar Funcionalidades Básicas
- [ ] Click en "Usuarios" - carga lista
- [ ] Click en "Cursos" - carga lista
- [ ] Click en "Programas" - carga lista
- [ ] Menú de perfil funciona
- [ ] Notificaciones funcionan

---

## 🎉 COMPLETADO

Si todos los checkboxes están marcados, ¡el sistema está funcionando en producción!

**URL del sistema:** https://intranet.upeducacion-uncp.edu.pe  
**Usuario admin:** upeducacionuncp@gmail.com  
**Password:** Admin2024!

---

## 🚨 SI ALGO FALLA

### Error 500
- [ ] Verificar Document Root: `intranet.upeducacion-uncp.edu.pe/public`
- [ ] Verificar permisos: `storage/` = 755
- [ ] Verificar permisos: `bootstrap/cache/` = 755
- [ ] Ver logs en: `storage/logs/laravel.log`

### Página en Blanco
- [ ] Verificar que `vendor/` existe
- [ ] Verificar que `public/index.php` existe
- [ ] Verificar permisos de storage

### Ver Logs
- [ ] File Manager → `storage/logs/`
- [ ] Abrir archivo más reciente
- [ ] Ver último error

### Logs del Servidor
- [ ] cPanel → Metrics → Errors
- [ ] Ver últimos errores

---

## 📞 Información de Acceso

### Sistema
- **URL:** https://intranet.upeducacion-uncp.edu.pe
- **Admin:** upeducacionuncp@gmail.com
- **Password:** Admin2024!

### Servidor
- **cPanel:** https://paul.ihost1001.com:2083/
- **Ubicación:** /home/upeducac/intranet.upeducacion-uncp.edu.pe/

---

**¡Éxito! 🚀**
