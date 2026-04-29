<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserActivationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $user)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Activa tu cuenta de NezaGo',
        );
    }

    public function content(): Content
    {
        $frontendUrl = rtrim((string) env('FRONTEND_URL', config('app.url')), '/');
        $activationUrl = $frontendUrl . '/auth/activar?token=' . $this->user->token_activacion;

        return new Content(
            view: 'emails.user-activation',
            with: [
                'user' => $this->user,
                'activationUrl' => $activationUrl,
            ],
        );
    }
}
