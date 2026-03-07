# 09 — Requerimientos del Sistema

## 1. Requerimientos Funcionales

### Módulo de Autenticación

| ID | Requerimiento |
|---|---|
| RF-AUTH-01 | El sistema debe permitir el inicio de sesión con email y contraseña. |
| RF-AUTH-02 | El sistema debe redirigir al usuario a su portal según su rol (admin/docente/alumno) tras el login. |
| RF-AUTH-03 | El sistema debe bloquear el acceso a usuarios con `status = inactivo`. |
| RF-AUTH-04 | El sistema debe permitir la recuperación de contraseña via email con token de validez de 60 minutos. |
| RF-AUTH-05 | El sistema debe permitir el cierre de sesión invalidando la sesión del servidor. |
| RF-AUTH-06 | El sistema debe ofrecer la opción "recordar sesión" en el formulario de login. |

### Módulo de Usuarios

| ID | Requerimiento |
|---|---|
| RF-USR-01 | El administrador debe poder registrar usuarios con roles de admin, docente o alumno. |
| RF-USR-02 | Al registrar un docente, el sistema debe crear automáticamente su perfil profesional (DocenteProfile). |
| RF-USR-03 | Al registrar un alumno, el sistema debe crear automáticamente su perfil académico (AlumnoProfile) con código único. |
| RF-USR-04 | El administrador debe poder activar o desactivar cuentas de usuario. |
| RF-USR-05 | El administrador debe poder buscar usuarios por nombre, email o DNI. |
| RF-USR-06 | Cualquier usuario autenticado debe poder editar sus datos personales (nombre, DNI, teléfono). |
| RF-USR-07 | Cualquier usuario autenticado debe poder subir o cambiar su foto de perfil. |
| RF-USR-08 | Cualquier usuario autenticado debe poder cambiar su contraseña verificando la actual. |

### Módulo de Semestres

| ID | Requerimiento |
|---|---|
| RF-SEM-01 | El administrador debe poder crear semestres académicos con nombre, año, período (I/II), fechas de inicio y fin. |
| RF-SEM-02 | El sistema debe garantizar que solo un semestre esté activo al mismo tiempo. |
| RF-SEM-03 | El administrador debe poder cerrar un semestre, lo que lo archiva sin eliminarlo. |
| RF-SEM-04 | El sistema debe filtrar los cursos del dashboard por el semestre activo. |

### Módulo de Cursos

| ID | Requerimiento |
|---|---|
| RF-CUR-01 | El administrador debe poder crear cursos con nombre, código único, descripción, docente asignado y semestre. |
| RF-CUR-02 | El sistema debe impedir la eliminación de un curso con matrículas activas. |
| RF-CUR-03 | El administrador debe poder activar o desactivar cursos. |
| RF-CUR-04 | El docente debe poder ver únicamente los cursos que tiene asignados. |
| RF-CUR-05 | El alumno debe ver únicamente los cursos en los que está matriculado activamente. |

### Módulo de Matrículas

| ID | Requerimiento |
|---|---|
| RF-MAT-01 | El administrador debe poder matricular y retirar alumnos de cualquier curso. |
| RF-MAT-02 | El docente debe poder buscar alumnos (AJAX) y matricularlos en su curso. |
| RF-MAT-03 | El docente debe poder retirar alumnos de su curso (cambiar estado a `dropped`). |
| RF-MAT-04 | Si un alumno previamente retirado es matriculado de nuevo, el sistema debe reactivar su matrícula existente. |
| RF-MAT-05 | El sistema debe impedir duplicados de matrícula por combinación (alumno, curso). |

### Módulo de Aula Virtual

