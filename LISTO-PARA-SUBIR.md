# ✅ SISTEMA PS-EDU - LISTO PARA SUBIR AL SERVIDOR

**Fecha:** 3 de Mayo 2026  
**Estado:** ✅ VERIFICADO Y LISTO

---

## 🎯 Resumen Ejecutivo

El sistema PS-EDU está **completamente preparado** para ser subido al servidor de producción. Laravel 11 ha sido restaurado exitosamente y todas las configuraciones están correctas.

---

## ✅ Verificaciones Completadas

### Sistema
- ✅ Laravel 11.51.0 funcionando correctamente
- ✅ PHP 8.1+ compatible (servidor tiene 8.3.30)
- ✅ Todas las rutas funcionan
- ✅ Cachés limpiadas
- ✅ Archivos .md innecesarios eliminados
- ✅ Sin symlinks (todas las carpetas son reales en public/)

### Configuración
- ✅ `.env` configurado para producción
- ✅ Base de datos AWS RDS configurada
- ✅ Email Gmail SMTP configurado
- ✅ Usuario admin creado
- ✅ Seguridad OWASP implementada

---

## 🚀 PASOS RÁPIDOS PARA SUBIR

### 1️⃣ Subir Archivos (cPanel File Manager)

**Ubicación en servidor:**
```
/home/upeducac/intranet.upeducacion-uncp.edu.pe/
```

**Subir TODO excepto:**
- ❌ `node_modules/`
- ❌ `.git/`

**IMPORTANTE:** Asegúrate de subir la carpeta `vendor/` completa (contiene todas las dependencias de Laravel).

---

### 2️⃣ Configurar Document Root

**En cPanel → Domains:**

1. Buscar: `intranet.upeducacion-uncp.edu.pe`
2. Click en **Manage**
3. Cambiar **Document Root** a:
   ```
   intranet.upeducacion-uncp.edu.pe/public
   ```
4. Guardar

⚠️ **NO usar:** `/home/upeducac/home/upeducac/...` (ruta duplicada)

---

### 3️⃣ Configurar Permisos

**En cPanel File Manager:**

1. Ir a `/home/upeducac/intranet.upeducacion-uncp.edu.pe/`
2. Click derecho en carpeta `storage/` → **Change Permissions**
3. Establecer: **755**
4. ✅ Marcar "Recurse into subdirectories"
5. Aplicar

Repetir para `bootstrap/cache/` → **755**

---

### 4️⃣ Verificar que Funciona

**Visitar:**
```
https://intranet.upeducacion-uncp.edu.pe
```

**Login:**
- Email: `upeducacionuncp@gmail.com`
- Password: `Admin2024!`

---

## 🔧 Si Aparece Error 500

### Causa Principal: Document Root Incorrecto

**Verificar en cPanel → Domains:**

✅ **CORRECTO:**
```
intranet.upeducacion-uncp.edu.pe/public
```

❌ **INCORRECTO:**
```
/home/upeducac/home/upeducac/intranet.upeducacion-uncp.edu.pe/public
```

### Ver Logs de Error

**Logs de Laravel:**
```
storage/logs/laravel.log
```

**Logs del Servidor:**
- cPanel → **Metrics** → **Errors**

---

## 📋 Checklist de Subida

- [ ] Archivos subidos a `/home/upeducac/intranet.upeducacion-uncp.edu.pe/`
- [ ] Carpeta `vendor/` completa subida
- [ ] Document Root: `intranet.upeducacion-uncp.edu.pe/public`
- [ ] Permisos `storage/` = 755 (recursivo)
- [ ] Permisos `bootstrap/cache/` = 755 (recursivo)
- [ ] Sitio carga sin Error 500
- [ ] Login funciona
- [ ] Dashboard admin carga

---

## 📞 Información de Acceso

### Sistema
- **URL:** https://intranet.upeducacion-uncp.edu.pe
- **Admin:** upeducacionuncp@gmail.com / Admin2024!

### Base de Datos (AWS RDS)
- **Host:** cpapcentro.cr40yws4ssdu.us-east-2.rds.amazonaws.com
- **DB:** ps_edu
- **User:** cpapcentro
- **Pass:** cpapcentro2026

### Email (Gmail SMTP)
- **Email:** upeducacionuncp@gmail.com
- **App Password:** ntyo ebtl qfbo kkji

---

## 📄 Documentación Completa

Para instrucciones detalladas, ver:
- `VERIFICACION-FINAL-PRODUCCION.md` - Guía completa paso a paso
- `README-PSEDU.md` - Documentación del sistema

---

**¡El sistema está listo! Solo falta subirlo al servidor y configurar el Document Root correctamente.**
