<?php

namespace App\Policies;

use App\Models\ForumTopic;
use App\Models\User;

class ForumTopicPolicy
{
    /**
     * Un alumno solo puede eliminar su propio tema.
     * El docente del curso puede eliminar cualquier tema (moderador).
     * Admin puede eliminar cualquier tema.
     */
    public function destroy(User $user, ForumTopic $topic): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ((int) $topic->user_id === (int) $user->id) {
            return true;
        }

        // Docente del curso correspondiente
        return (int) $topic->course->teacher_id === (int) $user->id;
    }
}
