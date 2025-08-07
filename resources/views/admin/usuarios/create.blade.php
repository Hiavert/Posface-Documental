@extends('adminlte::page')

@section('title', 'Crear usuario')

@section('content_header')
    <div class="unah-header">
         <h1 class="mb-0"><i class="fas fa-user-plus mr-2"></i> Crear usuario</h1>
    </div>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg">
                <div class="card-body p-0">
                    <div class="row no-gutters">
                        <div class="col-12">
                            <div class="p-4">
                                <p class="text-posface-primary small font-weight-bold mb-1">GESTIÓN DE USUARIOS</p>
                                <h2 class="h3 font-weight-bold text-dark mb-4">Crear usuario</h2>
                                @if ($errors->any())
                                    <div class="alert alert-danger custom-alert">
                                        <ul class="mb-0 pl-3">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                @if (session('success'))
                                    <div class="alert alert-success custom-alert">
                                        {{ session('success') }}
                                    </div>
                                @endif
                                <form id="crearUsuarioForm" method="POST" action="{{ route('usuarios.store') }}" novalidate>
                                    @csrf
                                    <div class="form-group">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-white border-right-0"><i class="bi bi-person text-posface-primary"></i></span>
                                            </div>
                                            <input type="text" id="nombres" name="nombres" class="form-control border-left-0" placeholder="Nombres" value="{{ old('nombres') }}" required 
                                                   oninput="validarNombreApellido(this)" maxlength="50" />
                                        </div>
                                        <div class="invalid-feedback" id="nombres_error"></div>
                                    </div>
                                    <div class="form-group">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-white border-right-0"><i class="bi bi-person text-posface-primary"></i></span>
                                            </div>
                                            <input type="text" id="apellidos" name="apellidos" class="form-control border-left-0" placeholder="Apellidos" value="{{ old('apellidos') }}" required 
                                                   oninput="validarNombreApellido(this)" maxlength="50" />
                                        </div>
                                        <div class="invalid-feedback" id="apellidos_error"></div>
                                    </div>
                                    <div class="form-group">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-white border-right-0"><i class="bi bi-envelope text-posface-primary"></i></span>
                                            </div>
                                            <input type="email" id="email" name="email" class="form-control border-left-0" placeholder="Correo electrónico" value="{{ old('email') }}" required />
                                        </div>
                                        <div class="invalid-feedback" id="email_error"></div>
                                    </div>
                                    <div class="form-group">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-white border-right-0"><i class="bi bi-card-text text-posface-primary"></i></span>
                                            </div>
                                            <input type="text" id="identidad" name="identidad" class="form-control border-left-0" placeholder="Identidad (13 dígitos)" value="{{ old('identidad') }}" required 
                                                   oninput="validarIdentidad(this)" maxlength="13" />
                                        </div>
                                        <div class="invalid-feedback" id="identidad_error"></div>
                                    </div>
                                    <div class="form-group">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-white border-right-0"><i class="bi bi-person-badge text-posface-primary"></i></span>
                                            </div>
                                            <select id="rol" name="rol" class="form-control border-left-0" required>
                                                <option value="">Seleccione un rol</option>
                                                @foreach($roles as $rol)
                                                    <option value="{{ $rol->id_rol }}" {{ old('rol') == $rol->id_rol ? 'selected' : '' }}>{{ $rol->nombre_rol }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="invalid-feedback" id="rol_error"></div>
                                    </div>
                                    <div class="form-group">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-white border-right-0"><i class="bi bi-toggle-on text-posface-primary"></i></span>
                                            </div>
                                            <select id="estado" name="estado" class="form-control border-left-0" required>
                                                <option value="0" {{ old('estado', '0') == '0' ? 'selected' : '' }}>Inactivo (por defecto)</option>
                                                <option value="1" {{ old('estado') == '1' ? 'selected' : '' }}>Activo</option>
                                            </select>
                                        </div>
                                        <div class="invalid-feedback" id="estado_error"></div>
                                    </div>
                                    <button type="submit" class="btn btn-posface btn-block py-2 font-weight-bold">
                                        CREAR USUARIO
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Estilos CSS personalizados --}}
<style>
    :root {
        --posface-dark-blue: #0A225F;
        --posface-light-blue: #3E8DCF;
        --posface-white: #FFFFFF;
        --posface-text-dark: #333333;
        --error-color: #dc3545;
        --success-color: #28a745;
    }

    .unah-header {
        background: linear-gradient(135deg, #0b2e59, #1a5a8d);
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        color: white;
        margin-bottom: 25px;
    }

    .bg-posface-dark {
        background-color: var(--posface-dark-blue) !important;
    }

    .text-posface-primary {
        color: var(--posface-dark-blue) !important;
    }
    
    .logo-img {
        width: 300px;
        height: auto;
        margin-bottom: 30px;
        background-color: transparent;
        padding: 0;
        box-shadow: none;
        border-radius: 12px;
    }

    .btn-posface {
        background-color: var(--posface-dark-blue);
        border-color: var(--posface-dark-blue);
        color: var(--posface-white);
        transition: all 0.3s ease;
    }

    .btn-posface:hover {
        background-color: var(--posface-light-blue);
        border-color: var(--posface-light-blue);
        color: var(--posface-white);
    }

    .card {
        border: none;
        border-radius: 15px;
        overflow: hidden;
    }

    .input-group-text {
        background-color: var(--posface-white) !important;
        border: 1px solid #ced4da;
        border-right: none !important;
    }

    .form-control {
        height: calc(2.25rem + 12px);
        border-left: 0 !important;
        border-radius: 0.25rem;
        border: 1px solid #ced4da;
    }

    .form-control:focus {
        box-shadow: 0 0 0 0.2rem rgba(10, 34, 95, 0.25);
        border-color: var(--posface-dark-blue);
    }

    .custom-alert {
        border-radius: 0.5rem;
        padding: 0.75rem 1.25rem;
        margin-bottom: 1rem;
        border: 1px solid transparent;
    }

    .alert-danger.custom-alert {
        background-color: #f8d7da;
        color: #721c24;
        border-color: #f5c6cb;
    }

    .alert-success.custom-alert {
        background-color: #d4edda;
        color: #155724;
        border-color: #c3e6cb;
    }

    select.form-control {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%23212529' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 0.75rem center;
        background-size: 1rem 1rem;
        padding-right: 2.25rem;
    }
    
    /* Estilos para validaciones */
    .is-invalid {
        border-color: var(--error-color) !important;
    }
    
    .invalid-feedback {
        display: block;
        color: var(--error-color);
        font-size: 0.85rem;
        margin-top: 5px;
        /* Solución para el problema de superposición */
        position: relative;
        z-index: 1;
        min-height: 20px;
        white-space: normal;
        word-wrap: break-word;
    }
    
    /* Indicadores de validación */
    .validation-icon {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        right: 15px;
        font-size: 1.2rem;
        z-index: 10;
    }
    
    .valid-icon {
        color: var(--success-color);
    }
    
    .invalid-icon {
        color: var(--error-color);
    }
    
    /* Contenedor para los iconos */
    .input-icon-container {
        position: relative;
    }
    
    /* Ajustar padding para los inputs con iconos */
    .has-validation-icon {
        padding-right: 40px !important;
    }

    /* Espacio adicional para evitar superposición */
    .form-group {
        margin-bottom: 25px;
        position: relative;
        z-index: 1;
    }

    @media (max-width: 768px) {
        .bg-posface-dark {
            border-radius: 15px 15px 0 0 !important;
        }
        
        /* Ajuste adicional para dispositivos móviles */
        .invalid-feedback {
            font-size: 0.75rem;
            min-height: 18px;
        }
    }
</style>

@endsection

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('crearUsuarioForm');
    const nombresInput = document.getElementById('nombres');
    const apellidosInput = document.getElementById('apellidos');
    const emailInput = document.getElementById('email');
    const identidadInput = document.getElementById('identidad');
    const rolSelect = document.getElementById('rol');
    const estadoSelect = document.getElementById('estado');
    
    // Elementos de error
    const nombresError = document.getElementById('nombres_error');
    const apellidosError = document.getElementById('apellidos_error');
    const emailError = document.getElementById('email_error');
    const identidadError = document.getElementById('identidad_error');
    const rolError = document.getElementById('rol_error');
    const estadoError = document.getElementById('estado_error');
    
    // Expresiones regulares
    const nombreRegex = /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/;
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const identidadRegex = /^\d{13}$/;
    
    // Agregar iconos de validación solo a campos de texto
    agregarIconoValidacion(nombresInput);
    agregarIconoValidacion(apellidosInput);
    agregarIconoValidacion(identidadInput);
    
    // Event listeners para validación en tiempo real
    nombresInput.addEventListener('input', function() {
        validarNombreApellido(this);
        validateNombres();
    });
    apellidosInput.addEventListener('input', function() {
        validarNombreApellido(this);
        validateApellidos();
    });
    emailInput.addEventListener('input', validateEmail);
    identidadInput.addEventListener('input', function() {
        validarIdentidad(this);
        validateIdentidad();
    });
    rolSelect.addEventListener('change', validateRol);
    estadoSelect.addEventListener('change', validateEstado);
    
    // Validar formulario al enviar
    form.addEventListener('submit', function(event) {
        // Validar todos los campos
        const isNombresValid = validateNombres();
        const isApellidosValid = validateApellidos();
        const isEmailValid = validateEmail();
        const isIdentidadValid = validateIdentidad();
        const isRolValid = validateRol();
        const isEstadoValid = validateEstado();
        
        // Si algún campo no es válido, prevenir el envío
        if (!(isNombresValid && isApellidosValid && isEmailValid && 
              isIdentidadValid && isRolValid && isEstadoValid)) {
            event.preventDefault();
            
            // Mostrar mensaje general si hay errores
            const errorMessage = "Por favor, corrija los errores en el formulario";
            if (!document.querySelector('.form-error-alert')) {
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-danger custom-alert form-error-alert';
                alertDiv.textContent = errorMessage;
                form.insertBefore(alertDiv, form.firstChild);
            }
        }
    });
    
    // Función para agregar icono de validación
    function agregarIconoValidacion(input) {
        // Verificar si ya existe un icono
        if (document.getElementById(input.id + '_icon')) return;
        
        const icon = document.createElement('span');
        icon.className = 'validation-icon';
        icon.id = input.id + '_icon';
        icon.style.display = 'none';
        
        const container = input.parentElement;
        container.classList.add('input-icon-container');
        input.classList.add('has-validation-icon');
        
        container.appendChild(icon);
    }
    
    // Función para mostrar icono de validación
    function mostrarIcono(input, isValid) {
        const icon = document.getElementById(input.id + '_icon');
        if (icon) {
            icon.style.display = 'block';
            icon.className = 'validation-icon ' + (isValid ? 'valid-icon fas fa-check-circle' : 'invalid-icon fas fa-times-circle');
        }
    }
    
    // Función para validar nombres y apellidos
    function validarNombreApellido(input) {
        // Permitir solo letras y espacios
        input.value = input.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '');
        
        // Limitar longitud a 50 caracteres
        if (input.value.length > 50) {
            input.value = input.value.substring(0, 50);
        }
        
        // Verificar si hay más de 3 letras iguales consecutivas
        if (/([a-zA-ZáéíóúÁÉÍÓÚñÑ])\1{3,}/.test(input.value)) {
            // Eliminar la cuarta letra consecutiva y posteriores
            input.value = input.value.replace(/([a-zA-ZáéíóúÁÉÍÓÚñÑ])\1{3,}/g, function(match) {
                return match.substring(0, 3);
            });
        }
    }
    
    // Función para validar identidad
    function validarIdentidad(input) {
        // Permitir solo números
        input.value = input.value.replace(/\D/g, '');
        
        // Limitar a 13 dígitos
        if (input.value.length > 13) {
            input.value = input.value.substring(0, 13);
        }
    }
    
    // Funciones de validación
    function validateNombres() {
        const value = nombresInput.value.trim();
        let isValid = true;
        
        // Limpiar mensaje de error previo
        nombresError.textContent = '';
        nombresInput.classList.remove('is-invalid');
        mostrarIcono(nombresInput, false);
        
        // Validar requerido
        if (value === '') {
            nombresError.textContent = 'Los nombres son obligatorios.';
            nombresInput.classList.add('is-invalid');
            isValid = false;
        } 
        // Validar formato (solo letras y espacios)
        else if (!nombreRegex.test(value)) {
            nombresError.textContent = 'Los nombres solo deben contener letras y espacios.';
            nombresInput.classList.add('is-invalid');
            isValid = false;
        }
        // Validar caracteres repetidos
        else if (/([a-zA-ZáéíóúÁÉÍÓÚñÑ])\1{3,}/.test(value)) {
            nombresError.textContent = 'No puede haber más de tres letras iguales consecutivas.';
            nombresInput.classList.add('is-invalid');
            isValid = false;
        }
        // Validar longitud
        else if (value.length > 50) {
            nombresError.textContent = 'Los nombres no pueden exceder los 50 caracteres.';
            nombresInput.classList.add('is-invalid');
            isValid = false;
        } else {
            mostrarIcono(nombresInput, true);
        }
        
        return isValid;
    }
    
    function validateApellidos() {
        const value = apellidosInput.value.trim();
        let isValid = true;
        
        // Limpiar mensaje de error previo
        apellidosError.textContent = '';
        apellidosInput.classList.remove('is-invalid');
        mostrarIcono(apellidosInput, false);
        
        // Validar requerido
        if (value === '') {
            apellidosError.textContent = 'Los apellidos son obligatorios.';
            apellidosInput.classList.add('is-invalid');
            isValid = false;
        } 
        // Validar formato (solo letras y espacios)
        else if (!nombreRegex.test(value)) {
            apellidosError.textContent = 'Los apellidos solo deben contener letras y espacios.';
            apellidosInput.classList.add('is-invalid');
            isValid = false;
        }
        // Validar caracteres repetidos
        else if (/([a-zA-ZáéíóúÁÉÍÓÚñÑ])\1{3,}/.test(value)) {
            apellidosError.textContent = 'No puede haber más de tres letras iguales consecutivas.';
            apellidosInput.classList.add('is-invalid');
            isValid = false;
        }
        // Validar longitud
        else if (value.length > 50) {
            apellidosError.textContent = 'Los apellidos no pueden exceder los 50 caracteres.';
            apellidosInput.classList.add('is-invalid');
            isValid = false;
        } else {
            mostrarIcono(apellidosInput, true);
        }
        
        return isValid;
    }
    
    function validateEmail() {
        const value = emailInput.value.trim();
        let isValid = true;
        
        // Limpiar mensaje de error previo
        emailError.textContent = '';
        emailInput.classList.remove('is-invalid');
        
        // Validar requerido
        if (value === '') {
            emailError.textContent = 'El correo electrónico es obligatorio.';
            emailInput.classList.add('is-invalid');
            isValid = false;
        } 
        // Validar formato de email
        else if (!emailRegex.test(value)) {
            emailError.textContent = 'Por favor ingrese un correo electrónico válido.';
            emailInput.classList.add('is-invalid');
            isValid = false;
        }
        
        return isValid;
    }
    
    function validateIdentidad() {
        const value = identidadInput.value.trim();
        let isValid = true;
        
        // Limpiar mensaje de error previo
        identidadError.textContent = '';
        identidadInput.classList.remove('is-invalid');
        mostrarIcono(identidadInput, false);
        
        // Validar requerido
        if (value === '') {
            identidadError.textContent = 'La identidad es obligatoria.';
            identidadInput.classList.add('is-invalid');
            isValid = false;
        } 
        // Validar formato (13 dígitos)
        else if (!/^\d{13}$/.test(value)) {
            identidadError.textContent = 'La identidad debe tener exactamente 13 dígitos.';
            identidadInput.classList.add('is-invalid');
            isValid = false;
        } else {
            mostrarIcono(identidadInput, true);
        }
        
        return isValid;
    }
    
    function validateRol() {
        const value = rolSelect.value;
        let isValid = true;
        
        // Limpiar mensaje de error previo
        rolError.textContent = '';
        rolSelect.classList.remove('is-invalid');
        
        // Validar requerido
        if (value === '') {
            rolError.textContent = 'Debe seleccionar un rol.';
            rolSelect.classList.add('is-invalid');
            isValid = false;
        }
        
        return isValid;
    }
    
    function validateEstado() {
        const value = estadoSelect.value;
        let isValid = true;
        
        // Limpiar mensaje de error previo
        estadoError.textContent = '';
        estadoSelect.classList.remove('is-invalid');
        
        // Validar requerido
        if (value === '') {
            estadoError.textContent = 'Debe seleccionar un estado.';
            estadoSelect.classList.add('is-invalid');
            isValid = false;
        }
        
        return isValid;
    }
    
    // Validar campos al cargar la página si ya tienen valores
    validateNombres();
    validateApellidos();
    validateEmail();
    validateIdentidad();
    validateRol();
    validateEstado();
});
</script>
@endsection
