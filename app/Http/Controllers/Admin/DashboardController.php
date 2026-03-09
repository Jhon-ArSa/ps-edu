<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Announcement;
use App\Models\Semester;

class DashboardController extends Controller
{
    public function index()
    {
        $activeSemester = Semester::getActive();

        $stats = [
            'total_students'       => User::where('role', 'alumno')->count(),
            'total_teachers'       => User::where('role', 'docente')->count(),
            'total_courses'        => Course::count(),
            'total_enrollments'    => Enrollment::where('status', 'active')->count(),
            'active_courses'       => Course::where('status', 'active')->count(),
            'recent_users'         => User::latest()->take(5)->get(),
            'recent_announcements' => Announcement::published()->latest('published_at')->take(5)->get(),
        ];

        return view('admin.dashboard', compact('stats', 'activeSemester'));
    }
}
