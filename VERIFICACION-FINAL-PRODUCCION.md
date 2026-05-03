# ✅ Verificación Final - Sistema PS-EDU Listo para Producción

**Fecha:** 3 de Mayo 2026  
**Estado:** ✅ LISTO PARA SUBIR AL SERVIDOR

---

## 📋 Resumen del Sistema

### Versiones
- **Laravel:** 11.51.0 ✅
- **PHP Requerido:** 8.2+ (servidor tiene 8.3.30 ✅)
- **Base de Datos:** MySQL 8.0 (AWS RDS)

### Configuración Actual
- ✅ Laravel 11 restaurado y funcional
- ✅ Archivos .md innecesarios eliminados
- ✅ Solo 3 archivos .env (correcto)
- ✅ Configuración de producción en `.env`
- ✅ Seguridad OWASP implementada
- ✅ Sistema de emails configurado
- ✅ Usuario admin creado

---

## 🔧 Configuración de Producción

### Base de Datos (AWS RDS)
```
Host: cpapcentro.cr40yws4ssdu.us-east-2.rds.amazonaws.com
Database: ps_edu
Usuario: cpapcentro
Password: cpapcentro2026
```

### Email (Gmail SMTP)
```
Email: upeducacionuncp@gmail.com
Password App: ntyo ebtl qfbo kkji
```

### Usuario Administrador
```
Email: upeducacionuncp@gmail.com
Password: Admin2024!
```

---

## 📁 Estructura de Archivos en Servidor

**Ubicación del proyecto:**
```
/home/upeducac/intranet.upeducacion-uncp.edu.pe/
```

**Document Root debe apuntar a:**
```
intranet.upeducacion-uncp.edu.pe/public
```

⚠️ **IMPORTANTE:** NO usar la ruta duplicada `/home/upeducac/home/upeducac/...`

---

## 🚀 Pasos para Subir al Servidor

### 1. Preparar Archivos Localmente
```bash
# Verificar que todo está correcto
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Verificar que composer está actualizado
composer install --no-dev --optimize-autoloader
```

### 2. Archivos a Subir (vía cPanel File Manager)

**Subir TODO el proyecto EXCEPTO:**
- ❌ `node_modules/` (no es necesario en producción)
- ❌ `.git/` (opcional, no necesario)
- ❌ `tests/` (opcional)
- ❌ Archivos `.md` de documentación (ya eliminados)

**Archivos CRÍTICOS que DEBEN estar:**
- ✅ `vendor/` (todas las dependencias de Composer)
- ✅ `public/` (punto de entrada)
- ✅ `app/` (código de la aplicación)
- ✅ `bootstrap/` (bootstrap de Laravel)
- ✅ `config/` (configuraciones)
- ✅ `database/` (migraciones y seeders)
- ✅ `resources/` (vistas, CSS, JS)
- ✅ `routes/` (rutas)
- ✅ `storage/` (logs, cache, sesiones)
- ✅ `.env` (configuración de producción)
- ✅ `artisan` (CLI de Laravel)
- ✅ `composer.json` y `composer.lock`

### 3. Configurar Document Root en cPanel

**Opción A: Desde "Domains" en cPanel**
1. Ir a **Domains** en cPanel
2. Buscar el dominio `intranet.upeducacion-uncp.edu.pe`
3. Click en **Manage** o **Administrar**
4. En **Document Root**, cambiar a:
   ```
   intranet.upeducacion-uncp.edu.pe/public
   ```
5. Guardar cambios

**Opción B: Desde File Manager**
1. Verificar que los archivos estén en:
   ```
   /home/upeducac/intranet.upeducacion-uncp.edu.pe/
   ```
2. Verificar que `public/index.php` exista
3. Configurar Document Root desde Domains

### 4. Configurar Permisos en Servidor

**Vía cPanel File Manager:**
1. Seleccionar carpeta `storage/`
2. Click derecho → **Change Permissions**
3. Establecer: **755** (rwxr-xr-x)
4. ✅ Marcar "Recurse into subdirectories"
5. Aplicar

Repetir para:
- `bootstrap/cache/` → **755**

### 5. Verificar Configuración

**Crear archivo de diagnóstico:**
1. En cPanel File Manager, ir a `public/`
2. Crear archivo `check.php` con este contenido:

