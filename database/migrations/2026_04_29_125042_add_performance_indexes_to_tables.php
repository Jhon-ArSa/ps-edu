<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Agrega índices críticos para optimizar el rendimiento con 200-300 usuarios.
     * Basado en las recomendaciones de contexto/04-base-de-datos.md
     */
    public function up(): void
    {
        // ── ENROLLMENTS ───────────────────────────────────────────────────────
        // Consultas frecuentes del alumno: "mis cursos activos"
        Schema::table('enrollments', function (Blueprint $table) {
            $table->index(['user_id', 'status'], 'idx_enrollments_user_status');
        });

        // ── COURSES ───────────────────────────────────────────────────────────
        // Consultas frecuentes del docente: "mis cursos activos"
        Schema::table('courses', function (Blueprint $table) {
            $table->index(['teacher_id', 'status'], 'idx_courses_teacher_status');
        });

        // ── SUBMISSIONS ───────────────────────────────────────────────────────
        // Búsqueda de entregas por tarea y estado
        Schema::table('submissions', function (Blueprint $table) {
            $table->index(['task_id', 'status'], 'idx_submissions_task_status');
        });

        // ── NOTIFICATIONS ─────────────────────────────────────────────────────
        // Notificaciones no leídas (badge en header, cada page load)
        Schema::table('notifications', function (Blueprint $table) {
            $table->index(['notifiable_id', 'read_at'], 'idx_notifications_notifiable_read');
        });

        // ── SEMESTERS ─────────────────────────────────────────────────────────
        // Semestre activo (lectura constante en dashboard admin)
        // Ya existe INDEX(status) en la migración original, verificar
        if (!$this->indexExists('semesters', 'semesters_status_index')) {
            Schema::table('semesters', function (Blueprint $table) {
                $table->index('status', 'idx_semesters_status');
            });
        }

        // ── FORUM_TOPICS ──────────────────────────────────────────────────────
        // Foro por curso, ordenado por pinned y fecha
        Schema::table('forum_topics', function (Blueprint $table) {
            $table->index(['course_id', 'is_pinned', 'created_at'], 'idx_forum_topics_course_pinned_date');
        });

        // ── ANNOUNCEMENTS ─────────────────────────────────────────────────────
        // Anuncios publicados por rol
        Schema::table('announcements', function (Blueprint $table) {
            $table->index(['target_role', 'published_at'], 'idx_announcements_role_published');
        });

        // ── TASKS ─────────────────────────────────────────────────────────────
        // Búsqueda de tareas por semana y estado
        Schema::table('tasks', function (Blueprint $table) {
            $table->index(['week_id', 'status'], 'idx_tasks_week_status');
        });

        // ── EVALUATIONS ───────────────────────────────────────────────────────
        // Búsqueda de evaluaciones por semana y estado
        Schema::table('evaluations', function (Blueprint $table) {
            $table->index(['week_id', 'status'], 'idx_evaluations_week_status');
        });

        // ── EVALUATION_ATTEMPTS ───────────────────────────────────────────────
        // Búsqueda de intentos por evaluación y usuario
        Schema::table('evaluation_attempts', function (Blueprint $table) {
            $table->index(['evaluation_id', 'user_id', 'status'], 'idx_attempts_eval_user_status');
        });

        // ── GRADES ────────────────────────────────────────────────────────────
        // Búsqueda de calificaciones por alumno
        Schema::table('grades', function (Blueprint $table) {
            $table->index(['user_id', 'grade_item_id'], 'idx_grades_user_item');
        });

        // ── USERS ─────────────────────────────────────────────────────────────
        // Búsqueda de usuarios por rol y estado (admin)
        Schema::table('users', function (Blueprint $table) {
            $table->index(['role', 'status'], 'idx_users_role_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropIndex('idx_enrollments_user_status');
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->dropIndex('idx_courses_teacher_status');
        });

        Schema::table('submissions', function (Blueprint $table) {
            $table->dropIndex('idx_submissions_task_status');
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex('idx_notifications_notifiable_read');
        });

        if ($this->indexExists('semesters', 'idx_semesters_status')) {
            Schema::table('semesters', function (Blueprint $table) {
                $table->dropIndex('idx_semesters_status');
            });
        }

        Schema::table('forum_topics', function (Blueprint $table) {
            $table->dropIndex('idx_forum_topics_course_pinned_date');
        });

        Schema::table('announcements', function (Blueprint $table) {
            $table->dropIndex('idx_announcements_role_published');
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->dropIndex('idx_tasks_week_status');
        });

        Schema::table('evaluations', function (Blueprint $table) {
            $table->dropIndex('idx_evaluations_week_status');
        });

        Schema::table('evaluation_attempts', function (Blueprint $table) {
            $table->dropIndex('idx_attempts_eval_user_status');
        });

        Schema::table('grades', function (Blueprint $table) {
            $table->dropIndex('idx_grades_user_item');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_role_status');
        });
    }

    /**
     * Verifica si un índice existe en una tabla
     */
    private function indexExists(string $table, string $index): bool
    {
        // En SQLite (testing), siempre retornar false para intentar crear
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            return false;
        }

        try {
            $indexes = Schema::getConnection()->getDoctrineSchemaManager()->listTableIndexes($table);
            return isset($indexes[$index]);
        } catch (\Exception $e) {
            return false;
        }
    }
};
