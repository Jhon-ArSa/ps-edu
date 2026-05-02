# 📋 PLAN DE ACCIÓN — Mejoras Priorizadas PS-EDU

**Fecha:** 30 de abril de 2026  
**Estado del Sistema:** 8.0/10 — Funcionalmente completo, necesita mejoras

---

## 🎯 OBJETIVO

Llevar el sistema PS-EDU de **8.0/10 a 9.5/10** implementando mejoras críticas en:
- Testing y estabilidad
- Experiencia de usuario
- Funcionalidades administrativas
- Rendimiento y escalabilidad

---

## 📊 PRIORIZACIÓN (Matriz Impacto vs Esfuerzo)

```
ALTO IMPACTO, BAJO ESFUERZO (Hacer primero) ⭐⭐⭐
├─ Matriculación masiva (Excel)
├─ Exportación de reportes (Excel/PDF)
└─ Carga masiva de usuarios

ALTO IMPACTO, MEDIO ESFUERZO (Hacer después) ⭐⭐
├─ Calendario académico
├─ Búsqueda global
├─ Recordatorios por email
└─ Historial de auditoría

ALTO IMPACTO, ALTO ESFUERZO (Planificar bien) ⭐
├─ Cobertura de tests (60% → 80%)
├─ Notificaciones en tiempo real
└─ Estadísticas avanzadas

BAJO IMPACTO (Opcional)
├─ Modo oscuro
├─ 2FA
├─ PWA
└─ Chat en vivo
```

---

## 🚀 FASE 1: QUICK WINS (1 semana)

### ✅ Tareas Inmediatas

#### 1. Matriculación Masiva (1 día)
**Problema:** Matricular 200 alumnos uno por uno toma horas.

**Solución:**
```php
// Ruta: POST /admin/enrollments/import
// Archivo Excel: codigo_alumno | codigo_curso

public function import(Request $request)
{
    $file = $request->file('excel');
    $rows = Excel::toArray(new EnrollmentsImport, $file)[0];
    
    $created = 0;
    foreach ($rows as $row) {
        $student = User::where('alumno_profile.code', $row[0])->first();
        $course = Course::where('code', $row[1])->first();
        
        if ($student && $course) {
            Enrollment::firstOrCreate([
                'user_id' => $student->id,
                'course_id' => $course->id,
            ], ['status' => 'active']);
            $created++;
        }
    }
    
    return back()->with('success', "$created matrículas creadas.");
}
```

**Entregable:**
- Vista: `resources/views/admin/enrollments/import.blade.php`
- Controlador: `Admin\EnrollmentController@import`
- Validación de datos
- Reporte de errores (alumnos/cursos no encontrados)

---

#### 2. Exportación de Libreta de Notas (1 día)
**Problema:** Los docentes necesitan imprimir las notas.

**Solución:**
```bash
composer require maatwebsite/excel
composer require barryvdh/laravel-dompdf
```

```php
// Exportar a Excel
public function exportExcel(Course $course)
{
    return Excel::download(
        new GradesExport($course),
        "notas-{$course->code}-" . now()->format('Y-m-d') . ".xlsx"
    );
}

// Exportar a PDF
public function exportPdf(Course $course)
{
    $grades = $course->grades()->with('student')->get();
    $pdf = PDF::loadView('docente.grades.pdf', compact('course', 'grades'));
    return $pdf->download("notas-{$course->code}.pdf");
}
```

**Entregable:**
- Botones "Exportar Excel" y "Exportar PDF" en libreta de notas
- Formato profesional con logo institucional
- Incluir: código alumno, nombre, notas por ítem, promedio

---

#### 3. Carga Masiva de Usuarios (1 día)
**Problema:** Registrar 200 alumnos uno por uno es tedioso.

**Solución:**
```php
// Formato Excel: nombre | email | dni | rol | programa | codigo

public function importUsers(Request $request)
{
    $file = $request->file('excel');
    $rows = Excel::toArray(new UsersImport, $file)[0];
    
    $created = 0;
    $errors = [];
    
    foreach ($rows as $index => $row) {
        try {
            $user = User::create([
                'name' => $row[0],
                'email' => $row[1],
                'dni' => $row[2],
                'role' => $row[3],
                'password' => bcrypt('password123'), // Temporal
            ]);
            
            if ($user->role === 'alumno') {
                AlumnoProfile::create([
                    'user_id' => $user->id,
                    'code' => $row[5],
                    'program_id' => Program::where('name', $row[4])->first()?->id,
                ]);
            }
            
            // Enviar email con credenciales
            $user->notify(new WelcomeNotification('password123'));
            
            $created++;
        } catch (\Exception $e) {
            $errors[] = "Fila " . ($index + 2) . ": " . $e->getMessage();
        }
    }
    
    return back()->with('success', "$created usuarios creados.")
                 ->with('errors', $errors);
}
```

