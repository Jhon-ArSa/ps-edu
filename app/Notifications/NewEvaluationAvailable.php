<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NewEvaluationAvailable extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly string $evaluationTitle,
        private readonly int    $courseId,
        private readonly string $courseName,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'icon'      => 'evaluation',
            'title'     => 'Nueva evaluación disponible',
            'body'      => "Se habilitó la evaluación \"{$this->evaluationTitle}\" en el curso {$this->courseName}.",
            'url'       => route('alumno.courses.show', $this->courseId),
        ];
    }
}
