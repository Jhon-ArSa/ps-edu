<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeUserNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $userName;
    public $userEmail;
    public $temporaryPassword;
    public $userRole;
    public $loginUrl;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $userName, string $userEmail, string $temporaryPassword, string $userRole)
    {
        $this->userName = $userName;
        $this->userEmail = $userEmail;
        $this->temporaryPassword = $temporaryPassword;
        $this->userRole = $userRole;
        $this->loginUrl = url('/login');
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $roleName = match($this->userRole) {
            'admin' => 'Administrador',
            'docente' => 'Docente',
            'alumno' => 'Alumno',
            default => 'Usuario',
        };

        return (new MailMessage)
            ->subject('Bienvenido a PS-EDU - Tus Credenciales de Acceso')
            ->view('emails.welcome', [
                'userName' => $this->userName,
                'userEmail' => $this->userEmail,
                'temporaryPassword' => $this->temporaryPassword,
                'roleName' => $roleName,
                'loginUrl' => $this->loginUrl,
            ]);
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'Bienvenido a PS-EDU. Revisa tu email para obtener tus credenciales de acceso.',
            'user_name' => $this->userName,
            'user_role' => $this->userRole,
        ];
    }
}
