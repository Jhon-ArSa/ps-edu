<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class TaskGraded extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly string $taskTitle,
        private readonly int    $courseId,
        private readonly string $courseName,
        private readonly float  $score,
        private readonly float  $maxScore,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $score    = number_format($this->score, 1);
        $maxScore = number_format($this->maxScore, 1);

        return [
            'icon'      => 'grade',
            'title'     => 'Tarea calificada',
            'body'      => "Tu tarea \"{$this->taskTitle}\" fue calificada con {$score} / {$maxScore} en {$this->courseName}.",
            'url'       => route('alumno.courses.show', $this->courseId),
        ];
    }
}
