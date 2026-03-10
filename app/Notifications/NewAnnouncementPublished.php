<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NewAnnouncementPublished extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly string $announcementTitle,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $url = match ($notifiable->role) {
            'admin'   => route('admin.dashboard'),
            'docente' => route('docente.intranet'),
            default   => route('alumno.intranet'),
        };

        return [
            'icon'      => 'announcement',
            'title'     => 'Nuevo comunicado',
            'body'      => "\"{$this->announcementTitle}\" ha sido publicado en la Intranet.",
            'url'       => $url,
        ];
    }
}
