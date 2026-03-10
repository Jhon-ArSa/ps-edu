Juan — Semestres + Entregas de Tareas
Archivos que crea (solo suyos, cero conflicto)
Migrations:
  create_semesters_table
  add_semester_id_to_courses_table
  create_submissions_table

Models:
  app/Models/Semester.php
  app/Models/Submission.php

Controllers:
  app/Http/Controllers/Admin/SemesterController.php
  app/Http/Controllers/Docente/SubmissionController.php
  app/Http/Controllers/Alumno/SubmissionController.php

Views (carpetas nuevas que él crea):
  resources/views/admin/semesters/index.blade.php
  resources/views/admin/semesters/create.blade.php
  resources/views/admin/semesters/edit.blade.php
  resources/views/docente/submissions/index.blade.php   ← ver entregas por tarea
  resources/views/alumno/submissions/create.blade.php   ← form de entrega
  resources/views/alumno/submissions/show.blade.php     ← ver entrega + nota

Partials nuevos (no toca archivos de nadie):
  resources/views/docente/courses/_task-submission-btn.blade.php
  resources/views/alumno/courses/_task-submission-form.blade.php
Archivos compartidos que toca (coordinación)
routes/web.php                           ← solo su bloque marcado
admin/courses/create.blade.php           ← agrega <select> semestre
admin/courses/edit.blade.php             ← agrega <select> semestre
Admin/CourseController.php               ← agrega semester_id en store()/update()
docente/courses/show.blade.php           ← agrega UNA línea: @include('...._task-submission-btn')
alumno/courses/show.blade.php            ← agrega UNA línea: @include('...._task-submission-form')
________________________________________
Jhon — Evaluaciones en Línea + Foro de Discusión
Archivos que crea (solo suyos, cero conflicto)
Migrations:
  create_evaluations_table
  create_questions_table
  create_question_options_table
  create_evaluation_attempts_table
  create_attempt_answers_table
  create_forum_topics_table
  create_forum_replies_table

Models:
  Evaluation.php  Question.php  QuestionOption.php
  EvaluationAttempt.php  AttemptAnswer.php
  ForumTopic.php  ForumReply.php

Controllers:
  Docente/EvaluationController.php
  Alumno/EvaluationController.php
  Docente/ForumController.php
  Alumno/ForumController.php

Views (carpetas nuevas que él crea):
  docente/evaluations/create.blade.php
  docente/evaluations/show.blade.php    ← resultados de alumnos
  alumno/evaluations/show.blade.php     ← rendir evaluación (con cronómetro Alpine)
  docente/forum/index.blade.php
  docente/forum/show.blade.php
  alumno/forum/index.blade.php
  alumno/forum/show.blade.php

Partials nuevos (no toca archivos de nadie):
  docente/courses/_week-evaluations.blade.php
  alumno/courses/_week-evaluations.blade.php
Archivos compartidos que toca (coordinación)
routes/web.php                           ← solo su bloque marcado
docente/courses/show.blade.php           ← agrega UNA línea: @include('...._week-evaluations')
alumno/courses/show.blade.php            ← agrega UNA línea: @include('...._week-evaluations')
________________________________________
Zair — Calificaciones + Notificaciones + Reportes
Archivos que crea (solo suyos, cero conflicto)
Migrations:
  create_grade_items_table
  create_grades_table
  (php artisan notifications:table → notifications)

Models:
  GradeItem.php  Grade.php

Notifications (carpeta nueva):
  app/Notifications/NewTaskPublished.php
  app/Notifications/TaskGraded.php
  app/Notifications/NewEvaluationAvailable.php
  app/Notifications/NewAnnouncementPublished.php

Controllers:
  Docente/GradeController.php
  Alumno/GradeController.php
  NotificationController.php             ← marcar leídas, listar
  Admin/ReportController.php

Views (carpetas nuevas que él crea):
  docente/grades/index.blade.php         ← libreta de notas
  alumno/grades/index.blade.php          ← mis notas
  admin/reports/index.blade.php          ← reportes y estadísticas
Archivos compartidos que toca (coordinación)
routes/web.php                           ← solo su bloque marcado
layouts/app.blade.php                    ← badge notificaciones + links sidebar
                                           (Notas, Foro, Reportes)
Zair también recibe de Juan y Jhon la lista de links de sidebar que necesitan. Él los agrega todos juntos en el layout para no duplicar toques.
________________________________________
Reglas de coordinación (para no pisarse)
1. routes/web.php — cada uno trabaja en su bloque
<?php
// ============================================
// JUAN — Semestres + Entregas
// ============================================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::resource('semesters', SemesterController::class);
    // ...
});

// ============================================
// JHON — Evaluaciones + Foro
// ============================================
// ...

// ============================================
// ZAIR — Calificaciones + Notificaciones + Reportes
// ============================================
// ...

2. docente/courses/show.blade.php y alumno/courses/show.blade.php
Nadie escribe HTML directamente en estos archivos. Cada uno solo agrega su @include al final de la sección que le corresponde:
{{-- Juan agrega esto en la sección de tareas --}}
@include('docente.courses._task-submission-btn', ['task' => $task])

{{-- Jhon agrega esto en la sección de semanas --}}
@include('docente.courses._week-evaluations', ['week' => $week])

3. layouts/app.blade.php — solo Zair lo toca
Juan y Jhon le dicen a Zair qué links necesitan en el sidebar y Zair los agrega todos juntos.
________________________________________
Orden sugerido (dependencias)
Sprint paralelo (todos al mismo tiempo):
  Juan:  Semestres → Entregas
  Jhon:  Evaluaciones → Foro
  Zair:  Notificaciones → Reportes → Calificaciones (base manual)

Después de que Juan mergee:
  Zair:  Integra scores de submissions en la libreta de notas

Después de que Jhon mergee:
  Zair:  Integra scores de evaluations en la libreta de notas
________________________________________
Resumen visual
#	Feature	Dueño	Conflicto con otros
Semestres académicos	Juan	Solo toca CourseController + forms de admin	
Entregas de tareas	Juan	Solo agrega @include en show.blade	
Evaluaciones en línea	Jhon	Solo agrega @include en show.blade	
Foro de discusión	Jhon	Carpetas nuevas, sin cruce	
Libreta de calificaciones	Zair	Depende de Juan+Jhon al final	
Notificaciones	Zair	Solo layout (que él controla)	
Reportes	Zair	Carpetas nuevas, sin cruce	

