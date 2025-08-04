<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TareaAsignadaNotification extends Notification
{
    use Queueable;

    public $tarea;

    /**
     * Create a new notification instance.
     */
    public function __construct($tarea)
    {
        $this->tarea = $tarea;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject('Nueva tarea asignada: ' . $this->tarea->nombre)
            ->markdown('tareas.mail_tarea_asignada', [
                'tarea' => $this->tarea,
                'usuario' => $notifiable
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray($notifiable)
    {
        return [
            'tarea_id' => $this->tarea->id_tarea,
            'nombre' => $this->tarea->nombre,
            'descripcion' => $this->tarea->descripcion,
            'fecha_vencimiento' => $this->tarea->fecha_vencimiento,
        ];
    }
}
