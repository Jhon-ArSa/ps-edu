<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Program;
use App\Models\Semester;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Dashboard de reportes con filtros multi-select por semestre, programas y cursos.
     */
    public function index(Request $request): View
    {
        $semesters = Semester::orderByDesc('year')
            ->orderByRaw("FIELD(period,'II','I')")
            ->get();

        $programs = Program::orderBy('name')->get();

        $activeSemester = Semester::getActive();
        $semesterId     = $request->input('semester_id', $activeSemester?->id);
        $semester       = $semesterId ? Semester::find($semesterId) : null;

        // Filtros multi-select (arrays)
        $selectedProgramIds = $request->input('program_ids', []);
        $selectedCourseIds  = $request->input('course_ids', []);

        // Normalizar a arrays
        if (!is_array($selectedProgramIds)) {
            $selectedProgramIds = $selectedProgramIds ? explode(',', $selectedProgramIds) : [];
        }
        if (!is_array($selectedCourseIds)) {
            $selectedCourseIds = $selectedCourseIds ? explode(',', $selectedCourseIds) : [];
        }

        // ── Estadísticas globales ─────────────────────────────────────────
        $globalStats = [
            'total_students'     => User::where('role', 'alumno')->where('status', true)->count(),
            'total_teachers'     => User::where('role', 'docente')->where('status', true)->count(),
            'active_courses'     => Course::where('status', 'active')->count(),
            'active_enrollments' => Enrollment::where('status', 'active')->count(),
        ];

        $semesterStats = null;
        $courseReports = collect();
        $programReports = collect();
        $chartData = null;
        $availableCourses = collect();

        if ($semester) {
            // Consulta base de cursos del semestre
            $coursesQuery = Course::where('semester_id', $semester->id)
                ->with('teacher', 'programBelongs');

            // Cargar todos los cursos del semestre para el selector
            $availableCourses = (clone $coursesQuery)->orderBy('name')->get();

            // Filtrar por programas seleccionados
            if (!empty($selectedProgramIds)) {
                $coursesQuery->whereIn('program_id', $selectedProgramIds);
            }

            // Filtrar por cursos específicos seleccionados
            if (!empty($selectedCourseIds)) {
                $coursesQuery->whereIn('id', $selectedCourseIds);
            }

            $courses   = $coursesQuery->orderBy('name')->get();
            $courseIds = $courses->pluck('id')->toArray();

            // ── Bulk queries ────────────────────────────────────────────────
            $studentsByCourse = DB::table('enrollments')
                ->whereIn('course_id', $courseIds)
                ->where('status', 'active')
                ->selectRaw('course_id, count(*) as cnt')
                ->groupBy('course_id')
                ->pluck('cnt', 'course_id');

            $weeksByCourse = DB::table('weeks')
                ->whereIn('course_id', $courseIds)
                ->selectRaw('course_id, count(*) as cnt')
                ->groupBy('course_id')
                ->pluck('cnt', 'course_id');

            $materialsByCourse = DB::table('materials')
                ->join('weeks', 'weeks.id', '=', 'materials.week_id')
                ->whereIn('weeks.course_id', $courseIds)
                ->selectRaw('weeks.course_id, count(*) as cnt')
                ->groupBy('weeks.course_id')
                ->pluck('cnt', 'course_id');

            $tasksByCourse = DB::table('tasks')
                ->join('weeks', 'weeks.id', '=', 'tasks.week_id')
                ->whereIn('weeks.course_id', $courseIds)
                ->selectRaw('weeks.course_id, count(*) as cnt')
                ->groupBy('weeks.course_id')
                ->pluck('cnt', 'course_id');

            // ── Construir reporte por curso ──────────────────────────────────
            $courseReports = $courses->map(function (Course $course) use (
                $studentsByCourse, $weeksByCourse, $materialsByCourse, $tasksByCourse
            ) {
                $id = $course->id;

                return [
                    'course'          => $course,
                    'program'         => $course->programBelongs,
                    'active_students' => (int) ($studentsByCourse[$id] ?? 0),
                    'weeks'           => (int) ($weeksByCourse[$id] ?? 0),
                    'materials'       => (int) ($materialsByCourse[$id] ?? 0),
                    'tasks'           => (int) ($tasksByCourse[$id] ?? 0),
                ];
            });

            // ── Agrupar reportes por programa ────────────────────────────────
            $programReports = $courseReports->groupBy(function ($report) {
                return $report['program']?->id ?? 0;
            })->map(function ($programCourses, $progId) {
                $program = $programCourses->first()['program'];

                return [
                    'program' => $program,
                    'program_name' => $program?->name ?? 'Sin programa',
                    'program_code' => $program?->code ?? null,
                    'courses_count' => $programCourses->count(),
                    'total_students' => $programCourses->sum('active_students'),
                    'total_materials' => $programCourses->sum('materials'),
                    'total_tasks' => $programCourses->sum('tasks'),
                    'courses' => $programCourses,
                ];
            })->sortBy('program_name')->values();

            $semesterStats = [
                'courses'     => $courses->count(),
                'teachers'    => $courses->pluck('teacher_id')->filter()->unique()->count(),
                'enrollments' => $studentsByCourse->sum(),
                'materials'   => $courseReports->sum('materials'),
                'tasks'       => $courseReports->sum('tasks'),
                'programs'    => $programReports->count(),
            ];

            // ── Datos para gráficos (sin notas) ──────────────────────────────
            $chartData = [
                'coursesByProgram' => $programReports->map(fn($p) => [
                    'name' => $p['program_code'] ?? 'Sin prog.',
                    'value' => $p['courses_count'],
                ])->values(),
                'studentsByProgram' => $programReports->map(fn($p) => [
                    'name' => $p['program_code'] ?? 'Sin prog.',
                    'value' => $p['total_students'],
                ])->values(),
                'materialsByProgram' => $programReports->map(fn($p) => [
                    'name' => $p['program_code'] ?? 'Sin prog.',
                    'value' => $p['total_materials'],
                ])->values(),
                'topCoursesByStudents' => $courseReports->sortByDesc('active_students')->take(5)->map(fn($c) => [
                    'name' => \Str::limit($c['course']->name, 20),
                    'value' => $c['active_students'],
                ])->values(),
            ];
        }

        // Programas seleccionados para mostrar en la UI
        $selectedPrograms = !empty($selectedProgramIds)
            ? Program::whereIn('id', $selectedProgramIds)->get()
            : collect();

        // Cursos seleccionados para mostrar en la UI
        $selectedCourses = !empty($selectedCourseIds)
            ? Course::whereIn('id', $selectedCourseIds)->get()
            : collect();

        // Datos simplificados de cursos para el filtro JavaScript
        $coursesForFilter = $availableCourses->map(function ($c) {
            return [
                'id' => $c->id,
                'name' => $c->name,
                'code' => $c->code,
                'program_id' => $c->program_id,
                'program_code' => $c->programBelongs?->code ?? null,
            ];
        })->values()->toArray();

        return view('admin.reports.index', compact(
            'semesters', 'programs', 'semester',
            'selectedProgramIds', 'selectedCourseIds',
            'selectedPrograms', 'selectedCourses',
            'availableCourses', 'coursesForFilter',
            'globalStats', 'semesterStats', 'courseReports', 'programReports', 'chartData'
        ));
    }

    /**
     * Vista para imprimir / exportar a PDF (respeta filtros).
     */
    public function print(Request $request): View
    {
        $activeSemester = Semester::getActive();
        $semesterId     = $request->input('semester_id', $activeSemester?->id);
        $semester       = $semesterId ? Semester::find($semesterId) : null;

        $selectedProgramIds = $request->input('program_ids', []);
        $selectedCourseIds  = $request->input('course_ids', []);

        if (!is_array($selectedProgramIds)) {
            $selectedProgramIds = $selectedProgramIds ? explode(',', $selectedProgramIds) : [];
        }
        if (!is_array($selectedCourseIds)) {
            $selectedCourseIds = $selectedCourseIds ? explode(',', $selectedCourseIds) : [];
        }

        $courseReports  = collect();
        $programReports = collect();

        if ($semester) {
            $coursesQuery = Course::where('semester_id', $semester->id)
                ->with('teacher', 'programBelongs');

            if (!empty($selectedProgramIds)) {
                $coursesQuery->whereIn('program_id', $selectedProgramIds);
            }
            if (!empty($selectedCourseIds)) {
                $coursesQuery->whereIn('id', $selectedCourseIds);
            }

            $courses   = $coursesQuery->orderBy('name')->get();
            $courseIds = $courses->pluck('id')->toArray();

            $studentsByCourse = DB::table('enrollments')
                ->whereIn('course_id', $courseIds)->where('status', 'active')
                ->selectRaw('course_id, count(*) as cnt')->groupBy('course_id')
                ->pluck('cnt', 'course_id');

            $weeksByCourse = DB::table('weeks')
                ->whereIn('course_id', $courseIds)
                ->selectRaw('course_id, count(*) as cnt')->groupBy('course_id')
                ->pluck('cnt', 'course_id');

            $materialsByCourse = DB::table('materials')
                ->join('weeks', 'weeks.id', '=', 'materials.week_id')
                ->whereIn('weeks.course_id', $courseIds)
                ->selectRaw('weeks.course_id, count(*) as cnt')->groupBy('weeks.course_id')
                ->pluck('cnt', 'course_id');

            $tasksByCourse = DB::table('tasks')
                ->join('weeks', 'weeks.id', '=', 'tasks.week_id')
                ->whereIn('weeks.course_id', $courseIds)
                ->selectRaw('weeks.course_id, count(*) as cnt')->groupBy('weeks.course_id')
                ->pluck('cnt', 'course_id');

            $courseReports = $courses->map(function (Course $course) use (
                $studentsByCourse, $weeksByCourse, $materialsByCourse, $tasksByCourse
            ) {
                $id = $course->id;
                return [
                    'course'          => $course,
                    'program'         => $course->programBelongs,
                    'active_students' => (int) ($studentsByCourse[$id] ?? 0),
                    'weeks'           => (int) ($weeksByCourse[$id] ?? 0),
                    'materials'       => (int) ($materialsByCourse[$id] ?? 0),
                    'tasks'           => (int) ($tasksByCourse[$id] ?? 0),
                ];
            });

            $programReports = $courseReports->groupBy(function ($report) {
                return $report['program']?->id ?? 0;
            })->map(function ($programCourses, $progId) {
                $program = $programCourses->first()['program'];
                return [
                    'program' => $program,
                    'program_name' => $program?->name ?? 'Sin programa',
                    'program_code' => $program?->code ?? null,
                    'courses' => $programCourses,
                ];
            })->sortBy('program_name')->values();
        }

        $selectedPrograms = !empty($selectedProgramIds)
            ? Program::whereIn('id', $selectedProgramIds)->get()
            : collect();

        return view('admin.reports.print', compact(
            'semester', 'courseReports', 'programReports', 'selectedPrograms'
        ));
    }

    /**
     * Exporta el reporte como CSV (respeta filtros).
     */
    public function exportCsv(Request $request): Response
    {
        $activeSemester = Semester::getActive();
        $semesterId     = $request->input('semester_id', $activeSemester?->id);
        $semester       = $semesterId ? Semester::find($semesterId) : null;

        $selectedProgramIds = $request->input('program_ids', []);
        $selectedCourseIds  = $request->input('course_ids', []);

        if (!is_array($selectedProgramIds)) {
            $selectedProgramIds = $selectedProgramIds ? explode(',', $selectedProgramIds) : [];
        }
        if (!is_array($selectedCourseIds)) {
            $selectedCourseIds = $selectedCourseIds ? explode(',', $selectedCourseIds) : [];
        }

        $filename = 'reporte_' . ($semester ? str($semester->name)->slug() : 'general') . '_' . now()->format('Ymd') . '.csv';

        $rows   = [];
        $rows[] = ['Programa', 'Curso', 'Código', 'Docente', 'Alumnos', 'Semanas', 'Materiales', 'Tareas'];

        if ($semester) {
            $coursesQuery = Course::where('semester_id', $semester->id)
                ->with('teacher', 'programBelongs');

            if (!empty($selectedProgramIds)) {
                $coursesQuery->whereIn('program_id', $selectedProgramIds);
            }
            if (!empty($selectedCourseIds)) {
                $coursesQuery->whereIn('id', $selectedCourseIds);
            }

            $courses   = $coursesQuery->orderBy('name')->get();
            $courseIds = $courses->pluck('id')->toArray();

            $studentsByCourse = DB::table('enrollments')
                ->whereIn('course_id', $courseIds)->where('status', 'active')
                ->selectRaw('course_id, count(*) as cnt')->groupBy('course_id')
                ->pluck('cnt', 'course_id');

            $weeksByCourse = DB::table('weeks')
                ->whereIn('course_id', $courseIds)
                ->selectRaw('course_id, count(*) as cnt')->groupBy('course_id')
                ->pluck('cnt', 'course_id');

            $materialsByCourse = DB::table('materials')
                ->join('weeks', 'weeks.id', '=', 'materials.week_id')
                ->whereIn('weeks.course_id', $courseIds)
                ->selectRaw('weeks.course_id, count(*) as cnt')->groupBy('weeks.course_id')
                ->pluck('cnt', 'course_id');

            $tasksByCourse = DB::table('tasks')
                ->join('weeks', 'weeks.id', '=', 'tasks.week_id')
                ->whereIn('weeks.course_id', $courseIds)
                ->selectRaw('weeks.course_id, count(*) as cnt')->groupBy('weeks.course_id')
                ->pluck('cnt', 'course_id');

            foreach ($courses as $course) {
                $id = $course->id;
                $rows[] = [
                    $course->programBelongs?->name ?? 'Sin programa',
                    $course->name,
                    $course->code,
                    $course->teacher?->name ?? '—',
                    (int) ($studentsByCourse[$id] ?? 0),
                    (int) ($weeksByCourse[$id] ?? 0),
                    (int) ($materialsByCourse[$id] ?? 0),
                    (int) ($tasksByCourse[$id] ?? 0),
                ];
            }
        }

        return $this->csvResponse($filename, $rows);
    }

    /**
     * Genera una Response de descarga CSV.
     */
    private function csvResponse(string $filename, array $rows): Response
    {
        $csv = "\xEF\xBB\xBF"; // BOM para compatibilidad con Excel
        foreach ($rows as $row) {
            $csv .= implode(',', array_map(function ($cell) {
                $cell = str_replace('"', '""', (string) $cell);
                return '"' . $cell . '"';
            }, $row)) . "\r\n";
        }

        return response($csv, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
