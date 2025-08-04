<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DocumentoRecibidoNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $documento;
    public $enviadoPor;

    public function __construct($documento, $enviadoPor)
    {
        $this->documento = $documento;
        $this->enviadoPor = $enviadoPor;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'tipo' => 'DocumentoRecibido',
            'titulo' => 'Nuevo documento recibido',
            'mensaje' => 'Has recibido un documento de ' . $this->enviadoPor->nombres,
            'url' => route('documentos.recepcion'),
            'documento_id' => $this->documento->id,
            'enviado_por' => $this->enviadoPor->id_usuario,
        ];
    }
}