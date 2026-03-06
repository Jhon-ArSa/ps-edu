<?php

namespace App\Http\Controllers\Alumno;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;

class CourseController extends Controller
{
    public function show(Course $course)
    {
        $user = auth()->user();

        $enrollment = Enrollment::where('course_id', $course->id)
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->firstOrFail();

        $course->load(['teacher', 'weeks.materials']);

        return view('alumno.courses.show', compact('course', 'enrollment'));
    }
}
