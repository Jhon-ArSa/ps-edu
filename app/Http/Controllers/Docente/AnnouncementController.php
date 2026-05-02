<?php

namespace App\Http\Controllers\Docente;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Course;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::where('author_id', auth()->id())
            ->with(['courses'])
            ->latest()
            ->paginate(10);

        return view('docente.announcements.index', compact('announcements'));
    }

    public function create()
    {
        // Programas que tienen al menos un curso asignado a este docente
        $programs = Program::whereHas('courses', fn($q) => $q->where('teacher_id', auth()->id()))
            ->with(['courses' => fn($q) => $q
                ->where('teacher_id', auth()->id())
                ->select('id', 'name', 'code', 'program_id')
                ->orderBy('name')
            ])
            ->orderBy('name')
            ->get();

        return view('docente.announcements.create', compact('programs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'         => 'required|string|max:255',
            'content'       => 'required|string',
            'course_ids'    => 'required|array|min:1',
            'course_ids.*'  => 'exists:courses,id',
            'published_at'  => 'nullable|date',
            'image'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'course_ids.required' => 'Seleccione al menos un curso destinatario.',
            'course_ids.min'      => 'Seleccione al menos un curso destinatario.',
        ]);

        // Verificar que el docente dicta esos cursos
        $ownCourseIds = Course::where('teacher_id', auth()->id())->pluck('id');
        $invalid = collect($validated['course_ids'])->diff($ownCourseIds);
        if ($invalid->isNotEmpty()) {
            return back()->withErrors(['course_ids' => 'No dictas uno o más cursos seleccionados.']);
        }

        $publishedAt = null;
        if ($request->boolean('publish_now')) {
            $publishedAt = now();
        } elseif (!empty($validated['published_at'])) {
            $publishedAt = $validated['published_at'];
        }

        $announcement = Announcement::create([
            'title'        => $validated['title'],
            'content'      => $validated['content'],
            'author_id'    => auth()->id(),
            'target_role'  => 'alumno',
            'is_popup'     => true,
            'published_at' => $publishedAt,
            'image_path'   => $request->hasFile('image')
                                ? $request->file('image')->store('announcements', 'public')
                                : null,
        ]);

        $announcement->courses()->attach($validated['course_ids']);

        return redirect()->route('docente.announcements.index')
            ->with('success', 'Anuncio emergente creado exitosamente.');
    }

    public function destroy(Announcement $announcement)
    {
        if ($announcement->author_id !== auth()->id()) {
            abort(403);
        }

        $announcement->courses()->detach();
        $announcement->programs()->detach();
        if ($announcement->image_path) {
            Storage::disk('public')->delete($announcement->image_path);
        }
        $announcement->delete();

        return back()->with('success', 'Anuncio eliminado.');
    }
}
