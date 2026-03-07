# 06 — Estructura del Proyecto

## 1. Árbol de Directorios Completo

```
ps-edu/
│
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── UserController.php
│   │   │   │   ├── CourseController.php
│   │   │   │   ├── AnnouncementController.php
│   │   │   │   ├── EnrollmentController.php
│   │   │   │   ├── ReportController.php          ← pendiente
│   │   │   │   ├── SemesterController.php         ← pendiente
│   │   │   │   └── SettingsController.php
│   │   │   │
│   │   │   ├── Alumno/
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── CourseController.php
│   │   │   │   ├── TaskController.php             ← pendiente (entregas)
│   │   │   │   ├── EvaluationController.php       ← pendiente
│   │   │   │   ├── GradeController.php            ← pendiente
│   │   │   │   └── ForumController.php            ← pendiente
│   │   │   │
│   │   │   ├── Auth/
│   │   │   │   ├── LoginController.php
│   │   │   │   └── PasswordResetController.php
│   │   │   │
│   │   │   ├── Docente/
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── CourseController.php
│   │   │   │   ├── WeekController.php
│   │   │   │   ├── MaterialController.php
│   │   │   │   ├── TaskController.php
│   │   │   │   ├── SubmissionController.php       ← pendiente (calificar)
│   │   │   │   ├── EvaluationController.php       ← pendiente
│   │   │   │   ├── GradeController.php            ← pendiente
│   │   │   │   ├── StudentController.php
│   │   │   │   ├── ForumController.php            ← pendiente
│   │   │   │   ├── EscalafonController.php
│   │   │   │   └── SupportController.php
│   │   │   │
│   │   │   ├── ProfileController.php
│   │   │   ├── NotificationController.php         ← pendiente
│   │   │   └── Controller.php
│   │   │
│   │   └── Middleware/
│   │       └── RoleMiddleware.php
│   │
│   ├── Models/
│   │   ├── User.php
│   │   ├── Semester.php                           ← pendiente
│   │   ├── Course.php
│   │   ├── Week.php
│   │   ├── Material.php
│   │   ├── Task.php
│   │   ├── Submission.php                         ← pendiente
│   │   ├── Enrollment.php
│   │   ├── Evaluation.php                         ← pendiente
│   │   ├── Question.php                           ← pendiente
│   │   ├── QuestionOption.php                     ← pendiente
│   │   ├── EvaluationAttempt.php                  ← pendiente
│   │   ├── AttemptAnswer.php                      ← pendiente
│   │   ├── GradeItem.php                          ← pendiente
│   │   ├── Grade.php                              ← pendiente
│   │   ├── ForumTopic.php                         ← pendiente
│   │   ├── ForumReply.php                         ← pendiente
│   │   ├── Announcement.php
│   │   ├── DocenteProfile.php
│   │   ├── AlumnoProfile.php
│   │   └── Setting.php
│   │
│   ├── Notifications/
│   │   ├── NewTaskPublished.php                   ← pendiente
│   │   ├── TaskGraded.php                         ← pendiente
│   │   ├── NewEvaluationAvailable.php             ← pendiente
│   │   └── NewAnnouncementPublished.php           ← pendiente
│   │
│   ├── Policies/
│   │   ├── CoursePolicy.php
│   │   ├── SubmissionPolicy.php                   ← pendiente
│   │   └── ForumTopicPolicy.php                   ← pendiente
│   │
│   └── Providers/
│       └── AppServiceProvider.php
│
├── bootstrap/
│   ├── app.php
│   └── cache/
│
├── config/
│   ├── app.php
│   ├── auth.php
│   ├── cache.php
│   ├── database.php
│   ├── filesystems.php
│   ├── logging.php
│   ├── mail.php
│   ├── queue.php
│   ├── services.php
│   └── session.php
│
├── contexto/                                      ← documentación del sistema
│   ├── README.md
│   ├── 01-vision-y-alcance.md
│   ├── 02-arquitectura-tecnica.md
│   ├── 03-modulos-del-sistema.md
│   ├── 04-base-de-datos.md
│   ├── 05-roles-y-permisos.md
│   ├── 06-estructura-del-proyecto.md
│   ├── 07-frontend-y-estilos.md
│   ├── 08-rendimiento-y-escalabilidad.md
│   └── 09-requerimientos.md
│
├── database/
│   ├── factories/
│   │   └── UserFactory.php
│   ├── migrations/
│   │   ├── 0001_01_01_000000_create_users_table.php
│   │   ├── 0001_01_01_000001_create_cache_table.php
│   │   ├── 0001_01_01_000002_create_jobs_table.php
│   │   ├── 2025_03_05_000001_create_docente_profiles_table.php
│   │   ├── 2025_03_05_000002_create_alumno_profiles_table.php
│   │   ├── 2025_03_05_000003_create_courses_table.php
│   │   ├── 2025_03_05_000004_create_weeks_table.php
│   │   ├── 2025_03_05_000005_create_materials_table.php
│   │   ├── 2025_03_05_000006_create_tasks_table.php
│   │   ├── 2025_03_05_000007_create_enrollments_table.php
│   │   ├── 2025_03_05_000008_create_announcements_table.php
│   │   └── 2025_03_05_000009_create_settings_table.php
│   │   ── (migraciones pendientes para nuevos módulos)
│   └── seeders/
│       └── DatabaseSeeder.php
│
├── public/
│   ├── index.php
│   ├── .htaccess
│   ├── favicon.ico
│   └── storage/                                  ← symlink a storage/app/public
│
├── resources/
│   ├── css/
│   │   └── app.css                               ← Tailwind v4 + theme tokens
│   │
│   ├── js/
│   │   ├── app.js                                ← Alpine.js bootstrap
│   │   └── bootstrap.js                          ← Axios config
│   │
│   └── views/
│       ├── layouts/
│       │   ├── app.blade.php                     ← layout autenticado (sidebar)
│       │   └── auth.blade.php                    ← layout de login/reset
│       │
│       ├── components/
│       │   ├── sidebar-link.blade.php
│       │   ├── alert.blade.php                   ← pendiente (ya existe inline)
│       │   ├── badge.blade.php                   ← pendiente
│       │   ├── modal.blade.php                   ← pendiente
│       │   ├── empty-state.blade.php             ← pendiente
│       │   └── pagination.blade.php              ← pendiente (o usar el nativo)
│       │
│       ├── auth/
│       │   ├── login.blade.php
│       │   ├── forgot-password.blade.php
│       │   └── reset-password.blade.php
│       │
│       ├── admin/
│       │   ├── dashboard.blade.php
│       │   ├── users/
│       │   │   ├── index.blade.php
│       │   │   ├── create.blade.php
│       │   │   └── edit.blade.php
│       │   ├── courses/
│       │   │   ├── index.blade.php
│       │   │   ├── create.blade.php
│       │   │   └── edit.blade.php
│       │   ├── announcements/
│       │   │   ├── index.blade.php
│       │   │   ├── create.blade.php
│       │   │   └── edit.blade.php
│       │   ├── semesters/                        ← pendiente
│       │   │   ├── index.blade.php
│       │   │   └── create.blade.php
│       │   ├── enrollments/
│       │   │   └── index.blade.php
│       │   ├── reports/                          ← pendiente
│       │   │   └── index.blade.php
│       │   └── settings/
│       │       └── index.blade.php
│       │
│       ├── docente/
│       │   ├── dashboard.blade.php
│       │   ├── cursos/
│       │   │   ├── index.blade.php
│       │   │   └── show.blade.php                ← panel principal del aula
│       │   ├── semanas/                          ← vistas inline in show.blade actualmente
│       │   ├── materiales/
│       │   ├── tareas/
│       │   ├── entregas/                         ← pendiente
│       │   │   └── index.blade.php               ← listado por tarea
│       │   ├── evaluaciones/                     ← pendiente
│       │   │   ├── create.blade.php
│       │   │   └── show.blade.php
│       │   ├── notas/                            ← pendiente
│       │   │   └── index.blade.php
│       │   ├── foro/                             ← pendiente
│       │   │   ├── index.blade.php
│       │   │   └── show.blade.php
│       │   ├── intranet.blade.php
│       │   ├── escalafon.blade.php
│       │   └── soporte.blade.php
│       │
│       ├── alumno/
│       │   ├── dashboard.blade.php
│       │   ├── mis-cursos/
│       │   │   └── show.blade.php
│       │   ├── tareas/                           ← pendiente
│       │   │   └── show.blade.php
│       │   ├── evaluaciones/                     ← pendiente
│       │   │   └── show.blade.php               ← rendir evaluación
│       │   ├── notas/                            ← pendiente
│       │   │   └── index.blade.php
│       │   ├── foro/                             ← pendiente
│       │   │   ├── index.blade.php
│       │   │   └── show.blade.php
│       │   └── intranet.blade.php
│       │
│       └── profile/
│           └── edit.blade.php
│
├── routes/
│   ├── web.php
│   └── console.php
│
├── storage/
│   ├── app/
│   │   └── public/
│   │       ├── avatars/
│   │       ├── materials/{course_id}/
│   │       ├── tasks/{course_id}/
│   │       └── submissions/{task_id}/            ← pendiente
│   ├── framework/
│   └── logs/
│
└── tests/
    ├── Feature/
    │   └── ExampleTest.php
    └── Unit/
        └── ExampleTest.php
```

