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
            background-color: #fff5f5;
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
            font-weight: 500;
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
        .input-container {
            position: relative;
        }
        .input-container i {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="reset-container">
        <span class="icon-lock">
            <i class="bi bi-lock"></i>
        </span>
        <h2>¿Olvidaste tu contraseña?</h2>
        <p>Ingresa tu email institucional y te enviaremos un enlace para restablecerla.</p>
        
        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif
        
        <form method="POST" action="{{ route('password.email') }}" id="passwordResetForm">
            @csrf
            <div class="form-group">
                <label for="email">Email institucional</label>
                <div class="input-container">
                    <input type="email" name="email" id="email" class="form-control" 
                           required autofocus maxlength="50"
                           placeholder="usuario@correo.com"
                           oninput="validarEmail(this)"
                           onkeypress="return permitirCaracteresEmail(event)">
                    <i class="bi bi-x-circle" id="email-clear" title="Limpiar campo" onclick="limpiarEmail()"></i>
                </div>
                @error('email')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
                <span class="validation-error" id="email-error"></span>
            </div>
            <button type="submit" class="btn btn-primary" id="submit-btn">Enviar enlace</button>
        </form>
        
        <a href="{{ route('login') }}" class="back-link">
            <i class="bi bi-arrow-left"></i> Volver al inicio de sesión
        </a>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const emailInput = document.getElementById('email');
            const submitBtn = document.getElementById('submit-btn');
            const form = document.getElementById('passwordResetForm');
            
            // Validar email al cargar si hay valor
            if (emailInput.value) {
                validarEmail(emailInput);
            }
            
            // Validar formulario al enviar
            form.addEventListener('submit', function(e) {
                if (!validarEmail(emailInput)) {
                    e.preventDefault();
                    mostrarError(emailInput, 'Por favor corrige los errores en el campo de email');
                }
            });
            
            // Habilitar/deshabilitar botón según validación
            emailInput.addEventListener('input', function() {
                const esValido = validarEmail(this);
                submitBtn.disabled = !esValido;
            });
        });
        
        function validarEmail(input) {
            const email = input.value.trim();
            const errorElement = document.getElementById('email-error');
            
            // Resetear estado
            input.classList.remove('error');
            errorElement.style.display = 'none';
            errorElement.textContent = '';
            
            // Validar campo vacío
            if (email === '') {
                mostrarError(input, 'El correo electrónico es obligatorio');
                return false;
            }
            
            // Validar formato de email
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                mostrarError(input, 'Debe ser un correo electrónico válido');
                return false;
            }
            
            // Validar longitud máxima
            if (email.length > 50) {
                mostrarError(input, 'El correo no puede tener más de 50 caracteres');
                return false;
            }
            
            // Validar que no tenga más de 3 letras iguales consecutivas
            if (tieneMasDeTresRepetidas(email)) {
                mostrarError(input, 'El correo no puede tener más de 3 letras iguales consecutivas');
                return false;
            }
            
            // Si pasa todas las validaciones
            input.classList.remove('error');
            errorElement.style.display = 'none';
            return true;
        }
        
        function tieneMasDeTresRepetidas(texto) {
            // Eliminar caracteres especiales y números para verificar solo letras
            const soloLetras = texto.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ]/g, '');
            const regexRepetidas = /([a-zA-ZáéíóúÁÉÍÓÚñÑ])\1{3,}/g;
            return regexRepetidas.test(soloLetras);
        }
        
        function permitirCaracteresEmail(event) {
            const charCode = event.which ? event.which : event.keyCode;
            const charStr = String.fromCharCode(charCode);
            
            // Permitir letras, números, @, ., -, _ y teclas de control
            const regex = /^[a-zA-Z0-9@._\-]$/;
            
            // Permitir teclas de control (backspace, delete, tab, flechas, etc.)
            if (charCode === 8 || charCode === 9 || charCode === 37 || charCode === 39 || charCode === 46) {
                return true;
            }
            
            if (!regex.test(charStr)) {
                event.preventDefault();
                return false;
            }
            
            return true;
        }
        
        function mostrarError(input, mensaje) {
            input.classList.add('error');
            const errorElement = document.getElementById('email-error');
            errorElement.textContent = mensaje;
            errorElement.style.display = 'block';
            
            // Enfocar el campo con error
            input.focus();
        }
        
        function limpiarEmail() {
            const emailInput = document.getElementById('email');
            const errorElement = document.getElementById('email-error');
            const submitBtn = document.getElementById('submit-btn');
            
            emailInput.value = '';
            emailInput.classList.remove('error');
            errorElement.style.display = 'none';
            submitBtn.disabled = false;
            emailInput.focus();
        }
        
        // Validación en tiempo real mientras se escribe
        function validarEmailEnTiempoReal(input) {
            let valor = input.value;
            
            // Filtrar caracteres no permitidos
            valor = valor.replace(/[^a-zA-Z0-9@._\-]/g, '');
            
            // Validar que no haya más de 3 letras iguales consecutivas
            const regexRepetidas = /([a-zA-Z])\1{3,}/g;
            if (regexRepetidas.test(valor)) {
                // Eliminar letras repetidas más allá de 3
                valor = valor.replace(regexRepetidas, (match) => {
                    return match.substring(0, 3);
                });
            }
            
            // Limitar a 50 caracteres
            if (valor.length > 50) {
                valor = valor.substring(0, 50);
            }
            
            // Actualizar el valor del input si cambió
            if (input.value !== valor) {
                input.value = valor;
            }
            
            // Validar el email
            validarEmail(input);
        }
    </script>
</body>
</html>
