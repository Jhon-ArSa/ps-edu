<?php

namespace App\Http\Controllers\Alumno;

use App\Http\Controllers\Controller;
use App\Models\Announcement;

class IntranetController extends Controller
{
    public function index()
    {
        $announcements = Announcement::published()
            ->forRole('alumno')
            ->latest('published_at')
            ->paginate(10);

        return view('alumno.intranet', compact('announcements'));
    }
}