<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class AcuseEnviadoNotification extends Notification
{
   protected $titulo;
protected $mensaje;
protected $nombre;
protected $url;

public function __construct($titulo, $mensaje, $nombre, $url)
{
    $this->titulo = $titulo;
    $this->mensaje = $mensaje;
    $this->nombre = $nombre;
    $this->url = $url;
}

public function via($notifiable)
{
    return ['database'];
}

public function toDatabase($notifiable)
{
    return [
        'titulo' => $this->titulo,
        'mensaje' => $this->mensaje,
        'nombre' => $this->nombre,
        'url' => $this->url,
    ];
}

}
