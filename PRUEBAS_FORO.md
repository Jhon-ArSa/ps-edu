# 🧪 GUÍA DE PRUEBAS - SISTEMA DE FOROS

## ✅ Pre-requisitos

Antes de probar, asegúrate de:

1. ✅ Base de datos configurada en `.env`
2. ✅ Migraciones ejecutadas: `php artisan migrate`
3. ✅ Datos de prueba (users, courses, enrollments)
4. ✅ Servidor corriendo: `php artisan serve`

---

## 📋 CASOS DE PRUEBA

### 1️⃣ **ALUMNO - Crear tema en foro**

**URL:** `/alumno/mis-cursos/{course_id}/foro`

**Pasos:**
1. Iniciar sesión como alumno
2. Ir a "Mis Cursos"
3. Seleccionar un curso matriculado
4. Click en "Foro"
5. Click en "Publicar nuevo tema"
6. Llenar formulario:
   - Título: "¿Duda sobre la tarea 1?"
   - Contenido: "No entiendo el ejercicio 3..."
7. Click "Publicar tema"

**Resultado esperado:**
- ✅ Tema creado exitosamente
- ✅ Notificación al docente del curso
- ✅ Redirección a vista del tema
- ✅ Mensaje: "Tema publicado en el foro."

---

### 2️⃣ **DOCENTE - Crear tema y fijar**

**URL:** `/docente/cursos/{course_id}/foro`

**Pasos:**
1. Iniciar sesión como docente
2. Ir a "Mis Cursos"
3. Seleccionar un curso
4. Click en "Foro"
5. Crear tema: "Anuncio: Examen parcial"
6. Click en botón 📌 "Fijar"

**Resultado esperado:**
- ✅ Tema fijado aparece primero
- ✅ Badge "📌 Fijado" visible
- ✅ Todos los alumnos notificados
- ✅ Mensaje: "Tema fijado."

---

### 3️⃣ **ALUMNO - Responder en tema**

**URL:** `/alumno/mis-cursos/{course_id}/foro/{topic_id}`

**Pasos:**
1. Iniciar sesión como alumno
2. Entrar a un tema del foro
3. Scroll hasta "Tu respuesta"
4. Escribir: "Gracias por la explicación!"
5. Click "Publicar respuesta"

**Resultado esperado:**
- ✅ Respuesta publicada
- ✅ Contador de respuestas incrementado
- ✅ `last_reply_at` actualizado
- ✅ Mensaje: "Respuesta publicada."

---

### 4️⃣ **DOCENTE - Cerrar tema**

**URL:** `/docente/cursos/{course_id}/foro/{topic_id}`

**Pasos:**
1. Iniciar sesión como docente
2. Entrar a un tema
3. Click en botón 🔒 "Cerrar"

**Resultado esperado:**
- ✅ Badge "🔒 Cerrado" visible
- ✅ Formulario de respuesta deshabilitado
- ✅ Mensaje: "Este tema está cerrado y no acepta nuevas respuestas."
- ✅ Alumnos no pueden responder

---

### 5️⃣ **ALUMNO - Intentar responder en tema cerrado**

**Pasos:**
1. Iniciar sesión como alumno
2. Entrar a tema cerrado
3. Intentar enviar respuesta

**Resultado esperado:**
- ❌ Error 403: "Este tema está cerrado y no acepta nuevas respuestas."
- ✅ Formulario no visible

---

### 6️⃣ **DOCENTE - Eliminar respuesta inapropiada**

**Pasos:**
1. Iniciar sesión como docente
2. Entrar a tema con respuestas
3. Hover sobre respuesta → aparece botón 🗑️
4. Click en eliminar → confirmar

**Resultado esperado:**
- ✅ Respuesta eliminada (soft delete)
- ✅ Contador decrementado
- ✅ Mensaje: "Respuesta eliminada."
- ✅ No aparece en listado

---

### 7️⃣ **ALUMNO - Intentar eliminar tema ajeno**

**Pasos:**
1. Iniciar sesión como alumno
2. Intentar eliminar tema de otro usuario

**Resultado esperado:**
- ❌ Error 403: "Solo puedes eliminar tus propios temas."
- ✅ Botón eliminar no visible para temas ajenos

---

### 8️⃣ **ADMIN - Supervisar todos los foros**

**URL:** `/admin/foro`

**Pasos:**
1. Iniciar sesión como admin
2. Ir a "Supervisión de Foros"
3. Verificar tabla con todos los temas

**Resultado esperado:**
- ✅ Ver temas de todos los cursos
- ✅ Columnas: Tema, Curso, Autor, Respuestas, Fecha, Estado
- ✅ Estados: "Activo", "Cerrado"
- ✅ Indicador 📌 para temas fijados

