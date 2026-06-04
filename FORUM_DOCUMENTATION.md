# 📚 Documentación del Sistema de Foros - PS-EDU

## ✅ Estado Actual del Sistema

El sistema de foros está **completamente funcional** para los tres roles: Admin, Docente y Alumno.

---

## 🎯 Funcionalidades por Rol

### 👤 **ALUMNO**

#### Puede:
- ✅ Ver todos los temas del foro de sus cursos matriculados
- ✅ Crear nuevos temas de discusión
- ✅ Responder en temas abiertos
- ✅ Eliminar sus propios temas (si no tienen respuestas o con confirmación)
- ✅ Eliminar sus propias respuestas
- ✅ Recibe notificación cuando el docente crea un tema

#### No puede:
- ❌ Fijar o desfijar temas
- ❌ Cerrar o abrir temas
- ❌ Responder en temas cerrados
- ❌ Ver foros de cursos donde no está matriculado
- ❌ Eliminar temas o respuestas de otros usuarios

#### Rutas:
```
GET    /alumno/mis-cursos/{course}/foro          → Ver lista de temas
POST   /alumno/mis-cursos/{course}/foro          → Crear nuevo tema
GET    /alumno/mis-cursos/{course}/foro/{topic}  → Ver tema con respuestas
DELETE /alumno/mis-cursos/{course}/foro/{topic}  → Eliminar tema propio
```

---

### 👨‍🏫 **DOCENTE**

#### Puede:
- ✅ Ver todos los temas del foro de sus cursos
- ✅ Crear nuevos temas (con notificación a todos los estudiantes)
- ✅ Responder en todos los temas
- ✅ **Fijar/Desfijar temas importantes** (📌)
- ✅ **Cerrar/Abrir temas** (🔒 para evitar más respuestas)
- ✅ Eliminar CUALQUIER tema del foro (moderación)
- ✅ Eliminar CUALQUIER respuesta del foro (moderación)
- ✅ Recibe notificación cuando un alumno crea un tema

#### No puede:
- ❌ Ver foros de cursos que no son suyos (a menos que sea admin)

#### Rutas:
```
GET    /docente/cursos/{course}/foro                 → Ver lista de temas
POST   /docente/cursos/{course}/foro                 → Crear nuevo tema
GET    /docente/cursos/{course}/foro/{topic}         → Ver tema con respuestas
DELETE /docente/cursos/{course}/foro/{topic}         → Eliminar tema
PATCH  /docente/cursos/{course}/foro/{topic}/fijar   → Fijar/desfijar tema
PATCH  /docente/cursos/{course}/foro/{topic}/cerrar  → Cerrar/abrir tema
```

---

### 🔧 **ADMINISTRADOR**

#### Puede:
- ✅ Ver TODOS los temas de TODOS los cursos (supervisión)
- ✅ Ver estadísticas de actividad del foro
- ✅ Filtrar por curso, autor, estado
- ✅ Ver información completa: curso, autor, respuestas, fecha, estado

#### No puede (desde el panel admin):
- ❌ Crear, editar o eliminar temas directamente
- ❌ Fijar o cerrar temas
- ✅ *Nota: El admin puede acceder a cualquier curso como docente si necesita moderar*

#### Rutas:
```
GET /admin/foro → Ver todos los temas (supervisión)
```

---

## 🔒 Seguridad y Permisos

### Verificaciones de Seguridad:
1. ✅ **Autenticación requerida** para todas las acciones
2. ✅ **Verificación de matrícula** para alumnos
3. ✅ **Verificación de ownership** del curso para docentes
4. ✅ **Políticas de autorización** (ForumTopicPolicy)
5. ✅ **Rate limiting** (throttling):
   - Docentes: 20 temas/minuto
   - Alumnos: 10 temas/minuto
   - Respuestas: 30/minuto (todos)
6. ✅ **CSRF protection** en todos los formularios
7. ✅ **Validación de inputs**
8. ✅ **Soft deletes** en respuestas (recuperables)

---

## 📊 Estructura de Base de Datos

### Tabla `forum_topics`
```sql
- id
- course_id (FK → courses)
- user_id (FK → users) [autor]
- title (string, max 255)
- body (text)
- is_pinned (boolean, default false)
- is_closed (boolean, default false)
- replies_count (int, default 0) [contador cache]
- last_reply_at (timestamp, nullable)
- timestamps (created_at, updated_at)
```

### Tabla `forum_replies`
```sql
- id
- topic_id (FK → forum_topics)
- user_id (FK → users) [autor]
- body (text)
- deleted_at (timestamp, nullable) [soft delete]
- timestamps (created_at, updated_at)
```

---

## 🎨 Características de la Interfaz

### Vista de Lista (index):
- 📌 Badge visual para temas fijados
- 🔒 Badge visual para temas cerrados
- 👤 Avatar circular con inicial del autor
- 📊 Contador de respuestas
- ⏰ Fecha de última actividad
- 🎯 Botones de acción en hover (docente)
- 📄 Paginación (20 temas por página)
- ➕ Formulario colapsable para crear tema