**Entregable:**
- Vista: `resources/views/admin/users/import.blade.php`
- Plantilla Excel de ejemplo
- Validación: email único, DNI único, código único
- Envío de credenciales por email

---

#### 4. Búsqueda Global Básica (2 días)
**Problema:** No hay forma rápida de encontrar cursos, alumnos, tareas.

**Solución (versión simple con SQL):**
```php
// Ruta: GET /search?q=...

public function search(Request $request)
{
    $query = $request->input('q');
    $user = auth()->user();
    
    $results = [
        'courses' => Course::where('name', 'LIKE', "%{$query}%")
                          ->limit(5)->get(),
        'students' => User::where('role', 'alumno')
                          ->where('name', 'LIKE', "%{$query}%")
                          ->limit(5)->get(),
        'tasks' => Task::where('title', 'LIKE', "%{$query}%")
                       ->limit(5)->get(),
    ];
    
    return response()->json($results);
}
```

**Entregable:**
- Barra de búsqueda en header (Ctrl+K para abrir)
- Resultados en modal con Alpine.js
- Búsqueda con AJAX (sin recargar)
- Resultados agrupados por tipo

---

## 🔧 FASE 2: ESTABILIZACIÓN (2 semanas)

### ✅ Tareas Críticas

#### 5. Tests para Evaluaciones (3 días)
**Objetivo:** Cobertura 0% → 80%

**Tests a implementar:**
```php
// tests/Feature/Evaluation/EvaluationTest.php

✅ test_docente_can_create_evaluation()
✅ test_docente_can_add_questions()
✅ test_docente_cannot_edit_published_evaluation()
✅ test_alumno_can_start_evaluation()
✅ test_alumno_cannot_start_if_max_attempts_reached()
✅ test_evaluation_auto_submits_when_time_expires()
✅ test_multiple_choice_questions_are_auto_graded()
✅ test_short_answer_questions_require_manual_grading()
✅ test_alumno_can_see_results_if_enabled()
✅ test_alumno_cannot_see_results_if_disabled()
✅ test_docente_can_manually_grade_short_answers()
✅ test_evaluation_respects_open_and_close_dates()
✅ test_alumno_cannot_access_evaluation_from_other_course()
✅ test_docente_can_delete_draft_evaluation()
✅ test_docente_cannot_delete_published_evaluation()
```

**Total: 15 tests**

---

#### 6. Tests para Calificaciones (2 días)
**Objetivo:** Cobertura 0% → 75%

**Tests a implementar:**
```php
// tests/Feature/Grades/GradesTest.php

✅ test_grades_from_tasks_are_reflected_automatically()
✅ test_grades_from_evaluations_are_reflected_automatically()
✅ test_docente_can_add_manual_grade()
✅ test_average_is_calculated_correctly()
✅ test_weighted_average_is_calculated_correctly()
✅ test_alumno_can_see_own_grades()
✅ test_alumno_cannot_see_other_student_grades()
✅ test_docente_can_export_grades_to_excel()
✅ test_docente_can_export_grades_to_pdf()
✅ test_grades_are_filtered_by_course()
```

**Total: 10 tests**

---

#### 7. Tests para Foro (2 días)
**Objetivo:** Cobertura 0% → 70%

**Tests a implementar:**
```php
// tests/Feature/Forum/ForumTest.php

✅ test_alumno_can_create_topic_in_enrolled_course()
✅ test_alumno_cannot_create_topic_in_unenrolled_course()
✅ test_docente_can_create_topic_in_own_course()
✅ test_users_can_reply_to_topic()
✅ test_docente_can_pin_topic()
✅ test_docente_can_close_topic()
✅ test_users_cannot_reply_to_closed_topic()
✅ test_users_can_delete_own_reply()
```

**Total: 8 tests**

---

#### 8. Calendario Académico (3 días)
**Objetivo:** Vista unificada de fechas importantes.

**Implementación:**
```bash
composer require spatie/laravel-calendar
npm install @fullcalendar/core @fullcalendar/daygrid
```

**Migración:**
```php
Schema::create('calendar_events', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->text('description')->nullable();
    $table->enum('type', ['task', 'evaluation', 'holiday', 'exam', 'event']);
    $table->dateTime('start_at');
    $table->dateTime('end_at')->nullable();
    $table->foreignId('course_id')->nullable()->constrained()->cascadeOnDelete();
    $table->foreignId('created_by')->nullable()->constrained('users');
    $table->string('color', 7)->default('#3b82f6');
    $table->boolean('all_day')->default(false);
    $table->timestamps();
});
```

