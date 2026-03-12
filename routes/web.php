<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Docente;
use App\Http\Controllers\Alumno;
use App\Http\Controllers\ForumReplyController;
use App\Models\Announcement;

// ── RAÍZ → REDIRIGE SEGÚN ESTADO DE AUTENTICACIÓN ────────────────────────────
Route::get('/', function () {
    if (auth()->check()) {
        return match(auth()->user()->role) {
            'admin'   => redirect()->route('admin.dashboard'),
            'docente' => redirect()->route('docente.dashboard'),
            'alumno'  => redirect()->route('alumno.dashboard'),
            default   => redirect()->route('login'),
        };
    }
    return redirect()->route('login');
});

// ── AUTH ─────────────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',  [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.attempt')->middleware('throttle:login');

    Route::get('/forgot-password',         [PasswordResetController::class, 'showForgotForm'])->name('password.request');
    Route::post('/forgot-password',        [PasswordResetController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}',  [PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password',         [PasswordResetController::class, 'resetPassword'])->name('password.update');
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

// ── PERFIL COMPARTIDO ─────────────────────────────────────────────────────────
Route::middleware('auth')->prefix('profile')->name('profile.')->group(function () {
    Route::get('/',            [ProfileController::class, 'edit'])->name('edit');
    Route::put('/',            [ProfileController::class, 'update'])->name('update');
    Route::post('/avatar',     [ProfileController::class, 'updateAvatar'])->name('avatar');
    Route::put('/password',    [ProfileController::class, 'updatePassword'])->name('password');
});

// ── ADMIN ────────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

    Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');

    Route::resource('users', Admin\UserController::class);
    Route::patch('users/{user}/toggle-status', [Admin\UserController::class, 'toggleStatus'])->name('users.toggle-status');

    Route::resource('courses', Admin\CourseController::class);

    Route::resource('programs', Admin\ProgramController::class);

    // Menciones dentro de programas
    Route::prefix('programs/{program}/mentions')->name('programs.mentions.')->group(function () {
        Route::get('/create',           [Admin\MentionController::class, 'create'])->name('create');
        Route::post('/',                [Admin\MentionController::class, 'store'])->name('store');
        Route::get('/{mention}/edit',   [Admin\MentionController::class, 'edit'])->name('edit');
        Route::put('/{mention}',        [Admin\MentionController::class, 'update'])->name('update');
        Route::delete('/{mention}',     [Admin\MentionController::class, 'destroy'])->name('destroy');
    });

    Route::resource('semesters', Admin\SemesterController::class);
    Route::patch('semesters/{semester}/activate', [Admin\SemesterController::class, 'activate'])->name('semesters.activate');
    Route::patch('semesters/{semester}/close', [Admin\SemesterController::class, 'close'])->name('semesters.close');

    Route::resource('announcements', Admin\AnnouncementController::class)->except(['show']);

    Route::get('/enrollments',                         [Admin\EnrollmentController::class, 'index'])->name('enrollments.index');
    Route::patch('/enrollments/{enrollment}/toggle',   [Admin\EnrollmentController::class, 'toggle'])->name('enrollments.toggle');

    Route::get('/settings',  [Admin\SettingsController::class, 'index'])->name('settings');
    Route::put('/settings',  [Admin\SettingsController::class, 'update'])->name('settings.update');

    // ── Reportes Admin (Zair) ─────────────────────────────────────────────
    Route::get('/reportes',          [Admin\ReportController::class, 'index'])->name('reports.index');
    Route::get('/reportes/imprimir', [Admin\ReportController::class, 'print'])->name('reports.print');
    Route::get('/reportes/exportar', [Admin\ReportController::class, 'exportCsv'])->name('reports.csv');

    // Detalle de curso para admin (reutiliza el controller de docente; CoursePolicy::manage ya permite al admin)
    Route::prefix('/reportes/cursos/{course}')->name('reports.course.')->group(function () {
        Route::get('/',         [Docente\ReportController::class, 'show'])->name('show');
        Route::get('/imprimir', [Docente\ReportController::class, 'print'])->name('print');
        Route::get('/exportar', [Docente\ReportController::class, 'exportCsv'])->name('csv');
    });

    // Intranet (vista de comunicados publicados para el admin)
    Route::get('/intranet', [Admin\AnnouncementController::class, 'intranet'])->name('intranet');

    // ── Supervisión de foros ──────────────────────────────────────────────
    Route::get('/foro', [Admin\ForumController::class, 'index'])->name('forum.index');
});

