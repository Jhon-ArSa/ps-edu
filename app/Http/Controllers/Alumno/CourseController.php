<?php

namespace App\Http\Controllers\Alumno;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\EvaluationAttempt;
use App\Models\Submission;

class CourseController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $enrollments = Enrollment::where('user_id', $user->id)
            ->where('status', 'active')
            ->with(['course.teacher', 'course' => fn($q) => $q->withCount('weeks')])
            ->latest()
            ->get();

        return view('alumno.courses.index', compact('enrollments'));
    }

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
            'weeks.tasks'       => fn($q) => $q->where('status', 'active'),
            'weeks.evaluations' => fn($q) => $q->where('status', 'published')->orderBy('created_at'),
        ]);

        // Obtener las entregas del alumno para todas las tareas de este curso
        $taskIds = $course->weeks->pluck('tasks')->flatten()->pluck('id');
        $submissions = Submission::where('user_id', $user->id)
            ->whereIn('task_id', $taskIds)
            ->get()
            ->keyBy('task_id');

        // Obtener el intento más relevante por evaluación (graded > submitted > in_progress)
        $evalIds = $course->weeks->pluck('evaluations')->flatten()->pluck('id');
        $evalAttempts = EvaluationAttempt::where('user_id', $user->id)
            ->whereIn('evaluation_id', $evalIds)
            ->get()
            ->groupBy('evaluation_id')
            ->map(fn($group) => $group->sortByDesc(fn($a) => match($a->status) {
                'graded'      => 3,
                'submitted'   => 2,
                'in_progress' => 1,
                default       => 0,
            })->first());

        // Stats para el alumno
        $totalMaterials = $course->weeks->sum(fn($w) => $w->materials->count());
        $totalTasks     = $course->weeks->sum(fn($w) => $w->tasks->count());
        $submitted      = $submissions->count();
        $graded         = $submissions->where('status', 'graded')->count();
        $pending        = $totalTasks - $submitted;

        $stats = compact('totalMaterials', 'totalTasks', 'submitted', 'graded', 'pending');

        return view('alumno.courses.show', compact('course', 'enrollment', 'submissions', 'evalAttempts', 'stats'));
    }
}
