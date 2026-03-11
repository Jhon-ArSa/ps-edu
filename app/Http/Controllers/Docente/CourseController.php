<?php

namespace App\Http\Controllers\Docente;

use App\Http\Controllers\Controller;
use App\Models\Course;

class CourseController extends Controller
{
    public function index()
    {
        $courses = auth()->user()->coursesTaught()
            ->withCount(['enrollments as active_students' => fn($q) => $q->where('status', 'active')])
            ->withCount('weeks')
            ->latest()
            ->paginate(12);

        return view('docente.courses.index', compact('courses'));
    }

    public function show(Course $course)
    {
        $this->authorize('manage', $course);

        $course->load([
            'semesterPeriod',
            'programBelongs',
            'weeks.materials',
            'weeks.tasks' => function ($q) {
                $q->withCount([
                    'submissions',
                    'submissions as graded_count' => fn($s) => $s->where('status', 'graded'),
                ]);
            },
            'students.alumnoProfile',
            'enrollments',
        ]);
        $weekNumbers = $course->weeks->pluck('number')->toArray();

        $totalMaterials  = $course->weeks->sum(fn($w) => $w->materials->count());
        $totalTasks      = $course->weeks->sum(fn($w) => $w->tasks->count());
        $totalSubmissions = $course->weeks->sum(fn($w) => $w->tasks->sum('submissions_count'));
        $totalGraded     = $course->weeks->sum(fn($w) => $w->tasks->sum('graded_count'));
        $pendingGrading  = $totalSubmissions - $totalGraded;

        $stats = compact('totalMaterials', 'totalTasks', 'totalSubmissions', 'totalGraded', 'pendingGrading');

        return view('docente.courses.show', compact('course', 'weekNumbers', 'stats'));
    }
}
