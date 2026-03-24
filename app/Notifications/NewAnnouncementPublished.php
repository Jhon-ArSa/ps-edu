<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class NewAnnouncementPublished extends Notification
{

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
            'admin'   => route('admin.intranet', [], false),
            'docente' => route('docente.intranet', [], false),
            default   => route('alumno.intranet', [], false),
        };

        return [
            'icon'      => 'announcement',
            'title'     => 'Nuevo comunicado',
            'body'      => "\"{$this->announcementTitle}\" ha sido publicado en la Intranet.",
            'url'       => $url,
        ];
    }
}
