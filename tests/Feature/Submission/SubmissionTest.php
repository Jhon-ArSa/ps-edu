<?php

namespace Tests\Feature\Submission;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Submission;
use App\Models\Task;
use App\Models\User;
use App\Models\Week;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SubmissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_enrolled_student_can_submit_task(): void
    {
        $teacher = User::factory()->create(['role' => 'docente']);
        $student = User::factory()->create(['role' => 'alumno']);
        $course = Course::factory()->create(['teacher_id' => $teacher->id]);
        $week = Week::factory()->create(['course_id' => $course->id]);
        $task = Task::factory()->create(['week_id' => $week->id]);

        // Matricular estudiante
        Enrollment::create([
            'course_id' => $course->id,
            'user_id' => $student->id,
            'status' => 'active',
        ]);

        $file = UploadedFile::fake()->create('tarea.pdf', 1000);

        $response = $this->actingAs($student)->post(
            route('alumno.submissions.store', [$course, $task]),
            [
                'file' => $file,
                'comments' => 'Mi entrega de tarea',
            ]
        );

        $response->assertRedirect();
        
        $this->assertDatabaseHas('submissions', [
            'task_id' => $task->id,
            'user_id' => $student->id,
            'status' => 'submitted',
        ]);

        // Verificar que el archivo se guardó
        $submission = Submission::where('task_id', $task->id)
            ->where('user_id', $student->id)
            ->first();
        
        $this->assertNotNull($submission->file_path);
        Storage::disk('public')->assertExists($submission->file_path);
    }

    public function test_non_enrolled_student_cannot_submit_task(): void
    {
        $teacher = User::factory()->create(['role' => 'docente']);
        $student = User::factory()->create(['role' => 'alumno']);
        $course = Course::factory()->create(['teacher_id' => $teacher->id]);
        $week = Week::factory()->create(['course_id' => $course->id]);
        $task = Task::factory()->create(['week_id' => $week->id]);

        // NO matricular al estudiante

        $file = UploadedFile::fake()->create('tarea.pdf', 1000);

        $response = $this->actingAs($student)->post(
            route('alumno.submissions.store', [$course, $task]),
            [
                'file' => $file,
                'comments' => 'Mi entrega de tarea',
            ]
        );

        $response->assertStatus(403);
    }

    public function test_teacher_can_grade_submission(): void
    {
        $teacher = User::factory()->create(['role' => 'docente']);
        $student = User::factory()->create(['role' => 'alumno']);
        $course = Course::factory()->create(['teacher_id' => $teacher->id]);
        $week = Week::factory()->create(['course_id' => $course->id]);
        $task = Task::factory()->create(['week_id' => $week->id]);

        $submission = Submission::create([
            'task_id' => $task->id,
            'user_id' => $student->id,
            'status' => 'submitted',
            'file_path' => 'submissions/test.pdf',
        ]);

        $response = $this->actingAs($teacher)->patch(
            route('docente.submissions.grade', [$course, $task, $submission]),
            [
                'score' => 18.5,
                'feedback' => 'Buen trabajo, pero falta profundidad en el análisis.',
            ]
        );

        $response->assertRedirect();
        
        $submission->refresh();
        $this->assertEquals('graded', $submission->status);
        $this->assertEquals(18.5, $submission->score);
        $this->assertEquals('Buen trabajo, pero falta profundidad en el análisis.', $submission->feedback);
        $this->assertEquals($teacher->id, $submission->graded_by);
        $this->assertNotNull($submission->graded_at);
    }

    public function test_teacher_cannot_grade_submission_from_other_course(): void
    {
        $teacher1 = User::factory()->create(['role' => 'docente']);
        $teacher2 = User::factory()->create(['role' => 'docente']);
        $student = User::factory()->create(['role' => 'alumno']);
        
        $course = Course::factory()->create(['teacher_id' => $teacher2->id]);
        $week = Week::factory()->create(['course_id' => $course->id]);
        $task = Task::factory()->create(['week_id' => $week->id]);

        $submission = Submission::create([
            'task_id' => $task->id,
            'user_id' => $student->id,
            'status' => 'submitted',
        ]);

        $response = $this->actingAs($teacher1)->patch(
            route('docente.submissions.grade', [$course, $task, $submission]),
            [
                'score' => 18.5,
                'feedback' => 'Feedback',
            ]
        );

        $response->assertStatus(403);
    }

    public function test_student_can_update_submission_before_grading(): void
    {
        $teacher = User::factory()->create(['role' => 'docente']);
        $student = User::factory()->create(['role' => 'alumno']);
        $course = Course::factory()->create(['teacher_id' => $teacher->id]);
        $week = Week::factory()->create(['course_id' => $course->id]);
        $task = Task::factory()->create(['week_id' => $week->id]);

        Enrollment::create([
            'course_id' => $course->id,
            'user_id' => $student->id,
            'status' => 'active',
        ]);

        $submission = Submission::create([
            'task_id' => $task->id,
            'user_id' => $student->id,
            'status' => 'submitted',
            'file_path' => 'submissions/old.pdf',
        ]);

        $newFile = UploadedFile::fake()->create('tarea_corregida.pdf', 1000);

        $response = $this->actingAs($student)->post(
            route('alumno.submissions.update', [$course, $task, $submission]),
            [
                'file' => $newFile,
                'comments' => 'Versión corregida',
            ]
        );

        $response->assertRedirect();
        
        $submission->refresh();
        $this->assertStringContainsString('tarea_corregida', $submission->file_path);
    }

    public function test_student_cannot_update_graded_submission(): void
    {
        $teacher = User::factory()->create(['role' => 'docente']);
        $student = User::factory()->create(['role' => 'alumno']);
        $course = Course::factory()->create(['teacher_id' => $teacher->id]);
        $week = Week::factory()->create(['course_id' => $course->id]);
        $task = Task::factory()->create(['week_id' => $week->id]);

        Enrollment::create([
            'course_id' => $course->id,
            'user_id' => $student->id,
            'status' => 'active',
        ]);

        $submission = Submission::create([
            'task_id' => $task->id,
            'user_id' => $student->id,
            'status' => 'graded', // Ya calificada
            'score' => 18,
            'graded_by' => $teacher->id,
        ]);

        $newFile = UploadedFile::fake()->create('tarea_nueva.pdf', 1000);

        $response = $this->actingAs($student)->post(
            route('alumno.submissions.update', [$course, $task, $submission]),
            [
                'file' => $newFile,
            ]
        );

        $response->assertStatus(403);
    }

    public function test_submission_validates_file_size(): void
    {
        $teacher = User::factory()->create(['role' => 'docente']);
        $student = User::factory()->create(['role' => 'alumno']);
        $course = Course::factory()->create(['teacher_id' => $teacher->id]);
        $week = Week::factory()->create(['course_id' => $course->id]);
        $task = Task::factory()->create(['week_id' => $week->id]);

        Enrollment::create([
            'course_id' => $course->id,
            'user_id' => $student->id,
            'status' => 'active',
        ]);

        // Archivo muy grande (15MB, límite es 10MB)
        $file = UploadedFile::fake()->create('tarea_grande.pdf', 15000);

        $response = $this->actingAs($student)->post(
            route('alumno.submissions.store', [$course, $task]),
            [
                'file' => $file,
            ]
        );

        $response->assertSessionHasErrors('file');
    }
}
