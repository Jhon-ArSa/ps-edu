<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Semester;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SemesterController extends Controller
{
    public function index(Request $request)
    {
        $query = Semester::withCount([
            'courses',
            'courses as active_courses_count' => fn($q) => $q->where('status', 'active'),
        ]);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        $semesters = $query->reverseChronological()->paginate(12)->withQueryString();

        // Available years for filter
        $years = Semester::selectRaw('DISTINCT year')->orderByDesc('year')->pluck('year');

        // Active semester
        $activeSemester = Semester::getActive();

        // Timeline data: all semesters chronologically for the visual timeline
        $timeline = Semester::withCount('courses')
            ->chronological()
            ->get()
            ->groupBy('year');

        return view('admin.semesters.index', compact('semesters', 'years', 'activeSemester', 'timeline'));
    }

    public function create()
    {
        // Suggest next semester name/year
        $lastSemester = Semester::reverseChronological()->first();
        $suggestedYear = $lastSemester ? $lastSemester->year : (int) date('Y');
        $suggestedPeriod = $lastSemester && $lastSemester->period === 'I' ? 'II' : 'I';
        if ($lastSemester && $lastSemester->period === 'II') {
            $suggestedYear++;
        }

        return view('admin.semesters.create', compact('suggestedYear', 'suggestedPeriod'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'year'        => 'required|integer|min:2020|max:2100',
            'period'      => 'required|in:I,II',
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after:start_date',
            'description' => 'nullable|string|max:500',
            'status'      => 'required|in:planned,active,closed',
        ]);

        $validated['name'] = $validated['year'] . '-' . $validated['period'];

        // Check uniqueness
        if (Semester::where('year', $validated['year'])->where('period', $validated['period'])->exists()) {
            return back()->withInput()->with('error', 'Ya existe un semestre para ' . $validated['name'] . '.');
        }

        // If setting as active, deactivate others in a transaction
        if ($validated['status'] === 'active') {
            DB::transaction(function () use ($validated) {
                Semester::where('status', 'active')->update(['status' => 'planned']);
                Semester::create($validated);
            });
            Semester::clearActiveCache();
        } else {
            Semester::create($validated);
        }

        return redirect()->route('admin.semesters.index')
            ->with('success', 'Semestre ' . $validated['name'] . ' creado exitosamente.');
    }

    public function show(Semester $semester)
    {
        $semester->loadCount([
            'courses',
            'courses as active_courses_count' => fn($q) => $q->where('status', 'active'),
        ]);

        $courses = $semester->courses()
            ->with('teacher')
            ->withCount(['enrollments as students_count' => fn($q) => $q->where('status', 'active')])
            ->orderBy('name')
            ->get();

        $stats = [
            'total_courses'     => $courses->count(),
            'active_courses'    => $courses->where('status', 'active')->count(),
            'total_students'    => $courses->sum('students_count'),
            'total_teachers'    => $courses->pluck('teacher_id')->unique()->count(),
        ];

        return view('admin.semesters.show', compact('semester', 'courses', 'stats'));
    }

    public function edit(Semester $semester)
    {
        return view('admin.semesters.edit', compact('semester'));
    }

    public function update(Request $request, Semester $semester)
    {
        $validated = $request->validate([
            'year'        => 'required|integer|min:2020|max:2100',
            'period'      => 'required|in:I,II',
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after:start_date',
            'description' => 'nullable|string|max:500',
            'status'      => 'required|in:planned,active,closed',
        ]);

        $validated['name'] = $validated['year'] . '-' . $validated['period'];

        // Check uniqueness excluding current
        $exists = Semester::where('year', $validated['year'])
            ->where('period', $validated['period'])
            ->where('id', '!=', $semester->id)
            ->exists();

        if ($exists) {
            return back()->withInput()->with('error', 'Ya existe otro semestre para ' . $validated['name'] . '.');
        }

        // If setting as active, deactivate others
        if ($validated['status'] === 'active' && $semester->status !== 'active') {
            DB::transaction(function () use ($semester, $validated) {
                Semester::where('status', 'active')
                    ->where('id', '!=', $semester->id)
                    ->update(['status' => 'closed']);
                $semester->update($validated);
            });
        } else {
            $semester->update($validated);
        }

        Semester::clearActiveCache();

        return redirect()->route('admin.semesters.index')
            ->with('success', 'Semestre ' . $validated['name'] . ' actualizado exitosamente.');
    }

    public function destroy(Semester $semester)
    {
        if ($semester->courses()->exists()) {
            return back()->with('error', 'No se puede eliminar un semestre que tiene cursos asignados.');
        }

        $name = $semester->name;
        $semester->delete();
        Semester::clearActiveCache();

        return redirect()->route('admin.semesters.index')
            ->with('success', 'Semestre ' . $name . ' eliminado exitosamente.');
    }

    /**
     * Activate a semester (AJAX or standard POST)
     */
    public function activate(Semester $semester)
    {
        DB::transaction(function () use ($semester) {
            Semester::where('status', 'active')->update(['status' => 'closed']);
            $semester->update(['status' => 'active']);
        });

        Semester::clearActiveCache();

        return back()->with('success', 'Semestre ' . $semester->name . ' activado. Los semestres anteriores fueron cerrados.');
    }

    /**
     * Close a semester
     */
    public function close(Semester $semester)
    {
        $semester->update(['status' => 'closed']);
        Semester::clearActiveCache();

        return back()->with('success', 'Semestre ' . $semester->name . ' cerrado exitosamente.');
    }
}
