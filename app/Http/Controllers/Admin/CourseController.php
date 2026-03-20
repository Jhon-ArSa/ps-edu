<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Program;
use App\Models\Semester;
use App\Models\User;
use App\Notifications\CourseAssigned;
use App\Notifications\NewEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $query = Course::with(['teacher', 'semesterPeriod']);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('code', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('semester_id')) {
            $query->where('semester_id', $request->semester_id);
        }

        $courses   = $query->latest()->paginate(15)->withQueryString();
        $semesters = Semester::orderByDesc('year')->orderByDesc('period')->get();

        return view('admin.courses.index', compact('courses', 'semesters'));
    }

    public function create()
    {
        $teachers  = User::where('role', 'docente')->where('status', true)->orderBy('name')->get();
        $students  = User::where('role', 'alumno')->where('status', true)->with('alumnoProfile')->orderBy('name')->get();
        $semesters = Semester::orderByDesc('year')->orderByDesc('period')->get();
        $programs  = Program::where('status', 'active')->orderBy('name')->get();

        $studentsJson = $students->map(fn ($s) => [
            'id'   => (string) $s->id,
            'name' => $s->name,
            'email'=> $s->email,
            'dni'  => $s->dni ?? '',
        ])->values();

        return view('admin.courses.create', compact('teachers', 'students', 'semesters', 'programs', 'studentsJson'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'code'       => 'required|string|max:30|unique:courses,code',
            'description'=> 'nullable|string',
            'teacher_id' => 'required|exists:users,id',
            'semester_id'=> 'nullable|exists:semesters,id',
            'program_id' => 'nullable|exists:programs,id',
            'program'    => 'nullable|string|max:255',
            'cycle'      => 'nullable|integer|min:1|max:10',
            'year'       => 'nullable|integer|min:2000|max:2100',
            'semester'   => 'nullable|in:I,II',
            'status'     => 'required|in:active,inactive',
            'students'   => 'nullable|array',
            'students.*' => 'exists:users,id',
        ]);

        $studentIds = $validated['students'] ?? [];
        unset($validated['students']);

        $course = null;
        DB::transaction(function () use ($validated, $studentIds, &$course) {
            $course = Course::create($validated);

            if (!empty($studentIds)) {
                $enrollments = collect($studentIds)->map(fn ($uid) => [
                    'course_id'   => $course->id,
                    'user_id'     => $uid,
                    'enrolled_at' => now(),
                    'status'      => 'active',
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
                Enrollment::insert($enrollments->toArray());
            }
        });

        // Notificar al docente asignado
        $course->teacher?->notify(new CourseAssigned($course->id, $course->name));

        // Notificar a los alumnos matriculados al crear
        if (!empty($studentIds)) {
            User::whereIn('id', $studentIds)->get()
                ->each->notify(new NewEnrollment($course->id, $course->name));
        }

        return redirect()->route('admin.courses.index')
            ->with('success', 'Curso creado exitosamente.');
    }

    public function show(Course $course)
    {
        $course->load(['teacher', 'semesterPeriod', 'programBelongs', 'enrollments.student', 'weeks.materials']);
        return view('admin.courses.show', compact('course'));
    }

    public function edit(Course $course)
    {
        $teachers   = User::where('role', 'docente')->where('status', true)->orderBy('name')->get();
        $students   = User::where('role', 'alumno')->where('status', true)->with('alumnoProfile')->orderBy('name')->get();
        $semesters  = Semester::orderByDesc('year')->orderByDesc('period')->get();
        $programs   = Program::where('status', 'active')->orderBy('name')->get();
        $enrolledIds = $course->enrollments()->where('status', 'active')->pluck('user_id')->map(fn ($id) => (int) $id)->toArray();

        $studentsJson = $students->map(fn ($s) => [
            'id'   => (string) $s->id,
            'name' => $s->name,
            'email'=> $s->email,
            'dni'  => $s->dni ?? '',
        ])->values();

        return view('admin.courses.edit', compact('course', 'teachers', 'students', 'semesters', 'programs', 'enrolledIds', 'studentsJson'));
    }

    public function update(Request $request, Course $course)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'code'       => 'required|string|max:30|unique:courses,code,' . $course->id,
            'description'=> 'nullable|string',
            'teacher_id' => 'required|exists:users,id',
            'semester_id'=> 'nullable|exists:semesters,id',
            'program_id' => 'nullable|exists:programs,id',
            'program'    => 'nullable|string|max:255',
            'cycle'      => 'nullable|integer|min:1|max:10',
            'year'       => 'nullable|integer|min:2000|max:2100',
            'semester'   => 'nullable|in:I,II',
            'status'     => 'required|in:active,inactive',
            'students'   => 'nullable|array',
            'students.*' => 'exists:users,id',
        ]);

        $studentIds = collect($validated['students'] ?? [])->map(fn ($id) => (int) $id);
        unset($validated['students']);

        $oldTeacherId = $course->teacher_id;
        $toAdd        = collect();

        DB::transaction(function () use ($course, $validated, $studentIds, &$toAdd) {
            $course->update($validated);

            $currentEnrolled = $course->enrollments()->where('status', 'active')->pluck('user_id');

            // Drop students that were removed
            $toDrop = $currentEnrolled->diff($studentIds);
            if ($toDrop->isNotEmpty()) {
                $course->enrollments()->whereIn('user_id', $toDrop)->update(['status' => 'dropped']);
            }

            // Add new students
            $toAdd = $studentIds->diff($currentEnrolled);
            foreach ($toAdd as $uid) {
                Enrollment::updateOrCreate(
                    ['course_id' => $course->id, 'user_id' => $uid],
                    ['status' => 'active', 'enrolled_at' => now()]
                );
            }
        });

        // Notificar al nuevo docente si cambió
        if ($oldTeacherId !== $course->teacher_id) {
            $course->teacher?->notify(new CourseAssigned($course->id, $course->name));
        }

        // Notificar a los alumnos recién matriculados
        if ($toAdd->isNotEmpty()) {
            User::whereIn('id', $toAdd->toArray())->get()
                ->each->notify(new NewEnrollment($course->id, $course->name));
        }

        return redirect()->route('admin.courses.index')
            ->with('success', 'Curso actualizado exitosamente.');
    }

    public function destroy(Course $course)
    {
        if ($course->enrollments()->where('status', 'active')->exists()) {
            return back()->with('error', 'No se puede eliminar un curso con alumnos matriculados activos.');
        }

        $course->delete();
        return redirect()->route('admin.courses.index')
            ->with('success', 'Curso eliminado exitosamente.');
    }
}
