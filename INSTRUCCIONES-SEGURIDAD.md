# 🔐 INSTRUCCIONES DE SEGURIDAD — PS-EDU

**Fecha:** 1 de mayo de 2026  
**Para:** Administradores del Sistema PS-EDU  
**De:** Equipo de Desarrollo

---

## 📋 RESUMEN

Se han implementado medidas de seguridad críticas basadas en OWASP Top 10. Este documento explica cómo usar y mantener estas funcionalidades.

---

## 🎯 FUNCIONALIDADES IMPLEMENTADAS

### 1. Rate Limiting (Límite de Intentos)
**¿Qué hace?** Limita los intentos de login para prevenir ataques de fuerza bruta.

**Configuración:**
- 5 intentos por 5 minutos por dirección IP
- Mensaje al usuario: "Le quedan X intento(s)"
- Bloqueo temporal de 5 minutos después del 5º intento

**Ejemplo:**
```
Intento 1: ❌ "Le quedan 4 intento(s)"
Intento 2: ❌ "Le quedan 3 intento(s)"
Intento 3: ❌ "Le quedan 2 intento(s)"
Intento 4: ❌ "Le quedan 1 intento(s)"
Intento 5: ❌ "Su acceso ha sido bloqueado temporalmente"
Espera 5 minutos...
Intento 6: ✅ Puede intentar nuevamente
```

---

### 2. Bloqueo Automático de Cuentas
**¿Qué hace?** Bloquea cuentas después de múltiples intentos fallidos.

**Configuración:**
- 10 intentos fallidos = bloqueo automático
- Duración del bloqueo: 30 minutos
- El usuario ve: "Su cuenta está bloqueada temporalmente"

**¿Cómo desbloquear una cuenta?**

**Opción 1: Esperar 30 minutos** (desbloqueo automático)

**Opción 2: Desbloqueo manual por administrador**
```bash
php artisan user:unlock usuario@ejemplo.com
```

**Ejemplo de uso:**
```bash
# Usuario reporta que no puede entrar
php artisan user:unlock jhonyaroni650@gmail.com

# Salida:
✓ Cuenta desbloqueada exitosamente:
  Usuario: Jhony Aroni
  Email: jhonyaroni650@gmail.com
  Intentos fallidos reseteados: 10 → 0
```

---

### 3. Contraseñas Fuertes
**¿Qué hace?** Valida que las contraseñas sean seguras.

