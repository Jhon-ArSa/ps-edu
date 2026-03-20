<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ForumTopic;

class ForumController extends Controller
{
    /**
     * Supervisión de todos los foros de la plataforma (RF — Admin supervisa todos los foros).
     */
    public function index()
    {
        $topics = ForumTopic::with(['author', 'course'])
            ->orderByDesc('created_at')
            ->paginate(30);

        return view('admin.forum.index', compact('topics'));
    }
}
