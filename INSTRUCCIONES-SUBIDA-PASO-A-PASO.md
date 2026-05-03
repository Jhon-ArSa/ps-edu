# 📝 INSTRUCCIONES PASO A PASO - SUBIDA A PRODUCCIÓN

**Sistema:** PS-EDU FAEDU  
**Fecha:** 3 de Mayo 2026  
**Tiempo estimado:** 15-20 minutos

---

## 🎯 Objetivo

Subir el sistema Laravel 11 al servidor y configurarlo correctamente para que funcione en:
**https://intranet.upeducacion-uncp.edu.pe**

---

## 📦 PASO 1: Preparar Archivos para Subir

### 1.1 Comprimir el Proyecto

**En tu computadora local:**

1. Ir a la carpeta del proyecto: `C:\laragon\www\psedu-plataforma\`
2. Seleccionar **TODOS** los archivos y carpetas
3. Click derecho → **Enviar a** → **Carpeta comprimida**
4. Nombrar el archivo: `psedu-sistema.zip`

**Archivos que DEBEN estar incluidos:**
- ✅ `app/` (código)
- ✅ `bootstrap/` (bootstrap)
- ✅ `config/` (configuraciones)
- ✅ `database/` (migraciones)
- ✅ `public/` (punto de entrada)
- ✅ `resources/` (vistas)
- ✅ `routes/` (rutas)
- ✅ `storage/` (logs)
- ✅ `vendor/` ⚠️ **MUY IMPORTANTE** (dependencias)
- ✅ `.env` (configuración)
- ✅ `artisan` (CLI)
- ✅ `composer.json`
- ✅ `composer.lock`

**Archivos que puedes EXCLUIR (opcional):**
- ❌ `node_modules/` (no necesario)
- ❌ `.git/` (no necesario)
- ❌ `tests/` (opcional)

---

## 🌐 PASO 2: Subir al Servidor

### 2.1 Acceder a cPanel

1. Ir a: **https://paul.ihost1001.com:2083/**
2. Iniciar sesión con tus credenciales

### 2.2 Abrir File Manager

1. En cPanel, buscar **"File Manager"**
2. Click en **File Manager**
3. Se abrirá el administrador de archivos

### 2.3 Navegar a la Carpeta Correcta

1. En el panel izquierdo, buscar: `/home/upeducac/`
2. Buscar la carpeta: `intranet.upeducacion-uncp.edu.pe`
3. **Si la carpeta ya existe:**
   - Entrar a la carpeta
   - Seleccionar TODO el contenido
   - Click en **Delete** (eliminar contenido viejo)
4. **Si la carpeta NO existe:**
   - Click en **+ Folder** (crear carpeta)
   - Nombre: `intranet.upeducacion-uncp.edu.pe`

### 2.4 Subir el Archivo ZIP

1. Entrar a la carpeta `intranet.upeducacion-uncp.edu.pe`
2. Click en **Upload** (arriba a la derecha)
3. Click en **Select File**
4. Seleccionar `psedu-sistema.zip`
5. Esperar a que termine la subida (puede tardar varios minutos)
6. Cerrar la ventana de upload

### 2.5 Extraer el Archivo

1. En File Manager, buscar el archivo `psedu-sistema.zip`
2. Click derecho en el archivo → **Extract**
3. Verificar que la ruta sea: `/home/upeducac/intranet.upeducacion-uncp.edu.pe/`
4. Click en **Extract File(s)**
5. Esperar a que termine la extracción
6. Click en **Close**
7. **Eliminar** el archivo `psedu-sistema.zip` (ya no es necesario)

### 2.6 Verificar Estructura

Debes ver esta estructura:
```
/home/upeducac/intranet.upeducacion-uncp.edu.pe/
├── app/
├── bootstrap/
├── config/
├── database/
├── public/
│   ├── index.php ⚠️ IMPORTANTE
│   ├── .htaccess
│   ├── logo/
│   ├── build/
│   └── ...
├── resources/
├── routes/
├── storage/
├── vendor/ ⚠️ IMPORTANTE
├── .env
├── artisan
├── composer.json
└── composer.lock
```

---

## ⚙️ PASO 3: Configurar Document Root

### 3.1 Ir a Configuración de Dominios

1. Volver al inicio de cPanel
2. Buscar **"Domains"** o **"Dominios"**
3. Click en **Domains**

### 3.2 Encontrar el Subdominio

1. Buscar en la lista: `intranet.upeducacion-uncp.edu.pe`
2. Click en **Manage** o **Administrar** (al lado del dominio)

### 3.3 Cambiar Document Root

1. Buscar el campo **"Document Root"**
2. **Borrar** el contenido actual
3. **Escribir exactamente:**
   ```
   intranet.upeducacion-uncp.edu.pe/public
   ```
4. ⚠️ **IMPORTANTE:** NO escribir `/home/upeducac/` al inicio
5. ⚠️ **IMPORTANTE:** Debe terminar en `/public`
6. Click en **Save** o **Guardar**

**Ejemplo de cómo debe quedar:**
```
✅ CORRECTO:
Document Root: intranet.upeducacion-uncp.edu.pe/public

