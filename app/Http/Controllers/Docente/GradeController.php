<?php

namespace App\Http\Controllers\Docente;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Grade;
use App\Models\GradeItem;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class GradeController extends Controller
{
    /**
     * Libreta de calificaciones del curso.
     * Retorna una tabla: filas = alumnos, columnas = grade_items.
     */
    public function index(Course $course): View
    {
        $this->authorize('manage', $course);

        // Alumnos matriculados activos
        $students = $course->students()
            ->with('alumnoProfile')
            ->orderBy('users.name')
            ->get();

        // Ítems de calificación del curso
        $items = $course->gradeItems()->get();

        // Carga pesada de notas: pivot grade_item_id → user_id → Grade
        $gradesRaw = Grade::whereIn('grade_item_id', $items->pluck('id'))
            ->whereIn('user_id', $students->pluck('id'))
            ->get();

        // Mapear para acceso rápido: $gradesMap[$itemId][$userId] = Grade
        $gradesMap = [];
        foreach ($gradesRaw as $grade) {
            $gradesMap[$grade->grade_item_id][$grade->user_id] = $grade;
        }

        return view('docente.grades.index', compact('course', 'students', 'items', 'gradesMap'));
    }

    // ── Gestión de ítems ─────────────────────────────────────────────────────

    /**
     * Crea un ítem manual (participación, oral, final, otro).
     */
    public function storeItem(Request $request, Course $course): RedirectResponse
    {
        $this->authorize('manage', $course);

        $data = $request->validate([
            'name'      => 'required|string|max:80',
            'type'      => 'required|in:participation,oral,final,other',
            'weight'    => 'nullable|numeric|min:0|max:100',
            'max_score' => 'nullable|numeric|min:1|max:100',
        ]);

        $data['course_id'] = $course->id;
        $data['weight']    = $data['weight'] ?? 0;
        $data['max_score'] = $data['max_score'] ?? 20;
        $data['order']     = GradeItem::where('course_id', $course->id)->max('order') + 1;

        GradeItem::create($data);

        return back()->with('success', 'Ítem de calificación agregado.');
    }

    /**
     * Actualiza nombre / peso de un ítem.
     */
    public function updateItem(Request $request, Course $course, GradeItem $gradeItem): RedirectResponse
    {
        $this->authorize('manage', $course);
        abort_unless($gradeItem->course_id === $course->id, 404);

        $data = $request->validate([
            'name'      => 'required|string|max:80',
            'weight'    => 'nullable|numeric|min:0|max:100',
            'max_score' => 'nullable|numeric|min:1|max:100',
        ]);

        $gradeItem->update([
            'name'      => $data['name'],
            'weight'    => $data['weight'] ?? $gradeItem->weight,
            'max_score' => $data['max_score'] ?? $gradeItem->max_score,
        ]);

        return back()->with('success', 'Ítem actualizado.');
    }

    /**
     * Elimina un ítem (sólo si es manual).
     */
    public function destroyItem(Course $course, GradeItem $gradeItem): RedirectResponse
    {
        $this->authorize('manage', $course);
        abort_unless($gradeItem->course_id === $course->id, 404);
        abort_unless($gradeItem->isManual(), 403, 'Solo se pueden eliminar ítems manuales.');

        $gradeItem->delete();

        return back()->with('success', 'Ítem eliminado.');
    }

    // ── Ingreso/edición de nota individual (AJAX) ────────────────────────────

    /**
     * Guarda una nota. Devuelve JSON con la nota, el nuevo promedio del alumno
     * en el curso y el estado de color.
     */
    public function updateGrade(
        Request    $request,
        Course     $course,
        GradeItem  $gradeItem,
        User       $user
    ): JsonResponse {
        $this->authorize('manage', $course);
        abort_unless($gradeItem->course_id === $course->id, 404);
        abort_unless($gradeItem->isManual(), 403, 'Este ítem no admite edición manual.');

        // Verificar que el alumno esté matriculado activamente
        abort_unless(
            $course->students()->where('users.id', $user->id)->exists(),
            404,
            'El alumno no está matriculado en este curso.'
        );

        $data = $request->validate([
            'score'    => 'required|numeric|min:0|max:' . $gradeItem->max_score,
            'comments' => 'nullable|string|max:500',
        ]);

        $grade = Grade::updateOrCreate(
            ['grade_item_id' => $gradeItem->id, 'user_id' => $user->id],
            [
                'score'      => $data['score'],
                'comments'   => $data['comments'] ?? null,
                'graded_by'  => auth()->id(),
                'graded_at'  => now(),
            ]
        );

        // Calcular el promedio actualizado del alumno en este curso
        $average = $this->calcAverage($course, $user->id);

        return response()->json([
            'success'         => true,
            'score'           => $grade->score,
            'average'         => $average,
            'score_color'     => $this->scoreColor($grade->score, $gradeItem->max_score),
            'average_color'   => $average !== null ? $this->scoreColor($average, 20.0) : 'gray',
        ]);
    }

    // ── Helpers privados ─────────────────────────────────────────────────────

    /**
     * Promedio ponderado (o simple si todos los pesos son 0).
     */
    private function calcAverage(Course $course, int $userId): ?float
    {
        $items = $course->gradeItems;

        if ($items->isEmpty()) return null;

        // Cargar notas del alumno
        $grades = Grade::whereIn('grade_item_id', $items->pluck('id'))
            ->where('user_id', $userId)
            ->get()
            ->keyBy('grade_item_id');

        $totalWeight    = $items->sum('weight');
        $useWeighted    = $totalWeight > 0;
        $weightedSum    = 0;
        $weightSum      = 0;
        $simpleSum      = 0;
        $simpleCount    = 0;

        foreach ($items as $item) {
            $grade = $grades->get($item->id);
            if (! $grade || $grade->score === null) continue;

            // Normalizar a escala 0-20
            $normalized = ($grade->score / $item->max_score) * 20.0;

            if ($useWeighted && $item->weight > 0) {
                $weightedSum += $normalized * $item->weight;
                $weightSum   += $item->weight;
            }
            $simpleSum   += $normalized;
            $simpleCount++;
        }

        if ($useWeighted && $weightSum > 0) {
            return round($weightedSum / $weightSum, 1);
        }

        if ($simpleCount === 0) return null;
        return round($simpleSum / $simpleCount, 1);
    }

    private function scoreColor(float $score, float $maxScore): string
    {
        $normalized = ($score / $maxScore) * 20.0;
        if ($normalized < 11) return 'red';
        if ($normalized < 14) return 'amber';
        return 'green';
    }
}