// ── DOCENTE ───────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:docente'])
    ->prefix('docente')
    ->name('docente.')
    ->group(function () {

    Route::get('/dashboard', [Docente\DashboardController::class, 'index'])->name('dashboard');

    // Aula Virtual - Cursos
    Route::prefix('cursos')->name('courses.')->group(function () {
        Route::get('/',         [Docente\CourseController::class, 'index'])->name('index');
        Route::get('/{course}', [Docente\CourseController::class, 'show'])->name('show');

        // Semanas
        Route::post('/{course}/semanas',               [Docente\WeekController::class, 'store'])->name('weeks.store');
        Route::put('/{course}/semanas/{week}',          [Docente\WeekController::class, 'update'])->name('weeks.update');
        Route::delete('/{course}/semanas/{week}',       [Docente\WeekController::class, 'destroy'])->name('weeks.destroy');

        // Materiales
        Route::post('/{course}/semanas/{week}/materiales',               [Docente\MaterialController::class, 'store'])->name('materials.store');
        Route::put('/{course}/semanas/{week}/materiales/{material}',     [Docente\MaterialController::class, 'update'])->name('materials.update');
        Route::delete('/{course}/semanas/{week}/materiales/{material}',  [Docente\MaterialController::class, 'destroy'])->name('materials.destroy');
        Route::post('/{course}/semanas/{week}/materiales/reorder',                [Docente\MaterialController::class, 'reorder'])->name('materials.reorder');

        // Tareas
        Route::post('/{course}/semanas/{week}/tareas',             [Docente\TaskController::class, 'store'])->name('tasks.store');
        Route::put('/{course}/semanas/{week}/tareas/{task}',       [Docente\TaskController::class, 'update'])->name('tasks.update');
        Route::delete('/{course}/semanas/{week}/tareas/{task}',    [Docente\TaskController::class, 'destroy'])->name('tasks.destroy');

        // Entregas de tareas (Juan)
        Route::get('/{course}/tareas/{task}/entregas',                                [Docente\SubmissionController::class, 'index'])->name('submissions.index');
        Route::patch('/{course}/tareas/{task}/entregas/{submission}/calificar',       [Docente\SubmissionController::class, 'grade'])->name('submissions.grade');

        // Alumnos
        Route::get('/{course}/estudiantes/buscar',       [Docente\StudentController::class, 'search'])->name('students.search');
        Route::post('/{course}/estudiantes',             [Docente\StudentController::class, 'enroll'])->name('students.enroll');
        Route::delete('/{course}/estudiantes/{student}', [Docente\StudentController::class, 'unenroll'])->name('students.unenroll');
    });

    // ── Evaluaciones (fuera del grupo courses. para que el nombre sea docente.evaluations.*) ──
    Route::prefix('cursos')->name('evaluations.')->group(function () {
        Route::post('/{course}/semanas/{week}/evaluaciones',                              [Docente\EvaluationController::class, 'store'])->name('store');
        Route::get('/{course}/evaluaciones/{evaluation}',                                 [Docente\EvaluationController::class, 'show'])->name('show');
        Route::put('/{course}/evaluaciones/{evaluation}',                                 [Docente\EvaluationController::class, 'update'])->name('update');
        Route::delete('/{course}/evaluaciones/{evaluation}',                              [Docente\EvaluationController::class, 'destroy'])->name('destroy');
        Route::patch('/{course}/evaluaciones/{evaluation}/estado',                        [Docente\EvaluationController::class, 'toggleStatus'])->name('toggle');
        Route::post('/{course}/evaluaciones/{evaluation}/archivo',                        [Docente\EvaluationController::class, 'updateFile'])->name('file');
        Route::post('/{course}/evaluaciones/{evaluation}/preguntas',                      [Docente\EvaluationController::class, 'addQuestion'])->name('questions.store');
        Route::delete('/{course}/evaluaciones/{evaluation}/preguntas/{question}',         [Docente\EvaluationController::class, 'removeQuestion'])->name('questions.destroy');
        Route::get('/{course}/evaluaciones/{evaluation}/intentos',                        [Docente\EvaluationAttemptController::class, 'index'])->name('attempts.index');
        Route::patch('/{course}/evaluaciones/{evaluation}/intentos/{attempt}/calificar',  [Docente\EvaluationAttemptController::class, 'gradeShort'])->name('attempts.grade');
    });

    // ── Reportes del curso (Zair) ─────────────────────────────────────────
    Route::prefix('cursos/{course}/reporte')->name('reports.')->group(function () {
        Route::get('/',         [Docente\ReportController::class, 'show'])->name('show');
        Route::get('/imprimir', [Docente\ReportController::class, 'print'])->name('print');
        Route::get('/exportar', [Docente\ReportController::class, 'exportCsv'])->name('csv');
    });

    // ── Calificaciones (Zair) ──────────────────────────────────────────────
    Route::prefix('cursos/{course}/notas')->name('grades.')->group(function () {
        Route::get('/',                                         [Docente\GradeController::class, 'index'])->name('index');
        Route::get('/exportar',                                 [Docente\GradeController::class, 'exportCsv'])->name('csv');
        Route::post('/items',                                   [Docente\GradeController::class, 'storeItem'])->name('items.store');
        Route::patch('/items/{gradeItem}',                      [Docente\GradeController::class, 'updateItem'])->name('items.update');
        Route::delete('/items/{gradeItem}',                     [Docente\GradeController::class, 'destroyItem'])->name('items.destroy');
        Route::patch('/{gradeItem}/alumnos/{user}',             [Docente\GradeController::class, 'updateGrade'])->name('update');
    });

    // ── Foro del curso ────────────────────────────────────────────────────────
    Route::prefix('cursos/{course}/foro')->name('forum.')->group(function () {
        Route::get('/',               [Docente\ForumController::class, 'index'])->name('index');
        Route::post('/',              [Docente\ForumController::class, 'store'])->name('store');
        Route::get('/{topic}',        [Docente\ForumController::class, 'show'])->name('show');
        Route::delete('/{topic}',     [Docente\ForumController::class, 'destroy'])->name('destroy');
        Route::patch('/{topic}/fijar',  [Docente\ForumController::class, 'pin'])->name('pin');
        Route::patch('/{topic}/cerrar', [Docente\ForumController::class, 'close'])->name('close');
    });

    // Intranet
    Route::get('/intranet', function () {
        return view('docente.intranet', [
            'announcements' => Announcement::published()->forRole('docente')->latest('published_at')->paginate(10),
        ]);
    })->name('intranet');
    Route::get('/escalafon',        [Docente\EscalafonController::class, 'show'])->name('escalafon.show');
    Route::get('/escalafon/editar', [Docente\EscalafonController::class, 'edit'])->name('escalafon.edit');
    Route::put('/escalafon',        [Docente\EscalafonController::class, 'update'])->name('escalafon.update');

    // Soporte
    Route::get('/soporte',  fn() => view('docente.soporte'))->name('soporte');
    Route::post('/soporte', [Docente\SupportController::class, 'send'])->name('soporte.send');
});

