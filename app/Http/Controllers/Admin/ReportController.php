<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\GradeItem;
use App\Models\Semester;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Dashboard general de reportes del semestre seleccionado.
     * Optimizado: consultas en bulk (1 query por tabla, no 1 por curso).
     */
    public function index(Request $request): View
    {
        $semesters = Semester::orderByDesc('year')
            ->orderByRaw("FIELD(period,'II','I')")
            ->get();

        $activeSemester = Semester::getActive();
        $semesterId     = $request->input('semester_id', $activeSemester?->id);
        $semester       = $semesterId ? Semester::find($semesterId) : null;

        // ── Estadísticas globales (4 queries simples) ─────────────────────
        $globalStats = [
            'total_students'     => User::where('role', 'alumno')->where('status', true)->count(),
            'total_teachers'     => User::where('role', 'docente')->where('status', true)->count(),
            'active_courses'     => Course::where('status', 'active')->count(),
            'active_enrollments' => Enrollment::where('status', 'active')->count(),
        ];

        $semesterStats = null;
        $courseReports = collect();

        if ($semester) {
            $courses    = Course::where('semester_id', $semester->id)
                ->with('teacher')
                ->get();
            $courseIds  = $courses->pluck('id')->toArray();

            // ── Bulk queries (1 por tabla) ────────────────────────────────

            // Alumnos activos por curso
            $studentsByCourse = DB::table('enrollments')
                ->whereIn('course_id', $courseIds)
                ->where('status', 'active')
                ->selectRaw('course_id, count(*) as cnt')
                ->groupBy('course_id')
                ->pluck('cnt', 'course_id');

            // Semanas por curso
            $weeksByCourse = DB::table('weeks')
                ->whereIn('course_id', $courseIds)
                ->selectRaw('course_id, count(*) as cnt')
                ->groupBy('course_id')
                ->pluck('cnt', 'course_id');

            // Materiales por curso (join weeks)
            $materialsByCourse = DB::table('materials')
                ->join('weeks', 'weeks.id', '=', 'materials.week_id')
                ->whereIn('weeks.course_id', $courseIds)
                ->selectRaw('weeks.course_id, count(*) as cnt')
                ->groupBy('weeks.course_id')
                ->pluck('cnt', 'course_id');

            // Tareas por curso (join weeks)
            $tasksByCourse = DB::table('tasks')
                ->join('weeks', 'weeks.id', '=', 'tasks.week_id')
                ->whereIn('weeks.course_id', $courseIds)
                ->selectRaw('weeks.course_id, count(*) as cnt')
                ->groupBy('weeks.course_id')
                ->pluck('cnt', 'course_id');

            // Grade items agrupados por curso
            $itemsByCourse = GradeItem::whereIn('course_id', $courseIds)
                ->get()
                ->groupBy('course_id');

            // Todos los student_ids activos por curso (para grades)
            $enrollmentsByCourse = DB::table('enrollments')
                ->whereIn('course_id', $courseIds)
                ->where('status', 'active')
                ->select('course_id', 'user_id')
                ->get()
                ->groupBy('course_id');

            // Todas las notas de todos los ítems de todos los cursos
            $allItemIds = $itemsByCourse->flatten()->pluck('id');
            $allGradesByItem = $allItemIds->isNotEmpty()
                ? Grade::whereIn('grade_item_id', $allItemIds)->whereNotNull('score')->get()->groupBy('grade_item_id')
                : collect();

            // ── Construir reporte por curso (solo aritmética en PHP) ──────
            $courseReports = $courses->map(function (Course $course) use (
                $studentsByCourse, $weeksByCourse, $materialsByCourse,
                $tasksByCourse, $itemsByCourse, $enrollmentsByCourse, $allGradesByItem
            ) {
                $id             = $course->id;
                $activeStudents = (int) ($studentsByCourse[$id] ?? 0);
                $items          = $itemsByCourse[$id] ?? collect();
                $studentIds     = ($enrollmentsByCourse[$id] ?? collect())->pluck('user_id');

                // Calcular promedio usando notas ya cargadas en memoria
                $avgData = $this->courseAverageFromMemory($items, $studentIds, $allGradesByItem);

                return [
                    'course'          => $course,
                    'active_students' => $activeStudents,
                    'weeks'           => (int) ($weeksByCourse[$id] ?? 0),
                    'materials'       => (int) ($materialsByCourse[$id] ?? 0),
                    'tasks'           => (int) ($tasksByCourse[$id] ?? 0),
                    'grade_items'     => $items->count(),
                    'average'         => $avgData['average'],
                    'approval_rate'   => $avgData['approval_rate'],
                ];
            });

            $semesterStats = [
                'courses'     => $courses->count(),
                'teachers'    => $courses->pluck('teacher_id')->filter()->unique()->count(),
                'enrollments' => $studentsByCourse->sum(),
                'materials'   => $courseReports->sum('materials'),
                'tasks'       => $courseReports->sum('tasks'),
            ];
        }

        return view('admin.reports.index', compact(
            'semesters', 'semester', 'globalStats', 'semesterStats', 'courseReports'
        ));
    }

    /**
     * Vista limpia para imprimir / exportar a PDF.
     * Misma lógica bulk que index().
     */
    public function print(Request $request): View
    {
        $semesters      = Semester::orderByDesc('year')->orderByRaw("FIELD(period,'II','I')")->get();
        $activeSemester = Semester::getActive();
        $semesterId     = $request->input('semester_id', $activeSemester?->id);
        $semester       = $semesterId ? Semester::find($semesterId) : null;
        $courseReports  = collect();

        if ($semester) {
            $courses   = Course::where('semester_id', $semester->id)->with('teacher')->get();
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

            $itemsByCourse = GradeItem::whereIn('course_id', $courseIds)->get()->groupBy('course_id');

            $enrollmentsByCourse = DB::table('enrollments')
                ->whereIn('course_id', $courseIds)->where('status', 'active')
                ->select('course_id', 'user_id')->get()->groupBy('course_id');

            $allItemIds = $itemsByCourse->flatten()->pluck('id');
            $allGradesByItem = $allItemIds->isNotEmpty()
                ? Grade::whereIn('grade_item_id', $allItemIds)->whereNotNull('score')->get()->groupBy('grade_item_id')
                : collect();

            $courseReports = $courses->map(function (Course $course) use (
                $studentsByCourse, $weeksByCourse, $materialsByCourse, $tasksByCourse,
                $itemsByCourse, $enrollmentsByCourse, $allGradesByItem
            ) {
                $id         = $course->id;
                $items      = $itemsByCourse[$id] ?? collect();
                $studentIds = ($enrollmentsByCourse[$id] ?? collect())->pluck('user_id');
                $avgData    = $this->courseAverageFromMemory($items, $studentIds, $allGradesByItem);

                return [
                    'course'          => $course,
                    'active_students' => (int) ($studentsByCourse[$id] ?? 0),
                    'weeks'           => (int) ($weeksByCourse[$id] ?? 0),
                    'materials'       => (int) ($materialsByCourse[$id] ?? 0),
                    'tasks'           => (int) ($tasksByCourse[$id] ?? 0),
                    'grade_items'     => $items->count(),
                    'average'         => $avgData['average'],
                    'approval_rate'   => $avgData['approval_rate'],
                ];
            });
        }

        return view('admin.reports.print', compact('semester', 'courseReports'));
    }

    /**
     * Exporta el reporte del semestre como CSV.
     */
    public function exportCsv(Request $request): Response
    {
        $activeSemester = Semester::getActive();
        $semesterId     = $request->input('semester_id', $activeSemester?->id);
        $semester       = $semesterId ? Semester::find($semesterId) : null;
        $filename       = 'reporte_' . ($semester ? str($semester->name)->slug() : 'general') . '_' . now()->format('Ymd') . '.csv';

        $rows   = [];
        $rows[] = ['Curso', 'Código', 'Docente', 'Alumnos', 'Semanas', 'Materiales', 'Tareas', 'Ítems Notas', 'Promedio', 'Aprobados %'];

        if ($semester) {
            $courses   = Course::where('semester_id', $semester->id)->with('teacher')->get();
            $courseIds = $courses->pluck('id')->toArray();

            $studentsByCourse  = DB::table('enrollments')
                ->whereIn('course_id', $courseIds)->where('status', 'active')
                ->selectRaw('course_id, count(*) as cnt')->groupBy('course_id')
                ->pluck('cnt', 'course_id');

            $weeksByCourse     = DB::table('weeks')
                ->whereIn('course_id', $courseIds)
                ->selectRaw('course_id, count(*) as cnt')->groupBy('course_id')
                ->pluck('cnt', 'course_id');

            $materialsByCourse = DB::table('materials')
                ->join('weeks', 'weeks.id', '=', 'materials.week_id')
                ->whereIn('weeks.course_id', $courseIds)
                ->selectRaw('weeks.course_id, count(*) as cnt')->groupBy('weeks.course_id')
                ->pluck('cnt', 'course_id');

            $tasksByCourse     = DB::table('tasks')
                ->join('weeks', 'weeks.id', '=', 'tasks.week_id')
                ->whereIn('weeks.course_id', $courseIds)
                ->selectRaw('weeks.course_id, count(*) as cnt')->groupBy('weeks.course_id')
                ->pluck('cnt', 'course_id');

            $itemsByCourse     = GradeItem::whereIn('course_id', $courseIds)->get()->groupBy('course_id');
            $enrollmentsByCourse = DB::table('enrollments')
                ->whereIn('course_id', $courseIds)->where('status', 'active')
                ->select('course_id', 'user_id')->get()->groupBy('course_id');

            $allItemIds = $itemsByCourse->flatten()->pluck('id');
            $allGradesByItem = $allItemIds->isNotEmpty()
                ? Grade::whereIn('grade_item_id', $allItemIds)->whereNotNull('score')->get()->groupBy('grade_item_id')
                : collect();

            foreach ($courses as $course) {
                $id         = $course->id;
                $items      = $itemsByCourse[$id] ?? collect();
                $studentIds = ($enrollmentsByCourse[$id] ?? collect())->pluck('user_id');
                $avg        = $this->courseAverageFromMemory($items, $studentIds, $allGradesByItem);

                $rows[] = [
                    $course->name,
                    $course->code,
                    $course->teacher?->name ?? '—',
                    (int) ($studentsByCourse[$id] ?? 0),
                    (int) ($weeksByCourse[$id] ?? 0),
                    (int) ($materialsByCourse[$id] ?? 0),
                    (int) ($tasksByCourse[$id] ?? 0),
                    $items->count(),
                    $avg['average'] !== null ? number_format($avg['average'], 1) : '—',
                    $avg['approval_rate'] !== null ? $avg['approval_rate'] . '%' : '—',
                ];
            }
        }

        return $this->csvResponse($filename, $rows);
    }

    // ── Helpers privados ─────────────────────────────────────────────────────

    /**
     * Calcula promedio y tasa de aprobados usando datos ya cargados en memoria.
     * No ejecuta ninguna query adicional.
     *
     * @param  \Illuminate\Support\Collection  $items          GradeItems del curso
     * @param  \Illuminate\Support\Collection  $studentIds     IDs de alumnos activos
     * @param  \Illuminate\Support\Collection  $gradesByItem   Grade::groupBy('grade_item_id') precargado
     */
    private function courseAverageFromMemory($items, $studentIds, $gradesByItem): array
    {
        if ($items->isEmpty() || $studentIds->isEmpty()) {
            return ['average' => null, 'approval_rate' => null];
        }

        $studentSet = $studentIds->flip(); // O(1) lookup
        $normalized = collect();

        foreach ($items as $item) {
            $itemGrades = $gradesByItem[$item->id] ?? collect();
            foreach ($itemGrades as $grade) {
                if (! isset($studentSet[$grade->user_id])) continue;
                if ($item->max_score <= 0) continue;
                $normalized->push(($grade->score / $item->max_score) * 20.0);
            }
        }

        if ($normalized->isEmpty()) {
            return ['average' => null, 'approval_rate' => null];
        }

        $avg          = round($normalized->avg(), 1);
        $approvedCount = $normalized->filter(fn ($s) => $s >= 11)->count();
        $approvalRate  = round($approvedCount / $normalized->count() * 100);

        return ['average' => $avg, 'approval_rate' => $approvalRate];
    }

    /**
     * Genera una Response de descarga CSV.
     */
    private function csvResponse(string $filename, array $rows): Response
    {
        $csv = '';
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
