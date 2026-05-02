<?php

namespace Tests\Feature\Policies;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CoursePolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_manage_any_course(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $teacher = User::factory()->create(['role' => 'docente']);
        $course = Course::factory()->create(['teacher_id' => $teacher->id]);

        $this->assertTrue($admin->can('manage', $course));
    }

    public function test_teacher_can_manage_own_course(): void
    {
        $teacher = User::factory()->create(['role' => 'docente']);
        $course = Course::factory()->create(['teacher_id' => $teacher->id]);

        $this->assertTrue($teacher->can('manage', $course));
    }

    public function test_teacher_cannot_manage_other_teacher_course(): void
    {
        $teacher1 = User::factory()->create(['role' => 'docente']);
        $teacher2 = User::factory()->create(['role' => 'docente']);
        $course = Course::factory()->create(['teacher_id' => $teacher2->id]);

        $this->assertFalse($teacher1->can('manage', $course));
    }

    public function test_student_cannot_manage_course(): void
    {
        $student = User::factory()->create(['role' => 'alumno']);
        $teacher = User::factory()->create(['role' => 'docente']);
        $course = Course::factory()->create(['teacher_id' => $teacher->id]);

        $this->assertFalse($student->can('manage', $course));
    }

    public function test_admin_can_view_any_course(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $teacher = User::factory()->create(['role' => 'docente']);
        $course = Course::factory()->create(['teacher_id' => $teacher->id]);

        $this->assertTrue($admin->can('view', $course));
    }

    public function test_teacher_can_view_own_course(): void
    {
        $teacher = User::factory()->create(['role' => 'docente']);
        $course = Course::factory()->create(['teacher_id' => $teacher->id]);

        $this->assertTrue($teacher->can('view', $course));
    }

    public function test_enrolled_student_can_view_course(): void
    {
        $student = User::factory()->create(['role' => 'alumno']);
        $teacher = User::factory()->create(['role' => 'docente']);
        $course = Course::factory()->create(['teacher_id' => $teacher->id]);

        // Matricular al estudiante
        Enrollment::create([
            'course_id' => $course->id,
            'user_id' => $student->id,
            'status' => 'active',
        ]);

        $this->assertTrue($student->can('view', $course));
    }

    public function test_non_enrolled_student_cannot_view_course(): void
    {
        $student = User::factory()->create(['role' => 'alumno']);
        $teacher = User::factory()->create(['role' => 'docente']);
        $course = Course::factory()->create(['teacher_id' => $teacher->id]);

        $this->assertFalse($student->can('view', $course));
    }

    public function test_dropped_student_cannot_view_course(): void
    {
        $student = User::factory()->create(['role' => 'alumno']);
        $teacher = User::factory()->create(['role' => 'docente']);
        $course = Course::factory()->create(['teacher_id' => $teacher->id]);

        // Matricular y luego retirar
        Enrollment::create([
            'course_id' => $course->id,
            'user_id' => $student->id,
            'status' => 'dropped',
        ]);

        $this->assertFalse($student->can('view', $course));
    }
}
