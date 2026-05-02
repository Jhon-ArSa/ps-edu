# 🚀 GUÍA DE DEPLOYMENT — PS-EDU

## 📋 Índice

1. [Requisitos del Servidor](#requisitos-del-servidor)
2. [Instalación Inicial](#instalación-inicial)
3. [Configuración de Producción](#configuración-de-producción)
4. [Deployment Automatizado](#deployment-automatizado)
5. [Configuración de Servicios](#configuración-de-servicios)
6. [Monitoreo y Mantenimiento](#monitoreo-y-mantenimiento)
7. [Troubleshooting](#troubleshooting)

---

## 💻 Requisitos del Servidor

### Software Requerido

| Componente | Versión Mínima | Recomendado |
|------------|----------------|-------------|
| PHP | 8.2 | 8.3 |
| MySQL | 8.0 | 8.0+ |
| Redis | 6.0 | 7.0+ |
| Nginx / Apache | - | Nginx 1.24+ |
| Composer | 2.0 | 2.7+ |
| Node.js | 18.x | 20.x LTS |
| npm | 9.x | 10.x |
| Supervisor | 4.x | 4.2+ |

### Extensiones PHP Requeridas

```bash
# Verificar extensiones instaladas
php -m

# Extensiones necesarias:
- BCMath
- Ctype
- cURL
- DOM
- Fileinfo
- JSON
- Mbstring
- OpenSSL
- PCRE
- PDO
- PDO_MySQL
- Redis (phpredis)
- Tokenizer
- XML
```

### Recursos del Servidor

Para 200-300 usuarios concurrentes:

- **CPU:** 4 cores mínimo
- **RAM:** 8 GB mínimo (16 GB recomendado)
- **Disco:** 50 GB SSD mínimo
- **Ancho de banda:** 100 Mbps

---

## 📦 Instalación Inicial

### 1. Clonar Repositorio

```bash
# Conectar al servidor
ssh usuario@servidor.adesa.edu.pe

# Ir al directorio web
cd /var/www

# Clonar proyecto
git clone https://github.com/tu-org/psedu-plataforma.git psedu
cd psedu
```

### 2. Instalar Dependencias

```bash
# Dependencias PHP
composer install --no-dev --optimize-autoloader

# Dependencias JavaScript
npm ci --production=false

# Compilar assets
npm run build
```

### 3. Configurar Permisos

```bash
# Propietario correcto
sudo chown -R www-data:www-data /var/www/psedu

# Permisos de directorios
sudo chmod -R 775 storage bootstrap/cache

# Permisos de archivos
sudo find /var/www/psedu -type f -exec chmod 664 {} \;
sudo find /var/www/psedu -type d -exec chmod 775 {} \;
```

### 4. Configurar Entorno

```bash
# Copiar archivo de ejemplo
cp .env.production.example .env

# Editar configuración
nano .env

# Generar clave de aplicación
php artisan key:generate

# Crear symlink de storage
php artisan storage:link
```

### 5. Configurar Base de Datos

```bash
# Ejecutar migraciones
php artisan migrate --force

# (Opcional) Seeders para datos iniciales
php artisan db:seed --class=AdminSeeder
```

---

## ⚙️ Configuración de Producción

### 1. Archivo .env

Editar `/var/www/psedu/.env` con valores de producción:

```env
APP_NAME="PS-EDU FAEDU"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://psedu.adesa.edu.pe

DB_CONNECTION=mysql
DB_HOST=tu-rds-endpoint.amazonaws.com
DB_DATABASE=psedu_production
DB_USERNAME=psedu_user
DB_PASSWORD=contraseña_segura_aquí

CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=noreply@adesa.edu.pe
MAIL_PASSWORD=tu_app_password
MAIL_FROM_ADDRESS="noreply@adesa.edu.pe"
```

### 2. Optimizar Laravel

```bash
# Cachear configuración
php artisan config:cache

# Cachear rutas
php artisan route:cache

# Cachear vistas
php artisan view:cache

# Cachear eventos
php artisan event:cache

# Todo en uno
php artisan optimize
```

### 3. Configurar Nginx

Crear `/etc/nginx/sites-available/psedu`:

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name psedu.adesa.edu.pe;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name psedu.adesa.edu.pe;
    root /var/www/psedu/public;

    # SSL
    ssl_certificate /etc/letsencrypt/live/psedu.adesa.edu.pe/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/psedu.adesa.edu.pe/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;

    # Logs
    access_log /var/log/nginx/psedu-access.log;
    error_log /var/log/nginx/psedu-error.log;

    # Index
    index index.php;

    # Charset
    charset utf-8;

    # Max upload size
    client_max_body_size 10M;

    # Root location
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # PHP
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    # Deny access to hidden files
    location ~ /\. {
        deny all;
    }

    # Cache static assets
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
```

Activar sitio:

```bash
sudo ln -s /etc/nginx/sites-available/psedu /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### 4. Configurar SSL con Let's Encrypt

```bash
# Instalar Certbot
sudo apt install certbot python3-certbot-nginx

# Obtener certificado
sudo certbot --nginx -d psedu.adesa.edu.pe

# Renovación automática (ya configurada por Certbot)
sudo certbot renew --dry-run
```

---

## 🤖 Deployment Automatizado

### Usar Script de Deployment

```bash
# Hacer ejecutable
chmod +x deploy.sh

# Ejecutar deployment
./deploy.sh
```

El script `deploy.sh` realiza:

1. ✅ Activa modo mantenimiento
2. ✅ Descarga cambios de Git
3. ✅ Instala dependencias
4. ✅ Compila assets
5. ✅ Ejecuta migraciones
6. ✅ Optimiza aplicación
7. ✅ Reinicia workers
8. ✅ Desactiva modo mantenimiento

### Deployment Manual (Paso a Paso)

```bash
# 1. Modo mantenimiento
php artisan down --retry=60

# 2. Pull de cambios
git pull origin main

# 3. Dependencias
composer install --no-dev --optimize-autoloader
npm ci --production=false
npm run build

# 4. Limpiar caches
php artisan optimize:clear

# 5. Migraciones
php artisan migrate --force

# 6. Optimizar
php artisan optimize

# 7. Reiniciar workers
php artisan queue:restart

# 8. Modo online
php artisan up
```

---

## 🔧 Configuración de Servicios

### 1. Supervisor (Workers de Colas)

Copiar configuración:

```bash
sudo cp supervisor.conf /etc/supervisor/conf.d/psedu-worker.conf
```

Editar rutas si es necesario:

```bash
sudo nano /etc/supervisor/conf.d/psedu-worker.conf
```

Activar:

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start psedu-worker:*
```

Verificar estado:

```bash
sudo supervisorctl status
```

### 2. Cron (Laravel Scheduler)

Editar crontab del usuario web:

```bash
sudo crontab -u www-data -e
```

Agregar línea:

```cron
* * * * * cd /var/www/psedu && php artisan schedule:run >> /dev/null 2>&1
```

Verificar:

```bash
sudo crontab -u www-data -l
```

### 3. Redis

Instalar y configurar:

```bash
# Instalar
sudo apt install redis-server

# Configurar
sudo nano /etc/redis/redis.conf

# Cambiar:
# supervised no → supervised systemd
# bind 127.0.0.1 ::1 (mantener)
# requirepass tu_password_aqui (opcional pero recomendado)

# Reiniciar
sudo systemctl restart redis
sudo systemctl enable redis

# Verificar
redis-cli ping
# Debe responder: PONG
```

### 4. PHP-FPM Optimization

Editar `/etc/php/8.2/fpm/pool.d/www.conf`:

```ini
; Aumentar workers
pm = dynamic
pm.max_children = 50
pm.start_servers = 10
pm.min_spare_servers = 5
pm.max_spare_servers = 20
pm.max_requests = 500

; Timeouts
request_terminate_timeout = 60s
```

Reiniciar:

```bash
sudo systemctl restart php8.2-fpm
```

---

## 📊 Monitoreo y Mantenimiento

### 1. Logs

```bash
# Logs de Laravel
tail -f /var/www/psedu/storage/logs/laravel.log

# Logs de Nginx
tail -f /var/log/nginx/psedu-error.log
tail -f /var/log/nginx/psedu-access.log

# Logs de Supervisor
sudo tail -f /var/log/supervisor/psedu-worker.log

# Logs de PHP-FPM
sudo tail -f /var/log/php8.2-fpm.log
```

### 2. Monitoreo de Colas

```bash
# Ver trabajos pendientes
php artisan queue:monitor

# Ver trabajos fallidos
php artisan queue:failed

# Reintentar trabajos fallidos
php artisan queue:retry all
```

### 3. Limpieza Periódica

```bash
# Limpiar sesiones expiradas (automático con scheduler)
php artisan session:gc

# Limpiar cache antigua
php artisan cache:clear

# Limpiar vistas compiladas
php artisan view:clear

# Limpiar logs antiguos (manual)
find storage/logs -name "*.log" -mtime +30 -delete
```

### 4. Backup de Base de Datos

Instalar `spatie/laravel-backup`:

```bash
composer require spatie/laravel-backup
php artisan vendor:publish --provider="Spatie\Backup\BackupServiceProvider"
```

Configurar en `config/backup.php` y agregar al scheduler:

```php
Schedule::command('backup:run')->daily()->at('01:00');
```

Backup manual:

```bash
php artisan backup:run
```

### 5. Monitoreo de Recursos

```bash
# CPU y RAM
htop

# Espacio en disco
df -h

# Procesos PHP
ps aux | grep php

# Conexiones MySQL
mysql -u root -p -e "SHOW PROCESSLIST;"

# Estado de Redis
redis-cli info stats
```

---

## 🆘 Troubleshooting

### Error 500 - Internal Server Error

```bash
# Ver logs
tail -f storage/logs/laravel.log

# Verificar permisos
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# Limpiar caches
php artisan optimize:clear
```

### Workers No Procesan Colas

```bash
# Verificar Supervisor
sudo supervisorctl status

# Reiniciar workers
sudo supervisorctl restart psedu-worker:*

# Ver logs
sudo tail -f /var/log/supervisor/psedu-worker.log
```

### Sesiones No Persisten

```bash
# Verificar Redis
redis-cli ping

# Verificar configuración
php artisan config:show session

# Limpiar cache de configuración
php artisan config:clear
php artisan config:cache
```

### Assets No Cargan

```bash
# Verificar symlink
ls -la public/storage

# Recrear symlink
php artisan storage:link

# Verificar permisos
sudo chmod -R 775 storage/app/public
```

### Base de Datos No Conecta

```bash
# Verificar conexión
php artisan tinker
>>> DB::connection()->getPdo();

# Verificar credenciales en .env
cat .env | grep DB_

# Test de conexión MySQL
mysql -h host -u usuario -p base_de_datos
```

### Memoria Agotada

```bash
# Aumentar límite en php.ini
sudo nano /etc/php/8.2/fpm/php.ini
# memory_limit = 256M

# Reiniciar PHP-FPM
sudo systemctl restart php8.2-fpm
```

---

## 📋 Checklist de Deployment

### Pre-Deployment

- [ ] Tests pasan: `php artisan test`
- [ ] Código en rama `main` actualizado
- [ ] Migraciones revisadas
- [ ] Backup de base de datos realizado
- [ ] Variables de entorno verificadas

### Durante Deployment

- [ ] Modo mantenimiento activado
- [ ] Código actualizado desde Git
- [ ] Dependencias instaladas
- [ ] Assets compilados
- [ ] Migraciones ejecutadas
- [ ] Caches optimizadas
- [ ] Workers reiniciados

### Post-Deployment

- [ ] Sitio accesible
- [ ] Login funciona
- [ ] Colas procesando
- [ ] Logs sin errores críticos
- [ ] Monitoreo activo
- [ ] Modo mantenimiento desactivado

---

## 🔐 Seguridad en Producción

### Checklist de Seguridad

- [ ] `APP_DEBUG=false`
- [ ] `APP_ENV=production`
- [ ] SSL/HTTPS configurado
- [ ] Firewall configurado (UFW)
- [ ] Fail2ban instalado
- [ ] Permisos correctos (775/664)
- [ ] `.env` no accesible públicamente
- [ ] Rate limiting activo
- [ ] Backups automáticos configurados
- [ ] Logs monitoreados

### Firewall (UFW)

```bash
# Instalar
sudo apt install ufw

# Configurar
sudo ufw default deny incoming
sudo ufw default allow outgoing
sudo ufw allow ssh
sudo ufw allow 'Nginx Full'

# Activar
sudo ufw enable

# Verificar
sudo ufw status
```

---

**Última actualización:** 2026-04-29
**Versión:** 1.0.0
