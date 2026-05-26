<?php

namespace App\Mail;

use App\Models\Preregistro;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PreregistroReceivedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Preregistro $preregistro)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Gracias por tu registro',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.preregistro-received',
            with: [
                'nombreSolicitante' => trim(implode(' ', array_filter([
                    $this->preregistro->nombre_p,
                    $this->preregistro->app_p,
                    $this->preregistro->apm_p,
                ]))),
            ],
        );
    }
}
