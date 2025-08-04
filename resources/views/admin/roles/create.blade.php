@extends('adminlte::page')

@section('title', 'Crear Nuevo Rol')

@section('content_header')
    <div class="unah-header">
        <h1 class="mb-0"><i class="fas fa-plus mr-2"></i> Crear Nuevo Rol</h1>
        <p class="mb-0">Universidad Nacional Autónoma de Honduras - Posgrado en Informática Administrativa</p>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-user-tag mr-2"></i> Información del Rol</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('roles.store') }}" method="POST">
                            @csrf
                            
                            <div class="form-group">
                                <label for="nombre_rol">Nombre del Rol *</label>
                                <input type="text" class="form-control @error('nombre_rol') is-invalid @enderror" 
                                       id="nombre_rol" name="nombre_rol" value="{{ old('nombre_rol') }}" 
                                       placeholder="Ej: Administrador, Usuario, Supervisor" required>
                                @error('nombre_rol')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="descripcion_rol">Descripción</label>
                                <textarea class="form-control @error('descripcion_rol') is-invalid @enderror" 
                                          id="descripcion_rol" name="descripcion_rol" rows="3" 
                                          placeholder="Descripción del rol y sus responsabilidades">{{ old('descripcion_rol') }}</textarea>
                                @error('descripcion_rol')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="estado_rol">Estado *</label>
                                <select class="form-control @error('estado_rol') is-invalid @enderror" 
                                        id="estado_rol" name="estado_rol" required>
                                    <option value="">Seleccionar estado</option>
                                    <option value="1" {{ old('estado_rol') == '1' ? 'selected' : '' }}>Activo</option>
                                    <option value="0" {{ old('estado_rol') == '0' ? 'selected' : '' }}>Inactivo</option>
                                </select>
                                @error('estado_rol')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save mr-1"></i> Crear Rol
                                </button>
                                <a href="{{ route('roles.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left mr-1"></i> Cancelar
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card info-card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-info-circle mr-2"></i> Información</h3>
                    </div>
                    <div class="card-body">
                        <p class="mb-3"><strong><i class="fas fa-tag me-2"></i>Nombre del Rol:</strong> Debe ser único y descriptivo.</p>
                        <p class="mb-3"><strong><i class="fas fa-align-left me-2"></i>Descripción:</strong> Explica las responsabilidades del rol.</p>
                        <p class="mb-3"><strong><i class="fas fa-power-off me-2"></i>Estado:</strong> 
                            <span class="status-indicator status-active"></span>Activo - Puede ser asignado
                            <br>
                            <span class="status-indicator status-inactive"></span>Inactivo - No puede ser asignado
                        </p>
                        <hr>
                        <p class="text-muted mb-0">
                            <i class="fas fa-lightbulb me-1"></i>
                            Después de crear el rol, podrás asignarle permisos específicos.
                        </p>
                    </div>
                </div>
            </div>    
        </div>
    </div>
