<?php

namespace App\Policies;

use App\Models\Submission;
use App\Models\User;

class SubmissionPolicy
{
    /**
     * El alumno solo puede editar su propia entrega (si no está revisada y no venció).
     * El docente del curso puede ver cualquier entrega.
     */
    public function update(User $user, Submission $submission): bool
    {
        return (int) $submission->user_id === (int) $user->id
            && ! $submission->isGraded()
            && ! $submission->task->isExpired();
    }

    public function view(User $user, Submission $submission): bool
    {
        if ((int) $submission->user_id === (int) $user->id) {
            return true;
        }

        // ¿Es el docente del curso al que pertenece la tarea?
        return (int) $submission->task->week->course->teacher_id === (int) $user->id;
    }
}
