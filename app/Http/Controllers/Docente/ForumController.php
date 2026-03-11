<?php

namespace App\Http\Controllers\Docente;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\ForumTopic;
use Illuminate\Http\Request;

class ForumController extends Controller
{
    // ── Listar temas del foro ─────────────────────────────────────────────────

    public function index(Course $course)
    {
        $this->authorize('manage', $course);

        $topics = $course->forumTopics()
            ->with('author')
            ->orderByDesc('is_pinned')
            ->orderByDesc('last_reply_at')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('forum.index', [
            'course'  => $course,
            'topics'  => $topics,
            'role'    => 'docente',
            'storeRoute'   => 'docente.forum.store',
            'showRoute'    => 'docente.forum.show',
            'destroyRoute' => 'docente.forum.destroy',
            'pinRoute'     => 'docente.forum.pin',
            'closeRoute'   => 'docente.forum.close',
        ]);
    }

    // ── Crear tema ────────────────────────────────────────────────────────────

    public function store(Request $request, Course $course)
    {
        $this->authorize('manage', $course);

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
            ->route('docente.forum.show', [$course, $topic])
            ->with('success', 'Tema publicado en el foro.');
    }

    // ── Ver tema con respuestas ───────────────────────────────────────────────

    public function show(Course $course, ForumTopic $topic)
    {
        $this->authorize('manage', $course);
        abort_unless($topic->course_id === $course->id, 404);

        $replies = $topic->replies()->with(['author', 'topic.course'])->paginate(20);

        return view('forum.show', [
            'course'  => $course,
            'topic'   => $topic,
            'replies' => $replies,
            'role'    => 'docente',
            'indexRoute'   => 'docente.forum.index',
            'pinRoute'     => 'docente.forum.pin',
            'closeRoute'   => 'docente.forum.close',
            'destroyRoute' => 'docente.forum.destroy',
        ]);
    }

    // ── Eliminar tema ─────────────────────────────────────────────────────────

    public function destroy(Course $course, ForumTopic $topic)
    {
        $this->authorize('manage', $course);
        abort_unless($topic->course_id === $course->id, 404);

        $topic->delete();

        return redirect()
            ->route('docente.forum.index', $course)
            ->with('success', 'Tema eliminado.');
    }

    // ── Fijar / desfijar tema ─────────────────────────────────────────────────

    public function pin(Course $course, ForumTopic $topic)
    {
        $this->authorize('manage', $course);
        abort_unless($topic->course_id === $course->id, 404);

        $topic->update(['is_pinned' => !$topic->is_pinned]);

        $msg = $topic->is_pinned ? 'Tema fijado.' : 'Tema desfijado.';
        return back()->with('success', $msg);
    }

    // ── Cerrar / abrir tema ───────────────────────────────────────────────────

    public function close(Course $course, ForumTopic $topic)
    {
        $this->authorize('manage', $course);
        abort_unless($topic->course_id === $course->id, 404);

        $topic->update(['is_closed' => !$topic->is_closed]);

        $msg = $topic->is_closed ? 'Tema cerrado.' : 'Tema abierto nuevamente.';
        return back()->with('success', $msg);
    }
}
