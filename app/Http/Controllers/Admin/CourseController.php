<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $query = Course::with('teacher');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('code', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $courses = $query->latest()->paginate(15)->withQueryString();
        return view('admin.courses.index', compact('courses'));
    }

    public function create()
    {
        $teachers = User::where('role', 'docente')->where('status', true)->get();
        return view('admin.courses.create', compact('teachers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'code'       => 'required|string|max:30|unique:courses,code',
            'description'=> 'nullable|string',
            'teacher_id' => 'required|exists:users,id',
            'program'    => 'nullable|string|max:255',
            'cycle'      => 'nullable|integer|min:1|max:10',
            'year'       => 'nullable|integer|min:2000|max:2100',
            'semester'   => 'nullable|in:I,II',
            'status'     => 'required|in:active,inactive',
        ]);

        Course::create($validated);
        return redirect()->route('admin.courses.index')
            ->with('success', 'Curso creado exitosamente.');
    }

    public function show(Course $course)
    {
        $course->load(['teacher', 'enrollments.student', 'weeks.materials']);
        return view('admin.courses.show', compact('course'));
    }

    public function edit(Course $course)
    {
        $teachers = User::where('role', 'docente')->where('status', true)->get();
        return view('admin.courses.edit', compact('course', 'teachers'));
    }

    public function update(Request $request, Course $course)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'code'       => 'required|string|max:30|unique:courses,code,' . $course->id,
            'description'=> 'nullable|string',
            'teacher_id' => 'required|exists:users,id',
            'program'    => 'nullable|string|max:255',
            'cycle'      => 'nullable|integer|min:1|max:10',
            'year'       => 'nullable|integer|min:2000|max:2100',
            'semester'   => 'nullable|in:I,II',
            'status'     => 'required|in:active,inactive',
        ]);

        $course->update($validated);
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
