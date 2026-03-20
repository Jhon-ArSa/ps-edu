<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class NewEnrollment extends Notification
{
    public function __construct(
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
            'icon'  => 'course',
            'title' => 'Matriculado en nuevo curso',
            'body'  => "Fuiste matriculado en el curso \"{$this->courseName}\".",
            'url'   => route('alumno.courses.show', $this->courseId),
        ];
    }
}
