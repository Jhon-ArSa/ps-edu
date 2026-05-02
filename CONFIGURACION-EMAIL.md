# 📧 GUÍA DE CONFIGURACIÓN DE EMAIL — PS-EDU

**Fecha:** 30 de abril de 2026  
**Email Principal:** upeducacionuncp@gmail.com

---

## 🎯 RESUMEN

Esta guía te ayudará a configurar el envío de emails en PS-EDU para:
- ✅ Enviar credenciales a nuevos usuarios
- ✅ Recuperación de contraseña por email
- ✅ Notificaciones del sistema

---

## 📋 CONFIGURACIÓN DE GMAIL

### Paso 1: Activar Verificación en 2 Pasos

1. Ve a https://myaccount.google.com/security
2. Inicia sesión con `upeducacionuncp@gmail.com`
3. Busca "Verificación en 2 pasos"
4. Haz clic en "Activar" si no está activada
5. Sigue los pasos para configurar tu teléfono

### Paso 2: Generar Contraseña de Aplicación

1. Ve a https://myaccount.google.com/apppasswords
2. En "Seleccionar app", elige **"Correo"**
3. En "Seleccionar dispositivo", elige **"Otro (nombre personalizado)"**
4. Escribe: **"PS-EDU Plataforma"**
5. Haz clic en **"Generar"**
6. **Copia la contraseña de 16 caracteres** (ejemplo: `abcd efgh ijkl mnop`)
7. **Guárdala en un lugar seguro** (la necesitarás en el siguiente paso)

---

## ⚙️ CONFIGURACIÓN EN EL SERVIDOR

### Opción A: Desarrollo Local

Edita tu archivo `.env`:

```env
MAIL_MAILER=smtp
MAIL_SCHEME=tls
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=upeducacionuncp@gmail.com
MAIL_PASSWORD=abcdefghijklmnop              # ← Pega aquí la contraseña de 16 caracteres (sin espacios)
MAIL_FROM_ADDRESS="upeducacionuncp@gmail.com"
MAIL_FROM_NAME="PS-EDU - FAEDU"
MAIL_ENCRYPTION=tls
```

### Opción B: Producción

Edita tu archivo `.env` en el servidor:

```env
MAIL_MAILER=smtp
MAIL_SCHEME=tls
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=upeducacionuncp@gmail.com
MAIL_PASSWORD=abcdefghijklmnop              # ← Contraseña de aplicación
MAIL_FROM_ADDRESS="upeducacionuncp@gmail.com"
MAIL_FROM_NAME="PS-EDU - FAEDU"
MAIL_ENCRYPTION=tls

# Configurar colas para envío asíncrono
QUEUE_CONNECTION=redis
```

---

## 🧪 PROBAR LA CONFIGURACIÓN

### 1. Probar Envío de Email

```bash
# Ejecutar en la terminal del servidor
php artisan tinker
```

Luego ejecuta:

```php
Mail::raw('Email de prueba desde PS-EDU', function ($message) {
    $message->to('tu-email-personal@gmail.com')
            ->subject('Prueba de Email PS-EDU');
});
```

Si ves `null` sin errores, el email se envió correctamente. Revisa tu bandeja de entrada.

### 2. Probar Recuperación de Contraseña

1. Ve a http://tu-dominio.com/login
2. Haz clic en "¿Olvidó su contraseña?"
3. Ingresa: `upeducacionuncp@gmail.com`
4. Haz clic en "Enviar enlace de recuperación"
5. Revisa el email en `upeducacionuncp@gmail.com`
6. Haz clic en el enlace y cambia la contraseña

### 3. Probar Creación de Usuario

1. Inicia sesión como admin
2. Ve a "Usuarios" → "Crear Usuario"
3. Crea un usuario de prueba con tu email personal
4. Revisa tu email personal
5. Deberías recibir un email con las credenciales

---

## 🚀 CONFIGURAR COLAS (Recomendado para Producción)

Para enviar emails en segundo plano sin bloquear la aplicación:

### 1. Configurar Redis

```env
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### 2. Iniciar Worker de Colas

```bash
# Opción A: Manualmente (para pruebas)
php artisan queue:work --tries=3

# Opción B: Con Supervisor (producción)
# Ya está configurado en supervisor.conf
sudo supervisorctl start psedu-worker:*
```

### 3. Verificar que el Worker está Corriendo

```bash
sudo supervisorctl status
```

Deberías ver:

```
psedu-worker:psedu-worker_00   RUNNING   pid 12345, uptime 0:01:23
```

---

## 📧 EMAILS QUE SE ENVÍAN AUTOMÁTICAMENTE

### 1. Bienvenida a Nuevos Usuarios

**Cuándo:** Al crear un usuario manualmente o por importación masiva

**Contenido:**
- Nombre del usuario
- Email de acceso
- Contraseña temporal
- Enlace para iniciar sesión
- Recomendación de cambiar contraseña

**Ejemplo:**

```
Asunto: Bienvenido a PS-EDU - Tus Credenciales de Acceso

¡Bienvenido(a) a PS-EDU, Juan Pérez!

