<?php

namespace App\Policies;

use App\Models\Submission;
use App\Models\User;

class SubmissionPolicy
{
    /**
     * El alumno solo puede editar su propia entrega (si no está calificada y no venció).
     * El docente del curso puede ver cualquier entrega.
     */
    public function update(User $user, Submission $submission): bool
    {
        return $submission->user_id === $user->id
            && ! $submission->isGraded()
            && ! $submission->task->isExpired();
    }

    /**
     * Solo el dueño de la entrega puede verla (alumno), o el docente del curso.
     */
    public function view(User $user, Submission $submission): bool
    {
        if ($submission->user_id === $user->id) {
            return true;
        }

        // ¿Es el docente del curso al que pertenece la tarea?
        return $submission->task->week->course->teacher_id === $user->id;
    }
}
