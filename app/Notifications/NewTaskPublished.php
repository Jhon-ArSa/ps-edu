<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NewTaskPublished extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly string $taskTitle,
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
            'icon'      => 'task',
            'title'     => 'Nueva tarea publicada',
            'body'      => "Se publicó \"{$this->taskTitle}\" en el curso {$this->courseName}.",
            'url'       => route('alumno.courses.show', $this->courseId),
        ];
    }
}
