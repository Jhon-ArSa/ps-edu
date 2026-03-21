<?php

namespace App\Http\Controllers\Docente;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $query = auth()->user()->coursesTaught()
            ->with('programBelongs', 'semesterPeriod')
            ->withCount(['enrollments as active_students' => fn($q) => $q->where('status', 'active')])
            ->withCount('weeks')
            ->latest();

        // Filtro por programa
        if ($request->filled('program_id')) {
            $query->where('program_id', $request->program_id);
        }

        $allCourses = $query->get();

        // Agrupar todos los cursos por programa para mostrar organizado
        $coursesByProgram = $allCourses->groupBy(function ($course) {
            return $course->programBelongs?->id ?? 0;
        })->map(function ($programCourses, $programId) {
            $program = $programCourses->first()->programBelongs;
            return [
                'program' => $program,
                'program_name' => $program?->name ?? 'Sin programa asignado',
                'program_code' => $program?->code ?? null,
                'degree_type' => $program?->degree_type_label ?? null,
                'courses' => $programCourses,
            ];
        })->sortBy('program_name')->values();

        // Obtener lista de programas para el filtro
        $programs = $allCourses->pluck('programBelongs')->filter()->unique('id')->sortBy('name')->values();

        return view('docente.courses.index', compact('coursesByProgram', 'programs', 'allCourses'));
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
