<h2>¡Hola {{ $user->nombres }}! 👋</h2>

<p>Nos alegra darte la bienvenida a <strong>POSFACE</strong>. Tu cuenta ha sido creada exitosamente y ya casi estás listo para comenzar.</p>

<p>Para activar tu cuenta y establecer tu contraseña, haz clic en el siguiente botón:</p>

<p style="text-align: center;">
    <a href="{{ $url }}" class="button">🔐 Establecer Contraseña</a>
</p>

<p>Si el botón no funciona, copia y pega este enlace en tu navegador:</p>

<p style="word-break: break-all; background: #f9f9f9; padding: 12px; border-radius: 6px; font-size: 14px; border: 1px solid #eee;">
    {{ $url }}
</p>

<p>⚠️ Este enlace es válido por <strong>24 horas</strong>. Si no solicitaste esta cuenta, puedes ignorar este mensaje.</p>

<div class="signature">
    <p>Atentamente,</p>
    <p><strong>Equipo POSFACE</strong></p>
    <p>📚 Universidad Nacional Autónoma de Honduras</p>
</div>
