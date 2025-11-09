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
        }
        .btn-primary:hover {
            background: #002244;
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
        
        /* Estilos para el SweetAlert personalizado */
        .swal-overlay {
            background-color: rgba(0, 51, 102, 0.4);
        }
        .swal-modal {
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .swal-title {
            color: #003366;
            font-weight: 600;
        }
        .swal-text {
            color: #555;
            text-align: center;
        }
        .swal-button {
            background: #003366;
            border-radius: 6px;
            font-weight: 600;
        }
        .swal-button:hover {
            background: #002244 !important;
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
                       required autofocus maxlength="50"
                       oninput="validarEmailRecuperacion(this)"
                       onkeypress="return permitirCaracteresEmailRecuperacion(event)">
                @error('email')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <button type="submit" class="btn btn-primary">Enviar enlace</button>
        </form>
        <a href="{{ route('login') }}" class="back-link">
            <i class="bi bi-arrow-left"></i> Volver al inicio de sesión
        </a>
    </div>

    <!-- Incluir SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        // Función para mostrar alertas personalizadas
        function mostrarAlerta(titulo, texto, tipo = 'error') {
            Swal.fire({
                title: titulo,
                text: texto,
                icon: tipo,
                confirmButtonText: 'Aceptar',
                customClass: {
                    popup: 'swal-modal',
                    title: 'swal-title',
                    htmlContainer: 'swal-text',
                    confirmButton: 'swal-button'
                }
            });
        }

        // Función para permitir solo caracteres válidos en email
        function permitirCaracteresEmailRecuperacion(event) {
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
                mostrarAlerta('Carácter no permitido', 'Solo se permiten letras, números y los caracteres @ . - _');
                return false;
            }
            
            return true;
        }

        // Función para validar email en tiempo real
        function validarEmailRecuperacion(input) {
            let valor = input.value;
            
            // Filtrar caracteres no permitidos (solo letras, números, @, ., -, _)
            valor = valor.replace(/[^a-zA-Z0-9@._\-]/g, '');
            
            // Validar que no haya más de 3 letras iguales consecutivas
            const regexRepetidas = /([a-zA-Z])\1{3,}/g;
            if (regexRepetidas.test(valor)) {
                // Eliminar letras repetidas más allá de 3
                valor = valor.replace(regexRepetidas, (match) => {
                    return match.substring(0, 3);
                });
                
                // Mostrar alerta
                mostrarAlerta('Letras repetidas', 'No se permiten más de 3 letras iguales consecutivas');
            }
            
            // Limitar a 50 caracteres
            if (valor.length > 50) {
                valor = valor.substring(0, 50);
                mostrarAlerta('Límite de caracteres', 'El email no puede tener más de 50 caracteres');
            }
            
            // Actualizar el valor del input
            if (input.value !== valor) {
                input.value = valor;
            }
        }

        // Validación al enviar el formulario
        document.getElementById('passwordResetForm').addEventListener('submit', function(e) {
            const emailInput = document.getElementById('email');
            const email = emailInput.value.trim();
            
            // Validar longitud máxima
            if (email.length > 50) {
                e.preventDefault();
                mostrarAlerta('Email demasiado largo', 'El email no puede tener más de 50 caracteres');
                emailInput.focus();
                return false;
            }
            
            // Validar que no tenga más de 3 letras iguales consecutivas
            const regexRepetidas = /([a-zA-Z])\1{3,}/g;
            if (regexRepetidas.test(email)) {
                e.preventDefault();
                mostrarAlerta('Letras repetidas', 'El email no puede tener más de 3 letras iguales consecutivas');
                emailInput.focus();
                return false;
            }
            
            // Validar formato de email básico
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                e.preventDefault();
                mostrarAlerta('Formato inválido', 'Por favor ingresa un email válido');
                emailInput.focus();
                return false;
            }
            
            return true;
        });

        // Aplicar validación inicial si hay valor en el campo
        document.addEventListener('DOMContentLoaded', function() {
            const emailInput = document.getElementById('email');
            if (emailInput && emailInput.value) {
                validarEmailRecuperacion(emailInput);
            }
        });
    </script>
</body>
</html>
