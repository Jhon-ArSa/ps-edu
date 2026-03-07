# 03 — Módulos del Sistema

Este documento describe en detalle **cada módulo** del sistema: su propósito, funcionalidades por actor, estado de implementación y notas técnicas.

---

## Módulo 1: Autenticación y Sesiones

**Estado:** ✅ Implementado

### Descripción
Controla el acceso al sistema. Verifica identidad, gestiona sesiones y redirige a cada usuario a su portal según su rol.

### Funcionalidades

| Función | Admin | Docente | Alumno |
|---|:---:|:---:|:---:|
| Inicio de sesión con email + contraseña | ✅ | ✅ | ✅ |
| Recordar sesión (remember me) | ✅ | ✅ | ✅ |
| Recuperación de contraseña por email | ✅ | ✅ | ✅ |
| Cierre de sesión | ✅ | ✅ | ✅ |
| Bloqueo de cuentas inactivas | — | — | — |
| Redirección por rol al ingresar | ✅ | ✅ | ✅ |

### Notas técnicas
- Cuentas con `status = false` no pueden iniciar sesión.
- Al login exitoso: `Auth::login()` + `session()->regenerate()`.
- Rutas protegidas con middleware `auth` y `role:...`.
- Contraseñas con bcrypt, 12 rounds.

---

## Módulo 2: Gestión de Usuarios

**Estado:** ✅ Implementado

### Descripción
Permite al administrador registrar, editar, activar/desactivar y eliminar usuarios del sistema (docentes y alumnos).

### Funcionalidades

| Función | Admin |
|---|:---:|
| Listar usuarios con búsqueda y filtro por rol | ✅ |
| Registrar nuevo usuario (docente o alumno) | ✅ |
| Crear perfil automático al registrar (DocenteProfile / AlumnoProfile) | ✅ |
| Editar datos del usuario | ✅ |
| Activar / desactivar cuenta (toggle AJAX) | ✅ |
| Eliminar usuario (soft-deactivate) | ✅ |

### Notas técnicas
- La eliminación no borra el registro; establece `status = false`.
- Al crear un docente se genera una fila en `docente_profiles`.
- Al crear un alumno se genera una fila en `alumno_profiles` con `code` único.
- Búsqueda por nombre, email o DNI.

---

## Módulo 3: Semestres Académicos

**Estado:** 🔲 Pendiente

### Descripción
Permite organizar los cursos dentro de períodos académicos definidos (ej. 2025-I, 2025-II). Es el contenedor temporal de toda la actividad académica.

### Funcionalidades

| Función | Admin |
|---|:---:|
| Crear semestre con fechas de inicio y fin | ✅ |
| Activar un semestre (solo uno activo a la vez) | ✅ |
| Cerrar semestre (archiva cursos) | ✅ |
| Ver cursos por semestre | ✅ |
| Filtrar toda la plataforma por semestre activo | ✅ |

### Modelo de datos
```
semesters: id, name (ej. "2025-I"), year, period (I/II),
           start_date, end_date, status (active/closed), created_at
```

### Notas técnicas
- Un curso pertenece a un semestre (`semester_id` FK en `courses`).
- El dashboard del Admin muestra estadísticas filtradas por semestre activo.
- El semestre activo se resuelve con `Semester::active()->first()` + cache.

---

## Módulo 4: Gestión de Cursos / Asignaturas

**Estado:** ✅ Implementado (mejora: vincular a semestres)

### Descripción
Permite crear y administrar las asignaturas del semestre. El administrador crea el curso y asigna un docente; el docente gestiona el contenido.

### Funcionalidades

| Función | Admin | Docente |
|---|:---:|:---:|
| Crear curso con código único | ✅ | — |
| Asignar docente a curso | ✅ | — |
| Editar datos del curso | ✅ | — |
| Activar / desactivar curso | ✅ | — |
| Eliminar curso (sin matrículas activas) | ✅ | — |
| Ver lista de sus cursos | — | ✅ |
| Ver detalle del curso (semanas, alumnos) | — | ✅ |
| Agregar / quitar alumnos manualmente | — | ✅ |

### Notas técnicas
- Unicidad del código del curso: `UNIQUE KEY code`.
- No se puede eliminar un curso con matrículas activas.
- Cursos inactivos no son visibles para alumnos.

