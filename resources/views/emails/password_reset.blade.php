<p>Hola {{ $user->nombres }},</p>

<p>Hemos recibido una solicitud para restablecer tu contraseña. Haz clic en el siguiente botón para establecer una nueva contraseña:</p>

<p style="text-align: center;">
  <a href="{{ $url }}" class="button">Restablecer contraseña</a>
</p>

<p>Si no solicitaste este cambio, puedes ignorar este correo.</p>

<p style="word-break: break-all; background: #f8f9fa; padding: 10px; border-radius: 6px; font-size: 14px;">
  {{ $url }}
</p>

<div class="signature">
  <p>Atentamente,</p>
  <p><strong>Equipo de POSFACE</strong></p>
  <p>Universidad Nacional Autónoma de Honduras</p>
</div>