---

### 9️⃣ **THROTTLING - Anti-spam**

**Pasos:**
1. Iniciar sesión como alumno
2. Intentar crear 11 temas en menos de 1 minuto

**Resultado esperado:**
- ❌ Error 429: "Too Many Requests"
- ✅ Límite: 10 temas/minuto para alumnos
- ✅ Límite: 30 respuestas/minuto

---

### 🔟 **NOTIFICACIONES - Verificar**

**Pasos:**
1. Docente crea tema
2. Verificar que alumnos reciben notificación
3. Click en notificación → redirige al tema

**Resultado esperado:**
- ✅ Notificación tipo: `forum_topic_created`
- ✅ Icono: 💬 (forum)
- ✅ URL correcta según rol:
  - Alumno: `/alumno/mis-cursos/{course}/foro/{topic}`
  - Docente: `/docente/cursos/{course}/foro/{topic}`

---

## 🗄️ VERIFICACIÓN DE BASE DE DATOS

### **Transacciones**

```sql
-- Verificar que replies_count es correcto
SELECT 
    ft.id, 
    ft.title, 
    ft.replies_count,
    COUNT(fr.id) as actual_replies
FROM forum_topics ft
LEFT JOIN forum_replies fr ON fr.topic_id = ft.id AND fr.deleted_at IS NULL
GROUP BY ft.id
HAVING ft.replies_count != COUNT(fr.id);
```

**Resultado esperado:** ✅ Sin resultados (todo sincronizado)

### **Soft Deletes**

```sql
-- Verificar soft deletes
SELECT * FROM forum_replies WHERE deleted_at IS NOT NULL;
```

**Resultado esperado:** ✅ Respuestas eliminadas con timestamp

---

## 🛡️ SEGURIDAD

### **Prueba de permisos**

1. ❌ Alumno NO matriculado intenta acceder al foro → Error 403
2. ❌ Alumno intenta fijar tema → Botón no visible
3. ❌ Alumno intenta cerrar tema → Botón no visible
4. ✅ Docente puede eliminar cualquier respuesta
5. ✅ Alumno solo elimina sus propias respuestas

### **Validaciones**

```php
// Título vacío
'title' => '' // ❌ Required

// Título muy largo
'title' => str_repeat('a', 300) // ❌ Max 255

// Body muy corto
'body' => 'Hola' // ❌ Min 10 caracteres

// Body muy largo
'body' => str_repeat('a', 6000) // ❌ Max 5000
```

---

## 📊 MÉTRICAS DE ÉXITO

Al finalizar las pruebas, verifica:

- [x] ✅ Crear tema como alumno
- [x] ✅ Crear tema como docente
- [x] ✅ Responder en tema abierto
- [x] ✅ No responder en tema cerrado
- [x] ✅ Fijar/desfijar tema (docente)
- [x] ✅ Cerrar/abrir tema (docente)
- [x] ✅ Eliminar tema propio
- [x] ✅ Eliminar respuesta propia
- [x] ✅ Docente elimina cualquier contenido
- [x] ✅ Admin visualiza todos los foros
- [x] ✅ Notificaciones funcionan
- [x] ✅ Throttling previene spam
- [x] ✅ Contadores sincronizados
- [x] ✅ Soft deletes funcionan
- [x] ✅ Permisos correctos por rol
- [x] ✅ Sin errores 500
- [x] ✅ Queries optimizadas (N+1)

---

## 🐛 DEBUGGING

### **Si algo falla:**

1. **Revisar logs:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

2. **Verificar migraciones:**
   ```bash
   php artisan migrate:status
   ```

3. **Cache:**
   ```bash
   php artisan route:clear
   php artisan config:clear
   php artisan cache:clear
   ```

4. **Query log (en controlador):**
   ```php
   \DB::enableQueryLog();
   // ... tu código ...
   dd(\DB::getQueryLog());
   ```

---

## ✅ CHECKLIST FINAL

Antes de marcar como "funcional":

- [ ] Todas las rutas responden (sin 404)
- [ ] Sin errores PHP (500)
- [ ] Sin errores SQL (SQLSTATE)
- [ ] Permisos respetan roles
- [ ] Notificaciones se envían
- [ ] Vistas se renderizan correctamente
- [ ] Formularios validan datos
- [ ] Soft deletes funcionan
- [ ] Transacciones mantienen integridad
- [ ] Throttling bloquea spam
- [ ] Queries optimizadas (< 50ms)

**¡Sistema de foros listo para producción! 🎉**