Tu cuenta ha sido creada exitosamente en la plataforma PS-EDU 
de la Facultad de Educación.

Rol asignado: Docente

A continuación encontrarás tus credenciales de acceso:

Email: juan.perez@ejemplo.com
Contraseña temporal: MiClave123

⚠️ Importante: Por seguridad, te recomendamos cambiar tu 
contraseña después del primer inicio de sesión.

[Iniciar Sesión]

Si tienes alguna duda o problema para acceder, contacta con 
el administrador del sistema.

Saludos cordiales,
Equipo PS-EDU - FAEDU
```

---

### 2. Recuperación de Contraseña

**Cuándo:** El usuario hace clic en "¿Olvidó su contraseña?"

**Contenido:**
- Enlace para restablecer contraseña (válido por 60 minutos)
- Instrucciones claras

**Ejemplo:**

```
Asunto: Restablecer Contraseña - PS-EDU

Hola,

Recibimos una solicitud para restablecer la contraseña de tu 
cuenta en PS-EDU.

Haz clic en el siguiente enlace para crear una nueva contraseña:

[Restablecer Contraseña]

Este enlace expirará en 60 minutos.

Si no solicitaste este cambio, ignora este email. Tu contraseña 
actual seguirá siendo válida.

Saludos cordiales,
Equipo PS-EDU - FAEDU
```

---

### 3. Notificaciones del Sistema (Futuro)

- Nueva tarea publicada
- Tarea calificada
- Nueva evaluación disponible
- Nuevo anuncio
- Recordatorios de entregas

---

## 🔧 SOLUCIÓN DE PROBLEMAS

### Error: "Failed to authenticate on SMTP server"

**Causa:** Contraseña incorrecta o no es una contraseña de aplicación.

**Solución:**
1. Verifica que usaste una **contraseña de aplicación** (16 caracteres)
2. NO uses la contraseña normal de Gmail
3. Copia la contraseña sin espacios: `abcdefghijklmnop`

---

### Error: "Connection could not be established with host smtp.gmail.com"

**Causa:** Puerto bloqueado o firewall.

**Solución:**
1. Verifica que el puerto 587 esté abierto:
   ```bash
   telnet smtp.gmail.com 587
   ```
2. Si no funciona, intenta con puerto 465:
   ```env
   MAIL_PORT=465
   MAIL_ENCRYPTION=ssl
   ```

---

### Los emails no llegan

**Causa:** Pueden estar en spam o el worker de colas no está corriendo.

**Solución:**
1. Revisa la carpeta de spam
2. Verifica que el worker esté corriendo:
   ```bash
   sudo supervisorctl status
   ```
3. Revisa los logs:
   ```bash
   tail -f storage/logs/laravel.log
   ```

---

### Error: "Address in mailbox given does not comply with RFC 2822"

**Causa:** Email mal formado en MAIL_FROM_ADDRESS.

**Solución:**
```env
# ❌ MAL
MAIL_FROM_ADDRESS=PS-EDU <upeducacionuncp@gmail.com>

# ✅ BIEN
MAIL_FROM_ADDRESS="upeducacionuncp@gmail.com"
MAIL_FROM_NAME="PS-EDU - FAEDU"
```

---

## 📊 LÍMITES DE GMAIL

Gmail tiene límites de envío para prevenir spam:

| Tipo de Cuenta | Límite Diario |
|----------------|---------------|
| Gmail gratuito | 500 emails/día |
| Google Workspace | 2,000 emails/día |

**Recomendación:** Si necesitas enviar más de 500 emails/día, considera:
- Usar Google Workspace (pago)
- Usar un servicio de email transaccional (SendGrid, Mailgun, Amazon SES)

---

## 🔐 SEGURIDAD

### Buenas Prácticas

1. ✅ **Nunca compartas la contraseña de aplicación**
2. ✅ **No subas el archivo `.env` a Git** (ya está en `.gitignore`)
3. ✅ **Usa variables de entorno en producción**
4. ✅ **Revoca contraseñas de aplicación que no uses**
5. ✅ **Monitorea la actividad de la cuenta**

### Revocar Contraseña de Aplicación

Si crees que la contraseña fue comprometida:

1. Ve a https://myaccount.google.com/apppasswords
2. Busca "PS-EDU Plataforma"
3. Haz clic en "Eliminar"
4. Genera una nueva contraseña
5. Actualiza el `.env` en el servidor

---

## 📞 SOPORTE

**Email:** upeducacionuncp@gmail.com  
**Documentación:** Este archivo

---

## ✅ CHECKLIST DE CONFIGURACIÓN

- [ ] Verificación en 2 pasos activada en Gmail
- [ ] Contraseña de aplicación generada
- [ ] Archivo `.env` actualizado con credenciales
- [ ] Email de prueba enviado exitosamente
- [ ] Recuperación de contraseña probada
- [ ] Creación de usuario probada
- [ ] Worker de colas configurado (producción)
- [ ] Supervisor configurado (producción)

---

**Última actualización:** 30 de abril de 2026  
**Versión:** 1.0
