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
                    <i class="bi bi-check-circle-fill"></i> ¡Inicio de sesión exitoso! Redirigiendo...
                </div>

                <form id="loginForm" method="POST" action="{{ route('login') }}">
                    @csrf
                    <!-- Campo de email -->
                    <div class="input-container" id="email-container">
                        <i class="bi bi-envelope-fill input-icon-left"></i>
                        <input type="email" name="email" id="email" placeholder="usuario@correo.com" required />
                        <i class="bi bi-x-circle input-icon-right" id="email-clear" title="Limpiar campo"></i>
                        <div class="error-message" id="email-error"></div>
                    </div>

                    <!-- Campo de contraseña -->
                    <div class="input-container" id="password-container">
                        <i class="bi bi-lock-fill input-icon-left"></i>
                        <input type="password" name="password" id="password" placeholder="Contraseña" required />
                        <i class="bi bi-eye input-icon-right" id="toggle-password" title="Mostrar contraseña"></i>
                        <div class="error-message" id="password-error"></div>
                        <div class="password-strength" id="password-strength"></div>
                    </div>

                    <button type="submit" class="btn" id="submit-btn">
                        <span id="btn-text">Entrar</span>
                        <span id="btn-loading" style="display: none;">
                            <i class="bi bi-arrow-repeat spinning"></i> Procesando...
                        </span>
                    </button>
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
            const submitBtn = document.getElementById('submit-btn');
            const btnText = document.getElementById('btn-text');
            const btnLoading = document.getElementById('btn-loading');
            const passwordStrength = document.getElementById('password-strength');
            
            // Validación en tiempo real
            emailInput.addEventListener('input', validateEmail);
            passwordInput.addEventListener('input', validatePassword);
            
            // Toggle mostrar/ocultar contraseña
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.classList.toggle('bi-eye');
                this.classList.toggle('bi-eye-slash');
                
                // Actualizar título
                const isVisible = type === 'text';
                this.setAttribute('title', isVisible ? 'Ocultar contraseña' : 'Mostrar contraseña');
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
                    showLoadingState();
                    errorAlert.style.display = 'none';
                    successAlert.style.display = 'block';
                    setTimeout(() => form.submit(), 1500);
                } else {
                    showValidationErrors();
                }
            });
            
            // Validar email
            function validateEmail() {
                const email = emailInput.value.trim();
                const errorElement = document.getElementById('email-error');
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                
                resetInputState(emailInput, errorElement);
                
                if (email === '') {
                    showError(emailInput, errorElement, 'El correo electrónico es obligatorio');
                    return false;
                }
                
                if (!emailRegex.test(email)) {
                    showError(emailInput, errorElement, 'Debe ser un correo electrónico válido');
                    return false;
                }
                
                // Validar caracteres repetidos (más de 3 veces consecutivas)
                if (hasRepeatedCharacters(email, 3)) {
                    showError(emailInput, errorElement, 'El correo contiene caracteres repetidos de forma sospechosa');
                    return false;
                }
                
                showSuccess(emailInput);
                return true;
            }
            
            // Validar contraseña
            function validatePassword() {
                const password = passwordInput.value;
                const errorElement = document.getElementById('password-error');
                
                resetInputState(passwordInput, errorElement);
                passwordStrength.style.display = 'none';
                
                if (password === '') {
                    showError(passwordInput, errorElement, 'La contraseña es obligatoria');
                    return false;
                }
                
                // Validaciones de seguridad
                const errors = [];
                const strength = checkPasswordStrength(password);
                
                if (password.length < 8) {
                    errors.push('Al menos 8 caracteres');
                }
                
                if (!/(?=.*[a-z])/.test(password)) {
                    errors.push('Una letra minúscula');
                }
                
                if (!/(?=.*[A-Z])/.test(password)) {
                    errors.push('Una letra mayúscula');
                }
                
                if (!/(?=.*\d)/.test(password)) {
                    errors.push('Un número');
                }
                
                // Validar caracteres repetidos (más de 3 veces consecutivas)
                if (hasRepeatedCharacters(password, 3)) {
                    errors.push('No más de 3 caracteres idénticos consecutivos');
                }
                
                if (errors.length > 0) {
                    showError(passwordInput, errorElement, 'Requisitos: ' + errors.join(', '));
                    updatePasswordStrength(strength);
                    return false;
                }
                
                showSuccess(passwordInput);
                updatePasswordStrength(strength);
                return true;
            }
            
            // Verificar si hay caracteres repetidos
            function hasRepeatedCharacters(text, maxRepeat) {
                const regex = new RegExp(`(.)\\1{${maxRepeat},}`, 'g');
                return regex.test(text);
            }
            
            // Verificar fortaleza de la contraseña
            function checkPasswordStrength(password) {
                let score = 0;
                
                // Longitud
                if (password.length >= 8) score += 1;
                if (password.length >= 12) score += 1;
                
                // Variedad de caracteres
                if (/[a-z]/.test(password)) score += 1;
                if (/[A-Z]/.test(password)) score += 1;
                if (/[0-9]/.test(password)) score += 1;
                
                // Penalizar caracteres repetidos
                if (hasRepeatedCharacters(password, 2)) score -= 1;
                if (hasRepeatedCharacters(password, 3)) score -= 2;
                
                return Math.max(0, score);
            }
            
            // Actualizar indicador de fortaleza
            function updatePasswordStrength(strength) {
                passwordStrength.style.display = 'block';
                passwordStrength.className = 'password-strength';
                
                if (strength <= 2) {
                    passwordStrength.classList.add('strength-weak');
                    passwordStrength.innerHTML = '<i class="bi bi-exclamation-triangle"></i> Contraseña débil';
                } else if (strength <= 4) {
                    passwordStrength.classList.add('strength-medium');
                    passwordStrength.innerHTML = '<i class="bi bi-check-circle"></i> Contraseña media';
                } else {
                    passwordStrength.classList.add('strength-strong');
                    passwordStrength.innerHTML = '<i class="bi bi-shield-check"></i> Contraseña fuerte';
                }
            }
            
            function resetInputState(input, errorElement) {
                input.parentElement.classList.remove('error', 'success');
                errorElement.style.display = 'none';
            }
            
            function showError(input, errorElement, message) {
                input.parentElement.classList.add('error');
                errorElement.textContent = message;
                errorElement.style.display = 'block';
            }
            
            function showSuccess(input) {
                input.parentElement.classList.add('success');
            }
            
            function showLoadingState() {
                btnText.style.display = 'none';
                btnLoading.style.display = 'inline';
                submitBtn.disabled = true;
            }
            
            function showValidationErrors() {
                errorList.innerHTML = '';
                
                const emailError = document.getElementById('email-error');
                const passwordError = document.getElementById('password-error');
                
                if (emailError.style.display === 'block') {
                    errorList.innerHTML += '<li><i class="bi bi-x-circle"></i> ' + emailError.textContent + '</li>';
                }
                
                if (passwordError.style.display === 'block') {
                    errorList.innerHTML += '<li><i class="bi bi-x-circle"></i> ' + passwordError.textContent + '</li>';
                }
                
                errorAlert.style.display = 'block';
                successAlert.style.display = 'none';
            }
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
            vertical-align: -0.125em;
        }
    </style>
</body>
</html>