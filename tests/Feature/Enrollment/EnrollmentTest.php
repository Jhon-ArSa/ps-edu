<?php

namespace Tests\Feature\Enrollment;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnrollmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_can_enroll_student_in_their_course(): void
    {
        $teacher = User::factory()->create(['role' => 'docente']);
        $student = User::factory()->create(['role' => 'alumno']);
        $course = Course::factory()->create(['teacher_id' => $teacher->id]);

        $response = $this->actingAs($teacher)->post(route('docente.courses.students.enroll', $course), [
            'user_id' => $student->id,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('enrollments', [
            'course_id' => $course->id,
            'user_id' => $student->id,
            'status' => 'active',
        ]);
    }

    public function test_teacher_cannot_enroll_student_in_other_teacher_course(): void
    {
        $teacher1 = User::factory()->create(['role' => 'docente']);
        $teacher2 = User::factory()->create(['role' => 'docente']);
        $student = User::factory()->create(['role' => 'alumno']);
        $course = Course::factory()->create(['teacher_id' => $teacher2->id]);

        $response = $this->actingAs($teacher1)->post(route('docente.courses.students.enroll', $course), [
            'user_id' => $student->id,
        ]);

        $response->assertStatus(403);
    }

    public function test_cannot_create_duplicate_enrollment(): void
    {
        $teacher = User::factory()->create(['role' => 'docente']);
        $student = User::factory()->create(['role' => 'alumno']);
        $course = Course::factory()->create(['teacher_id' => $teacher->id]);

        // Primera matrícula
        Enrollment::create([
            'course_id' => $course->id,
            'user_id' => $student->id,
            'status' => 'active',
        ]);

        // Intentar matricular de nuevo
        $response = $this->actingAs($teacher)->post(route('docente.courses.students.enroll', $course), [
            'user_id' => $student->id,
        ]);

        // Debe redirigir con mensaje (ya matriculado)
        $response->assertRedirect();
        
        // Solo debe haber una matrícula
        $this->assertEquals(1, Enrollment::where('course_id', $course->id)
            ->where('user_id', $student->id)
            ->count());
    }

    public function test_re_enrolling_dropped_student_reactivates_enrollment(): void
    {
        $teacher = User::factory()->create(['role' => 'docente']);
        $student = User::factory()->create(['role' => 'alumno']);
        $course = Course::factory()->create(['teacher_id' => $teacher->id]);

        // Crear matrícula retirada
        $enrollment = Enrollment::create([
            'course_id' => $course->id,
            'user_id' => $student->id,
            'status' => 'dropped',
        ]);

        // Re-matricular
        $response = $this->actingAs($teacher)->post(route('docente.courses.students.enroll', $course), [
            'user_id' => $student->id,
        ]);

        $response->assertRedirect();
        
        // Verificar que se reactivó la matrícula existente
        $enrollment->refresh();
        $this->assertEquals('active', $enrollment->status);
    }

    public function test_teacher_can_unenroll_student_from_their_course(): void
    {
        $teacher = User::factory()->create(['role' => 'docente']);
        $student = User::factory()->create(['role' => 'alumno']);
        $course = Course::factory()->create(['teacher_id' => $teacher->id]);

        $enrollment = Enrollment::create([
            'course_id' => $course->id,
            'user_id' => $student->id,
            'status' => 'active',
        ]);

        $response = $this->actingAs($teacher)->delete(route('docente.courses.students.unenroll', [$course, $student]));

        $response->assertRedirect();
        
        // Verificar que el estado cambió a dropped
        $enrollment->refresh();
        $this->assertEquals('dropped', $enrollment->status);
    }

    public function test_student_can_only_see_their_active_enrollments(): void
    {
        $student = User::factory()->create(['role' => 'alumno']);
        $teacher = User::factory()->create(['role' => 'docente']);
        
        $course1 = Course::factory()->create(['teacher_id' => $teacher->id]);
        $course2 = Course::factory()->create(['teacher_id' => $teacher->id]);
        $course3 = Course::factory()->create(['teacher_id' => $teacher->id]);

        // Matrícula activa
        Enrollment::create([
            'course_id' => $course1->id,
            'user_id' => $student->id,
            'status' => 'active',
        ]);

        // Matrícula retirada
        Enrollment::create([
            'course_id' => $course2->id,
            'user_id' => $student->id,
            'status' => 'dropped',
        ]);

        // No matriculado en course3

        $response = $this->actingAs($student)->get(route('alumno.courses.index'));

        $response->assertStatus(200);
        $response->assertSee($course1->name);
        $response->assertDontSee($course2->name);
        $response->assertDontSee($course3->name);
    }
}
