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
                <img src="{{ asset('Imagen/Posface_logo.jpeg') }}" alt="Logo POSFACE - Formamos profesionales con valores y visión gerencial" class="logo-img" />
            </div>
            <p class="mission-text">Formamos profesionales con valores y visión gerencial para el desarrollo económico del país.</p>
        </div>

        <!-- Panel Derecho -->
        <div class="right-panel">
            <div class="login-container">
                <h1 class="welcome-text">Bienvenido al sistema de gestión académica</h1>
                <h2>Iniciar sesión</h2>

                <!-- Mensajes de error/success -->
                <div class="alert alert-danger" id="error-alert" style="display: none;" role="alert" aria-live="polite">
                    <ul id="error-list"></ul>
                </div>
                
                <div class="alert alert-success" id="success-alert" style="display: none;" role="alert" aria-live="polite">
                    ¡Inicio de sesión exitoso! Redirigiendo...
                </div>

                <form id="loginForm" method="POST" action="{{ route('login') }}">
                    @csrf
                    <!-- Campo de email -->
                    <div class="input-container" id="email-container">
                        <label for="email" class="sr-only">Correo electrónico</label>
                        <div class="icon-wrapper">
                            <i class="bi bi-person input-icon-left" aria-hidden="true"></i>
                        </div>
                        <input type="email" name="email" id="email" placeholder="usuario@correo.com" required maxlength="50" aria-describedby="email-error" />
                        <div class="icon-wrapper">
                            <i class="bi bi-x-circle input-icon-right email-clear" id="email-clear" title="Limpiar campo" aria-hidden="true"></i>
                        </div>
                        <div class="error-message" id="email-error" role="alert" aria-live="polite"></div>
                    </div>

                    <!-- Campo de contraseña -->
                    <div class="input-container" id="password-container">
                        <label for="password" class="sr-only">Contraseña</label>
                        <div class="icon-wrapper">
                            <i class="bi bi-lock input-icon-left" aria-hidden="true"></i>
                        </div>
                        <input type="password" name="password" id="password" placeholder="Contraseña" required maxlength="20" aria-describedby="password-error" />
                        <div class="icon-wrapper">
                            <i class="bi bi-eye input-icon-right" id="toggle-password" title="Mostrar contraseña" aria-hidden="true"></i>
                        </div>
                        <div class="error-message" id="password-error" role="alert" aria-live="polite"></div>
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
            
            // Función para verificar caracteres repetidos y bloquear la entrada
            function preventRepeatingCharacters(input, maxRepeats) {
                const value = input.value;
                
                if (value.length > 0) {
                    let count = 1;
                    for (let i = 1; i < value.length; i++) {
                        if (value[i] === value[i-1]) {
                            count++;
                            if (count > maxRepeats) {
                                // Eliminar el último carácter ingresado
                                input.value = value.substring(0, value.length - 1);
                                return true; // Se bloqueó un carácter
                            }
                        } else {
                            count = 1;
                        }
                    }
                }
                return false; // No se bloqueó ningún carácter
            }
            
            // Validación en tiempo real
            emailInput.addEventListener('input', function() {
                if (preventRepeatingCharacters(emailInput, 3)) {
                    // Mostrar mensaje de error temporal
                    const errorElement = document.getElementById('email-error');
                    showError(emailInput, errorElement, 'No se permiten más de 3 caracteres iguales consecutivos');
                    setTimeout(() => {
                        errorElement.style.display = 'none';
                        emailInput.parentElement.classList.remove('error');
                    }, 2000);
                } else {
                    validateEmail();
                }
            });
            
            passwordInput.addEventListener('input', function() {
                if (preventRepeatingCharacters(passwordInput, 3)) {
                    // Mostrar mensaje de error temporal
                    const errorElement = document.getElementById('password-error');
                    showError(passwordInput, errorElement, 'No se permiten más de 3 caracteres iguales consecutivos');
                    setTimeout(() => {
                        errorElement.style.display = 'none';
                        passwordInput.parentElement.classList.remove('error');
                    }, 2000);
                } else {
                    validatePassword();
                }
            });
            
            // Toggle mostrar/ocultar contraseña
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.classList.toggle('bi-eye');
                this.classList.toggle('bi-eye-slash');
                
                // Actualizar el texto del tooltip para accesibilidad
                const isVisible = type === 'text';
                this.setAttribute('title', isVisible ? 'Ocultar contraseña' : 'Mostrar contraseña');
                this.setAttribute('aria-label', isVisible ? 'Ocultar contraseña' : 'Mostrar contraseña');
            });
            
            // Limpiar campo email
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
                    errorAlert.style.display = 'none';
                    successAlert.style.display = 'block';
                    
                    // Simulamos el envío al servidor
                    setTimeout(() => {
                        // Si la contraseña es incorrecta, mostramos error y recargamos
                        if (passwordInput.value !== 'contraseñaCorrecta') { // Esto es solo un ejemplo
                            errorList.innerHTML = '<li>Contraseña incorrecta</li>';
                            errorAlert.style.display = 'block';
                            successAlert.style.display = 'none';
                            
                            // Recargar la página después de 2 segundos
                            setTimeout(() => {
                                location.reload();
                            }, 2000);
                        } else {
                            // Si es correcta, enviamos el formulario
                            form.submit();
                        }
                    }, 1500);
                } else {
                    errorList.innerHTML = '';
                    if (!emailValid) errorList.innerHTML += '<li>Correo electrónico inválido</li>';
                    if (!passwordValid) errorList.innerHTML += '<li>Contraseña inválida</li>';
                    errorAlert.style.display = 'block';
                    successAlert.style.display = 'none';
                    
                    // Enfocar el primer campo con error para accesibilidad
                    if (!emailValid) {
                        emailInput.focus();
                    } else if (!passwordValid) {
                        passwordInput.focus();
                    }
                }
            });
            
            // Validar email
            function validateEmail() {
                const email = emailInput.value.trim();
                const errorElement = document.getElementById('email-error');
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                
                emailInput.parentElement.classList.remove('error', 'success');
                errorElement.style.display = 'none';
                
                if (email === '') {
                    showError(emailInput, errorElement, 'El correo electrónico es obligatorio');
                    return false;
                }
                
                if (email.length > 50) {
                    showError(emailInput, errorElement, 'El correo no puede tener más de 50 caracteres');
                    return false;
                }
                
                if (!emailRegex.test(email)) {
                    showError(emailInput, errorElement, 'Debe ser un correo válido');
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
                
                if (password.length > 20) {
                    showError(passwordInput, errorElement, 'La contraseña no puede tener más de 20 caracteres');
                    return false;
                }
                
                showSuccess(passwordInput);
                return true;
            }
            
            function showError(input, errorElement, message) {
                input.parentElement.classList.add('error');
                errorElement.textContent = message;
                errorElement.style.display = 'block';
                input.setAttribute('aria-invalid', 'true');
            }
            
            function showSuccess(input) {
                input.parentElement.classList.add('success');
                input.setAttribute('aria-invalid', 'false');
            }
            
            // Manejo de teclado para accesibilidad
            emailInput.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    emailClear.click();
                }
            });
            
            passwordInput.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    passwordInput.value = '';
                    validatePassword();
                }
            });
        });
    </script>
</body>
</html>
