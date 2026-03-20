<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class NewTaskPublished extends Notification
{

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
