<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ProcesoCompletadoNotification extends Notification
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
            'titulo' => 'Proceso de pago terna completado',
            'mensaje' => 'El proceso ' . $this->pagoTerna->codigo . ' ha sido completado',
            'url' => route('terna.admin.show', $this->pagoTerna->id),
            'tipo' => 'proceso-completado-terna'
        ];
    }
}