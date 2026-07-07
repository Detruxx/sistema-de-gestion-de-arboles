<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ClaimMergedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $linkedTo;

    /**
     * Create a new message instance.
     */
    public function __construct($linkedTo)
    {
        $this->linkedTo = $linkedTo;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Tu reclamo ha sido unificado - Sistema TreeBA',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            htmlString: "
                <h2>Hola,</h2>
                <p>Te informamos desde el municipio que tu reclamo fue unificado con el incidente principal <strong>#{$this->linkedTo}</strong> que ya está siendo procesado por nuestras cuadrillas.</p>
                <p>Hacemos esto para optimizar los tiempos de respuesta y resolver el problema en tu zona de forma conjunta.</p>
                <br>
                <p>Saludos cordiales,<br><strong>Equipo de Atención Ciudadana - TreeBA</strong></p>
            "
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}