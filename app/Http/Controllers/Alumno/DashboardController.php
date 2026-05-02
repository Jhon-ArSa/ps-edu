<?php

namespace App\Http\Controllers\Alumno;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Enrollment;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $enrollments = Enrollment::where('user_id', $user->id)
            ->where('status', 'active')
            ->with(['course.teacher', 'course' => fn($q) => $q->withCount('weeks')])
            ->latest()
            ->get();

        $latestAnnouncements = Announcement::published()
            ->forRole('alumno')
            ->latest('published_at')
            ->take(3)
            ->get();

        $userCourseIds = $enrollments->pluck('course_id');

        $popupAnnouncements = Announcement::published()->popup()
            ->where(function ($q) use ($userCourseIds) {
                // Admin popups (no course restriction, targeting all or alumno)
                $q->whereDoesntHave('courses')
                  ->whereDoesntHave('programs')
                  ->where(fn($q2) => $q2->where('target_role', 'all')->orWhere('target_role', 'alumno'));
                // Docente popups targeting this student's courses
                if ($userCourseIds->isNotEmpty()) {
                    $q->orWhereHas('courses', fn($q2) => $q2->whereIn('announcement_courses.course_id', $userCourseIds));
                }
            })
            ->latest('published_at')
            ->get();

        return view('alumno.dashboard', compact('enrollments', 'latestAnnouncements', 'popupAnnouncements'));
    }
}
