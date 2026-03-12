<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class GradeRecorded extends Notification
{
    public function __construct(
        private readonly string $itemName,
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
            'icon'  => 'grade',
            'title' => 'Nota registrada',
            'body'  => "Tu calificación en \"{$this->itemName}\" es {$score} / {$maxScore} en {$this->courseName}.",
            'url'   => route('alumno.courses.show', $this->courseId),
        ];
    }
}
