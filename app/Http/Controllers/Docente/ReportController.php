<?php

namespace App\Http\Controllers\Docente;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\GradeItem;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Reporte detallado del curso: promedio por alumno, estado por ítem.
     */
    public function show(Request $request, Course $course): View
    {
        $this->authorize('manage', $course);

        $students = $course->students()
            ->with('alumnoProfile')
            ->orderBy('users.name')
            ->get();

        $items = $course->gradeItems;

        // Cargar todas las notas del curso de una sola consulta
        $grades = Grade::whereIn('grade_item_id', $items->pluck('id'))
            ->whereIn('user_id', $students->pluck('id'))
            ->get();

        // Mapear [item_id][user_id] => Grade
        $gradesMap = [];
        foreach ($grades as $grade) {
            $gradesMap[$grade->grade_item_id][$grade->user_id] = $grade;
        }

        // Construir la tabla: por alumno → stats
        $totalWeight = $items->sum('weight');
        $useWeighted = $totalWeight > 0;

        $studentRows = $students->map(function ($student) use ($items, $gradesMap, $useWeighted) {
            $wSum = 0; $wW = 0; $sSum = 0; $sC = 0;
            $scoresRaw = [];

            foreach ($items as $item) {
                $grade = $gradesMap[$item->id][$student->id] ?? null;
                $score = $grade?->score;
                $scoresRaw[$item->id] = $score;

                if ($score === null) continue;
                $norm = ($score / $item->max_score) * 20.0;
                if ($useWeighted && $item->weight > 0) { $wSum += $norm * $item->weight; $wW += $item->weight; }
                $sSum += $norm; $sC++;
            }

            $average = null;
            if ($useWeighted && $wW > 0)  $average = round($wSum / $wW, 1);
            elseif ($sC > 0)               $average = round($sSum / $sC, 1);

            return [
                'student'    => $student,
                'scores'     => $scoresRaw,   // item_id → score|null
                'average'    => $average,
                'approved'   => $average !== null && $average >= 11,
                'graded_pct' => $items->count() > 0 ? round($sC / $items->count() * 100) : 0,
            ];
        });

        // Estadísticas globales del curso
        $scored   = $studentRows->filter(fn ($r) => $r['average'] !== null);
        $approved = $scored->filter(fn ($r) => $r['approved']);

        $courseStats = [
            'total'         => $students->count(),
            'scored'        => $scored->count(),
            'approved'      => $approved->count(),
            'failed'        => $scored->count() - $approved->count(),
            'average'       => $scored->count() > 0
                                ? round($scored->avg(fn ($r) => $r['average']), 1)
                                : null,
            'approval_rate' => $scored->count() > 0
                                ? round($approved->count() / $scored->count() * 100)
                                : null,
        ];

        // Juan — resumen de entregas por alumno (preparado, se activa cuando la tabla existe)
        $submissionStats = $this->submissionStats($course, $students->pluck('id'));

        // Jhon — resumen de intentos de evaluación por alumno
        $attemptStats = $this->attemptStats($course, $students->pluck('id'));

        return view('docente.reports.show', compact(
            'course', 'items', 'studentRows', 'gradesMap',
            'courseStats', 'submissionStats', 'attemptStats'
        ));
    }

    /**
     * Vista limpia para imprimir / guardar como PDF desde el navegador.
     */
    public function print(Course $course): View
    {
        $this->authorize('manage', $course);

        $students  = $course->students()->with('alumnoProfile')->orderBy('users.name')->get();
        $items     = $course->gradeItems;

        $grades   = Grade::whereIn('grade_item_id', $items->pluck('id'))
            ->whereIn('user_id', $students->pluck('id'))
            ->get();

        $gradesMap = [];
        foreach ($grades as $grade) {
            $gradesMap[$grade->grade_item_id][$grade->user_id] = $grade;
        }

        $totalWeight = $items->sum('weight');
        $useWeighted = $totalWeight > 0;

        $studentRows = $students->map(function ($student) use ($items, $gradesMap, $useWeighted) {
            $wSum = 0; $wW = 0; $sSum = 0; $sC = 0;
            $scoresRaw = [];
            foreach ($items as $item) {
                $grade = $gradesMap[$item->id][$student->id] ?? null;
                $scoresRaw[$item->id] = $grade?->score;
                if ($grade?->score === null) continue;
                $norm = ($grade->score / $item->max_score) * 20.0;
                if ($useWeighted && $item->weight > 0) { $wSum += $norm * $item->weight; $wW += $item->weight; }
                $sSum += $norm; $sC++;
            }
            $average = null;
            if ($useWeighted && $wW > 0) $average = round($wSum / $wW, 1);
            elseif ($sC > 0)             $average = round($sSum / $sC, 1);

            return ['student' => $student, 'scores' => $scoresRaw, 'average' => $average];
        });

        return view('docente.reports.print', compact('course', 'items', 'studentRows'));
    }

    /**
     * Descarga la libreta del curso como CSV.
     */
    public function exportCsv(Course $course): Response
    {
        $this->authorize('manage', $course);

        $students  = $course->students()->with('alumnoProfile')->orderBy('users.name')->get();
        $items     = $course->gradeItems;

        $grades   = Grade::whereIn('grade_item_id', $items->pluck('id'))
            ->whereIn('user_id', $students->pluck('id'))
            ->get();

        $gradesMap = [];
        foreach ($grades as $grade) {
            $gradesMap[$grade->grade_item_id][$grade->user_id] = $grade;
        }

        $totalWeight = $items->sum('weight');
        $useWeighted = $totalWeight > 0;

        // Cabecera
        $header  = ['Alumno', 'Código'];
        foreach ($items as $item) {
            $header[] = $item->name . ' (/' . $item->max_score . ')';
        }
        $header[] = 'Promedio';
        $header[] = 'Estado';

        $rows = [$header];

        foreach ($students as $student) {
            $wSum = 0; $wW = 0; $sSum = 0; $sC = 0;
            $row = [
                $student->name,
                $student->alumnoProfile?->student_code ?? '—',
            ];
            foreach ($items as $item) {
                $grade  = $gradesMap[$item->id][$student->id] ?? null;
                $score  = $grade?->score;
                $row[]  = $score !== null ? number_format($score, 1) : '';
                if ($score !== null) {
                    $norm = ($score / $item->max_score) * 20.0;
                    if ($useWeighted && $item->weight > 0) { $wSum += $norm * $item->weight; $wW += $item->weight; }
                    $sSum += $norm; $sC++;
                }
            }
            $avg    = null;
            if ($useWeighted && $wW > 0) $avg = round($wSum / $wW, 1);
            elseif ($sC > 0)             $avg = round($sSum / $sC, 1);

            $row[] = $avg !== null ? number_format($avg, 1) : '';
            $row[] = $avg === null ? '' : ($avg >= 11 ? 'Aprobado' : 'Desaprobado');
            $rows[] = $row;
        }

        $filename = 'notas_' . str($course->code)->slug() . '_' . now()->format('Ymd') . '.csv';
        return $this->csvResponse($filename, $rows);
    }

    // ── Puntos de integración Juan/Jhon ──────────────────────────────────────

    /**
     * Cuenta entregas y calificadas por alumno.
     * JUAN — intégrate aquí: suma se activa automáticamente cuando la tabla `submissions` exista.
     */
    private function submissionStats(Course $course, $studentIds): array
    {
        try {
            if (! \Schema::hasTable('submissions') || ! \Schema::hasTable('tasks')) {
                return ['available' => false];
            }

            $taskIds = DB::table('tasks')
                ->join('weeks', 'weeks.id', '=', 'tasks.week_id')
                ->where('weeks.course_id', $course->id)
                ->pluck('tasks.id');

            if ($taskIds->isEmpty()) return ['available' => true, 'submitted' => 0, 'graded' => 0];

            $submitted = DB::table('submissions')
                ->whereIn('task_id', $taskIds)
                ->whereIn('user_id', $studentIds)
                ->count();

            $graded = DB::table('submissions')
                ->whereIn('task_id', $taskIds)
                ->whereIn('user_id', $studentIds)
                ->whereNotNull('score')
                ->count();

            $total = $taskIds->count() * $studentIds->count();

            return [
                'available'      => true,
                'submitted'      => $submitted,
                'graded'         => $graded,
                'total_expected' => $total,
                'submission_pct' => $total > 0 ? round($submitted / $total * 100) : 0,
                'graded_pct'     => $total > 0 ? round($graded / $total * 100) : 0,
            ];
        } catch (\Throwable) {
            return ['available' => false];
        }
    }

    /**
     * Cuenta intentos de evaluación.
     * JHON — intégrate aquí: se activa cuando la tabla `evaluation_attempts` exista.
     */
    private function attemptStats(Course $course, $studentIds): array
    {
        try {
            if (! \Schema::hasTable('evaluation_attempts') || ! \Schema::hasTable('evaluations')) {
                return ['available' => false];
            }

            $evalIds = DB::table('evaluations')
                ->join('weeks', 'weeks.id', '=', 'evaluations.week_id')
                ->where('weeks.course_id', $course->id)
                ->pluck('evaluations.id');

            if ($evalIds->isEmpty()) return ['available' => true, 'completed' => 0, 'total' => 0];

            $completed = DB::table('evaluation_attempts')
                ->whereIn('evaluation_id', $evalIds)
                ->whereIn('user_id', $studentIds)
                ->whereNotNull('submitted_at')
                ->count();

            return [
                'available' => true,
                'completed' => $completed,
                'total'     => $evalIds->count() * $studentIds->count(),
            ];
        } catch (\Throwable) {
            return ['available' => false];
        }
    }

    // ── Helper CSV ───────────────────────────────────────────────────────────

    private function csvResponse(string $filename, array $rows): Response
    {
        $csv = "\xEF\xBB\xBF"; // BOM para que Excel abra correctamente con UTF-8
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