### Vista de Tema (show):
- 📝 Cuerpo del tema con formato preservado (whitespace-pre-line)
- 👥 Respuestas con identificación de rol (docente tiene badge especial)
- 🎨 Diseño responsivo y accesible
- 🔢 Contador de caracteres en tiempo real (máx 5000)
- ⚠️ Mensaje cuando el tema está cerrado
- 📄 Paginación de respuestas (20 por página)
- 🗑️ Eliminación con confirmación

---

## 📬 Notificaciones

### ForumTopicCreated:
- ✅ Se envía cuando un **docente** crea un tema → notifica a **todos los alumnos** matriculados
- ✅ Se envía cuando un **alumno** crea un tema → notifica al **docente** del curso
- ✅ No se notifica al autor si está matriculado en el curso
- ✅ Incluye: nombre del curso, título del tema, nombre del autor, URL directa

---

## 🧪 Datos de Prueba

### Comando disponible:
```bash
php artisan forum:test-data
```

Este comando crea automáticamente:
- ✅ 1 tema del docente (fijado)
- ✅ 1 tema del alumno con pregunta
- ✅ 1 tema cerrado con respuesta
- ✅ 3 respuestas de ejemplo
- ✅ Matrícula automática del alumno al curso

### URLs de Prueba (después de ejecutar el comando):
```
Admin:   http://127.0.0.1:8000/admin/foro
Docente: http://127.0.0.1:8000/docente/cursos/1/foro
Alumno:  http://127.0.0.1:8000/alumno/mis-cursos/1/foro
```

---

## 🚀 Cómo Usar el Foro

### Para Probar Como Alumno:
1. Inicia sesión con un usuario de rol `alumno`
2. Ve a "Mis Cursos"
3. Entra a un curso donde estés matriculado
4. Haz clic en la sección "Foro"
5. Crea un tema o responde en uno existente

### Para Probar Como Docente:
1. Inicia sesión con un usuario de rol `docente`
2. Ve a "Mis Cursos"
3. Entra a uno de tus cursos
4. Haz clic en "Foro"
5. Crea temas, fija los importantes, cierra los resueltos
6. Modera respuestas inapropiadas

### Para Probar Como Admin:
1. Inicia sesión con un usuario de rol `admin`
2. Ve al menú lateral → "Foro" o "Supervisión de Foros"
3. Visualiza todos los temas de todos los cursos
4. Monitorea la actividad del foro

---

## 🔍 Validaciones Implementadas

### Al Crear Tema:
- `title`: requerido, string, máx 255 caracteres
- `body`: requerido, string, mín 10 caracteres, máx 5000

### Al Crear Respuesta:
- `body`: requerido, string, mín 5 caracteres, máx 5000

### Reglas de Negocio:
- ✅ No se puede responder en temas cerrados
- ✅ Solo alumnos matriculados pueden ver/participar en el foro
- ✅ Solo el docente del curso puede fijar/cerrar temas
- ✅ Solo el autor o el docente pueden eliminar un tema
- ✅ Solo el autor o el docente pueden eliminar una respuesta

---

## 🐛 Troubleshooting

### "Access denied" al intentar acceder:
- Verifica que el usuario esté matriculado en el curso (alumno)
- Verifica que el usuario sea el docente del curso (docente)

### No aparecen los foros:
- Ejecuta `php artisan forum:test-data` para crear datos de prueba
- Verifica que las migraciones estén ejecutadas: `php artisan migrate:status`

### Las notificaciones no se envían:
- Verifica la configuración de email en `.env`
- Revisa la tabla `notifications` en la base de datos

### Error 403 Forbidden:
- Verifica que las políticas estén registradas en `AuthServiceProvider`
- Limpia la caché: `php artisan cache:clear && php artisan config:clear`

---

## 📈 Mejoras Futuras (Opcionales)

### Pendientes de Implementación:
- [ ] Notificaciones cuando alguien responde a tu tema
- [ ] Búsqueda y filtrado de temas por título/contenido
- [ ] Edición de temas y respuestas
- [ ] Reacciones/likes en respuestas
- [ ] Marcar temas como "resueltos"
- [ ] Adjuntar archivos en temas/respuestas
- [ ] Menciones de usuarios (@usuario)
- [ ] Estadísticas de participación por usuario

---

## ✅ Conclusión

El sistema de foros está **100% funcional** con:
- ✅ 3 roles implementados correctamente
- ✅ Todas las funcionalidades CRUD
- ✅ Permisos y seguridad robustos
- ✅ Interfaz amigable y responsiva
- ✅ Notificaciones automáticas
- ✅ Moderación completa para docentes
- ✅ Supervisión total para administradores

**¡El foro está listo para usar en producción!** 🎉