@stop 
<style>
      .unah-header {
            background: linear-gradient(135deg, #0b2e59, #1a5a8d);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            color: white;
            margin-bottom: 25px;
        }
        
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
            overflow: hidden;
        }
        
        .card:hover {
            transform: translateY(-5px);
        }
        
        .card-header {
            background: linear-gradient(to right, #f8f9fa, #e9ecef);
            border-bottom: 1px solid #dee2e6;
            font-weight: 600;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #1a5a8d;
            box-shadow: 0 0 0 0.25rem rgba(26, 90, 141, 0.25);
        }
        
        .btn-primary {
            background: linear-gradient(to right, #0b2e59, #1a5a8d);
            border: none;
        }
        
        .btn-secondary {
            background: linear-gradient(to right, #6c757d, #495057);
            border: none;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        .info-card .card-body {
            background-color: #f8f9fa;
            border-radius: 0 0 10px 10px;
        }
        
        .info-card .card-header {
            background: linear-gradient(to right, #e9ecef, #dee2e6);
        }
        
        .input-icon {
            position: relative;
        }
        
        .input-icon i {
            position: absolute;
            left: 12px;
            top: 12px;
            color: #6c757d;
        }
        
        .input-icon input, .input-icon select, .input-icon textarea {
            padding-left: 40px;
        }
        
        .error-message {
            display: none;
            color: #dc3545;
            font-size: 0.875em;
            margin-top: 5px;
        }
        
        .is-invalid {
            border-color: #dc3545 !important;
        }
        
        .is-valid {
            border-color: #198754 !important;
        }
        
        .validation-icon {
            position: absolute;
            right: 12px;
            top: 12px;
            display: none;
        }
        
        .fa-check-circle {
            color: #198754;
        }
        
        .fa-exclamation-circle {
            color: #dc3545;
        }
        
        .status-indicator {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 5px;
        }
        
        .status-active {
            background-color: #28a745;
        }
        
        .status-inactive {
            background-color: #dc3545;
        }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('roleForm');
        const nombreInput = document.getElementById('nombre_rol');
        const descripcionInput = document.getElementById('descripcion_rol');
        const estadoSelect = document.getElementById('estado_rol');
        
        // Elementos de validación
        const nombreValid = document.getElementById('nombre_valid');
        const nombreInvalid = document.getElementById('nombre_invalid');
        const descValid = document.getElementById('desc_valid');
        const descInvalid = document.getElementById('desc_invalid');
        const estadoValid = document.getElementById('estado_valid');
        const estadoInvalid = document.getElementById('estado_invalid');
        
        // Validación en tiempo real
        nombreInput.addEventListener('input', validarNombre);
        descripcionInput.addEventListener('input', validarDescripcion);
        estadoSelect.addEventListener('change', validarEstado);
        
        // Validación al enviar
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            let isValid = true;
            if (!validarNombre()) isValid = false;
            if (!validarDescripcion()) isValid = false;
            if (!validarEstado()) isValid = false;
            
            if (isValid) {
                // Simular envío exitoso
                Swal.fire({
                    title: '¡Rol creado!',
                    text: 'El nuevo rol se ha creado exitosamente.',
                    icon: 'success',
                    confirmButtonText: 'Continuar',
                    confirmButtonColor: '#0b2e59'
                }).then(() => {
                    form.reset();
                    // Resetear estados visuales
                    resetValidationStates();
                });
            } else {
                mostrarToastError();
            }
        });
        
        // Funciones de validación
        function validarNombre() {
            const valor = nombreInput.value.trim();
            const errorElement = document.getElementById('error-nombre_rol');
            
            if (valor.length === 0) {
                mostrarError(nombreInput, nombreInvalid, errorElement, 'El nombre del rol es obligatorio');
                return false;
            }
            
            if (valor.length < 3) {
                mostrarError(nombreInput, nombreInvalid, errorElement, 'Mínimo 3 caracteres');
                return false;
            }
            
            if (valor.length > 50) {
                mostrarError(nombreInput, nombreInvalid, errorElement, 'Máximo 50 caracteres');
                return false;
            }
            
            mostrarValido(nombreInput, nombreValid, errorElement);
            return true;
        }
        
        function validarDescripcion() {
            const valor = descripcionInput.value.trim();
            const errorElement = document.getElementById('error-descripcion_rol');
            
            if (valor.length > 255) {
                mostrarError(descripcionInput, descInvalid, errorElement, 'Máximo 255 caracteres');
                return false;
            }
            
            mostrarValido(descripcionInput, descValid, errorElement);
            return true;
        }
        
        function validarEstado() {
            const valor = estadoSelect.value;
            const errorElement = document.getElementById('error-estado_rol');
            
            if (!valor) {
                mostrarError(estadoSelect, estadoInvalid, errorElement, 'Seleccione un estado');
                return false;
            }
            
            mostrarValido(estadoSelect, estadoValid, errorElement);
            return true;
        }
        
        function mostrarError(input, invalidIcon, errorElement, mensaje) {
            input.classList.add('is-invalid');
            input.classList.remove('is-valid');
            invalidIcon.style.display = 'block';
            errorElement.textContent = mensaje;
            errorElement.style.display = 'block';
            
            // Ocultar icono válido si está visible
            const validIcon = invalidIcon.previousElementSibling;
            if (validIcon && validIcon.classList.contains('validation-icon')) {
                validIcon.style.display = 'none';
            }
        }
        
        function mostrarValido(input, validIcon, errorElement) {
            input.classList.remove('is-invalid');
            input.classList.add('is-valid');
            validIcon.style.display = 'block';
            errorElement.style.display = 'none';
            
            // Ocultar icono inválido si está visible
            const invalidIcon = validIcon.nextElementSibling;
            if (invalidIcon && invalidIcon.classList.contains('validation-icon')) {
                invalidIcon.style.display = 'none';
            }
        }
        
        function resetValidationStates() {
            const inputs = [nombreInput, descripcionInput, estadoSelect];
            const validIcons = [nombreValid, descValid, estadoValid];
            const invalidIcons = [nombreInvalid, descInvalid, estadoInvalid];
            const errorElements = [
                document.getElementById('error-nombre_rol'),
                document.getElementById('error-descripcion_rol'),
                document.getElementById('error-estado_rol')
            ];
            
            inputs.forEach(input => {
                input.classList.remove('is-valid', 'is-invalid');
            });
            
            validIcons.forEach(icon => icon.style.display = 'none');
            invalidIcons.forEach(icon => icon.style.display = 'none');
            errorElements.forEach(el => el.style.display = 'none');
        }
        
        function mostrarToastError() {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 5000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });
            
            Toast.fire({
                icon: 'error',
                title: 'Por favor corrija los errores en el formulario'
            });
        }
    });
    </script>
