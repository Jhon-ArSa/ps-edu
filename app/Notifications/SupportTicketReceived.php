<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SupportTicketReceived extends Notification
{
    use Queueable;

    public function __construct(
        public int $ticketId,
        public string $subject,
        public string $category,
        public string $requesterName,
    ) {}

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Nuevo ticket de soporte #' . $this->ticketId)
            ->greeting('Hola, ' . $notifiable->name)
            ->line('Se ha registrado un nuevo ticket de soporte en la plataforma.')
            ->line('Ticket: #' . $this->ticketId)
            ->line('Solicitante: ' . $this->requesterName)
            ->line('Categoría: ' . $this->category)
            ->line('Asunto: ' . $this->subject)
            ->action('Ver ticket', url('/admin/soporte/' . $this->ticketId));
    }

    public function toArray($notifiable): array
    {
        return [
            'type'      => 'support_ticket_received',
            'icon'      => 'support',
            'title'     => 'Nuevo ticket de soporte',
            'body'      => $this->requesterName . ' registró el ticket #' . $this->ticketId . ': ' . $this->subject,
            'ticket_id' => $this->ticketId,
            'url'       => url('/admin/soporte/' . $this->ticketId),
        ];
    }
}
