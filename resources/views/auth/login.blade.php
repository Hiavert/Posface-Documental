<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión - POSFACE</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
</head>
<body>
    <div class="container">
        <!-- Panel Izquierdo -->
        <div class="left-panel">
            <div class="logo-container">
                <img src="{{ asset('Imagen/Posface_logo.jpeg') }}" alt="Logo POSFACE - Universidad Nacional Autónoma de Honduras" class="logo-img" />
            </div>
            <p class="mission-text">Formamos profesionales con valores y visión gerencial para el desarrollo económico del país.</p>
        </div>

        <!-- Panel Derecho -->
        <div class="right-panel">
            <div class="login-container">
                <p class="welcome-text">Bienvenido al sistema de gestión académica</p>
                <h2>Iniciar sesión</h2>

                <!-- Mensajes de error/success -->
                <div class="alert alert-danger" id="error-alert" style="display: none;" role="alert" aria-live="assertive">
                    <ul id="error-list"></ul>
                </div>
                
                <div class="alert alert-success" id="success-alert" style="display: none;" role="alert" aria-live="polite">
                    <i class="bi bi-check-circle-fill mr-2" aria-hidden="true"></i> ¡Inicio de sesión exitoso! Redirigiendo...
                </div>

                <form id="loginForm" method="POST" action="{{ route('login') }}" novalidate>
                    @csrf
                    <!-- Campo de email -->
                    <div class="input-container" id="email-container">
                        <i class="bi bi-envelope input-icon-left" aria-hidden="true"></i>
                        <input 
                            type="email" 
                            name="email" 
                            id="email" 
                            placeholder="usuario@correo.com" 
                            required 
                            aria-describedby="email-error"
                            aria-invalid="false"
                        />
                        <i class="bi bi-x-circle input-icon-right email-clear" id="email-clear" title="Limpiar campo" role="button" tabindex="0" aria-label="Limpiar campo de correo electrónico"></i>
                        <div class="error-message" id="email-error" role="alert"></div>
                    </div>

                    <!-- Campo de contraseña -->
                    <div class="input-container" id="password-container">
                        <i class="bi bi-lock input-icon-left" aria-hidden="true"></i>
                        <input 
                            type="password" 
                            name="password" 
                            id="password" 
                            placeholder="Contraseña" 
                            required 
                            aria-describedby="password-error"
                            aria-invalid="false"
                        />
                        <i class="bi bi-eye input-icon-right" id="toggle-password" title="Mostrar contraseña" role="button" tabindex="0" aria-label="Mostrar u ocultar contraseña"></i>
                        <div class="error-message" id="password-error" role="alert"></div>
                    </div>

                    <button type="submit" class="btn" id="submit-btn" aria-label="Iniciar sesión en el sistema">
                        <span id="btn-text">Entrar al sistema</span>
                        <span id="btn-loading" style="display: none;">
                            <i class="bi bi-arrow-repeat spinning" aria-hidden="true"></i> Procesando...
                        </span>
                    </button>
                </form>

                <p class="forgot-password">
                    <a href="{{ route('password.request') }}" aria-label="Recuperar contraseña olvidada">
                        ¿Olvidaste tu contraseña?
                    </a>
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
            const submitBtn = document.getElementById('submit-btn');
            const btnText = document.getElementById('btn-text');
            const btnLoading = document.getElementById('btn-loading');
            
            // Estado de validación
            let isEmailValid = false;
            let isPasswordValid = false;
            
            // Validación en tiempo real
            emailInput.addEventListener('input', validateEmail);
            passwordInput.addEventListener('input', validatePassword);
            emailInput.addEventListener('blur', validateEmail);
            passwordInput.addEventListener('blur', validatePassword);
            
            // Toggle mostrar/ocultar contraseña
            togglePassword.addEventListener('click', togglePasswordVisibility);
            togglePassword.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    togglePasswordVisibility();
                }
            });
            
            // Limpiar campo email
            emailClear.addEventListener('click', clearEmailField);
            emailClear.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    clearEmailField();
                }
            });
            
            // Validación al enviar
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                validateEmail();
                validatePassword();
                
                if (isEmailValid && isPasswordValid) {
                    // Mostrar estado de carga
                    showLoadingState();
                    
                    // Ocultar errores previos
                    errorAlert.style.display = 'none';
                    successAlert.style.display = 'block';
                    
                    // Simular envío (en producción esto se enviaría inmediatamente)
                    setTimeout(() => {
                        form.submit();
                    }, 1500);
                } else {
                    showValidationErrors();
                }
            });
            
            function togglePasswordVisibility() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                togglePassword.classList.toggle('bi-eye');
                togglePassword.classList.toggle('bi-eye-slash');
                
                // Actualizar aria-label
                const isVisible = type === 'text';
                togglePassword.setAttribute('aria-label', isVisible ? 'Ocultar contraseña' : 'Mostrar contraseña');
                togglePassword.setAttribute('title', isVisible ? 'Ocultar contraseña' : 'Mostrar contraseña');
            }
            
            function clearEmailField() {
                emailInput.value = '';
                validateEmail();
                emailInput.focus();
            }
            
            function validateEmail() {
                const email = emailInput.value.trim();
                const errorElement = document.getElementById('email-error');
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                
                resetInputState(emailInput, errorElement);
                
                if (email === '') {
                    showError(emailInput, errorElement, 'El correo electrónico es obligatorio');
                    isEmailValid = false;
                    return false;
                }
                
                if (!emailRegex.test(email)) {
                    showError(emailInput, errorElement, 'Por favor, introduce un correo electrónico válido');
                    isEmailValid = false;
                    return false;
                }
                
                showSuccess(emailInput);
                isEmailValid = true;
                return true;
            }
            
            function validatePassword() {
                const password = passwordInput.value;
                const errorElement = document.getElementById('password-error');
                
                resetInputState(passwordInput, errorElement);
                
                if (password === '') {
                    showError(passwordInput, errorElement, 'La contraseña es obligatoria');
                    isPasswordValid = false;
                    return false;
                }
                
                if (password.length < 8) {
                    showError(passwordInput, errorElement, 'La contraseña debe tener al menos 8 caracteres');
                    isPasswordValid = false;
                    return false;
                }
                
                showSuccess(passwordInput);
                isPasswordValid = true;
                return true;
            }
            
            function resetInputState(input, errorElement) {
                input.parentElement.classList.remove('error', 'success');
                errorElement.style.display = 'none';
                input.setAttribute('aria-invalid', 'false');
            }
            
            function showError(input, errorElement, message) {
                input.parentElement.classList.add('error');
                input.parentElement.classList.remove('success');
                errorElement.textContent = message;
                errorElement.style.display = 'block';
                input.setAttribute('aria-invalid', 'true');
            }
            
            function showSuccess(input) {
                input.parentElement.classList.add('success');
                input.parentElement.classList.remove('error');
                input.setAttribute('aria-invalid', 'false');
            }
            
            function showLoadingState() {
                btnText.style.display = 'none';
                btnLoading.style.display = 'inline';
                submitBtn.disabled = true;
                submitBtn.style.opacity = '0.8';
                submitBtn.style.cursor = 'not-allowed';
            }
            
            function showValidationErrors() {
                errorList.innerHTML = '';
                
                if (!isEmailValid) {
                    errorList.innerHTML += '<li><i class="bi bi-exclamation-circle" aria-hidden="true"></i> Correo electrónico inválido</li>';
                }
                
                if (!isPasswordValid) {
                    errorList.innerHTML += '<li><i class="bi bi-exclamation-circle" aria-hidden="true"></i> Contraseña inválida</li>';
                }
                
                errorAlert.style.display = 'block';
                successAlert.style.display = 'none';
                
                // Enfocar el primer campo con error
                if (!isEmailValid) {
                    emailInput.focus();
                } else if (!isPasswordValid) {
                    passwordInput.focus();
                }
            }
            
            // Mejora: Validar campos al cargar la página si ya tienen valor
            if (emailInput.value) validateEmail();
            if (passwordInput.value) validatePassword();
        });
    </script>

    <style>
        .spinning {
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        .bi {
            vertical-align: middle;
        }
        
        .mr-2 {
            margin-right: 0.5rem;
        }
    </style>
</body>
</html>