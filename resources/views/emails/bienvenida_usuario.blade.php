@extends('vendor.mail.html.layout')

@section('content')
<h2>¡Hola {{ $user->nombres }}! 👋</h2>

<p>Nos alegra darte la bienvenida a <strong>POSFACE</strong>. Tu cuenta ha sido creada exitosamente y ya casi estás listo para comenzar.</p>

<p style="text-align:center;">
    <a href="{{ $url }}" class="button">🔐 Establecer Contraseña</a>
</p>

<p>Si el botón no funciona, copia y pega este enlace en tu navegador:</p>
<p style="background:#f9f9f9; padding:10px; border-radius:5px; font-size:13px; word-break:break-word;">
    {{ $url }}
</p>

<p class="signature">
    ⚠️ Este enlace es válido por 24 horas. Si no solicitaste esta cuenta, podés ignorar este correo.
</p>
@endsection