**Eventos automáticos:**
- Fecha límite de tareas → Evento rojo
- Fecha de evaluaciones → Evento naranja
- Inicio/fin de semestre → Evento azul

**Eventos manuales:**
- Feriados
- Exámenes presenciales
- Eventos institucionales

**Entregable:**
- Vista: `resources/views/calendario/index.blade.php`
- Calendario mensual con FullCalendar
- Filtros: por curso, por tipo de evento
- CRUD de eventos manuales (solo admin)

---

## 📈 FASE 3: OPTIMIZACIÓN (2 semanas)

### ✅ Tareas Importantes

#### 9. Recordatorios por Email (2 días)
**Objetivo:** Reducir entregas tardías.

**Implementación:**
```php
// app/Console/Commands/SendTaskReminders.php

public function handle()
{
    $tomorrow = now()->addDay()->startOfDay();
    
    $tasks = Task::where('due_date', '>=', $tomorrow)
                 ->where('due_date', '<', $tomorrow->copy()->endOfDay())
                 ->where('status', 'active')
                 ->get();
    
    foreach ($tasks as $task) {
        $students = $task->week->course->students;
        
        foreach ($students as $student) {
            $hasSubmitted = Submission::where('task_id', $task->id)
                                      ->where('user_id', $student->id)
                                      ->exists();
            
            if (!$hasSubmitted) {
                $student->notify(new TaskDueReminderNotification($task));
            }
        }
    }
    
    $this->info('Recordatorios enviados.');
}
```

**Programar en `app/Console/Kernel.php`:**
```php
$schedule->command('tasks:send-reminders')->dailyAt('08:00');
```

**Entregable:**
- Comando: `php artisan tasks:send-reminders`
- Notificación: `TaskDueReminderNotification`
- Email template profesional
- Configuración: alumno puede desactivar emails

---

#### 10. Historial de Auditoría (3 días)
**Objetivo:** Registrar cambios críticos.

**Implementación:**
```bash
composer require owen-it/laravel-auditing
```

**Modelos a auditar:**
- User (creación, edición, activación/desactivación)
- Course (creación, edición, asignación docente)
- Enrollment (matriculación, retiro)
- Submission (calificación)
- Evaluation (publicación, cierre)

**Configuración:**
```php
// En cada modelo
use OwenIt\Auditing\Contracts\Auditable;

class User extends Authenticatable implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    
    protected $auditInclude = [
        'name', 'email', 'role', 'status',
    ];
}
```

**Entregable:**
- Vista: `resources/views/admin/audit/index.blade.php`
- Filtros: por usuario, por modelo, por fecha
- Mostrar: quién, qué, cuándo, antes/después

---

#### 11. Estadísticas Avanzadas (4 días)
**Objetivo:** Dashboard con gráficos útiles.

**Implementación:**
```bash
npm install chart.js
```

**Gráficos a implementar:**
1. **Entregas por semana** (línea)
2. **Distribución de notas** (barras)
3. **Alumnos en riesgo** (tabla: promedio < 11)
4. **Docentes más activos** (barras: materiales + tareas)
5. **Cursos con más actividad** (barras: entregas + participación foro)
6. **Comparación entre semestres** (línea)

**Entregable:**
- Vista: `resources/views/admin/dashboard.blade.php` (mejorada)
- 6 gráficos interactivos con Chart.js
- Filtros: por semestre, por programa
- Exportar gráficos a PNG

---

## 🌟 FASE 4: MEJORAS AVANZADAS (3 semanas)

### ✅ Tareas Opcionales

#### 12. Notificaciones en Tiempo Real (5 días)
**Objetivo:** Notificaciones sin refrescar.

**Implementación:**
```bash
composer require laravel/reverb
php artisan reverb:install
```

**Configuración:**
```php
// config/broadcasting.php
'reverb' => [
    'driver' => 'reverb',
    'key' => env('REVERB_APP_KEY'),
    'secret' => env('REVERB_APP_SECRET'),
    'app_id' => env('REVERB_APP_ID'),
],
```

**Frontend:**
```javascript
// resources/js/app.js
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
});

// Escuchar notificaciones
Echo.private(`App.Models.User.${userId}`)
    .notification((notification) => {
        // Mostrar toast
        showToast(notification.message);
        // Actualizar badge
        updateNotificationBadge();
    });
```

**Entregable:**
- WebSocket server con Laravel Reverb
- Notificaciones en tiempo real
- Toast notifications (alertas flotantes)
- Badge actualizado automáticamente

