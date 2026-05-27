<?php

namespace App\Mail;

use App\Models\Preregistro;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PreregistroApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Preregistro $preregistro,
        public string $temporaryPassword,
        public string $loginUrl,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Tu registro en ExploraNeza fue aceptado',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.preregistro-approved',
            with: [
                'nombreSolicitante' => trim(implode(' ', array_filter([
                    $this->preregistro->nombre_p,
                    $this->preregistro->app_p,
                    $this->preregistro->apm_p,
                ]))),
                'nombreEstablecimiento' => $this->preregistro->nombre_est,
                'correo' => $this->preregistro->correo,
                'temporaryPassword' => $this->temporaryPassword,
                'loginUrl' => $this->loginUrl,
            ],
        );
    }
}
