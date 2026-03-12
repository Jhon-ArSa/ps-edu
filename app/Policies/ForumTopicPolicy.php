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

        if ($topic->user_id === $user->id) {
            return true;
        }

        // Docente del curso correspondiente
        return $topic->course->teacher_id === $user->id;
    }
}