---

#### 13. Modo Oscuro (2 días)
**Objetivo:** Tema oscuro para reducir fatiga visual.

**Implementación:**
```javascript
// resources/js/app.js
Alpine.store('theme', {
    dark: localStorage.getItem('theme') === 'dark',
    
    toggle() {
        this.dark = !this.dark;
        localStorage.setItem('theme', this.dark ? 'dark' : 'light');
        document.documentElement.classList.toggle('dark', this.dark);
    },
    
    init() {
        document.documentElement.classList.toggle('dark', this.dark);
    }
});
```

**CSS:**
```css
/* Agregar clases dark: en Tailwind */
.bg-white { @apply dark:bg-gray-900; }
.text-gray-900 { @apply dark:text-gray-100; }
```

**Entregable:**
- Toggle en header (icono sol/luna)
- Preferencia guardada en localStorage
- Todos los componentes adaptados
- Transición suave entre temas

---

#### 14. PWA (Progressive Web App) (3 días)
**Objetivo:** App instalable en móviles.

**Implementación:**
```bash
composer require silviolleite/laravel-pwa
php artisan vendor:publish --provider="LaravelPWA\Providers\LaravelPWAServiceProvider"
```

**Configuración:**
```php
// config/pwa.php
'name' => 'PS-EDU',
'short_name' => 'FAEDU',
'start_url' => '/',
'background_color' => '#ffffff',
'theme_color' => '#2563eb',
'display' => 'standalone',
'orientation' => 'portrait',
```

**Entregable:**
- Manifest.json configurado
- Service Worker para caché offline
- Instalable en iOS/Android
- Notificaciones push (opcional)

---

## 📅 CRONOGRAMA COMPLETO

| Fase | Duración | Tareas | Prioridad |
|------|----------|--------|-----------|
| **Fase 1: Quick Wins** | 1 semana | Matriculación masiva, Exportación reportes, Carga usuarios, Búsqueda global | 🔴 Alta |
| **Fase 2: Estabilización** | 2 semanas | Tests (evaluaciones, calificaciones, foro), Calendario académico | 🔴 Alta |
| **Fase 3: Optimización** | 2 semanas | Recordatorios email, Auditoría, Estadísticas avanzadas | 🟡 Media |
| **Fase 4: Avanzadas** | 3 semanas | Notificaciones tiempo real, Modo oscuro, PWA | 🟢 Baja |

**Total: 8 semanas (2 meses)**

---

## ✅ CHECKLIST DE IMPLEMENTACIÓN

### Antes de Empezar
- [ ] Crear branch `feature/mejoras-sistema`
- [ ] Configurar entorno de desarrollo
- [ ] Revisar documentación técnica
- [ ] Backup de base de datos

### Durante Implementación
- [ ] Escribir tests antes de implementar (TDD)
- [ ] Documentar cambios en CHANGELOG.md
- [ ] Actualizar documentación técnica
- [ ] Code review antes de merge

### Después de Implementar
- [ ] Ejecutar suite completa de tests
- [ ] Verificar cobertura de tests (>80%)
- [ ] Probar en staging antes de producción
- [ ] Actualizar README.md

---

## 🎯 MÉTRICAS DE ÉXITO

| Métrica | Antes | Después | Objetivo |
|---------|-------|---------|----------|
| Cobertura de tests | 60% | 80%+ | ✅ |
| Tiempo de matriculación (200 alumnos) | 2 horas | 5 minutos | ✅ |
| Tiempo de carga de usuarios (200) | 3 horas | 10 minutos | ✅ |
| Satisfacción docentes | N/A | 4.5/5 | ✅ |
| Satisfacción alumnos | N/A | 4.0/5 | ✅ |
| Entregas tardías | N/A | -30% | ✅ |

---

## 🚨 RIESGOS Y MITIGACIÓN

| Riesgo | Probabilidad | Impacto | Mitigación |
|--------|--------------|---------|------------|
| Tests rompen funcionalidad existente | Media | Alto | Code review exhaustivo, staging |
| Notificaciones tiempo real sobrecargan servidor | Baja | Alto | Usar Laravel Reverb (optimizado) |
| Importación masiva con datos incorrectos | Alta | Medio | Validación estricta, reporte de errores |
| Calendario académico confunde usuarios | Baja | Medio | Tutorial en primera visita |

---

## 📞 SOPORTE Y CONTACTO

**Desarrollador:** Kiro AI  
**Fecha:** 30 de abril de 2026  
**Versión:** 1.0

---

**¿Listo para empezar? Elige una fase y comencemos a implementar. 🚀**