// ── NOTIFICACIONES (Zair — todos los roles autenticados) ─────────────────────
Route::middleware('auth')->prefix('notificaciones')->name('notifications.')->group(function () {
    Route::get('/',             [\App\Http\Controllers\NotificationController::class, 'index'])->name('index');
    Route::patch('/{id}/leer',  [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('read');
    Route::patch('/leer-todas', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('read-all');
});

// ── ALUMNO ────────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:alumno'])
    ->prefix('alumno')
    ->name('alumno.')
    ->group(function () {

    Route::get('/dashboard', [Alumno\DashboardController::class, 'index'])->name('dashboard');

    Route::get('/mis-cursos',             [Alumno\CourseController::class, 'index'])->name('courses.index');
    Route::get('/mis-cursos/{course}',    [Alumno\CourseController::class, 'show'])->name('courses.show');
    Route::get('/mis-notas',              [Alumno\GradeController::class, 'index'])->name('grades.index');
    Route::get('/mis-cursos/{course}/notas', [Alumno\GradeController::class, 'show'])->name('grades.show');

    // Entregas de tareas (Juan)
    Route::post('/mis-cursos/{course}/tareas/{task}/entregar',                       [Alumno\SubmissionController::class, 'store'])->name('submissions.store');
    Route::post('/mis-cursos/{course}/tareas/{task}/entregas/{submission}',           [Alumno\SubmissionController::class, 'update'])->name('submissions.update');

    // Evaluaciones en línea
    Route::get('/mis-cursos/{course}/evaluaciones/{evaluation}',            [Alumno\EvaluationController::class, 'show'])->name('evaluations.show');
    Route::post('/mis-cursos/{course}/evaluaciones/{evaluation}/iniciar',   [Alumno\EvaluationController::class, 'start'])->name('evaluations.start');
    Route::get('/mis-cursos/{course}/evaluaciones/{evaluation}/tomar',      [Alumno\EvaluationController::class, 'take'])->name('evaluations.take');
    Route::post('/mis-cursos/{course}/evaluaciones/{evaluation}/enviar',    [Alumno\EvaluationController::class, 'submit'])->name('evaluations.submit');
    Route::get('/mis-cursos/{course}/evaluaciones/{evaluation}/resultado/{attempt}', [Alumno\EvaluationController::class, 'result'])->name('evaluations.result');

    // Foro del curso
    Route::prefix('mis-cursos/{course}/foro')->name('forum.')->group(function () {
        Route::get('/',          [Alumno\ForumController::class, 'index'])->name('index');
        Route::post('/',         [Alumno\ForumController::class, 'store'])->name('store');
        Route::get('/{topic}',   [Alumno\ForumController::class, 'show'])->name('show');
        Route::delete('/{topic}',[Alumno\ForumController::class, 'destroy'])->name('destroy');
    });

    Route::get('/intranet', function () {
        return view('alumno.intranet', [
            'announcements' => Announcement::published()->forRole('alumno')->latest('published_at')->paginate(10),
        ]);
    })->name('intranet');
});

// ── FORO — RESPUESTAS (todos los roles autenticados) ─────────────────────────
Route::middleware('auth')->prefix('foro')->name('forum.replies.')->group(function () {
    Route::post('/{topic}/respuestas',   [ForumReplyController::class, 'store'])->name('store');
    Route::delete('/{topic}/respuestas/{reply}', [ForumReplyController::class, 'destroy'])->name('destroy');
});