| ID | Requerimiento |
|---|---|
| RF-AUL-01 | El docente debe poder crear hasta 16 semanas por curso, cada una con número, título y descripción. |
| RF-AUL-02 | El docente debe poder editar el título y descripción de una semana sin recargar la página (AJAX inline). |
| RF-AUL-03 | El docente debe poder agregar materiales de tipo archivo, enlace externo o video (YouTube/Vimeo) a cada semana. |
| RF-AUL-04 | El docente debe poder reordenar los materiales de una semana mediante drag & drop. |
| RF-AUL-05 | El docente debe poder eliminar materiales; al eliminar un archivo, debe borrarse también del disco. |
| RF-AUL-06 | El sistema debe generar URLs de embed automáticamente para videos de YouTube y Vimeo. |
| RF-AUL-07 | El alumno matriculado debe poder ver todos los materiales de sus cursos y descargar archivos. |

### Módulo de Tareas

| ID | Requerimiento |
|---|---|
| RF-TAR-01 | El docente debe poder crear tareas con título, descripción, instrucciones, fecha límite y puntaje máximo. |
| RF-TAR-02 | El docente debe poder adjuntar un archivo de instrucciones a la tarea. |
| RF-TAR-03 | El docente debe poder activar o desactivar tareas. |
| RF-TAR-04 | El docente debe poder ver la lista de entregas de sus alumnos para cada tarea. |
| RF-TAR-05 | El docente debe poder calificar una entrega asignando puntaje y comentario de retroalimentación. |
| RF-TAR-06 | El alumno debe poder entregar una tarea subiendo un archivo y/o comentario, antes de la fecha límite. |
| RF-TAR-07 | El alumno debe poder reeditar su entrega antes de que el docente la califique y antes de la fecha límite. |
| RF-TAR-08 | El sistema debe mostrar al alumno el estado de cada tarea: Sin entregar / Entregada / Calificada / Vencida. |
| RF-TAR-09 | El alumno debe poder ver el puntaje y comentario del docente en sus tareas calificadas. |

### Módulo de Evaluaciones

| ID | Requerimiento |
|---|---|
| RF-EVA-01 | El docente debe poder crear evaluaciones con título, instrucciones, tiempo límite, fecha de apertura y cierre. |
| RF-EVA-02 | El docente debe poder crear preguntas de tipo: opción múltiple (una respuesta), opción múltiple (varias), V/F y respuesta corta. |
| RF-EVA-03 | El sistema debe calcular automáticamente el puntaje de preguntas autocorregibles al enviar la evaluación. |
| RF-EVA-04 | El docente debe poder revisar y puntuar manualmente las preguntas de respuesta corta. |
| RF-EVA-05 | El sistema debe respetar el límite de tiempo: enviar automáticamente la evaluación al expirar el cronómetro. |
| RF-EVA-06 | El alumno debe poder rendir la evaluación intentos (conforme a `max_attempts` definido). |
| RF-EVA-07 | El alumno debe poder ver su puntaje al terminar (si el docente lo habilita). |

### Módulo de Calificaciones

| ID | Requerimiento |
|---|---|
| RF-CAL-01 | El docente debe tener una libreta de notas por curso que consolide tareas, evaluaciones y notas manuales. |
| RF-CAL-02 | Las notas de tareas calificadas deben reflejarse automáticamente en la libreta. |
| RF-CAL-03 | Las notas de evaluaciones autocorregidas deben reflejarse automáticamente en la libreta. |
| RF-CAL-04 | El docente debe poder ingresar notas manuales (participación, oral, final). |
| RF-CAL-05 | El sistema debe calcular el promedio ponderado automáticamente según los pesos definidos. |
| RF-CAL-06 | El docente debe poder exportar la libreta de notas en Excel y PDF. |
| RF-CAL-07 | El alumno debe poder ver sus propias calificaciones por curso, incluyendo el promedio actual. |

### Módulo de Anuncios

| ID | Requerimiento |
|---|---|
| RF-ANU-01 | El administrador debe poder crear anuncios con título, contenido, audiencia (todos/docentes/alumnos) y fecha de publicación. |
| RF-ANU-02 | Un anuncio sin fecha de publicación debe tratarse como borrador y no ser visible para los usuarios. |
| RF-ANU-03 | Los usuarios deben ver en su sección "Intranet" solo los anuncios dirigidos a su rol o a todos. |
| RF-ANU-04 | Los anuncios deben mostrarse paginados, del más reciente al más antiguo. |

