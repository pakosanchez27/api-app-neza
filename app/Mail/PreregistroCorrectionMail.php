<?php

namespace App\Mail;

use App\Models\Preregistro;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PreregistroCorrectionMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Preregistro $preregistro,
        public string $correctionUrl,
    )
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Tu registro en ExploraNeza requiere correcciones',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.preregistro-correction',
            with: [
                'nombreSolicitante' => trim(implode(' ', array_filter([
                    $this->preregistro->nombre_p,
                    $this->preregistro->app_p,
                    $this->preregistro->apm_p,
                ]))),
                'nombreEstablecimiento' => $this->preregistro->nombre_est,
                'motivo' => $this->preregistro->observacion_registro,
                'correctionUrl' => $this->correctionUrl,
                'expirationDate' => $this->preregistro->token_correccion_expira_en,
            ],
        );
    }
}
