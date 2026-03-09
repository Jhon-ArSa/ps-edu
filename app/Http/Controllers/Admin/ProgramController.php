<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\Mention;
use App\Models\CurriculumItem;
use App\Models\User;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    public function index(Request $request)
    {
        $query = Program::with('coordinator')->withCount([
            'courses',
            'courses as active_courses_count' => fn ($q) => $q->where('status', 'active'),
            'mentions',
        ]);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('degree_type')) {
            $query->where('degree_type', $request->degree_type);
        }

        $programs = $query->orderBy('name')->paginate(12)->withQueryString();

        $stats = [
            'total'    => Program::count(),
            'active'   => Program::where('status', 'active')->count(),
            'inactive' => Program::where('status', 'inactive')->count(),
            'courses'  => Program::withCount('courses')->get()->sum('courses_count'),
        ];

        return view('admin.programs.index', compact('programs', 'stats'));
    }

    public function create()
    {
        $coordinators = User::where('role', 'docente')
            ->where('status', true)
            ->orderBy('name')
            ->get();

        return view('admin.programs.create', compact('coordinators'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'               => 'required|string|max:255',
            'code'               => 'required|string|max:20|unique:programs,code',
            'degree_type'        => 'required|in:maestria,doctorado,segunda_especialidad,diplomado',
            'description'        => 'nullable|string|max:2000',
            'duration_semesters' => 'required|integer|min:1|max:20',
            'has_propedeutic'    => 'boolean',
            'total_credits'      => 'nullable|integer|min:1|max:500',
            'resolution'         => 'nullable|string|max:255',
            'coordinator_id'     => 'nullable|exists:users,id',
            'status'             => 'required|in:active,inactive',
        ]);

        $validated['has_propedeutic'] = $request->boolean('has_propedeutic');

        Program::create($validated);

        return redirect()->route('admin.programs.index')
            ->with('success', 'Programa "' . $validated['name'] . '" creado exitosamente.');
    }

    public function show(Program $program)
    {
        $program->load(['coordinator', 'mentions.curriculumItems']);

        // Shared curriculum items (no mention, e.g. propedéutico)
        $sharedCurriculum = $program->curriculumItems()
            ->whereNull('mention_id')
            ->orderBy('semester_number')
            ->orderBy('order')
            ->get()
            ->groupBy('semester_number');

        $courses = $program->courses()
            ->with(['teacher', 'semesterPeriod'])
            ->withCount(['enrollments as students_count' => fn ($q) => $q->where('status', 'active')])
            ->orderBy('name')
            ->get();

        $stats = [
            'total_courses'  => $courses->count(),
            'active_courses' => $courses->where('status', 'active')->count(),
            'total_students' => $courses->sum('students_count'),
            'total_teachers' => $courses->pluck('teacher_id')->filter()->unique()->count(),
            'mentions'       => $program->mentions->count(),
        ];

        return view('admin.programs.show', compact('program', 'courses', 'stats', 'sharedCurriculum'));
    }

    public function edit(Program $program)
    {
        $coordinators = User::where('role', 'docente')
            ->where('status', true)
            ->orderBy('name')
            ->get();

        return view('admin.programs.edit', compact('program', 'coordinators'));
    }

    public function update(Request $request, Program $program)
    {
        $validated = $request->validate([
            'name'               => 'required|string|max:255',
            'code'               => 'required|string|max:20|unique:programs,code,' . $program->id,
            'degree_type'        => 'required|in:maestria,doctorado,segunda_especialidad,diplomado',
            'description'        => 'nullable|string|max:2000',
            'duration_semesters' => 'required|integer|min:1|max:20',
            'has_propedeutic'    => 'boolean',
            'total_credits'      => 'nullable|integer|min:1|max:500',
            'resolution'         => 'nullable|string|max:255',
            'coordinator_id'     => 'nullable|exists:users,id',
            'status'             => 'required|in:active,inactive',
        ]);

        $validated['has_propedeutic'] = $request->boolean('has_propedeutic');

        $program->update($validated);

        return redirect()->route('admin.programs.index')
            ->with('success', 'Programa "' . $validated['name'] . '" actualizado exitosamente.');
    }

    public function destroy(Program $program)
    {
        if ($program->courses()->exists()) {
            return back()->with('error', 'No se puede eliminar un programa que tiene cursos asignados. Reasigne o elimine los cursos primero.');
        }

        $name = $program->name;
        $program->delete();

        return redirect()->route('admin.programs.index')
            ->with('success', 'Programa "' . $name . '" eliminado exitosamente.');
    }
}
