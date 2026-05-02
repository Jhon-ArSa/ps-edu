# 🔧 SOLUCIÓN: Error de Versión de PHP en Hosting

**Error:** `Your Composer dependencies require a PHP version ">= 8.3.0"`

---

## 🎯 CAUSA DEL PROBLEMA

El hosting tiene una versión de PHP menor a 8.2.0, pero Laravel 12 requiere PHP 8.2 o superior.

---

## ✅ SOLUCIONES

### Solución 1: Cambiar Versión de PHP en el Hosting (RECOMENDADO)

La mayoría de los hostings modernos permiten cambiar la versión de PHP desde el panel de control.

#### cPanel:
1. Ir a **cPanel**
2. Buscar **"Select PHP Version"** o **"MultiPHP Manager"**
3. Seleccionar **PHP 8.2** o **PHP 8.3**
4. Guardar cambios
5. Ejecutar: `composer install --optimize-autoloader --no-dev`

#### Plesk:
1. Ir a **Plesk Panel**
2. Seleccionar tu dominio
3. Ir a **"PHP Settings"**
4. Cambiar versión a **PHP 8.2** o **PHP 8.3**
5. Guardar cambios

#### Hosting con .htaccess:
Agregar al inicio del archivo `.htaccess`:
```apache
# Forzar PHP 8.2 (ajustar según disponibilidad del hosting)
AddHandler application/x-httpd-php82 .php
# O también puede ser:
# AddHandler application/x-httpd-php83 .php
```

---

### Solución 2: Usar PHP 8.2 desde Terminal SSH

Si tienes acceso SSH, puedes especificar la versión de PHP:

```bash
# Verificar versiones disponibles
ls /usr/bin/php*

# Usar PHP 8.2 específicamente
/usr/bin/php82 /usr/local/bin/composer install --optimize-autoloader --no-dev

# O crear un alias
alias php='/usr/bin/php82'
composer install --optimize-autoloader --no-dev
```

---

### Solución 3: Configurar composer.json para PHP 8.2 (YA ESTÁ CONFIGURADO)

El proyecto ya está configurado para PHP 8.2+. Verificar que el `composer.json` tenga:

```json
{
    "require": {
        "php": "^8.2"
    }
}
```

✅ **Ya está configurado correctamente.**

---

### Solución 4: Ignorar Requisitos de Plataforma (NO RECOMENDADO)

**⚠️ SOLO USAR COMO ÚLTIMO RECURSO**

Si no puedes cambiar la versión de PHP, puedes ignorar los requisitos:

```bash
composer install --ignore-platform-reqs --optimize-autoloader --no-dev
```

**ADVERTENCIA:** Esto puede causar errores en tiempo de ejecución si el hosting tiene PHP < 8.2.

---

## 🔍 VERIFICAR VERSIÓN DE PHP ACTUAL

### Desde Terminal SSH:
```bash
php -v
```

### Desde Navegador:
Crear archivo `info.php` en la raíz del proyecto:
```php
<?php
phpinfo();
```

Acceder a: `https://tu-dominio.com/info.php`

**⚠️ ELIMINAR DESPUÉS DE VERIFICAR** por seguridad.

---

## 📋 PASOS RECOMENDADOS

### Paso 1: Verificar Versión Actual
```bash
php -v
```

**Resultado esperado:**
```
PHP 8.2.x o superior
```

### Paso 2: Si es menor a 8.2, cambiar versión
- Usar cPanel/Plesk para cambiar a PHP 8.2 o 8.3
- O contactar al soporte del hosting

### Paso 3: Reinstalar Dependencias
```bash
# Limpiar instalación anterior
rm -rf vendor/
rm composer.lock

# Instalar nuevamente
composer install --optimize-autoloader --no-dev
```

### Paso 4: Verificar que funciona
```bash
php artisan --version
```

**Resultado esperado:**
```
Laravel Framework 12.x.x
```

---

## 🆘 SI EL HOSTING NO TIENE PHP 8.2+

### Opción A: Cambiar de Hosting (RECOMENDADO)

Hostings recomendados con PHP 8.2+:
- **DigitalOcean** (desde $6/mes)
- **Vultr** (desde $6/mes)
- **Linode** (desde $5/mes)
- **AWS Lightsail** (desde $5/mes)
- **Hostinger** (desde $2.99/mes con PHP 8.2)
- **SiteGround** (desde $3.99/mes con PHP 8.2)

### Opción B: Downgrade a Laravel 11 (NO RECOMENDADO)

Si absolutamente no puedes cambiar de hosting ni actualizar PHP, puedes hacer downgrade a Laravel 11 que soporta PHP 8.1+:

**⚠️ ESTO REQUIERE RECONFIGURACIÓN Y PUEDE CAUSAR PROBLEMAS**

```bash
# Backup del proyecto actual
cp -r . ../backup-proyecto

# Downgrade (NO RECOMENDADO)
composer require laravel/framework:^11.0 --update-with-dependencies
```

---

## 📞 CONTACTAR AL SOPORTE DEL HOSTING

Si no encuentras cómo cambiar la versión de PHP, contacta al soporte con este mensaje:

```
Asunto: Solicitud de Actualización de PHP a 8.2 o superior

Hola,

Necesito actualizar la versión de PHP de mi hosting a PHP 8.2 o superior 
para poder ejecutar mi aplicación Laravel 12.

¿Podrían ayudarme a cambiar la versión de PHP o indicarme cómo hacerlo 
desde el panel de control?

Dominio: tu-dominio.com

Gracias.
```

---

## ✅ VERIFICACIÓN FINAL

Después de cambiar la versión de PHP:

```bash
# 1. Verificar versión
php -v

# 2. Limpiar e instalar
rm -rf vendor/ composer.lock
composer install --optimize-autoloader --no-dev

# 3. Verificar Laravel
php artisan --version

# 4. Ejecutar migraciones
php artisan migrate --force

# 5. Optimizar
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 6. Probar en navegador
# https://tu-dominio.com
```

---

## 📊 REQUISITOS MÍNIMOS DEL SISTEMA

Para que PS-EDU funcione correctamente:

| Requisito | Versión Mínima | Recomendada |
|-----------|----------------|-------------|
| PHP | 8.2.0 | 8.3.0 |
| MySQL | 8.0 | 8.0+ |
| Composer | 2.0 | 2.7+ |
| Node.js | 18.0 | 20.0+ |
| npm | 9.0 | 10.0+ |

**Extensiones PHP requeridas:**
- BCMath
- Ctype
- Fileinfo
- JSON
- Mbstring
- OpenSSL
- PDO
- PDO_MySQL
- Tokenizer
- XML
- cURL
- GD o Imagick (para imágenes)
- Zip

---

## 🎯 RESUMEN

1. **Verificar versión de PHP:** `php -v`
2. **Si es < 8.2:** Cambiar a PHP 8.2+ desde cPanel/Plesk
3. **Reinstalar dependencias:** `composer install --optimize-autoloader --no-dev`
4. **Verificar:** `php artisan --version`
5. **¡Listo!**

---

**¿Necesitas ayuda?** Contacta a: upeducacionuncp@gmail.com
