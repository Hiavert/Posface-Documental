@extends('vendor.mail.html.layout')

@section('slot')
<h2>Restablecé tu contraseña 🔐</h2>

<p>Hola {{ $user->nombres ?? 'Usuario' }}, recibimos una solicitud para restablecer tu contraseña.</p>
<p>Si fuiste vos, hacé clic en el botón para establecer una nueva contraseña:</p>

<p style="text-align:center;">
    <a href="{{ $url }}" class="button">Restablecer Contraseña</a>
</p>

<p>Si el botón no funciona, copiá y pegá este enlace en tu navegador:</p>
<p style="background:#f9f9f9; padding:10px; border-radius:5px; font-size:13px; word-break:break-word;">
    {{ $url }}
</p>

<p class="signature">
    ⚠️ Este enlace expirará en 24 horas. Si no solicitaste este cambio, ignorá este mensaje.
</p>
@endsection
