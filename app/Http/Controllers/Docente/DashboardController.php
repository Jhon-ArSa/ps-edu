<?php

namespace App\Http\Controllers\Docente;

use App\Http\Controllers\Controller;
use App\Models\Announcement;

class DashboardController extends Controller
{
    public function index()
    {
        $user   = auth()->user();
        $courses = $user->coursesTaught()
            ->withCount(['enrollments as active_students' => fn($q) => $q->where('status', 'active')])
            ->withCount('weeks')
            ->latest()
            ->get();

        $latestAnnouncements = Announcement::published()
            ->forRole('docente')
            ->latest('published_at')
            ->take(3)
            ->get();

        return view('docente.dashboard', compact('courses', 'latestAnnouncements'));
    }
}
