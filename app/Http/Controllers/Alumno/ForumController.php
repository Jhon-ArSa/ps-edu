<?php

namespace App\Http\Controllers\Alumno;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\ForumTopic;
use Illuminate\Http\Request;

class ForumController extends Controller
{
    // ── Listar temas ──────────────────────────────────────────────────────────

    public function index(Course $course)
    {
        abort_unless($course->students()->where('users.id', auth()->id())->exists(), 403);

        $topics = $course->forumTopics()
            ->with('author')
            ->orderByDesc('is_pinned')
            ->orderByDesc('last_reply_at')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('forum.index', [
            'course'  => $course,
            'topics'  => $topics,
            'role'    => 'alumno',
            'storeRoute'   => 'alumno.forum.store',
            'showRoute'    => 'alumno.forum.show',
            'destroyRoute' => 'alumno.forum.destroy',
            'pinRoute'     => null,
            'closeRoute'   => null,
        ]);
    }

    // ── Crear tema ────────────────────────────────────────────────────────────

    public function store(Request $request, Course $course)
    {
        abort_unless($course->students()->where('users.id', auth()->id())->exists(), 403);

        $request->validate([
            'title' => 'required|string|max:255',
            'body'  => 'required|string|min:10',
        ]);

        $topic = $course->forumTopics()->create([
            'user_id' => auth()->id(),
            'title'   => $request->title,
            'body'    => $request->body,
        ]);

        return redirect()
            ->route('alumno.forum.show', [$course, $topic])
            ->with('success', 'Tema publicado en el foro.');
    }

    // ── Ver tema ──────────────────────────────────────────────────────────────

    public function show(Course $course, ForumTopic $topic)
    {
        abort_unless($topic->course_id === $course->id, 404);
        abort_unless(
            $course->students()->where('users.id', auth()->id())->exists()
            || $course->teacher_id === auth()->id(),
            403
        );

        $replies = $topic->replies()->with(['author', 'topic.course'])->paginate(20);

        return view('forum.show', [
            'course'  => $course,
            'topic'   => $topic,
            'replies' => $replies,
            'role'    => 'alumno',
            'indexRoute'   => 'alumno.forum.index',
            'pinRoute'     => null,
            'closeRoute'   => null,
            'destroyRoute' => 'alumno.forum.destroy',
        ]);
    }

    // ── Eliminar tema propio ──────────────────────────────────────────────────

    public function destroy(Course $course, ForumTopic $topic)
    {
        abort_unless($topic->course_id === $course->id, 404);
        abort_unless($topic->user_id === auth()->id(), 403, 'Solo puedes eliminar tus propios temas.');

        $topic->delete();

        return redirect()
            ->route('alumno.forum.index', $course)
            ->with('success', 'Tema eliminado.');
    }
}
