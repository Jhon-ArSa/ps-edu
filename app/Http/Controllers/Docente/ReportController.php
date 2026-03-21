<?php

namespace App\Http\Controllers\Docente;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Task;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Reporte detallado del curso: entregas por alumno, estado por tarea.
     */
    public function show(Request $request, Course $course): View
    {
        $this->authorize('manage', $course);

        $students = $course->students()
            ->with('alumnoProfile')
            ->orderBy('users.name')
            ->get();

        // Obtener todas las tareas del curso
        $tasks = Task::join('weeks', 'weeks.id', '=', 'tasks.week_id')
            ->where('weeks.course_id', $course->id)
            ->select('tasks.*', 'weeks.number as week_number')
            ->orderBy('weeks.number')
            ->orderBy('tasks.created_at')
            ->get();

        // Cargar todas las entregas del curso
        $submissions = Submission::whereIn('task_id', $tasks->pluck('id'))
            ->whereIn('user_id', $students->pluck('id'))
            ->get();

        // Mapear [task_id][user_id] => Submission
        $submissionsMap = [];
        foreach ($submissions as $submission) {
            $submissionsMap[$submission->task_id][$submission->user_id] = $submission;
        }

        // Construir la tabla: por alumno → stats de entregas
        $studentRows = $students->map(function ($student) use ($tasks, $submissionsMap) {
            $submitted = 0;
            $reviewed = 0;
            $submissionStatus = [];

            foreach ($tasks as $task) {
                $submission = $submissionsMap[$task->id][$student->id] ?? null;
                $submissionStatus[$task->id] = $submission;

                if ($submission) {
                    $submitted++;
                    if ($submission->isGraded()) {
                        $reviewed++;
                    }
                }
            }

            $submissionPct = $tasks->count() > 0 ? round($submitted / $tasks->count() * 100) : 0;

            return [
                'student'           => $student,
                'submissions'       => $submissionStatus,
                'submitted_count'   => $submitted,
                'reviewed_count'    => $reviewed,
                'submission_pct'    => $submissionPct,
            ];
        });

        // Estadísticas globales del curso
        $totalTasks = $tasks->count();
        $totalExpectedSubmissions = $students->count() * $totalTasks;
        $totalSubmitted = $studentRows->sum('submitted_count');
        $totalReviewed = $studentRows->sum('reviewed_count');

        $courseStats = [
            'total_students'      => $students->count(),
            'total_tasks'         => $totalTasks,
            'submitted'           => $totalSubmitted,
            'reviewed'            => $totalReviewed,
            'pending'             => $totalExpectedSubmissions - $totalSubmitted,
            'submission_rate'     => $totalExpectedSubmissions > 0
                                        ? round($totalSubmitted / $totalExpectedSubmissions * 100)
                                        : 0,
            'review_rate'         => $totalExpectedSubmissions > 0
                                        ? round($totalReviewed / $totalExpectedSubmissions * 100)
                                        : 0,
        ];

        // Resumen adicional por task
        $attemptStats = $this->attemptStats($course, $students->pluck('id'));

        $routePrefix = auth()->user()->role === 'admin' ? 'admin.reports.course' : 'docente.reports';

        return view('docente.reports.show', compact(
            'course', 'tasks', 'studentRows', 'submissionsMap',
            'courseStats', 'attemptStats', 'routePrefix'
        ));
    }

    /**
     * Vista limpia para imprimir / guardar como PDF desde el navegador.
     */
    public function print(Course $course): View
    {
        $this->authorize('manage', $course);

        $students = $course->students()->with('alumnoProfile')->orderBy('users.name')->get();

        // Obtener todas las tareas del curso
        $tasks = Task::join('weeks', 'weeks.id', '=', 'tasks.week_id')
            ->where('weeks.course_id', $course->id)
            ->select('tasks.*', 'weeks.number as week_number')
            ->orderBy('weeks.number')
            ->orderBy('tasks.created_at')
            ->get();

        // Cargar entregas
        $submissions = Submission::whereIn('task_id', $tasks->pluck('id'))
            ->whereIn('user_id', $students->pluck('id'))
            ->get();

        $submissionsMap = [];
        foreach ($submissions as $submission) {
            $submissionsMap[$submission->task_id][$submission->user_id] = $submission;
        }

        $studentRows = $students->map(function ($student) use ($tasks, $submissionsMap) {
            $submissionStatus = [];
            foreach ($tasks as $task) {
                $submissionStatus[$task->id] = $submissionsMap[$task->id][$student->id] ?? null;
            }
            return ['student' => $student, 'submissions' => $submissionStatus];
        });

        return view('docente.reports.print', compact('course', 'tasks', 'studentRows'));
    }

    /**
     * Descarga el reporte del curso como CSV.
     */
    public function exportCsv(Course $course): Response
    {
        $this->authorize('manage', $course);

        $students = $course->students()->with('alumnoProfile')->orderBy('users.name')->get();

        // Obtener todas las tareas del curso
        $tasks = Task::join('weeks', 'weeks.id', '=', 'tasks.week_id')
            ->where('weeks.course_id', $course->id)
            ->select('tasks.*', 'weeks.number as week_number')
            ->orderBy('weeks.number')
            ->orderBy('tasks.created_at')
            ->get();

        $submissions = Submission::whereIn('task_id', $tasks->pluck('id'))
            ->whereIn('user_id', $students->pluck('id'))
            ->get();

        $submissionsMap = [];
        foreach ($submissions as $submission) {
            $submissionsMap[$submission->task_id][$submission->user_id] = $submission;
        }

        // Cabecera
        $header = ['Alumno', 'Código'];
        foreach ($tasks as $task) {
            $header[] = 'S' . $task->week_number . ': ' . \Str::limit($task->title, 20);
        }
        $header[] = 'Entregas';
        $header[] = 'Revisadas';

        $rows = [$header];

        foreach ($students as $student) {
            $submitted = 0;
            $reviewed = 0;
            $row = [
                $student->name,
                $student->alumnoProfile?->student_code ?? '—',
            ];

            foreach ($tasks as $task) {
                $submission = $submissionsMap[$task->id][$student->id] ?? null;
                if ($submission) {
                    $submitted++;
                    if ($submission->isGraded()) {
                        $reviewed++;
                        $row[] = 'Revisada';
                    } else {
                        $row[] = 'Entregada';
                    }
                } else {
                    $row[] = '';
                }
            }

            $row[] = $submitted . '/' . $tasks->count();
            $row[] = $reviewed . '/' . $tasks->count();
            $rows[] = $row;
        }

        $filename = 'entregas_' . str($course->code)->slug() . '_' . now()->format('Ymd') . '.csv';
        return $this->csvResponse($filename, $rows);
    }

    // ── Puntos de integración Juan/Jhon ──────────────────────────────────────

    /**
     * Cuenta entregas y revisadas por alumno.
     */
    private function submissionStats(Course $course, $studentIds): array
    {
        try {
            $taskIds = DB::table('tasks')
                ->join('weeks', 'weeks.id', '=', 'tasks.week_id')
                ->where('weeks.course_id', $course->id)
                ->pluck('tasks.id');

            if ($taskIds->isEmpty()) return ['available' => true, 'submitted' => 0, 'reviewed' => 0];

            $submitted = DB::table('submissions')
                ->whereIn('task_id', $taskIds)
                ->whereIn('user_id', $studentIds)
                ->count();

            $reviewed = DB::table('submissions')
                ->whereIn('task_id', $taskIds)
                ->whereIn('user_id', $studentIds)
                ->where('status', 'graded')
                ->count();

            $total = $taskIds->count() * $studentIds->count();

            return [
                'available'      => true,
                'submitted'      => $submitted,
                'reviewed'       => $reviewed,
                'total_expected' => $total,
                'submission_pct' => $total > 0 ? round($submitted / $total * 100) : 0,
                'review_pct'     => $total > 0 ? round($reviewed / $total * 100) : 0,
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
            $evalIds = DB::table('evaluations')
                ->join('weeks', 'weeks.id', '=', 'evaluations.week_id')
                ->where('weeks.course_id', $course->id)
                ->pluck('evaluations.id');

            if ($evalIds->isEmpty()) return ['available' => true, 'total_attempts' => 0, 'passed' => 0, 'total' => 0];

            $total_attempts = DB::table('evaluation_attempts')
                ->whereIn('evaluation_id', $evalIds)
                ->whereIn('user_id', $studentIds)
                ->whereNotNull('submitted_at')
                ->count();

            $passed = DB::table('evaluation_attempts')
                ->whereIn('evaluation_id', $evalIds)
                ->whereIn('user_id', $studentIds)
                ->where('status', 'graded')
                ->count();

            return [
                'available'      => true,
                'total_attempts' => $total_attempts,
                'passed'         => $passed,
                'total'          => $evalIds->count() * $studentIds->count(),
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