---

## Módulo 5: Matrículas

**Estado:** ✅ Implementado

### Descripción
Gestiona la asociación entre alumnos y cursos. Tanto el administrador como el docente pueden matricular alumnos. La matrícula nunca se borra, solo cambia de estado.

### Funcionalidades

| Función | Admin | Docente |
|---|:---:|:---:|
| Ver todas las matrículas con filtros | ✅ | — |
| Activar / desactivar matrícula | ✅ | — |
| Buscar alumno para matricular (AJAX) | — | ✅ |
| Matricular alumno en curso | — | ✅ |
| Retirar alumno (status: dropped) | — | ✅ |

### Notas técnicas
- Tabla: `enrollments (course_id, user_id, enrolled_at, status: active/dropped/inactive)`.
- El re-enrollment activa la matrícula existente en lugar de crear una nueva.
- Un alumno ve solo cursos con `enrollments.status = active`.

---

## Módulo 6: Aula Virtual (Semanas y Contenido)

**Estado:** ✅ Implementado

### Descripción
Núcleo del sistema. Cada curso tiene hasta 16 semanas. Cada semana contiene materiales y tareas. Es el espacio de trabajo principal del docente y el alumno.

### Funcionalidades — Semanas

| Función | Docente |
|---|:---:|
| Crear semana (máximo 16 por curso) | ✅ |
| Editar título y descripción de semana (AJAX inline) | ✅ |
| Eliminar semana (y todo su contenido) | ✅ |

### Funcionalidades — Materiales

| Función | Docente | Alumno |
|---|:---:|:---:|
| Subir archivo (PDF, DOCX, PPT, etc.) | ✅ | — |
| Agregar enlace externo | ✅ | — |
| Agregar video (YouTube / Vimeo embed) | ✅ | — |
| Editar título / descripción (AJAX inline) | ✅ | — |
| Reordenar materiales (drag and drop) | ✅ | — |
| Eliminar material (y archivo del disco) | ✅ | — |
| Ver y descargar materiales | — | ✅ |
| Ver video embebido | — | ✅ |

### Notas técnicas
- Tipos de material: `file`, `link`, `video`.
- Videos: YouTube y Vimeo detectados por regex en `Material::getEmbedUrlAttribute()`.
- Archivos en disco: `storage/app/public/materials/{course_id}/`.
- Orden por columna `order` (reordenamiento con AJAX, json array de IDs).

---

## Módulo 7: Tareas y Entregas

**Estado:** ⚠️ Parcial (creación docente ✅ / entrega alumno 🔲)

### Descripción
El docente crea tareas con fecha límite y puntaje máximo. El alumno sube su entrega (archivo y/o comentario). El docente califica y registra el puntaje.

### Funcionalidades — Docente

| Función | Estado |
|---|:---:|
| Crear tarea (título, descripción, instrucciones, fecha límite, puntaje máximo) | ✅ |
| Adjuntar archivo de referencia a la tarea | ✅ |
| Activar / desactivar tarea | ✅ |
| Ver entregas de alumnos | 🔲 |
| Calificar entrega con puntaje y comentario | 🔲 |

### Funcionalidades — Alumno

| Función | Estado |
|---|:---:|
| Ver tarea (descripción, fecha límite, estado) | ✅ (lectura) |
| Subir archivo de entrega | 🔲 |
| Agregar comentario a la entrega | 🔲 |
| Editar entrega antes de la fecha límite | 🔲 |
| Ver estado (Pendiente / Entregada / Calificada) | 🔲 |
| Ver puntaje y comentario del docente | 🔲 |

### Modelo de datos (tabla a crear)
```
submissions:
  id, task_id (FK), user_id (FK),
  file_path (nullable), comments (text, nullable),
  submitted_at, status (pending/submitted/graded),
  score (decimal 4,1, nullable),
  feedback (text, nullable),
  graded_at (nullable), graded_by (user_id, nullable),
  created_at, updated_at
```

### Estados de una tarea para el alumno
| Estado | Condición |
|---|---|
| `Sin entregar` | No existe submission del alumno |
| `Entregada` | submission.status = submitted |
| `Calificada` | submission.status = graded, score no nulo |
| `Vencida` | due_date < now() y no submission |

