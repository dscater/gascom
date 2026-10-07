<?php

namespace App\Mail;

use App\Models\Configuracion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DistribucionPagoMail extends Mailable
{
    use Queueable, SerializesModels;
    public $datos;
    /**
     * Create a new message instance.
     */
    public function __construct(array $datos)
    {
        $this->datos = $datos;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {


        return new Envelope(
            subject: 'Pagos - ' . $this->datos["meses"][$this->datos["mes"]] . ' ' . $this->datos["anio"],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.pagos',
            with: [
                "datos" => $this->datos
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        $configuracion = Configuracion::first();

        if (!$configuracion?->qr) {
            return [];
        }

        $path = public_path('imgs/' . $configuracion->qr);

        if (!file_exists($path)) {
            return [];
        }

        return [
            Attachment::fromPath($path)
                ->as('qr-pago.' . pathinfo($path, PATHINFO_EXTENSION))
                ->withMime(mime_content_type($path)),
        ];
    }
}
