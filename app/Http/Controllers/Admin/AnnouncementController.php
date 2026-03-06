<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index(Request $request)
    {
        $announcements = Announcement::with('author')
            ->latest()
            ->paginate(15);

        return view('admin.announcements.index', compact('announcements'));
    }

    public function create()
    {
        return view('admin.announcements.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'content'     => 'required|string',
            'target_role' => 'required|in:all,docente,alumno',
            'published_at'=> 'nullable|date',
        ]);

        $validated['author_id'] = auth()->id();

        Announcement::create($validated);
        return redirect()->route('admin.announcements.index')
            ->with('success', 'Comunicado creado exitosamente.');
    }

    public function edit(Announcement $announcement)
    {
        return view('admin.announcements.edit', compact('announcement'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'content'     => 'required|string',
            'target_role' => 'required|in:all,docente,alumno',
            'published_at'=> 'nullable|date',
        ]);

        $announcement->update($validated);
        return redirect()->route('admin.announcements.index')
            ->with('success', 'Comunicado actualizado exitosamente.');
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();
        return redirect()->route('admin.announcements.index')
            ->with('success', 'Comunicado eliminado exitosamente.');
    }
}