---

## Módulo 8: Evaluaciones en Línea

**Estado:** 🔲 Pendiente

### Descripción
Permite al docente crear exámenes y cuestionarios que el alumno responde directamente en la plataforma, con o sin límite de tiempo.

### Funcionalidades — Docente

| Función | Estado |
|---|:---:|
| Crear evaluación (título, descripción, tiempo límite, fecha de inicio/fin) | 🔲 |
| Crear preguntas (opción múltiple, V/F, respuesta corta) | 🔲 |
| Definir puntaje por pregunta | 🔲 |
| Activar / desactivar evaluación | 🔲 |
| Ver resultados de todos los alumnos | 🔲 |

### Funcionalidades — Alumno

| Función | Estado |
|---|:---:|
| Ver evaluaciones disponibles | 🔲 |
| Iniciar evaluación (con contador de tiempo) | 🔲 |
| Responder preguntas y guardar progreso | 🔲 |
| Enviar evaluación (manual o automática al vencer el tiempo) | 🔲 |
| Ver resultado y respuestas correctas (si el docente lo habilita) | 🔲 |

### Tipos de preguntas soportadas
| Tipo | Descripción | Autocorregible |
|---|---|:---:|
| Opción múltiple (1 respuesta) | 4 opciones, una correcta | ✅ |
| Opción múltiple (varias respuestas) | Múltiples correctas | ✅ |
| Verdadero / Falso | Dos opciones | ✅ |
| Respuesta corta (texto) | El docente revisa manualmente | ❌ |

### Modelo de datos
```
evaluations: id, week_id, title, description, instructions,
             time_limit (minutos, nullable), start_at, end_at,
             max_attempts (default 1), show_results (bool),
             status (draft/active/closed)

questions: id, evaluation_id, type, body, order, points

question_options: id, question_id, body, is_correct

evaluation_attempts: id, evaluation_id, user_id,
                     started_at, submitted_at, score,
                     status (in_progress/completed/expired)

attempt_answers: id, attempt_id, question_id,
                 option_id (nullable), text_answer (nullable)
```

---

## Módulo 9: Calificaciones (Libreta de Notas)

**Estado:** 🔲 Pendiente

### Descripción
Vista unificada de notas por curso. El docente ve y edita las notas de todos los alumnos. El alumno ve solo las suyas. Las notas de tareas y evaluaciones se consolidan automáticamente.

### Funcionalidades — Docente

| Función | Estado |
|---|:---:|
| Ver libreta de notas del curso (tabla alumno × ítems) | 🔲 |
| Las notas de tareas calificadas se reflejan automáticamente | 🔲 |
| Las notas de evaluaciones autocorregidas se reflejan automáticamente | 🔲 |
| Ingresar nota manual (participación, oral, etc.) | 🔲 |
| Ver promedio final calculado automáticamente | 🔲 |
| Exportar libreta a Excel / PDF | 🔲 |

### Funcionalidades — Alumno

| Función | Estado |
|---|:---:|
| Ver sus notas por curso | 🔲 |
| Ver promedio actual | 🔲 |
| Ver qué ítems faltan entregar | 🔲 |

### Modelo de datos
```
grade_items: id, course_id, name, type (task/evaluation/participation/oral/final),
             weight (decimal, para promedio ponderado), max_score, order

grades: id, grade_item_id, user_id, score (decimal 4,1),
        comments, graded_by (user_id), graded_at
```

> **Nota:** Los ítems de tipo `task` y `evaluation` son generados automáticamente al crear la tarea/evaluación. Los de tipo `participation`, `oral`, `final` son ingresados manualmente.

---

## Módulo 10: Anuncios e Intranet

**Estado:** ✅ Implementado

### Descripción
El administrador publica anuncios institucionales dirigidos a docentes, alumnos o todos. Los docentes también pueden publicar anuncios. Los usuarios ven los anuncios relevantes en su sección "Intranet".

### Funcionalidades

| Función | Admin | Docente | Alumno |
|---|:---:|:---:|:---:|
| Crear / editar / eliminar anuncio | ✅ | — | — |
| Definir audiencia (todos / docentes / alumnos) | ✅ | — | — |
| Programar publicación (published_at) | ✅ | — | — |
| Ver anuncios relevantes (feed paginado) | ✅ | ✅ | ✅ |

