# ✅ CONFIGURACIÓN COMPLETADA — Usuarios y Email

**Fecha:** 30 de abril de 2026  
**Sistema:** PS-EDU v1.0.0-beta

---

## 🎯 RESUMEN DE CAMBIOS

Se han implementado las siguientes funcionalidades:

1. ✅ **Usuario administrador principal creado**
2. ✅ **Envío automático de credenciales por email**
3. ✅ **Recuperación de contraseña por email**
4. ✅ **Configuración de Gmail lista**

---

## 👤 USUARIO ADMINISTRADOR PRINCIPAL

### Credenciales

```
Email: upeducacionuncp@gmail.com
Contraseña: Admin2024!
Rol: Administrador
```

### Crear el Usuario

**Opción A: Ejecutar el script automático**

```bash
bash crear-admin.sh
```

**Opción B: Ejecutar manualmente**

```bash
php artisan tinker
```

Luego ejecuta:

```php
$admin = \App\Models\User::firstOrCreate(
    ['email' => 'upeducacionuncp@gmail.com'],
    [
        'name' => 'Administrador Principal',
        'password' => \Illuminate\Support\Facades\Hash::make('Admin2024!'),
        'role' => 'admin',
        'status' => true,
    ]
);
```

**Opción C: Ejecutar el seeder**

```bash
php artisan db:seed --class=DatabaseSeeder
```

---

## 📧 CONFIGURACIÓN DE EMAIL

### 1. Configurar Gmail

Sigue la guía completa en: **[CONFIGURACION-EMAIL.md](CONFIGURACION-EMAIL.md)**

**Resumen rápido:**

1. Ve a https://myaccount.google.com/security
2. Activa "Verificación en 2 pasos"
3. Ve a https://myaccount.google.com/apppasswords
4. Genera una contraseña de aplicación para "Correo"
5. Copia la contraseña de 16 caracteres

### 2. Actualizar .env

Edita tu archivo `.env`:

```env
MAIL_MAILER=smtp
MAIL_SCHEME=tls
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=upeducacionuncp@gmail.com
MAIL_PASSWORD=abcdefghijklmnop              # ← Pega aquí la contraseña de aplicación
MAIL_FROM_ADDRESS="upeducacionuncp@gmail.com"
MAIL_FROM_NAME="PS-EDU - FAEDU"
MAIL_ENCRYPTION=tls
```

### 3. Probar Configuración

```bash
php artisan tinker
```

```php
Mail::raw('Email de prueba desde PS-EDU', function ($message) {
    $message->to('tu-email@gmail.com')
            ->subject('Prueba de Email PS-EDU');
});
```

---

## 📨 EMAILS AUTOMÁTICOS

### 1. Bienvenida a Nuevos Usuarios

**Cuándo se envía:**
- Al crear un usuario manualmente desde el panel de admin
- Al importar usuarios masivamente desde Excel

**Contenido del email:**
- Nombre del usuario
- Email de acceso
- Contraseña temporal
- Rol asignado (Admin, Docente, Alumno)
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

**Cuándo se envía:**
- Cuando el usuario hace clic en "¿Olvidó su contraseña?" en el login
- Ingresa su email y hace clic en "Enviar enlace de recuperación"

**Contenido del email:**
- Enlace para restablecer contraseña (válido por 60 minutos)
- Instrucciones claras
- Aviso de seguridad

**Flujo completo:**

1. Usuario va a `/login`
2. Hace clic en "¿Olvidó su contraseña?"
3. Ingresa su email: `upeducacionuncp@gmail.com`
4. Hace clic en "Enviar enlace de recuperación"
5. Recibe email con enlace
6. Hace clic en el enlace
7. Ingresa nueva contraseña
8. Contraseña actualizada exitosamente

---

## 🔧 ARCHIVOS MODIFICADOS

### 1. Notificación de Bienvenida

**Archivo:** `app/Notifications/WelcomeUserNotification.php`