### Módulo de Foro

| ID | Requerimiento |
|---|---|
| RF-FOR-01 | Cada curso debe tener un foro de discusión accesible para su docente y alumnos matriculados. |
| RF-FOR-02 | Docentes y alumnos deben poder crear temas en el foro. |
| RF-FOR-03 | Docentes y alumnos deben poder responder a temas existentes. |
| RF-FOR-04 | El docente debe poder fijar temas importantes y cerrar temas. |
| RF-FOR-05 | Los usuarios deben poder eliminar sus propias respuestas. |

### Módulo de Notificaciones

| ID | Requerimiento |
|---|---|
| RF-NOT-01 | El sistema debe notificar a los alumnos del curso cuando el docente publica una nueva tarea. |
| RF-NOT-02 | El sistema debe notificar al alumno cuando su tarea es calificada. |
| RF-NOT-03 | El sistema debe notificar a los alumnos cuando una nueva evaluación está disponible. |
| RF-NOT-04 | El sistema debe notificar a los usuarios de nuevos anuncios según su audiencia. |
| RF-NOT-05 | El usuario debe poder ver todas sus notificaciones y marcarlas como leídas. |
| RF-NOT-06 | El header de la aplicación debe mostrar un contador de notificaciones no leídas. |

### Módulo de Reportes

| ID | Requerimiento |
|---|---|
| RF-REP-01 | El administrador debe poder ver un reporte general del semestre activo: alumnos, cursos, docentes, matrículas. |
| RF-REP-02 | El administrador debe poder ver el estado de actividad por curso (materiales, tareas, entregas). |
| RF-REP-03 | El docente debe poder ver un reporte de su curso con promedio de notas y estado de entregas. |
| RF-REP-04 | El sistema debe permitir exportar reportes en formato Excel y PDF. |

---

## 2. Requerimientos No Funcionales

### Seguridad

| ID | Requerimiento |
|---|---|
| RNF-SEG-01 | El sistema debe usar control de acceso basado en roles (RBAC) con dos capas: middleware de rol y policies de recursos. |
| RNF-SEG-02 | Todas las contraseñas deben almacenarse con bcrypt (mínimo 12 rounds). |
| RNF-SEG-03 | El sistema debe regenerar el ID de sesión al autenticarse (prevención de session fixation). |
| RNF-SEG-04 | Todos los formularios deben incluir token CSRF. |
| RNF-SEG-05 | Los archivos subidos deben almacenarse con nombres generados por el sistema, sin conservar el nombre original del usuario. |
| RNF-SEG-06 | El sistema debe validar tipo MIME y tamaño de archivos en el backend, independientemente del cliente. |
| RNF-SEG-07 | El sistema debe verificar que el recurso solicitado pertenece al usuario autenticado (prevención de IDOR). |
| RNF-SEG-08 | El rate limiting de login debe restringir a 10 intentos fallidos en 5 minutos por IP. |
| RNF-SEG-09 | En producción, `APP_DEBUG` debe ser `false` para no exponer trazas de error. |

### Rendimiento

| ID | Requerimiento |
|---|---|
| RNF-REN-01 | El tiempo de respuesta para el 95% de las peticiones debe ser menor a 500ms bajo carga normal (< 50 usuarios simultáneos). |
| RNF-REN-02 | El sistema debe soportar picos de 50 usuarios concurrentes sin degradación de servicio. |
| RNF-REN-03 | Todas las vistas con listas de registros deben usar paginación. |
| RNF-REN-04 | Las consultas que requieren datos de relaciones deben usar eager loading (sin queries N+1). |
| RNF-REN-05 | Los datos de alta frecuencia de lectura (settings, semestre activo) deben estar cacheados. |
| RNF-REN-06 | Las operaciones de envío de email/notificaciones deben procesarse en cola asíncrona. |
| RNF-REN-07 | Los archivos de tamaño > 5MB subidos por alumnos deben ser rechazados con mensaje de error claro. |

