<?php

namespace App\Http\Controllers\Docente;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Week;
use Illuminate\Http\Request;

class WeekController extends Controller
{
    public function store(Request $request, Course $course)
    {
        $this->authorize('manage', $course);

        $request->validate([
            'title'       => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $nextNumber = ($course->weeks()->max('number') ?? 0) + 1;

        if ($nextNumber > 16) {
            return back()->with('error', 'El curso ya tiene el máximo de 16 semanas.');
        }

        $course->weeks()->create([
            'number'      => $nextNumber,
            'title'       => $request->title,
            'description' => $request->description,
        ]);

        return back()->with('success', 'Semana ' . $nextNumber . ' creada exitosamente.');
    }

    public function update(Request $request, Course $course, Week $week)
    {
        $this->authorize('manage', $course);

        $request->validate([
            'title'       => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $week->update($request->only(['title', 'description']));

        return response()->json(['success' => true, 'message' => 'Semana actualizada.']);
    }

    public function destroy(Course $course, Week $week)
    {
        $this->authorize('manage', $course);
        $week->delete();
        return back()->with('success', 'Semana eliminada exitosamente.');
    }
}