**Requisitos:**
- ✅ Mínimo 8 caracteres
- ✅ Al menos 1 letra MAYÚSCULA
- ✅ Al menos 1 letra minúscula
- ✅ Al menos 1 número (0-9)
- ✅ Al menos 1 carácter especial (!@#$%^&*()_+-=[]{}|;:,.<>?)
- ❌ No permite contraseñas comunes (password, 123456, etc.)
- ❌ No permite secuencias obvias (123, abc, etc.)

**Ejemplos:**

| Contraseña | ¿Válida? | Razón |
|------------|----------|-------|
| `12345678` | ❌ | Falta mayúscula, minúscula y especial |
| `Password` | ❌ | Falta número y especial |
| `Password1` | ❌ | Falta carácter especial |
| `Password1!` | ✅ | Cumple todos los requisitos |
| `MiClave2024!` | ✅ | Cumple todos los requisitos |
| `password123` | ❌ | Contraseña muy común |

**¿Dónde se aplica?**
- ✅ Creación de usuarios (admin)
- ✅ Actualización de contraseña (admin)
- ✅ Recuperación de contraseña (usuario)
- ✅ Cambio de contraseña (perfil)

---

### 4. Logs de Seguridad
**¿Qué hace?** Registra todos los eventos de seguridad del sistema.

**¿Qué se registra?**
- ✅ Login exitoso (usuario, IP, fecha/hora)
- ✅ Login fallido (email, IP, fecha/hora)
- ✅ Logout (usuario, IP, fecha/hora)
- ✅ Bloqueo de cuenta (usuario, IP, fecha/hora)
- ✅ Desbloqueo de cuenta (usuario, IP, fecha/hora)
- ✅ Recuperación de contraseña (usuario, IP, fecha/hora)

**¿Dónde se guardan?**
```
storage/logs/security.log
```

**¿Cómo ver los logs?**

**Ver últimas 50 líneas:**
```bash
tail -n 50 storage/logs/security.log
```

**Ver en tiempo real:**
```bash
tail -f storage/logs/security.log
```

**Buscar intentos fallidos:**
```bash
grep "Intento de login fallido" storage/logs/security.log
```

**Buscar bloqueos de cuenta:**
```bash
grep "Cuenta bloqueada" storage/logs/security.log
```

**Buscar actividad de un usuario:**
```bash
grep "jhonyaroni650@gmail.com" storage/logs/security.log
```

**Ejemplo de log:**
```json
[2026-05-01 23:30:15] security.WARNING: Intento de login fallido
{
  "email": "jhonyaroni650@gmail.com",
  "ip": "192.168.1.100",
  "attempts": 3,
  "user_agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64)..."
}
```

---

### 5. Headers de Seguridad HTTP
**¿Qué hace?** Protege contra ataques XSS, clickjacking, MITM, etc.

**Headers implementados:**
- `Content-Security-Policy` - Previene inyección de scripts maliciosos
- `X-Frame-Options: SAMEORIGIN` - Previene clickjacking
- `X-Content-Type-Options: nosniff` - Previene MIME type sniffing
- `X-XSS-Protection: 1; mode=block` - Protección XSS del navegador
- `Strict-Transport-Security` - Fuerza HTTPS (solo en producción)
- `Referrer-Policy` - Control de información de referrer
- `Permissions-Policy` - Deshabilita geolocalización, micrófono, cámara

**¿Cómo verificar?**
1. Abrir el navegador
2. Ir a la página de login
3. Abrir DevTools (F12)
4. Ir a la pestaña "Network"
5. Recargar la página
6. Hacer clic en la petición principal
7. Ver la pestaña "Headers"
8. Buscar los headers de seguridad

---

## 🛠️ COMANDOS ÚTILES

### Desbloquear Cuenta de Usuario
```bash
php artisan user:unlock usuario@ejemplo.com
```

### Ver Logs de Seguridad
```bash
# Últimas 50 líneas
tail -n 50 storage/logs/security.log

# En tiempo real
tail -f storage/logs/security.log

# Buscar intentos fallidos
grep "Intento de login fallido" storage/logs/security.log

# Buscar bloqueos
grep "Cuenta bloqueada" storage/logs/security.log

# Buscar actividad de un usuario
grep "usuario@ejemplo.com" storage/logs/security.log
```

### Limpiar Cache (si hay problemas)
```bash
php artisan cache:clear
```

### Ejecutar Pruebas de Seguridad
```bash
php test-seguridad.php
```

---

## 📊 MONITOREO RECOMENDADO

### Diario
- ✅ Revisar logs de seguridad para actividad sospechosa
- ✅ Verificar que no hay cuentas bloqueadas sin razón

### Semanal
- ✅ Revisar intentos de login fallidos por IP
- ✅ Identificar patrones de ataque
- ✅ Verificar que los logs se están generando correctamente

### Mensual
- ✅ Analizar estadísticas de seguridad
- ✅ Revisar políticas de contraseñas
- ✅ Actualizar lista de contraseñas comunes prohibidas

---

## 🚨 SITUACIONES COMUNES

### Situación 1: Usuario no puede entrar
**Síntomas:** "Su cuenta está bloqueada temporalmente"

**Causa:** 10 intentos fallidos de login

**Solución:**
1. Verificar identidad del usuario
2. Desbloquear cuenta:
   ```bash
   php artisan user:unlock usuario@ejemplo.com
   ```
3. Informar al usuario que puede intentar nuevamente
4. Revisar logs para detectar actividad sospechosa

---

### Situación 2: Usuario olvida su contraseña
**Síntomas:** "Las credenciales proporcionadas no son correctas"

**Solución:**
1. Usuario hace clic en "¿Olvidaste tu contraseña?"
2. Ingresa su email
3. Recibe email con enlace de recuperación
4. Crea nueva contraseña (debe cumplir requisitos)
5. Inicia sesión con nueva contraseña

**Nota:** La nueva contraseña debe cumplir con la política de contraseñas fuertes.

---

### Situación 3: Usuario reporta "Demasiados intentos"
**Síntomas:** "Demasiados intentos de inicio de sesión. Intente nuevamente en X segundos"

**Causa:** 5 intentos fallidos en 5 minutos (rate limiting por IP)

**Solución:**
1. Esperar 5 minutos
2. Intentar nuevamente
3. Si persiste, verificar que el usuario esté usando la contraseña correcta

**Nota:** Este es un bloqueo temporal por IP, no de la cuenta. No requiere desbloqueo manual.

---

### Situación 4: Contraseña rechazada al crear usuario
**Síntomas:** "La contraseña debe contener al menos..."

**Causa:** La contraseña no cumple con los requisitos de seguridad

**Solución:**
1. Crear contraseña que cumpla requisitos:
   - Mínimo 8 caracteres
   - Mayúsculas, minúsculas, números, especiales
   - No usar contraseñas comunes
2. Ejemplos válidos:
   - `MiClave2024!`
   - `Segura#123`
   - `Pass@word2024`

---

## 📞 SOPORTE

### Contacto
**Email:** upeducacionuncp@gmail.com

### Comandos de Emergencia
```bash
# Desbloquear cuenta
php artisan user:unlock usuario@ejemplo.com

# Ver logs de seguridad
tail -f storage/logs/security.log

# Limpiar cache
php artisan cache:clear
```

---

## 📚 DOCUMENTACIÓN ADICIONAL

1. **SEGURIDAD-OWASP-IMPLEMENTADA.md** - Guía técnica completa
2. **RESUMEN-SEGURIDAD-COMPLETADO.md** - Resumen ejecutivo
3. **AUDITORIA-SEGURIDAD-OWASP.md** - Auditoría de seguridad
4. **test-seguridad.php** - Script de pruebas automatizadas

---

## ✅ CHECKLIST DE VERIFICACIÓN

### Después de Implementar
- [ ] Ejecutar pruebas: `php test-seguridad.php`
- [ ] Verificar logs: `tail -f storage/logs/security.log`
- [ ] Probar rate limiting (5 intentos fallidos)
- [ ] Probar bloqueo de cuenta (10 intentos fallidos)
- [ ] Probar desbloqueo: `php artisan user:unlock test@ejemplo.com`
- [ ] Probar contraseña fuerte (crear usuario con contraseña débil)
- [ ] Verificar headers de seguridad en navegador (F12 > Network)

### En Producción
- [ ] Configurar `APP_ENV=production`
- [ ] Configurar `APP_DEBUG=false`
- [ ] Habilitar HTTPS
- [ ] Configurar `SESSION_LIFETIME=30` (30 minutos)
- [ ] Verificar que los logs se están generando
- [ ] Capacitar al equipo de soporte
- [ ] Documentar procedimiento de desbloqueo

---

## 🎓 CAPACITACIÓN DEL EQUIPO

### Para Administradores
1. Cómo desbloquear cuentas
2. Cómo revisar logs de seguridad
3. Cómo identificar actividad sospechosa
4. Cómo crear contraseñas seguras

### Para Usuarios
1. Política de contraseñas fuertes
2. Qué hacer si olvidan su contraseña
3. Qué hacer si su cuenta está bloqueada
4. Buenas prácticas de seguridad

---

**Implementado por:** Kiro AI  
**Fecha:** 1 de mayo de 2026  
**Versión:** 1.0