**Funcionalidad:**
- Envía email con credenciales al crear usuario
- Se ejecuta en cola (asíncrono) para no bloquear la aplicación
- Personalizado según el rol del usuario

---

### 2. Controlador de Usuarios

**Archivo:** `app/Http/Controllers/Admin/UserController.php`

**Cambios:**

#### Método `store()` (Crear usuario manualmente)

```php
// Guardar contraseña temporal para el email
$temporaryPassword = $validated['password'];

$user = User::create($validated);

// ... crear perfil ...

// Enviar email de bienvenida
$user->notify(new \App\Notifications\WelcomeUserNotification(
    $user->name,
    $user->email,
    $temporaryPassword,
    $user->role
));
```

#### Método `importStore()` (Importación masiva)

```php
$user = User::create([...]);

// ... crear perfil ...

// Enviar email de bienvenida
$user->notify(new \App\Notifications\WelcomeUserNotification(
    $user->name,
    $user->email,
    $password, // Contraseña en texto plano antes de hashear
    $user->role
));
```

---

### 3. Seeder de Base de Datos

**Archivo:** `database/seeders/DatabaseSeeder.php`

**Cambio:**

```php
// Admin principal
User::create([
    'name'     => 'Administrador Principal',
    'email'    => 'upeducacionuncp@gmail.com',
    'password' => Hash::make('Admin2024!'),
    'role'     => 'admin',
    'status'   => true,
]);
```

---

### 4. Configuración de Email

**Archivos:**
- `.env.example` — Configuración para desarrollo
- `.env.production.example` — Configuración para producción

