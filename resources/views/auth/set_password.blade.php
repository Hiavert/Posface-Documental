<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Establecer nueva contraseña - POSFACE</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', 'Roboto', sans-serif;
        }
        
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #1a5a8d 0%, #0b2e59 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .reset-container {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            width: 100%;
            max-width: 450px;
            overflow: hidden;
            position: relative;
        }
        
        .reset-header {
            background: linear-gradient(to right, #1a5a8d, #0b2e59);
            padding: 30px 20px;
            text-align: center;
            color: white;
        }
        
        .reset-header h2 {
            font-weight: 600;
            font-size: 24px;
            margin-bottom: 8px;
        }
        
        .reset-header p {
            opacity: 0.85;
            font-size: 15px;
        }
        
        .reset-body {
            padding: 30px;
        }
        
        .input-container {
            position: relative;
            margin-bottom: 25px;
        }
        
        .input-container i {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            font-size: 18px;
            z-index: 2;
            color: #6c757d;
            left: 15px;
        }
        
        .input-container input {
            width: 100%;
            padding: 14px 20px 14px 50px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s;
            background: #f9f9f9;
            height: 50px;
            color: #333;
        }
        
        .input-container input:focus {
            border-color: #1a5a8d;
            outline: none;
            box-shadow: 0 0 0 3px rgba(26, 90, 141, 0.1);
            background: #fff;
        }
        
        .input-container.error input {
            border-color: #e53935;
            background: #fff5f5;
        }
        
        .error-message {
            color: #e53935;
            font-size: 13px;
            margin-top: 8px;
            display: none;
            padding-left: 5px;
        }
        
        .password-rules {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            border-left: 4px solid #1a5a8d;
        }
        
        .password-rules h4 {
            color: #1a5a8d;
            font-size: 15px;
            margin-bottom: 8px;
        }
        
        .password-rules ul {
            padding-left: 20px;
            font-size: 13px;
            color: #6c757d;
        }
        
        .password-rules li {
            margin-bottom: 5px;
        }
        
        .btn {
            background: linear-gradient(to right, #1a5a8d, #0b2e59);
            color: white;
            border: none;
            padding: 14px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            width: 100%;
            margin-top: 10px;
            height: 50px;
            box-shadow: 0 4px 6px rgba(11, 46, 89, 0.15);
        }
        
        .btn:hover {
            background: linear-gradient(to right, #0b2e59, #1a5a8d);
            box-shadow: 0 5px 12px rgba(11, 46, 89, 0.25);
        }
        
        .reset-footer {
            text-align: center;
            padding: 20px;
            font-size: 13px;
            color: #6c757d;
            border-top: 1px solid #eee;
            background: #f9f9f9;
        }
        
        .alert {
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 25px;
            font-size: 14px;
        }
        
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            cursor: pointer;
            font-size: 18px;
            transition: color 0.2s;
            z-index: 3;
        }
        
        .password-toggle:hover {
            color: #1a5a8d;
        }
        
        @media (max-width: 480px) {
            .reset-container {
                max-width: 100%;
            }
            
            .reset-body {
                padding: 25px 20px;
            }
            
            .reset-header {
                padding: 25px 15px;
            }
            
            .reset-header h2 {
                font-size: 22px;
            }
            
            .input-container input {
                padding: 12px 15px 12px 45px;
                height: 46px;
                font-size: 14px;
            }
            
            .btn {
                height: 46px;
                font-size: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="reset-container">
        <div class="reset-header">
            <h2>Establecer nueva contraseña</h2>
            <p>Ingrese y confirme su nueva contraseña</p>
        </div>
        
        <div class="reset-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Por favor corrige los siguientes errores:</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <div class="password-rules">
                <h4>Requisitos de contraseña:</h4>
                <ul>
                    <li>Mínimo 8 caracteres</li>
                    <li>Al menos una letra mayúscula</li>
                    <li>Al menos un número o carácter especial</li>
                </ul>
            </div>
            
            <form id="passwordResetForm" method="POST" action="{{ route('password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                
                <!-- Campo de correo visible para validación -->
                <div class="input-container" id="email-container">
                    <i class="bi bi-envelope"></i>
                    <input type="email" name="email" id="email" placeholder="Correo electrónico" required 
                           value="{{ $email ?? old('email') }}" 
                           maxlength="50" 
                           oninput="validarEmail(this)"
                           onkeypress="return permitirCaracteresEmail(event)">
                    <div class="error-message" id="email-error"></div>
                </div>
                
                <div class="input-container" id="password-container">
                    <i class="bi bi-lock"></i>
                    <input type="password" name="password" id="password" placeholder="Nueva contraseña" required minlength="8">
                    <i class="bi bi-eye password-toggle" id="toggle-password"></i>
                    <div class="error-message" id="password-error"></div>
                </div>
                
                <div class="input-container" id="confirm-container">
                    <i class="bi bi-lock"></i>
                    <input type="password" name="password_confirmation" id="confirm-password" placeholder="Confirmar contraseña" required minlength="8">
                    <i class="bi bi-eye-slash password-toggle" id="toggle-confirm"></i>
                    <div class="error-message" id="confirm-error"></div>
                </div>
                
                <button type="submit" class="btn">Guardar contraseña</button>
            </form>
        </div>
        
        <div class="reset-footer">
            <p>&copy; {{ date('Y') }} POSFACE - Universidad Nacional Autónoma de Honduras</p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');
            const confirmInput = document.getElementById('confirm-password');
            const togglePassword = document.getElementById('toggle-password');
            const toggleConfirm = document.getElementById('toggle-confirm');
            const form = document.getElementById('passwordResetForm');
            
            // Toggle para mostrar/ocultar contraseña
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.classList.toggle('bi-eye');
                this.classList.toggle('bi-eye-slash');
            });
            
            // Toggle para mostrar/ocultar confirmación de contraseña
            toggleConfirm.addEventListener('click', function() {
                const type = confirmInput.getAttribute('type') === 'password' ? 'text' : 'password';
                confirmInput.setAttribute('type', type);
                this.classList.toggle('bi-eye');
                this.classList.toggle('bi-eye-slash');
            });
            
            // Validación en tiempo real
            emailInput.addEventListener('input', function() {
                validarEmail(this);
            });
            passwordInput.addEventListener('input', validatePassword);
            confirmInput.addEventListener('input', validateConfirm);
            
            // Validación al enviar
            form.addEventListener('submit', function(e) {
                const emailValid = validarEmail(emailInput);
                const passwordValid = validatePassword();
                const confirmValid = validateConfirm();
                
                if (!emailValid || !passwordValid || !confirmValid) {
                    e.preventDefault();
                    if (!emailValid) {
                        showEmailAlert();
                    }
                }
            });
            
            // Función para mostrar alerta de email
            function showEmailAlert() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error en el correo electrónico',
                    text: 'El correo electrónico no cumple con los requisitos: máximo 50 caracteres y no más de 3 letras iguales consecutivas.',
                    confirmButtonColor: '#1a5a8d',
                    confirmButtonText: 'Entendido'
                });
            }
            
            // Funciones de validación
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
                
                if (!/[A-Z]/.test(password)) {
                    showError(passwordInput, errorElement, 'Debe contener al menos una letra mayúscula');
                    return false;
                }
                
                if (!/[^A-Za-z0-9]/.test(password) && !/[0-9]/.test(password)) {
                    showError(passwordInput, errorElement, 'Debe contener al menos un número o carácter especial');
                    return false;
                }
                
                return true;
            }
            
            function validateConfirm() {
                const password = passwordInput.value;
                const confirm = confirmInput.value;
                const errorElement = document.getElementById('confirm-error');
                
                confirmInput.parentElement.classList.remove('error', 'success');
                errorElement.style.display = 'none';
                
                if (confirm === '') {
                    showError(confirmInput, errorElement, 'Por favor confirme su contraseña');
                    return false;
                }
                
                if (password !== confirm) {
                    showError(confirmInput, errorElement, 'Las contraseñas no coinciden');
                    return false;
                }
                
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
        });
    </script>
</body>
</html>
