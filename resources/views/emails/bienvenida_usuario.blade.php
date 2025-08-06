@extends('vendor.mail.html.layout')

@section('slot')
<p>Hola {{ $user->nombres }},</p>
<p>¡Bienvenido/a al Sistema de Gestión Académica de POSFACE!</p>
<p>Tu cuenta ha sido creada exitosamente. Para comenzar a utilizar el sistema, por favor establece tu contraseña haciendo clic en el siguiente botón:</p>

<p style="text-align: center;">
  <a href="{{ $url }}" class="button">Establecer contraseña</a>
</p>

<p>Este enlace de activación expirará en 24 horas. Si no puedes acceder al botón, copia y pega la siguiente URL en tu navegador:</p>
<p style="word-break: break-all; background: #f8f9fa; padding: 10px; border-radius: 6px; font-size: 14px;">
  {{ $url }}
</p>

<p>Si no solicitaste esta cuenta o crees que has recibido este correo por error, por favor ignóralo.</p>

<div class="signature">
  <p>Atentamente,</p>
  <p><strong>Equipo de POSFACE</strong></p>
  <p>Universidad Nacional Autónoma de Honduras</p>
</div>
@endsection