### Disponibilidad

| ID | Requerimiento |
|---|---|
| RNF-DIS-01 | El sistema debe tener una disponibilidad del 99% en horario académico (lunes a sábado, 7am–10pm). |
| RNF-DIS-02 | El sistema debe ejecutar `php artisan optimize` en cada deploy para cachear rutas y configuración. |
| RNF-DIS-03 | El worker de colas debe reiniciarse automáticamente si se detiene (via Supervisor en producción). |

### Usabilidad

| ID | Requerimiento |
|---|---|
| RNF-USA-01 | La interfaz debe ser completamente funcional en dispositivos móviles (responsive). |
| RNF-USA-02 | Los mensajes de error y confirmación deben mostrarse en español claro y sin tecnicismos. |
| RNF-USA-03 | Las operaciones destructivas (eliminar, retirar) deben requerir confirmación explícita. |
| RNF-USA-04 | Los formularios deben mostrar errores de validación junto al campo correspondiente. |
| RNF-USA-05 | El sistema debe mostrar mensajes de éxito o error después de cada operación mediante flash messages. |
| RNF-USA-06 | El tiempo de respuesta perceptible para el usuario debe ser < 300ms para operaciones AJAX comunes. |

### Mantenibilidad

| ID | Requerimiento |
|---|---|
| RNF-MAN-01 | El código debe seguir las convenciones PSR-12 y ser formateado con Laravel Pint. |
| RNF-MAN-02 | Todo archivo CSS personalizado debe vivir exclusivamente en `resources/css/app.css`. |
| RNF-MAN-03 | Todo JavaScript debe vivir en `resources/js/app.js` o en bloques `@section('scripts')` mínimos y específicos. |
| RNF-MAN-04 | No deben existir queries SQL directas fuera de los modelos Eloquent. |
| RNF-MAN-05 | Las carpetas de controladores, vistas y modelos deben seguir la estructura definida en `06-estructura-del-proyecto.md`. |
| RNF-MAN-06 | Cada funcionalidad nueva debe incluir al menos un test de feature en `tests/Feature/`. |

### Escalabilidad

| ID | Requerimiento |
|---|---|
| RNF-ESC-01 | La capa de cache debe poder migrarse de `database` a `redis` sin cambios en la lógica de negocio. |
| RNF-ESC-02 | El almacenamiento de archivos debe poder migrarse de disco local a S3 sin cambios en la lógica de negocio (via `Storage` facade). |
| RNF-ESC-03 | Las tablas de alta escritura (submissions, attempt_answers, notifications) deben tener los índices definidos antes de recibir carga. |

---

## 3. Restricciones Técnicas

| ID | Restricción |
|---|---|
| RT-01 | PHP 8.2 o superior. |
| RT-02 | Laravel 12.x. |
| RT-03 | MySQL 8.0 como motor de base de datos de producción. |
| RT-04 | No usar frameworks SPA (Vue, React, Angular, Inertia). |
| RT-05 | No crear archivos CSS adicionales fuera de `app.css`. |
| RT-06 | El tamaño máximo de archivos subidos es 10MB por archivo. |
| RT-07 | El número máximo de semanas por curso es 16. |

---

## 4. Casos de Uso Prioritarios

Ordenados por impacto en el usuario:

| Prioridad | Caso de Uso | Módulo |
|---|---|---|
| 1 | Alumno entrega una tarea | Tareas |
| 2 | Docente califica una entrega | Tareas |
| 3 | Alumno ve sus calificaciones | Calificaciones |
| 4 | Docente sube material a una semana | Aula Virtual |
| 5 | Alumno rinde una evaluación en línea | Evaluaciones |
| 6 | Admin crea semestre y asigna cursos | Semestres / Cursos |
| 7 | Admin matricula masivamente alumnos | Matrículas |
| 8 | Docente publica anuncio del curso | Anuncios |
| 9 | Alumno participa en foro del curso | Foro |
| 10 | Admin genera reporte trimestral | Reportes |
