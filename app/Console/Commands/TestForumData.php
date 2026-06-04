<?php

namespace App\Console\Commands;

use App\Models\Course;
use App\Models\ForumTopic;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TestForumData extends Command
{
    protected $signature = 'forum:test-data';
    protected $description = 'Crear datos de prueba para el sistema de foros';

    public function handle()
    {
        $this->info('Creando datos de prueba para el foro...');

        // Obtener usuarios
        $admin = User::where('role', 'admin')->first();
        $docente = User::where('role', 'docente')->first();
        $alumno = User::where('role', 'alumno')->first();

        if (!$admin || !$docente || !$alumno) {
            $this->error('No se encontraron usuarios admin, docente y alumno en la base de datos.');
            return 1;
        }

        // Obtener un curso
        $course = Course::with('teacher')->first();

        if (!$course) {
            $this->error('No hay cursos en la base de datos.');
            return 1;
        }

        $this->info("Curso seleccionado: {$course->name}");

        // Verificar si el docente es el profesor del curso
        if ($course->teacher_id !== $docente->id) {
            $this->warn("El docente no es profesor del curso. Asignándolo...");
            $course->update(['teacher_id' => $docente->id]);
        }

        // Matricular al alumno si no está matriculado
        if (!$course->students()->where('users.id', $alumno->id)->exists()) {
            $this->info("Matriculando alumno en el curso...");
            DB::table('enrollments')->insert([
                'course_id' => $course->id,
                'user_id' => $alumno->id,
                'status' => 'active',
                'enrolled_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Crear tema del docente
        $topicDocente = ForumTopic::create([
            'course_id' => $course->id,
            'user_id' => $docente->id,
            'title' => 'Bienvenida al curso - Importante leer',
            'body' => "Estimados estudiantes,\n\nBienvenidos a este curso. En este foro podrán hacer consultas sobre los temas que vemos en clase.\n\nPor favor mantengan un ambiente de respeto.\n\nSaludos,\n{$docente->name}",
            'is_pinned' => true,
            'is_closed' => false,
        ]);

        $this->info("✓ Tema del docente creado: {$topicDocente->title}");

        // Crear respuesta del alumno al tema del docente
        $reply1 = $topicDocente->replies()->create([
            'user_id' => $alumno->id,
            'body' => "Muchas gracias profesor. Estoy muy entusiasmado con este curso.",
        ]);

        $topicDocente->update([
            'replies_count' => 1,
            'last_reply_at' => now(),
        ]);

        $this->info("✓ Respuesta del alumno creada");

        // Crear tema del alumno con pregunta
        $topicAlumno = ForumTopic::create([
            'course_id' => $course->id,
            'user_id' => $alumno->id,
            'title' => '¿Cuándo es la primera evaluación?',
            'body' => "Hola profesor,\n\n¿Podría indicarnos cuándo será la primera evaluación del curso?\n\nGracias de antemano.",
            'is_pinned' => false,
            'is_closed' => false,
        ]);

        $this->info("✓ Tema del alumno creado: {$topicAlumno->title}");

        // Respuesta del docente al alumno
        $reply2 = $topicAlumno->replies()->create([
            'user_id' => $docente->id,
            'body' => "Hola,\n\nLa primera evaluación será la próxima semana. Revisa el cronograma en el aula virtual.\n\nSaludos.",
        ]);

        $topicAlumno->update([
            'replies_count' => 1,
            'last_reply_at' => now(),
        ]);

        $this->info("✓ Respuesta del docente creada");

        // Crear tema cerrado
        $topicCerrado = ForumTopic::create([
            'course_id' => $course->id,
            'user_id' => $alumno->id,
            'title' => 'Consulta sobre el examen parcial',
            'body' => "¿El examen parcial será presencial o virtual?",
            'is_pinned' => false,
            'is_closed' => true,
        ]);

        $reply3 = $topicCerrado->replies()->create([
            'user_id' => $docente->id,
            'body' => "Será presencial. Cerrando este tema porque ya se respondió en clase.",
        ]);

        $topicCerrado->update([
            'replies_count' => 1,
            'last_reply_at' => now(),
        ]);

        $this->info("✓ Tema cerrado creado: {$topicCerrado->title}");

        $this->newLine();
        $this->info('✅ Datos de prueba creados exitosamente!');
        $this->newLine();
        $this->line('Puedes probar el foro en:');
        $this->line("- Admin: http://127.0.0.1:8000/admin/foro");
        $this->line("- Docente: http://127.0.0.1:8000/docente/cursos/{$course->id}/foro");
        $this->line("- Alumno: http://127.0.0.1:8000/alumno/mis-cursos/{$course->id}/foro");

        return 0;
    }
}
