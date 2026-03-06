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

        return view('alumno.dashboard', compact('enrollments', 'latestAnnouncements'));
    }
}
