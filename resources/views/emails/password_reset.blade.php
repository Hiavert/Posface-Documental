@extends('vendor.mail.html.layout')

@section('slot')
<p>Hola {{ $user->nombres }},</p>
<p>Hemos recibido una solicitud para restablecer tu contraseña en el Sistema de Gestión Académica de POSFACE.</p>
<p>Para establecer una nueva contraseña, por favor haz clic en el siguiente botón:</p>

<p style="text-align: center;">
  <a href="{{ $url }}" class="button">Restablecer contraseña</a>
</p>

<p>Este enlace de restablecimiento expirará en 60 minutos. Si no puedes acceder al botón, copia y pega la siguiente URL en tu navegador:</p>
<p style="word-break: break-all; background: #f8f9fa; padding: 10px; border-radius: 6px; font-size: 14px;">
  {{ $url }}
</p>

<p>Si no solicitaste este cambio, por favor ignora este correo. Tu contraseña actual permanecerá segura.</p>

<div class="signature">
  <p>Atentamente,</p>
  <p><strong>Equipo de Soporte de POSFACE</strong></p>
  <p>Universidad Nacional Autónoma de Honduras</p>
</div>
@endsection