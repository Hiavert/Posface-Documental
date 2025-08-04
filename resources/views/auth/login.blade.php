<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar sesión - POSFACE</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body>
     <div class="container">
        <!-- Panel Izquierdo -->
        <div class="left-panel">
             <img src="{{ asset('Imagen/Posface_logo.jpeg') }}" alt="Logo POSFACE" class="logo-img" />
            <p>Formamos profesionales con valores y visión gerencial para el desarrollo económico del país.</p>
            <div class="socials">
                <a href="https://posface.unah.edu.hn/" target="_blank" class="info-link">
                    <i class="bi bi-globe"></i>
                </a>
                <a href="https://www.instagram.com/posface_oficial" target="_blank" class="info-link">
                    <i class="bi bi-instagram"></i>
                </a>
            </div>
        </div>

          <!-- Panel Derecho -->
        <div class="right-panel">
            <p class="small-text">Bienvenido al sistema de gestión académica</p>
            <h2>Iniciar sesión</h2>

            <!-- Mensajes de error/success -->
            <div class="alert alert-danger" id="error-alert" style="display: none;">
                <ul id="error-list"></ul>
            </div>
            
            <div class="alert alert-success" id="success-alert" style="display: none;">
                ¡Inicio de sesión exitoso! Redirigiendo...
            </div>

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <!-- Campo de email con icono de usuario a la izquierda -->
                <div class="input-container" id="email-container">
                    <i class="bi bi-person"></i>
                    <input type="email" name="email" id="email" placeholder="usuario@unah.edu.hn" required />
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

            <p style="text-align: right; margin-top: 15px;">
                <a href="{{ route('password.request') }}">¿Olvidaste tu contraseña?</a>
            </p>
            
            <div class="footer-text">
                &copy; 2023 POSFACE - Universidad Nacional Autónoma de Honduras
            </div>
        </div>
    </div>
</body>
  <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('loginForm');
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');
            const togglePassword = document.getElementById('toggle-password');
            const emailClear = document.getElementById('email-clear');
            const errorAlert = document.getElementById('error-alert');
            const errorList = document.getElementById('error-list');
            const successAlert = document.getElementById('success-alert');
            
            // Validación en tiempo real
            emailInput.addEventListener('input', validateEmail);
            passwordInput.addEventListener('input', validatePassword);
            
            // Toggle para mostrar/ocultar contraseña
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.innerHTML = type === 'password' ? '<i class="bi bi-eye"></i>' : '<i class="bi bi-eye-slash"></i>';
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
                    // Simulación de inicio de sesión exitoso
                    successAlert.textContent = '¡Inicio de sesión exitoso! Redirigiendo...';
                    successAlert.style.display = 'block';
                    errorAlert.style.display = 'none';
                    
                    // Simular redirección después de 1.5 segundos
                    setTimeout(() => {
                        alert('Redirigiendo al panel de control...');
                        form.reset();
                        successAlert.style.display = 'none';
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
                }
            });
            
            // Enlace "Olvidé mi contraseña"
            document.getElementById('forgot-password').addEventListener('click', function(e) {
                e.preventDefault();
                alert('Se ha enviado un enlace de recuperación a su correo electrónico');
            });
            
            // Funciones de validación
            function validateEmail() {
                const email = emailInput.value.trim();
                const errorElement = document.getElementById('email-error');
                const emailRegex = /^[a-zA-Z0-9._-]+@(unah\.edu\.hn)$/;
                
                // Resetear estado
                document.getElementById('email-valid').style.display = 'none';
                document.getElementById('email-invalid').style.display = 'none';
                errorElement.style.display = 'none';
                emailInput.parentElement.classList.remove('error', 'success');
                
                if (email === '') {
                    showError(emailInput, 'email-invalid', errorElement, 'El correo electrónico es obligatorio');
                    return false;
                }
                
                if (!emailRegex.test(email)) {
                    showError(emailInput, 'email-invalid', errorElement, 'Debe ser un correo institucional (@unah.edu.hn)');
                    return false;
                }
                
                showSuccess(emailInput, 'email-valid');
                return true;
            }
            
            function validatePassword() {
                const password = passwordInput.value;
                const errorElement = document.getElementById('password-error');
                
                // Resetear estado
                document.getElementById('password-valid').style.display = 'none';
                document.getElementById('password-invalid').style.display = 'none';
                errorElement.style.display = 'none';
                passwordInput.parentElement.classList.remove('error', 'success');
                
                if (password === '') {
                    showError(passwordInput, 'password-invalid', errorElement, 'La contraseña es obligatoria');
                    return false;
                }
                
                if (password.length < 8) {
                    showError(passwordInput, 'password-invalid', errorElement, 'La contraseña debe tener al menos 8 caracteres');
                    return false;
                }
                
                showSuccess(passwordInput, 'password-valid');
                return true;
            }
            
            function showError(input, invalidIconId, errorElement, message) {
                input.parentElement.classList.add('error');
                document.getElementById(invalidIconId).style.display = 'block';
                errorElement.textContent = message;
                errorElement.style.display = 'block';
            }
            
            function showSuccess(input, validIconId) {
                input.parentElement.classList.add('success');
                document.getElementById(validIconId).style.display = 'block';
            }
        });
    </script>
</html>