**Cambios:**

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=upeducacionuncp@gmail.com
MAIL_PASSWORD=                              # Contraseña de aplicación
MAIL_FROM_ADDRESS="upeducacionuncp@gmail.com"
MAIL_FROM_NAME="PS-EDU - FAEDU"
MAIL_ENCRYPTION=tls
```

---

### 5. Recuperación de Contraseña

**Archivo:** `app/Http/Controllers/Auth/PasswordResetController.php`

**Estado:** ✅ Ya estaba implementado correctamente

**Funcionalidad:**
- Envía email con enlace de recuperación
- Enlace válido por 60 minutos
- Permite cambiar contraseña de forma segura

---

## 🚀 CÓMO USAR

### Crear Usuario Manualmente

1. Inicia sesión como admin: `upeducacionuncp@gmail.com` / `Admin2024!`
2. Ve a **"Usuarios"** → **"Crear Usuario"**
3. Completa el formulario:
   - Nombre: Juan Pérez
   - Email: juan.perez@ejemplo.com
   - Contraseña: MiClave123
   - Confirmar contraseña: MiClave123
   - Rol: Docente
4. Haz clic en **"Crear Usuario"**
5. ✅ El usuario recibirá un email con sus credenciales

---

### Importar Usuarios Masivamente

1. Inicia sesión como admin
2. Ve a **"Usuarios"** → **"Importar Usuarios"**
3. Descarga la plantilla Excel
4. Completa la plantilla con los datos de los usuarios
5. Sube el archivo Excel
6. Haz clic en **"Importar"**
7. ✅ Todos los usuarios recibirán un email con sus credenciales

---

### Recuperar Contraseña

1. Ve a `/login`
2. Haz clic en **"¿Olvidó su contraseña?"**
3. Ingresa tu email: `upeducacionuncp@gmail.com`
4. Haz clic en **"Enviar enlace de recuperación"**
5. Revisa tu email
6. Haz clic en el enlace del email
7. Ingresa tu nueva contraseña
8. Haz clic en **"Restablecer Contraseña"**
9. ✅ Contraseña actualizada

---

## 📋 CHECKLIST DE VERIFICACIÓN

### Configuración Inicial

- [ ] Usuario admin creado: `upeducacionuncp@gmail.com`
- [ ] Contraseña de aplicación de Gmail generada
- [ ] Archivo `.env` actualizado con credenciales de Gmail
- [ ] Email de prueba enviado exitosamente

### Funcionalidades

- [ ] Crear usuario manualmente → Email recibido
- [ ] Importar usuarios masivamente → Emails recibidos
- [ ] Recuperar contraseña → Email recibido
- [ ] Enlace de recuperación funciona correctamente

### Producción (Opcional)

- [ ] Worker de colas configurado (Supervisor)
- [ ] Redis configurado para colas
- [ ] Logs de email monitoreados

---

## 🔐 SEGURIDAD

### Contraseña del Admin

⚠️ **IMPORTANTE:** Cambia la contraseña `Admin2024!` después del primer inicio de sesión.

**Cómo cambiar:**

1. Inicia sesión como admin
2. Ve a tu **Perfil** (esquina superior derecha)
3. Haz clic en **"Cambiar Contraseña"**
4. Ingresa:
   - Contraseña actual: `Admin2024!`
   - Nueva contraseña: (tu contraseña segura)
   - Confirmar nueva contraseña: (tu contraseña segura)
5. Haz clic en **"Actualizar Contraseña"**

### Contraseña de Gmail

⚠️ **NUNCA compartas la contraseña de aplicación de Gmail**

- No la subas a Git (`.env` está en `.gitignore`)
- No la compartas por email o chat
- Si crees que fue comprometida, revócala y genera una nueva

---

## 📊 LÍMITES DE GMAIL

| Tipo de Cuenta | Límite Diario |
|----------------|---------------|
| Gmail gratuito | 500 emails/día |
| Google Workspace | 2,000 emails/día |

**Recomendación:** Si necesitas enviar más de 500 emails/día, considera usar Google Workspace o un servicio de email transaccional (SendGrid, Mailgun, Amazon SES).

---

## 🆘 SOLUCIÓN DE PROBLEMAS

### Los emails no llegan

1. **Verifica la configuración de Gmail:**
   - ¿Generaste una contraseña de aplicación?
   - ¿Copiaste la contraseña sin espacios?

2. **Revisa los logs:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

3. **Verifica que el worker de colas esté corriendo:**
   ```bash
   php artisan queue:work
   ```

4. **Revisa la carpeta de spam**

---

### Error: "Failed to authenticate on SMTP server"

**Solución:**
- Verifica que usaste una **contraseña de aplicación** (16 caracteres)
- NO uses la contraseña normal de Gmail
- Copia la contraseña sin espacios

---

### Los emails se envían pero tardan mucho

**Solución:**
- Configura colas con Redis:
  ```env
  QUEUE_CONNECTION=redis
  ```
- Inicia el worker:
  ```bash
  php artisan queue:work
  ```

---

## 📞 SOPORTE

**Email:** upeducacionuncp@gmail.com  
**Documentación Completa:** [CONFIGURACION-EMAIL.md](CONFIGURACION-EMAIL.md)

---

## 📚 DOCUMENTOS RELACIONADOS

1. **[CONFIGURACION-EMAIL.md](CONFIGURACION-EMAIL.md)** — Guía completa de configuración de email
2. **[crear-admin.sh](crear-admin.sh)** — Script para crear el usuario admin
3. **[README-PSEDU.md](README-PSEDU.md)** — Documentación general del sistema

---

**Última actualización:** 30 de abril de 2026  
**Versión:** 1.0

---

## ✅ ¡TODO LISTO!

Ahora tu sistema PS-EDU está configurado para:

✅ Enviar credenciales automáticamente a nuevos usuarios  
✅ Permitir recuperación de contraseña por email  
✅ Usar `upeducacionuncp@gmail.com` como email principal  

**Próximo paso:** Configura la contraseña de aplicación de Gmail siguiendo [CONFIGURACION-EMAIL.md](CONFIGURACION-EMAIL.md)
