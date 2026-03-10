<?php

namespace App\Http\Controllers\Alumno;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Grade;
use Illuminate\View\View;

class GradeController extends Controller
{
    /**
     * Muestra las calificaciones del alumno en un curso específico.
     */
    public function show(Course $course): View
    {
        // Verificar matrícula activa
        $enrollment = Enrollment::where('course_id', $course->id)
            ->where('user_id', auth()->id())
            ->where('status', 'active')
            ->firstOrFail();

        $items = $course->gradeItems;

        // Cargar solo las notas del alumno autenticado
        $grades = Grade::whereIn('grade_item_id', $items->pluck('id'))
            ->where('user_id', auth()->id())
            ->get()
            ->keyBy('grade_item_id');

        // Calcular promedio
        $average = $this->calcAverage($items, $grades);

        return view('alumno.grades.index', compact('course', 'items', 'grades', 'average', 'enrollment'));
    }

    // ── Helper ───────────────────────────────────────────────────────────────

    private function calcAverage($items, $grades): ?float
    {
        if ($items->isEmpty()) return null;

        $totalWeight = $items->sum('weight');
        $useWeighted = $totalWeight > 0;
        $weightedSum = 0;
        $weightSum   = 0;
        $simpleSum   = 0;
        $simpleCount = 0;

        foreach ($items as $item) {
            $grade = $grades->get($item->id);
            if (! $grade || $grade->score === null) continue;

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
}
