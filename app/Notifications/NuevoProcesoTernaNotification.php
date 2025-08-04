<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NuevoProcesoTernaNotification extends Notification
{
    use Queueable;

    public $pagoTerna;

    public function __construct($pagoTerna)
    {
        $this->pagoTerna = $pagoTerna;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'titulo' => 'Nuevo proceso de pago terna asignado',
            'mensaje' => 'Se te ha asignado un nuevo proceso: ' . $this->pagoTerna->codigo,
            'url' => route('terna.asistente.show', $this->pagoTerna->id),
            'tipo' => 'nuevo-proceso-terna'
        ];
    }
}