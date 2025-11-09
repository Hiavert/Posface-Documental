<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recuperar contraseña - POSFACE</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body {
            background: #f4f6f9;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .reset-container {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            padding: 40px 32px 32px 32px;
            width: 100%;
            max-width: 400px;
        }
        .reset-container h2 {
            color: #003366;
            font-weight: 700;
            margin-bottom: 10px;
        }
        .reset-container p {
            color: #555;
            margin-bottom: 25px;
        }
        .form-group {
            margin-bottom: 18px;
            position: relative;
        }
        .form-control {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #cfd8dc;
            border-radius: 6px;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #003366;
            outline: none;
            box-shadow: 0 0 0 3px rgba(0, 51, 102, 0.1);
        }
        .form-control.error {
            border-color: #d32f2f;
            background: #fff5f5;
        }
        .form-control.success {
            border-color: #4caf50;
            background: #f8fff8;
        }
        .btn-primary {
            background: #003366;
            color: #fff;
            border: none;
            border-radius: 6px;
            padding: 10px 0;
            width: 100%;
            font-size: 16px;
            font-weight: 600;
            transition: background 0.2s;
            cursor: pointer;
        }
        .btn-primary:hover {
            background: #002244;
        }
        .btn-primary:disabled {
            background: #cccccc;
            cursor: not-allowed;
        }
        .alert-success {
            background: #e6f4ea;
            color: #256029;
            border: 1px solid #b7e1cd;
            border-radius: 6px;
            padding: 10px 15px;
            margin-bottom: 18px;
            font-size: 15px;
        }
        .text-danger {
            color: #d32f2f;
            font-size: 14px;
            margin-top: 5px;
            display: block;
        }
        .validation-error {
            color: #d32f2f;
            font-size: 14px;
            margin-top: 5px;
            display: none;
            animation: fadeIn 0.3s ease;
        }
        .validation-success {
            color: #4caf50;
            font-size: 14px;
            margin-top: 5px;
            display: none;
            animation: fadeIn 0.3s ease;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 22px;
            color: #003366;
            text-decoration: none;
            font-size: 15px;
        }
        .back-link:hover {
            text-decoration: underline;
        }
        .icon-lock {
            font-size: 38px;
            color: #003366;
            display: block;
            text-align: center;
            margin-bottom: 12px;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-5px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .shake {
            animation: shake 0.5s ease-in-out;
        }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
    </style>
</head>
<body>
    <div class="reset-container">
        <span class="icon-lock">
            <i class="bi bi-lock"></i>
        </span>
        <h2>¿Olvidaste tu contraseña?</h2>
        <p>Ingresa tu correo electrónico y te enviaremos un enlace para restablecerla.</p>
        
        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif
        
        <form method="POST" action="{{ route('password.email') }}" id="passwordResetForm">
            @csrf
            <div class="form-group">
                <label for="email">Correo electrónico</label>
                <input type="email" name="email" id="email" class="form-control" 
                       required autofocus maxlength="50"
                       placeholder="tu@correo.com"
                       oninput="validarEmail(this)">
                <span class="validation-error" id="email-validation-error"></span>
                <span class="validation-success" id="email-validation-success"></span>
                @error('email')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <button type="submit" class="btn btn-primary" id="submit-btn" disabled>Enviar enlace</button>
        </form>
        <a href="{{ route('login') }}" class="back-link">
            <i class="bi bi-arrow-left"></i> Volver al inicio de sesión
        </a>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const emailInput = document.getElementById('email');
            const form = document.getElementById('passwordResetForm');
            const submitBtn = document.getElementById('submit-btn');
            const validationError = document.getElementById('email-validation-error');
            const validationSuccess = document.getElementById('email-validation-success');
            
            // Validación inicial si hay valor en el campo
            if (emailInput.value) {
                validarEmail(emailInput);
            }
            
            // Validación al enviar el formulario
            form.addEventListener('submit', function(e) {
                if (!validarEmail(emailInput)) {
                    e.preventDefault();
                    mostrarError(validationError, 'Por favor corrige los errores en el campo de correo electrónico antes de enviar.');
                }
            });
        });
        
        // Función para validar email en tiempo real
        function validarEmail(input) {
            const email = input.value.trim();
            const validationError = document.getElementById('email-validation-error');
            const validationSuccess = document.getElementById('email-validation-success');
            const submitBtn = document.getElementById('submit-btn');
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            // Limpiar clases y mensajes
            input.classList.remove('error', 'success');
            limpiarMensaje(validationError);
            limpiarMensaje(validationSuccess);
            
            // Validar campo vacío
            if (email === '') {
                input.classList.add('error');
                mostrarError(validationError, 'El correo electrónico es obligatorio');
                deshabilitarBoton(true);
                return false;
            }
            
            // Validar formato de email
            if (!emailRegex.test(email)) {
                input.classList.add('error');
                input.classList.add('shake');
                setTimeout(() => input.classList.remove('shake'), 500);
                mostrarError(validationError, 'Debe ser un correo electrónico válido');
                deshabilitarBoton(true);
                return false;
            }
            
            // Validar longitud máxima
            if (email.length > 50) {
                input.classList.add('error');
                input.classList.add('shake');
                setTimeout(() => input.classList.remove('shake'), 500);
                mostrarError(validationError, 'El correo no puede tener más de 50 caracteres');
                deshabilitarBoton(true);
                return false;
            }
            
            // Validar que no tenga más de 3 letras iguales consecutivas
            if (tieneMasDeTresRepetidas(email)) {
                input.classList.add('error');
                input.classList.add('shake');
                setTimeout(() => input.classList.remove('shake'), 500);
                mostrarError(validationError, 'El correo no puede tener más de 3 letras iguales consecutivas');
                deshabilitarBoton(true);
                return false;
            }
            
            // Si pasa todas las validaciones
            input.classList.add('success');
            mostrarExito(validationSuccess, '✓ Correo válido');
            deshabilitarBoton(false);
            return true;
        }
        
        // Función para verificar más de 3 letras iguales consecutivas
        function tieneMasDeTresRepetidas(texto) {
            // Eliminar caracteres especiales y números para verificar solo letras
            const soloLetras = texto.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ]/g, '');
            
            // Convertir a minúsculas para hacer la comparación sin distinción de mayúsculas/minúsculas
            const textoMinusculas = soloLetras.toLowerCase();
            
            // Verificar si hay más de 3 letras iguales consecutivas
            for (let i = 0; i < textoMinusculas.length - 3; i++) {
                const char = textoMinusculas[i];
                if (char === textoMinusculas[i+1] && 
                    char === textoMinusculas[i+2] && 
                    char === textoMinusculas[i+3]) {
                    return true;
                }
            }
            
            return false;
        }
        
        // Función para mostrar errores
        function mostrarError(elemento, mensaje) {
            elemento.textContent = mensaje;
            elemento.style.display = 'block';
            elemento.style.color = '#d32f2f';
        }
        
        // Función para mostrar éxito
        function mostrarExito(elemento, mensaje) {
            elemento.textContent = mensaje;
            elemento.style.display = 'block';
            elemento.style.color = '#4caf50';
        }
        
        // Función para limpiar mensajes
        function limpiarMensaje(elemento) {
            elemento.style.display = 'none';
            elemento.textContent = '';
        }
        
        // Función para habilitar/deshabilitar botón
        function deshabilitarBoton(deshabilitar) {
            const submitBtn = document.getElementById('submit-btn');
            if (submitBtn) {
                submitBtn.disabled = deshabilitar;
            }
        }
    </script>
</body>
</html>
