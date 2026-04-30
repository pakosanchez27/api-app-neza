<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CommerceResetPasswordNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly string $token,
        private readonly string $email,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $frontendUrl = rtrim((string) env('FRONTEND_URL', config('app.url')), '/');
        $resetUrl = $frontendUrl . '/auth/comercios/restablecer-contrasena?token='
            . urlencode($this->token)
            . '&email='
            . urlencode($this->email);

        return (new MailMessage())
            ->subject('Restablece tu contrasena de comercio en NezaGo')
            ->greeting('Hola ' . ($notifiable->nombre_p ?: ''))
            ->line('Recibimos una solicitud para restablecer la contrasena de tu cuenta de comercio.')
            ->action('Restablecer contrasena', $resetUrl)
            ->line('Este enlace expirara en 60 minutos.')
            ->line('Si no solicitaste este cambio, puedes ignorar este mensaje.');
    }
}
