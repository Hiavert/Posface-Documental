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
                
                <div class="alert alert-success" id="success-alert" style="display: none;">
                    ¡Inicio de sesión exitoso! Redirigiendo...
                </div>

                <form id="loginForm" method="POST" action="{{ route('login') }}">
                    @csrf
                    <!-- Campo de email con icono de usuario a la izquierda -->
                    <div class="input-container" id="email-container">
                        <i class="bi bi-person"></i>
                        <input type="email" name="email" id="email" placeholder="usuario@unah.hn" required />
                        <i class="bi bi-x-circle toggle-icon email-clear" id="email-clear" title="Limpiar campo"></i>
                        <div class="error-message" id="email-error"></div>
                    </div>

                    <!-- Campo de contraseña con candado a la izquierda y ojo a la derecha -->
                    <div class="input-container" id="password-container">
                        <i class="bi bi-lock"></i>
                        <input type="password" name="password" id="password" placeholder="Contraseña" required />
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
            
            // Validación en tiempo real
            emailInput.addEventListener('input', validateEmail);
            passwordInput.addEventListener('input', validatePassword);
            
            // Toggle para mostrar/ocultar contraseña
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.classList.toggle('bi-eye');
                this.classList.toggle('bi-eye-slash');
            });
            
            // Limpiar campo de email
            emailClear.addEventListener('click', function() {
                emailInput.value = '';
                validateEmail();
                emailInput.focus();
            });
            
            // Validación al enviar
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const emailValid = validateEmail();
                const passwordValid = validatePassword();
                
                if (emailValid && passwordValid) {
                    // Ocultar errores
                    errorAlert.style.display = 'none';
                    
                    // Mostrar éxito
                    successAlert.style.display = 'block';
                    
                    // Simular envío
                    setTimeout(() => {
                        form.submit();
                    }, 1500);
                } else {
                    // Mostrar resumen de errores
                    errorList.innerHTML = '';
                    
                    if (!emailValid) {
                        const li = document.createElement('li');
                        li.textContent = 'Correo electrónico inválido';
                        errorList.appendChild(li);
                    }
                    
                    if (!passwordValid) {
                        const li = document.createElement('li');
                        li.textContent = 'Contraseña inválida';
                        errorList.appendChild(li);
                    }
                    
                    errorAlert.style.display = 'block';
                    successAlert.style.display = 'none';
                }
            });
            
            // Funciones de validación
            function validateEmail() {
                const email = emailInput.value.trim();
                const errorElement = document.getElementById('email-error');
                const emailRegex = /^[a-zA-Z0-9._-]$/;
                
                emailInput.parentElement.classList.remove('error', 'success');
                errorElement.style.display = 'none';
                
                if (email === '') {
                    showError(emailInput, errorElement, 'El correo electrónico es obligatorio');
                    return false;
                }
                
                if (!emailRegex.test(email)) {
                    showError(emailInput, errorElement, 'Debe ser un correo valido');
                    return false;
                }
                
                showSuccess(emailInput);
                return true;
            }
            
            function validatePassword() {
                const password = passwordInput.value;
                const errorElement = document.getElementById('password-error');
                
                passwordInput.parentElement.classList.remove('error', 'success');
                errorElement.style.display = 'none';
                
                if (password === '') {
                    showError(passwordInput, errorElement, 'La contraseña es obligatoria');
                    return false;
                }
                
                if (password.length < 8) {
                    showError(passwordInput, errorElement, 'La contraseña debe tener al menos 8 caracteres');
                    return false;
                }
                
                showSuccess(passwordInput);
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
        });
    </script>
</body>
</html>