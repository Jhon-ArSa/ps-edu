<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SupportTicketResponded extends Notification
{
    use Queueable;

    public function __construct(
        public int $ticketId,
        public string $subject,
        public string $response,
        public string $adminName,
    ) {}

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Respuesta a tu ticket de soporte #' . $this->ticketId)
            ->greeting('Hola, ' . $notifiable->name)
            ->line('Tu solicitud de soporte ha sido respondida por el equipo de administración.')
            ->line('Ticket: #' . $this->ticketId)
            ->line('Asunto: ' . $this->subject)
            ->line('Respuesta:')
            ->line($this->response)
            ->line('Atendido por: ' . $this->adminName)
                ->action('Ir al sistema', route($notifiable->role . '.dashboard'));
    }

    public function toArray($notifiable): array
    {
        $supportRoute = match ($notifiable->role) {
            'docente' => 'docente.soporte',
            'alumno'  => 'alumno.soporte',
            default   => 'admin.support.index',
        };

        return [
            'type'      => 'support_ticket_responded',
            'icon'      => 'support',
            'title'     => 'Respuesta de soporte disponible',
            'body'      => 'Tu ticket #' . $this->ticketId . ' recibió respuesta: ' . $this->subject,
            'ticket_id' => $this->ticketId,
            'url'       => route($supportRoute, [], false),
        ];
    }
}