```php
<?php
echo "<h1>🔍 Verificación del Sistema PS-EDU</h1>";

// 1. Versión de PHP
echo "<h2>1. Versión de PHP</h2>";
echo "Versión: " . phpversion();
echo (version_compare(phpversion(), '8.2.0', '>=') ? " ✅ Compatible" : " ❌ Requiere PHP 8.2+");

// 2. Rutas
echo "<h2>2. Rutas del Sistema</h2>";
echo "Script actual: " . __FILE__ . "<br>";
echo "Directorio actual: " . __DIR__ . "<br>";
echo "Directorio padre: " . dirname(__DIR__) . "<br>";

// 3. Archivos críticos
echo "<h2>3. Archivos Críticos</h2>";
$files = [
    'Composer Autoloader' => '../vendor/autoload.php',
    'Bootstrap Laravel' => '../bootstrap/app.php',
    'Configuración' => '../.env',
    'Index Principal' => 'index.php',
    'Configuración Apache' => '.htaccess'
];

foreach ($files as $name => $file) {
    echo (file_exists($file) ? "✅" : "❌") . " $name: $file<br>";
}

// 4. Permisos
echo "<h2>4. Permisos</h2>";
$dirs = [
    'Storage' => '../storage',
    'Bootstrap Cache' => '../bootstrap/cache'
];

foreach ($dirs as $name => $dir) {
    if (file_exists($dir)) {
        $perms = substr(sprintf('%o', fileperms($dir)), -3);
        $writable = is_writable($dir) ? "Escribible" : "No escribible";
        echo "✅ $name: Permisos $perms ($writable)<br>";
    } else {
        echo "❌ $name: No existe<br>";
    }
}

// 5. Extensiones PHP
echo "<h2>5. Extensiones de PHP</h2>";
$extensions = ['pdo', 'pdo_mysql', 'mbstring', 'openssl', 'tokenizer', 'xml', 'ctype', 'json', 'bcmath'];
foreach ($extensions as $ext) {
    echo (extension_loaded($ext) ? "✅" : "❌") . " $ext<br>";
}

// 6. Cargar Laravel
echo "<h2>6. Prueba de Carga de Laravel</h2>";
try {
    require '../vendor/autoload.php';
    echo "✅ Autoloader cargado correctamente<br>";
    
    $app = require_once '../bootstrap/app.php';
    echo "✅ Aplicación Laravel cargada<br>";
    echo "✅ Laravel versión: " . app()->version() . "<br>";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}

// 7. Información del servidor
echo "<h2>7. Información del Servidor</h2>";
echo "Servidor: " . $_SERVER['SERVER_SOFTWARE'] . "<br>";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "Script Name: " . $_SERVER['SCRIPT_NAME'] . "<br>";

echo "<hr>";
echo "<p><strong>⚠️ IMPORTANTE:</strong> Eliminar este archivo después de verificar.</p>";
echo "<p>Archivo: " . __FILE__ . "</p>";
?>
```

3. Visitar: `https://intranet.upeducacion-uncp.edu.pe/check.php`
4. Verificar que todo esté ✅
5. **ELIMINAR** el archivo `check.php` después

### 6. Acceder al Sistema

**URL:** https://intranet.upeducacion-uncp.edu.pe

**Login:**
- Email: `upeducacionuncp@gmail.com`
- Password: `Admin2024!`

---

## 🔍 Solución de Problemas

### Error 500 - Internal Server Error

**Causa más común:** Document Root incorrecto

**Solución:**
1. Verificar en cPanel → Domains que Document Root sea:
   ```
   intranet.upeducacion-uncp.edu.pe/public
   ```
2. NO debe ser:
   ```
   /home/upeducac/home/upeducac/intranet.upeducacion-uncp.edu.pe/public
   ```

### Ver Logs de Error

**Logs de Laravel:**
```
/home/upeducac/intranet.upeducacion-uncp.edu.pe/storage/logs/laravel.log
```

**Logs del Servidor (cPanel):**
1. Ir a **Metrics** → **Errors**
2. Ver últimos errores

### Permisos Incorrectos

Si aparece error de permisos:
```bash
# En cPanel File Manager:
storage/ → 755 (recursivo)
bootstrap/cache/ → 755 (recursivo)
```

### Base de Datos No Conecta

Verificar en `.env`:
```
DB_HOST=cpapcentro.cr40yws4ssdu.us-east-2.rds.amazonaws.com
DB_DATABASE=ps_edu
DB_USERNAME=cpapcentro
DB_PASSWORD=cpapcentro2026
```

---

## ✅ Checklist Final

Antes de declarar el sistema en producción:

- [ ] Archivos subidos al servidor
- [ ] Document Root configurado correctamente
- [ ] Permisos de `storage/` y `bootstrap/cache/` en 755
- [ ] Archivo `check.php` ejecutado y verificado
- [ ] Archivo `check.php` eliminado
- [ ] Login funciona correctamente
- [ ] Dashboard de admin carga
- [ ] Emails de prueba funcionan
- [ ] Base de datos conecta correctamente

---

## 📞 Soporte

Si persisten problemas después de seguir estos pasos:

1. Verificar logs en `storage/logs/laravel.log`
2. Verificar logs del servidor en cPanel → Errors
3. Verificar que PHP sea 8.2+ en el servidor
4. Verificar que todas las extensiones PHP estén instaladas

---

**Sistema preparado por:** Kiro AI  
**Fecha:** 3 de Mayo 2026  
**Versión Laravel:** 11.51.0  
**Estado:** ✅ LISTO PARA PRODUCCIÓN
