<?php

namespace App\Http\Controllers\Docente;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\User;
use App\Models\Enrollment;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function search(Request $request, Course $course)
    {
        $this->authorize('manage', $course);

        $request->validate(['q' => 'required|string|min:2']);

        $enrolledIds = $course->enrollments()->pluck('user_id');

        $students = User::where('role', 'alumno')
            ->where('status', true)
            ->whereNotIn('id', $enrolledIds)
            ->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->q . '%')
                  ->orWhere('email', 'like', '%' . $request->q . '%')
                  ->orWhere('dni', 'like', '%' . $request->q . '%');
            })
            ->take(10)
            ->get(['id', 'name', 'email', 'dni']);

        return response()->json($students);
    }

    public function enroll(Request $request, Course $course)
    {
        $this->authorize('manage', $course);

        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $student = User::findOrFail($request->user_id);

        if (!$student->isAlumno()) {
            return back()->with('error', 'Solo se pueden matricular alumnos.');
        }

        $existing = Enrollment::where('course_id', $course->id)
            ->where('user_id', $request->user_id)
            ->first();

        if ($existing) {
            if ($existing->status === 'dropped') {
                $existing->update(['status' => 'active', 'enrolled_at' => now()]);
                return back()->with('success', 'Alumno re-matriculado exitosamente.');
            }
            return back()->with('error', 'El alumno ya está matriculado en este curso.');
        }

        Enrollment::create([
            'course_id'   => $course->id,
            'user_id'     => $request->user_id,
            'enrolled_at' => now(),
            'status'      => 'active',
        ]);

        return back()->with('success', 'Alumno matriculado exitosamente.');
    }

    public function unenroll(Course $course, User $student)
    {
        $this->authorize('manage', $course);

        Enrollment::where('course_id', $course->id)
            ->where('user_id', $student->id)
            ->update(['status' => 'dropped']);

        return back()->with('success', 'Alumno dado de baja del curso.');
    }
}
