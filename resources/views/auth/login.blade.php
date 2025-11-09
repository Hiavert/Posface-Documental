<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión - POSFACE</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body>
    <div class="container">
        <!-- Panel Izquierdo -->
        <div class="left-panel">
            <div class="logo-container">
                <img src="{{ asset('Imagen/Posface_logo.jpeg') }}" alt="Logo POSFACE" class="logo-img" />
            </div>
            <p class="mission-text">Formamos profesionales con valores y visión gerencial para el desarrollo económico del país.</p>
        </div>

        <!-- Panel Derecho -->
        <div class="right-panel">
            <div class="login-container">
                <p class="welcome-text">Bienvenido al sistema de gestión académica</p>
                <h2>Iniciar sesión</h2>

                <!-- Mensajes de error/success -->
                <div class="alert alert-danger" id="error-alert" style="display: none;">
                    <ul id="error-list"></ul>
                </div>
                
                <!-- Mensaje de error del servidor -->
                @if($errors->any())
                <div class="alert alert-danger" id="server-error-alert">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                
                @if(session('login_error'))
                <div class="alert alert-danger" id="login-error-alert">
                    {{ session('login_error') }}
                </div>
                @endif
                
                <div class="alert alert-success" id="success-alert" style="display: none;">
                    ¡Inicio de sesión exitoso! Redirigiendo...
                </div>

                <form id="loginForm" method="POST" action="{{ route('login') }}">
                    @csrf
                    <!-- Campo de email -->
                    <div class="input-container" id="email-container">
                        <i class="bi bi-person"></i>
                        <input type="email" name="email" id="email" placeholder="usuario@correo.com" required 
                               maxlength="50" oninput="validarEmail(this)" onkeypress="return permitirCaracteresEmail(event)"/>
                        <i class="bi bi-x-circle toggle-icon email-clear" id="email-clear" title="Limpiar campo"></i>
                        <div class="error-message" id="email-error"></div>
                    </div>

                    <!-- Campo de contraseña -->
                    <div class="input-container" id="password-container">
                        <i class="bi bi-lock"></i>
                        <input type="password" name="password" id="password" placeholder="Contraseña" required 
                               maxlength="50" oninput="validarPassword(this)" onkeypress="return permitirCaracteresPassword(event)"/>
                        <i class="bi bi-eye toggle-icon" id="toggle-password" title="Mostrar contraseña"></i>
                        <div class="error-message" id="password-error"></div>
                    </div>

                    <button type="submit" class="btn">Entrar</button>
                </form>

                <p class="forgot-password">
                    <a href="{{ route('password.request') }}">¿Olvidaste tu contraseña?</a>
                </p>
            </div>
            
            <div class="footer-text">
                &copy; {{ date('Y') }} POSFACE - Universidad Nacional Autónoma de Honduras
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');
            const togglePassword = document.getElementById('toggle-password');
            const emailClear = document.getElementById('email-clear');
            const errorAlert = document.getElementById('error-alert');
            const errorList = document.getElementById('error-list');
            const successAlert = document.getElementById('success-alert');
            const form = document.getElementById('loginForm');
            
            // Ocultar mensajes de error del servidor después de 5 segundos
            setTimeout(() => {
                const serverError = document.getElementById('server-error-alert');
                const loginError = document.getElementById('login-error-alert');
                if (serverError) serverError.style.display = 'none';
                if (loginError) loginError.style.display = 'none';
            }, 5000);
            
            // Validación en tiempo real
            emailInput.addEventListener('input', function() {
                validarEmail(this);
            });
            passwordInput.addEventListener('input', function() {
                validarPassword(this);
            });
            
            // Toggle mostrar/ocultar contraseña
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.classList.toggle('bi-eye');
                this.classList.toggle('bi-eye-slash');
            });
            
            // Limpiar campo email
            emailClear.addEventListener('click', function() {
                emailInput.value = '';
                validarEmail(emailInput);
                emailInput.focus();
            });
            
            // Validación al enviar
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const emailValid = validarEmail(emailInput);
                const passwordValid = validarPassword(passwordInput);
                
                if (emailValid && passwordValid) {
                    errorAlert.style.display = 'none';
                    successAlert.style.display = 'block';
                    setTimeout(() => form.submit(), 1500);
                } else {
                    errorList.innerHTML = '';
                    if (!emailValid) errorList.innerHTML += '<li>Correo electrónico inválido</li>';
                    if (!passwordValid) errorList.innerHTML += '<li>Contraseña inválida</li>';
                    errorAlert.style.display = 'block';
                    successAlert.style.display = 'none';
                }
            });
            
            // Validar email
            function validarEmail(input) {
                const email = input.value.trim();
                const errorElement = document.getElementById('email-error');
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                
                input.parentElement.classList.remove('error', 'success');
                errorElement.style.display = 'none';
                
                if (email === '') {
                    showError(input, errorElement, 'El correo electrónico es obligatorio');
                    return false;
                }
                
                if (!emailRegex.test(email)) {
                    showError(input, errorElement, 'Debe ser un correo válido');
                    return false;
                }
                
                // Validar longitud máxima
                if (email.length > 50) {
                    showError(input, errorElement, 'El correo no puede tener más de 50 caracteres');
                    return false;
                }
                
                // Validar que no tenga más de 3 letras iguales consecutivas
                if (tieneMasDeTresRepetidas(email)) {
                    showError(input, errorElement, 'El correo no puede tener más de 3 letras iguales consecutivas');
                    return false;
                }
                
                showSuccess(input);
                return true;
            }
            
            function validarPassword(input) {
                const password = input.value;
                const errorElement = document.getElementById('password-error');
                
                input.parentElement.classList.remove('error', 'success');
                errorElement.style.display = 'none';
                
                if (password === '') {
                    showError(input, errorElement, 'La contraseña es obligatoria');
                    return false;
                }
                
                if (password.length < 8) {
                    showError(input, errorElement, 'La contraseña debe tener al menos 8 caracteres');
                    return false;
                }
                
                // Validar longitud máxima
                if (password.length > 20) {
                    showError(input, errorElement, 'La contraseña no puede tener más de 20 caracteres');
                    return false;
                }
                
                // Validar que no tenga más de 3 letras iguales consecutivas
                if (tieneMasDeTresRepetidas(password)) {
                    showError(input, errorElement, 'La contraseña no puede tener más de 3 letras iguales consecutivas');
                    return false;
                }
                
                showSuccess(input);
                return true;
            }
            
            function showError(input, errorElement, message) {
                input.parentElement.classList.add('error');
                errorElement.textContent = message;
                errorElement.style.display = 'block';
            }
            
            function showSuccess(input) {
                input.parentElement.classList.add('success');
            }
            
            // Función para verificar más de 3 letras iguales consecutivas
            function tieneMasDeTresRepetidas(texto) {
                // Eliminar caracteres especiales y números para verificar solo letras
                const soloLetras = texto.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ]/g, '');
                const regexRepetidas = /([a-zA-ZáéíóúÁÉÍÓÚñÑ])\1{3,}/g;
                return regexRepetidas.test(soloLetras);
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
            
            // Función para permitir solo caracteres válidos en password
            function permitirCaracteresPassword(event) {
                const charCode = event.which ? event.which : event.keyCode;
                const charStr = String.fromCharCode(charCode);
                
                // Permitir letras, números y caracteres especiales comunes en contraseñas
                const regex = /^[a-zA-Z0-9!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]$/;
                
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
        });
    </script>
</body>
</html>
