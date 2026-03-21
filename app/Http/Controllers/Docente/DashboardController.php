<?php

namespace App\Http\Controllers\Docente;

use App\Http\Controllers\Controller;
use App\Models\Announcement;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Cargar cursos con programa académico para agrupar
        $courses = $user->coursesTaught()
            ->with('programBelongs')
            ->withCount(['enrollments as active_students' => fn($q) => $q->where('status', 'active')])
            ->withCount('weeks')
            ->latest()
            ->get();

        // Agrupar cursos por programa académico
        $coursesByProgram = $courses->groupBy(function ($course) {
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

        $latestAnnouncements = Announcement::published()
            ->forRole('docente')
            ->latest('published_at')
            ->take(3)
            ->get();

        return view('docente.dashboard', compact('courses', 'coursesByProgram', 'latestAnnouncements'));
    }
}
