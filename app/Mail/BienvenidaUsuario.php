<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BienvenidaUsuario extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $token;

    /**
     * Create a new message instance.
     */
    public function __construct($user, $token)
    {
        $this->user = $user;
        $this->token = $token;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Bienvenida Usuario',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $url = url('/password/reset/' . $this->token . '?email=' . urlencode($this->user->email));
        return new Content(
            view: 'emails.bienvenida_usuario',
            with: ['url' => $url, 'user' => $this->user]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }

    public function build()
    {
        $url = url('/password/reset/' . $this->token . '?email=' . urlencode($this->user->email));
        return $this->subject('Bienvenido a POSFACE - Establece tu contraseña')
            ->view('emails.bienvenida_usuario')
            ->with(['url' => $url, 'user' => $this->user]);
    }
}
