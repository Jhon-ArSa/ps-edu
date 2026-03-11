<?php

namespace App\Http\Controllers\Alumno;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Submission;

class CourseController extends Controller
{
    public function show(Course $course)
    {
        $user = auth()->user();

        $enrollment = Enrollment::where('course_id', $course->id)
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->firstOrFail();

        $course->load([
            'teacher',
            'semesterPeriod',
            'programBelongs',
            'weeks.materials',
            'weeks.tasks' => fn($q) => $q->where('status', 'active'),
        ]);

        // Obtener las entregas del alumno para todas las tareas de este curso
        $taskIds = $course->weeks->pluck('tasks')->flatten()->pluck('id');
        $submissions = Submission::where('user_id', $user->id)
            ->whereIn('task_id', $taskIds)
            ->get()
            ->keyBy('task_id');

        // Stats para el alumno
        $totalMaterials = $course->weeks->sum(fn($w) => $w->materials->count());
        $totalTasks     = $course->weeks->sum(fn($w) => $w->tasks->count());
        $submitted      = $submissions->count();
        $graded         = $submissions->where('status', 'graded')->count();
        $pending        = $totalTasks - $submitted;

        $stats = compact('totalMaterials', 'totalTasks', 'submitted', 'graded', 'pending');

        return view('alumno.courses.show', compact('course', 'enrollment', 'submissions', 'stats'));
    }
}
