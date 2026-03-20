<?php

namespace App\Http\Controllers\Docente;

use App\Http\Controllers\Controller;
use App\Models\Announcement;

class IntranetController extends Controller
{
    public function index()
    {
        $announcements = Announcement::published()
            ->forRole('docente')
            ->latest('published_at')
            ->paginate(10);

        return view('docente.intranet', compact('announcements'));
    }
}