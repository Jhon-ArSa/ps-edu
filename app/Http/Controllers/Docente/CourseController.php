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

        $course->load(['weeks.materials', 'weeks.tasks', 'students.alumnoProfile', 'enrollments']);
        $weekNumbers = $course->weeks->pluck('number')->toArray();

        return view('docente.courses.show', compact('course', 'weekNumbers'));
    }
}
