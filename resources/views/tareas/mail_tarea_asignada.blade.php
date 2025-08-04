<x-mail::message>
# ¡Tienes una nueva tarea asignada!

Hola {{ $usuario->nombres }},

Se te ha asignado una nueva tarea en el sistema POSFACE:

- **Nombre:** {{ $tarea->nombre }}
- **Descripción:** {{ $tarea->descripcion ?? 'Sin descripción' }}
- **Fecha de vencimiento:** {{ $tarea->fecha_vencimiento ? date('d/m/Y', strtotime($tarea->fecha_vencimiento)) : 'No definida' }}

<x-mail::button :url="url('/tareas')">
Ver tarea
</x-mail::button>

Por favor, ingresa al sistema para ver los detalles y gestionar la tarea.

Gracias,<br>
</x-mail::message> 