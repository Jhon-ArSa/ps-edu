<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskReviewed extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $taskTitle,
        public int $courseId,
        public string $courseName,
    ) {}

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Tarea revisada: {$this->taskTitle}")
            ->greeting("Hola, {$notifiable->name}")
            ->line("Tu entrega de la tarea \"{$this->taskTitle}\" ha sido revisada.")
            ->line("Curso: {$this->courseName}")
            ->action('Ver tarea', url("/alumno/cursos/{$this->courseId}"))
            ->line('¡Gracias por tu participación!');
    }

    public function toArray($notifiable): array
    {
        return [
            'type'        => 'task_reviewed',
            'task_title'  => $this->taskTitle,
            'course_id'   => $this->courseId,
            'course_name' => $this->courseName,
            'message'     => "Tu entrega de la tarea \"{$this->taskTitle}\" ha sido revisada en {$this->courseName}.",
        ];
    }
}