---

## 2. Convenciones de Nomenclatura

### Controladores
- Singular, sufijo `Controller`: `CourseController`, `WeekController`
- Organizados por namespace de rol: `Admin\`, `Docente\`, `Alumno\`
- Un controlador = un recurso (no mezclar responsabilidades)

### Modelos
- Singular, PascalCase: `Course`, `ForumTopic`, `GradeItem`
- Sin sufijo extra

### Vistas
- Organizadas por rol luego por recurso: `docente/cursos/show.blade.php`
- Nombre de la acción en el nombre del archivo: `index`, `create`, `edit`, `show`
- Snake_case para nombres de archivo

### Rutas
- Prefijo de rol: `/admin/`, `/docente/`, `/alumno/`
- Rutas nombradas con punto como separador: `admin.users.index`, `docente.cursos.show`
- Verbos en español en la URL (para legibilidad del usuario): `/docente/cursos`, `/alumno/mis-cursos`
- Nombres de rutas en inglés con notación de punto: `docente.courses.show`

### Migraciones
- Formato `YYYY_MM_DD_HHMMSS_create_{tabla}_table.php`
- Las migraciones nuevas agrupadas por fecha (no intercalar con las existentes)

### Storage
- `avatars/{filename}` — fotos de perfil
- `materials/{course_id}/{filename}` — materiales de cursos
- `tasks/{course_id}/{filename}` — archivos adjuntos de tareas
- `submissions/{task_id}/{user_id}_{filename}` — entregas de alumnos

---

## 3. Reglas de Organización del Código

### Lo que va en el Modelo
- Relaciones (`hasMany`, `belongsTo`, etc.)
- Scopes de consulta (`scopeActive`, `scopePublished`)
- Accessors/Mutators (`getStatusLabelAttribute`, `setPasswordAttribute`)
- Casts (`'due_date' => 'datetime'`)
- Constantes de valores de enum (`STATUS_ACTIVE = 'active'`)
- Métodos de verificación simples (`isExpired()`, `isGraded()`)

### Lo que va en el Controlador
- Recibir request, validar, llamar al modelo, retornar respuesta
- Sin lógica de negocio compleja
- Sin consultas SQL directas (siempre a través del modelo)
- Sin HTML

### Lo que va en la Vista (Blade)
- Presentación y estructura HTML
- Lógica de presentación mínima (`@if`, `@foreach`)
- Sin lógica de negocio
- Sin queries directas al modelo (los datos llegan del controlador)

### Lo que va en un Componente Blade
- HTML reutilizable con parámetros
- Ejemplos: `<x-badge>`, `<x-modal>`, `<x-alert>`, `<x-empty-state>`

---

## 4. Archivos que NO deben crearse

| Archivo | Razón |
|---|---|
| CSS personalizados por página | Todo en `app.css` con clases Tailwind |
| JS por vista | Todo en `app.js` con Alpine.js o pequeños `<script>` inline |
| Helpers globales sueltos | Usar métodos del modelo o accessors |
| Múltiples archivos de rutas | Solo `web.php` y `console.php` |
| Views duplicadas | Usar `@include` o componentes para reutilizar |
