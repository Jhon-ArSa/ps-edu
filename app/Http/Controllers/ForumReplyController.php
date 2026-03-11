<?php

namespace App\Http\Controllers;

use App\Models\ForumReply;
use App\Models\ForumTopic;
use Illuminate\Http\Request;

class ForumReplyController extends Controller
{
    // ── Crear respuesta ───────────────────────────────────────────────────────

    public function store(Request $request, ForumTopic $topic)
    {
        $user = auth()->user();

        // Verificar que el tema no esté cerrado
        abort_unless(!$topic->is_closed, 403, 'Este tema está cerrado y no acepta nuevas respuestas.');

        // Verificar que el usuario puede responder (teacher o alumno matriculado)
        abort_unless($topic->canReply($user), 403, 'No tienes permiso para responder en este foro.');

        $request->validate([
            'body' => 'required|string|min:5|max:5000',
        ]);

        $reply = $topic->replies()->create([
            'user_id' => $user->id,
            'body'    => $request->body,
        ]);

        // Actualizar contadores del tema
        $topic->increment('replies_count');
        $topic->update(['last_reply_at' => now()]);

        return back()->with('success', 'Respuesta publicada.');
    }

    // ── Eliminar respuesta (soft delete) ──────────────────────────────────────

    public function destroy(ForumTopic $topic, ForumReply $reply)
    {
        $user = auth()->user();

        abort_unless($reply->topic_id === $topic->id, 404);
        abort_unless($reply->canDelete($user), 403, 'No tienes permiso para eliminar esta respuesta.');

        $reply->softDelete();

        return back()->with('success', 'Respuesta eliminada.');
    }
}