❌ INCORRECTO:
Document Root: /home/upeducac/home/upeducac/intranet.upeducacion-uncp.edu.pe/public
Document Root: intranet.upeducacion-uncp.edu.pe
Document Root: /home/upeducac/intranet.upeducacion-uncp.edu.pe/public
```

---

## 🔐 PASO 4: Configurar Permisos

### 4.1 Configurar Permisos de Storage

1. Volver a **File Manager**
2. Navegar a: `/home/upeducac/intranet.upeducacion-uncp.edu.pe/`
3. Buscar la carpeta **`storage`**
4. Click derecho en `storage` → **Change Permissions**
5. En la ventana que aparece:
   - Establecer: **755**
   - ✅ Marcar la casilla **"Recurse into subdirectories"**
   - ✅ Marcar **"Apply to directories"**
   - ✅ Marcar **"Apply to files"**
6. Click en **Change Permissions**

### 4.2 Configurar Permisos de Bootstrap Cache

1. En File Manager, entrar a la carpeta **`bootstrap`**
2. Buscar la carpeta **`cache`**
3. Click derecho en `cache` → **Change Permissions**
4. Establecer: **755**
5. ✅ Marcar **"Recurse into subdirectories"**
6. Click en **Change Permissions**

---

## ✅ PASO 5: Verificar que Funciona

### 5.1 Acceder al Sistema

1. Abrir un navegador
2. Ir a: **https://intranet.upeducacion-uncp.edu.pe**
3. Debe aparecer la **página de login** del sistema

### 5.2 Probar el Login

**Credenciales:**
- **Email:** `upeducacionuncp@gmail.com`
- **Password:** `Admin2024!`

1. Ingresar las credenciales
2. Click en **Iniciar Sesión**
3. Debe redirigir al **Dashboard de Administrador**

### 5.3 Verificar Dashboard

1. Debe cargar el dashboard sin errores
2. Verificar que aparezcan las estadísticas
3. Verificar que el menú lateral funcione

---

## 🚨 SOLUCIÓN DE PROBLEMAS

### ❌ Error 500 - Internal Server Error

**Causa más común:** Document Root incorrecto

**Solución:**
1. Ir a cPanel → **Domains**
2. Click en **Manage** del dominio
3. Verificar que Document Root sea exactamente:
   ```
   intranet.upeducacion-uncp.edu.pe/public
   ```
4. Si es diferente, corregirlo y guardar
5. Esperar 1-2 minutos y recargar la página

---

### ❌ Página en Blanco

**Causa:** Permisos incorrectos

**Solución:**
1. Verificar permisos de `storage/` → debe ser **755**
2. Verificar permisos de `bootstrap/cache/` → debe ser **755**
3. Asegurarse de haber marcado **"Recurse into subdirectories"**

---

### ❌ Error "Vendor not found"

**Causa:** La carpeta `vendor/` no se subió

**Solución:**
1. Verificar en File Manager que exista:
   ```
   /home/upeducac/intranet.upeducacion-uncp.edu.pe/vendor/
   ```
2. Si no existe, volver a comprimir el proyecto **incluyendo vendor/**
3. Subir y extraer nuevamente

---

### ❌ Ver Logs de Error

**Para ver qué está fallando:**

1. En File Manager, ir a:
   ```
   /home/upeducac/intranet.upeducacion-uncp.edu.pe/storage/logs/
   ```
2. Buscar el archivo más reciente: `laravel-YYYY-MM-DD.log`
3. Click derecho → **View**
4. Ver el último error registrado

**O desde cPanel:**
1. Ir a **Metrics** → **Errors**
2. Ver los últimos errores del servidor

---

## 📋 Checklist Final

Marca cada paso cuando lo completes:

- [ ] Proyecto comprimido en .zip
- [ ] Archivo subido a `/home/upeducac/intranet.upeducacion-uncp.edu.pe/`
- [ ] Archivo extraído correctamente
- [ ] Carpeta `vendor/` existe y tiene contenido
- [ ] Carpeta `public/` existe con `index.php` dentro
- [ ] Document Root configurado: `intranet.upeducacion-uncp.edu.pe/public`
- [ ] Permisos `storage/` = 755 (recursivo)
- [ ] Permisos `bootstrap/cache/` = 755 (recursivo)
- [ ] Sitio carga sin Error 500
- [ ] Página de login aparece
- [ ] Login funciona con credenciales admin
- [ ] Dashboard carga correctamente

---

## 🎉 ¡Listo!

Si todos los pasos del checklist están marcados, el sistema está **funcionando en producción**.

**URL del sistema:** https://intranet.upeducacion-uncp.edu.pe  
**Usuario admin:** upeducacionuncp@gmail.com  
**Password:** Admin2024!

---

## 📞 Información Adicional

### Base de Datos
Ya está configurada en AWS RDS, no necesitas hacer nada.

### Emails
Ya están configurados con Gmail SMTP, funcionarán automáticamente.

### Documentación
- `README-PSEDU.md` - Documentación completa del sistema
- `VERIFICACION-FINAL-PRODUCCION.md` - Guía técnica detallada

---

**¿Necesitas ayuda?** Revisa los logs en `storage/logs/laravel.log` o los errores del servidor en cPanel → Metrics → Errors.
