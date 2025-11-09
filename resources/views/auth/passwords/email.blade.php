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
        .form-control.success {
            border-color: #4caf50;
            background-color: #f8fff8;
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
        .validation-message {
            font-size: 13px;
            margin-top: 5px;
            display: none;
            padding: 5px;
            border-radius: 4px;
        }
        .validation-error {
            color: #d32f2f;
            background-color: #ffebee;
            border: 1px solid #ffcdd2;
        }
        .validation-success {
            color: #256029;
            background-color: #e6f4ea;
            border: 1px solid #b7e1cd;
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
                <input type="email" name="email" id="email" class="form-control" 
                       placeholder="usuario@unah.edu.hn" 
                       required autofocus 
                       maxlength="50"
                       oninput="validarEmailInstitucional(this)"
                       onkeypress="return permitirCaracteresEmail(event)">
                <div class="validation-message" id="email-validation"></div>
                @error('email')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
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
            
            // Validación inicial del campo email
            validarEmailInstitucional(emailInput);
            
            // Validación al enviar el formulario
            form.addEventListener('submit', function(e) {
                const emailValido = validarEmailInstitucional(emailInput);
                
                if (!emailValido) {
                    e.preventDefault();
                    mostrarError('Por favor corrija los errores en el campo de email antes de enviar.');
                    return false;
                }
                
                // Si la validación pasa, habilitar el envío
                submitBtn.disabled = false;
            });
            
            // Función para mostrar errores
            function mostrarError(mensaje) {
                const validationElement = document.getElementById('email-validation');
                validationElement.textContent = mensaje;
                validationElement.className = 'validation-message validation-error';
                validationElement.style.display = 'block';
                
                // Scroll al campo con error
                emailInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
                emailInput.focus();
            }
        });
        
        // Función para validar email institucional en tiempo real
        function validarEmailInstitucional(input) {
            const email = input.value.trim();
            const validationElement = document.getElementById('email-validation');
            const submitBtn = document.getElementById('submit-btn');
            
            // Resetear estados
            input.classList.remove('error', 'success');
            validationElement.style.display = 'none';
            
            // Validar campo vacío
            if (email === '') {
                input.classList.add('error');
                validationElement.textContent = 'El email institucional es obligatorio';
                validationElement.className = 'validation-message validation-error';
                validationElement.style.display = 'block';
                submitBtn.disabled = true;
                return false;
            }
            
            // Validar formato de email básico
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                input.classList.add('error');
                validationElement.textContent = 'Debe ser un email válido';
                validationElement.className = 'validation-message validation-error';
                validationElement.style.display = 'block';
                submitBtn.disabled = true;
                return false;
            }
            
            // Validar longitud máxima
            if (email.length > 50) {
                input.classList.add('error');
                validationElement.textContent = 'El email no puede tener más de 50 caracteres';
                validationElement.className = 'validation-message validation-error';
                validationElement.style.display = 'block';
                submitBtn.disabled = true;
                return false;
            }
            
            // Validar que no tenga más de 3 letras iguales consecutivas
            if (tieneMasDeTresRepetidas(email)) {
                input.classList.add('error');
                validationElement.textContent = 'El email no puede tener más de 3 letras iguales consecutivas';
                validationElement.className = 'validation-message validation-error';
                validationElement.style.display = 'block';
                submitBtn.disabled = true;
                return false;
            }
            
            // Validar que sea un email institucional (opcional, pero recomendado)
            if (!esEmailInstitucional(email)) {
                input.classList.add('error');
                validationElement.textContent = 'Por favor ingrese un email institucional válido';
                validationElement.className = 'validation-message validation-error';
                validationElement.style.display = 'block';
                submitBtn.disabled = true;
                return false;
            }
            
            // Si pasa todas las validaciones
            input.classList.add('success');
            validationElement.textContent = 'Email institucional válido';
            validationElement.className = 'validation-message validation-success';
            validationElement.style.display = 'block';
            submitBtn.disabled = false;
            return true;
        }
        
        // Función para verificar más de 3 letras iguales consecutivas
        function tieneMasDeTresRepetidas(texto) {
            // Eliminar caracteres especiales y números para verificar solo letras
            const soloLetras = texto.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ]/g, '');
            const regexRepetidas = /([a-zA-ZáéíóúÁÉÍÓÚñÑ])\1{3,}/g;
            return regexRepetidas.test(soloLetras);
        }
        
        // Función para validar dominio institucional (puedes ajustar los dominios según tu institución)
        function esEmailInstitucional(email) {
            const dominiosInstitucionales = [
                'unah.edu.hn',
                'unah.hn',
                'posface.unah.edu.hn',
                'est.unah.edu.hn'
                // Agrega aquí más dominios institucionales si es necesario
            ];
            
            const dominio = email.split('@')[1];
            return dominiosInstitucionales.some(dom => dominio === dom || dominio.endsWith('.' + dom));
        }
        
        // Función para permitir solo caracteres válidos en email
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
        
        // Validación en tiempo real mientras se escribe
        function validarEnTiempoReal(input) {
            // Limitar a 50 caracteres
            if (input.value.length > 50) {
                input.value = input.value.substring(0, 50);
            }
            
            // Validar caracteres repetidos
            let valor = input.value;
            const regexRepetidas = /([a-zA-ZáéíóúÁÉÍÓÚñÑ])\1{3,}/g;
            if (regexRepetidas.test(valor)) {
                // Eliminar letras repetidas más allá de 3
                valor = valor.replace(regexRepetidas, (match) => {
                    return match.substring(0, 3);
                });
                input.value = valor;
            }
            
            // Ejecutar validación completa
            validarEmailInstitucional(input);
        }
    </script>
</body>
</html>