### Notas técnicas
- `target_role`: `all`, `docente`, `alumno`.
- `published_at = null` → borrador, no visible.
- Scope `published()` filtra `published_at <= now()`.

---

## Módulo 11: Foro de Discusión

**Estado:** 🔲 Pendiente

### Descripción
Cada curso tiene un foro donde docentes y alumnos pueden abrir temas y responder. Facilita la comunicación académica asíncrona sin depender de WhatsApp.

### Funcionalidades

| Función | Docente | Alumno |
|---|:---:|:---:|
| Crear tema en el foro del curso | ✅ | ✅ |
| Responder a un tema | ✅ | ✅ |
| Fijar tema (pin) | ✅ | — |
| Cerrar tema (sin más respuestas) | ✅ | — |
| Eliminar su propia respuesta | ✅ | ✅ |

### Modelo de datos
```
forum_topics: id, course_id, user_id, title, body,
              is_pinned (bool), is_closed (bool),
              replies_count (cache), created_at

forum_replies: id, topic_id, user_id, body, created_at
```

---

## Módulo 12: Notificaciones

**Estado:** 🔲 Pendiente

### Descripción
Alertas en el sistema para eventos relevantes. Pull-based (el servidor no hace push; se carga al renderizar la página). Sin WebSockets.

### Eventos que generan notificación
| Evento | Notificado a |
|---|---|
| Nueva tarea publicada | Alumnos del curso |
| Tarea calificada | Alumno correspondiente |
| Nueva evaluación disponible | Alumnos del curso |
| Nuevo anuncio publicado | Audiencia del anuncio |
| Nuevo tema en foro | Alumnos y docente del curso |

### Implementación
- Tabla `notifications` de Laravel (vía `DatabaseChannel`).
- Columna `read_at` para marcar como leída.
- Badge en el header con conteo de no leídas.
- Al hacer clic: marcar como leída + redirigir al recurso.

---

## Módulo 13: Reportes y Supervisión

**Estado:** 🔲 Pendiente (parcial en dashboard Admin)

### Descripción
El administrador y directivos pueden ver el estado académico general: cursos activos, actividad docente, estadísticas de entregas y promedios de notas.

### Reportes disponibles

| Reporte | Descripción |
|---|---|
| Resumen semestral | Totales de alumnos, cursos, docentes activos |
| Actividad por curso | Materiales subidos, tareas creadas, entregas recibidas |
| Estado por docente | Cursos a cargo, semanas activas, materiales subidos |
| Notas por curso | Promedio general, distribución, alumnos desaprobados |
| Entregas pendientes | Alumnos que no han entregado cada tarea |
| Accesibilidad | Alumnos sin actividad en los últimos N días |

### Exportación
- Excel (via `maatwebsite/excel`) y PDF (via `barryvdh/laravel-dompdf`).
- Todos los reportes son filtrados por semestre.

---

## Módulo 14: Configuración del Sistema

**Estado:** ✅ Implementado

### Descripción
Permite al administrador configurar parámetros globales de la plataforma: nombre de la institución, acrónimo, subtítulo, colores (futuro). Los valores se almacenan en la tabla `settings` y se cachean.

### Parámetros actuales
| Clave | Descripción |
|---|---|
| `institution_name` | Nombre completo de la institución |
| `institution_acronym` | Acrónimo (ej. FAEDU) |
| `institution_subtitle` | Subtítulo o dependencia |

---

## Módulo 15: Perfil de Usuario

**Estado:** ✅ Implementado

### Descripción
Cada usuario puede actualizar sus datos personales, cambiar contraseña y subir foto de perfil. Los docentes tienen además el Escalafón (perfil profesional).

### Funcionalidades

| Función | Todos | Docente |
|---|:---:|:---:|
| Editar nombre, DNI, teléfono | ✅ | ✅ |
| Subir / cambiar foto de perfil | ✅ | ✅ |
| Cambiar contraseña | ✅ | ✅ |
| Editar escalafón (título, grado, especialidad, año de servicio) | — | ✅ |
| Editar código y programa (alumno) | ✅ | — |
