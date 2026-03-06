<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Enrollment::with(['student', 'course.teacher'])
            ->latest('enrolled_at');

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student', fn($q) =>
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
            );
        }

        $enrollments = $query->paginate(20)->withQueryString();
        $courses     = Course::orderBy('name')->get(['id', 'name', 'code']);

        return view('admin.enrollments.index', compact('enrollments', 'courses'));
    }

    public function toggle(Enrollment $enrollment)
    {
        $enrollment->status = $enrollment->status === 'active' ? 'inactive' : 'active';
        $enrollment->save();

        return back()->with('success', 'Estado de matrícula actualizado.');
    }
}
