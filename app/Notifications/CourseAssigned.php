<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class CourseAssigned extends Notification
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
            'title' => 'Nuevo curso asignado',
            'body'  => "Fuiste asignado como docente del curso \"{$this->courseName}\".",
            'url'   => route('docente.courses.show', $this->courseId),
        ];
    }
}
