@extends('adminlte::page')

@section('title', 'Editar Rol')

@section('content_header')
    <div class="unah-header">
        <h1 class="mb-0"><i class="fas fa-edit mr-2"></i> Editar Rol: {{ $rol->nombre_rol }}</h1>
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
                        <form id="editarRolForm" action="{{ route('roles.update', $rol->id_rol) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="form-group">
                                <label for="nombre_rol">Nombre del Rol *</label>
                                <input type="text" class="form-control @error('nombre_rol') is-invalid @enderror" 
                                       id="nombre_rol" name="nombre_rol" value="{{ old('nombre_rol', $rol->nombre_rol) }}" 
                                       placeholder="Ej: Administrador, Usuario, Supervisor" required>
                                @error('nombre_rol')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                                <div class="invalid-feedback" id="nombre_rol_error"></div>
                                <small class="form-text text-muted float-right" id="nombre_rol_counter">0/50 caracteres</small>
                            </div>

                            <div class="form-group">
                                <label for="descripcion_rol">Descripción</label>
                                <textarea class="form-control @error('descripcion_rol') is-invalid @enderror" 
                                          id="descripcion_rol" name="descripcion_rol" rows="3" 
                                          placeholder="Descripción del rol y sus responsabilidades">{{ old('descripcion_rol', $rol->descripcion_rol) }}</textarea>
                                @error('descripcion_rol')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                                <div class="invalid-feedback" id="descripcion_rol_error"></div>
                                <small class="form-text text-muted float-right" id="descripcion_rol_counter">0/255 caracteres</small>
                            </div>

                            <div class="form-group">
                                <label for="estado_rol">Estado *</label>
                                <select class="form-control @error('estado_rol') is-invalid @enderror" 
                                        id="estado_rol" name="estado_rol" required>
                                    <option value="">Seleccionar estado</option>
                                    <option value="1" {{ old('estado_rol', $rol->estado_rol) == '1' ? 'selected' : '' }}>Activo</option>
                                    <option value="0" {{ old('estado_rol', $rol->estado_rol) == '0' ? 'selected' : '' }}>Inactivo</option>
                                </select>
                                @error('estado_rol')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                                <div class="invalid-feedback" id="estado_rol_error"></div>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save mr-1"></i> Actualizar Rol
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
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-info-circle mr-2"></i> Información del Rol</h3>
                    </div>
                    <div class="card-body">
                        <p><strong>ID:</strong> {{ $rol->id_rol }}</p>
                        <p><strong>Usuarios Asignados:</strong> {{ $rol->usuarios->count() }}</p>
                        <p><strong>Permisos Configurados:</strong> {{ $rol->accesos->count() }}</p>
                        <p><strong>Fecha de Creación:</strong> {{ $rol->created_at ? $rol->created_at->format('d/m/Y H:i') : 'N/A' }}</p>
                        <hr>
                        <div class="text-center">
                            <a href="{{ route('roles.permisos', $rol->id_rol) }}" class="btn btn-info btn-sm">
                                <i class="fas fa-key mr-1"></i> Gestionar Permisos
                            </a>
                        </div>
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
    
    .is-invalid {
        border-color: #dc3545 !important;
    }
    
    .invalid-feedback {
        display: block;
        color: #dc3545;
    }
    
    .form-text {
        font-size: 0.85rem;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Referencias a los elementos del formulario
    const form = document.getElementById('editarRolForm');
    const nombreRolInput = document.getElementById('nombre_rol');
    const descripcionRolInput = document.getElementById('descripcion_rol');
    const estadoRolSelect = document.getElementById('estado_rol');
    
    // Contadores de caracteres
    const nombreCounter = document.getElementById('nombre_rol_counter');
    const descripcionCounter = document.getElementById('descripcion_rol_counter');
    
    // Elementos de error
    const nombreError = document.getElementById('nombre_rol_error');
    const descripcionError = document.getElementById('descripcion_rol_error');
    const estadoError = document.getElementById('estado_rol_error');
    
    // Inicializar contadores
    updateCounter(nombreRolInput, nombreCounter, 50);
    updateCounter(descripcionRolInput, descripcionCounter, 255);
    
    // Event listeners para cambios en tiempo real
    nombreRolInput.addEventListener('input', function() {
        validateNombreRol();
        updateCounter(this, nombreCounter, 50);
    });
    
    descripcionRolInput.addEventListener('input', function() {
        validateDescripcionRol();
        updateCounter(this, descripcionCounter, 255);
    });
    
    estadoRolSelect.addEventListener('change', validateEstadoRol);
    
    // Validar formulario al enviar
    form.addEventListener('submit', function(event) {
        // Validar todos los campos
        const isNombreValid = validateNombreRol();
        const isDescripcionValid = validateDescripcionRol();
        const isEstadoValid = validateEstadoRol();
        
        // Si algún campo no es válido, prevenir el envío
        if (!(isNombreValid && isDescripcionValid && isEstadoValid)) {
            event.preventDefault();
            // Mostrar mensaje general si hay errores
            if (!isNombreValid) {
                nombreRolInput.focus();
            } else if (!isEstadoValid) {
                estadoRolSelect.focus();
            }
        }
    });
    
    // Funciones de validación
    function validateNombreRol() {
        const value = nombreRolInput.value.trim();
        let isValid = true;
        
        // Limpiar mensaje de error previo
        nombreError.textContent = '';
        nombreRolInput.classList.remove('is-invalid');
        
        // Validar requerido
        if (value === '') {
            nombreError.textContent = 'El nombre del rol es obligatorio.';
            nombreRolInput.classList.add('is-invalid');
            isValid = false;
        } 
        // Validar longitud máxima
        else if (value.length > 50) {
            nombreError.textContent = 'El nombre no debe exceder los 50 caracteres.';
            nombreRolInput.classList.add('is-invalid');
            isValid = false;
        }
        
        return isValid;
    }
    
    function validateDescripcionRol() {
        const value = descripcionRolInput.value;
        let isValid = true;
        
        // Limpiar mensaje de error previo
        descripcionError.textContent = '';
        descripcionRolInput.classList.remove('is-invalid');
        
        // Validar longitud máxima
        if (value.length > 255) {
            descripcionError.textContent = 'La descripción no debe exceder los 255 caracteres.';
            descripcionRolInput.classList.add('is-invalid');
            isValid = false;
        }
        
        return isValid;
    }
    
    function validateEstadoRol() {
        const value = estadoRolSelect.value;
        let isValid = true;
        
        // Limpiar mensaje de error previo
        estadoError.textContent = '';
        estadoRolSelect.classList.remove('is-invalid');
        
        // Validar requerido
        if (value === '') {
            estadoError.textContent = 'Debe seleccionar un estado.';
            estadoRolSelect.classList.add('is-invalid');
            isValid = false;
        }
        
        return isValid;
    }
    
    // Función para actualizar contador de caracteres
    function updateCounter(inputElement, counterElement, maxLength) {
        const currentLength = inputElement.value.length;
        counterElement.textContent = `${currentLength}/${maxLength} caracteres`;
        
        // Cambiar color según la longitud
        if (currentLength > maxLength) {
            counterElement.style.color = '#dc3545';
        } else if (currentLength > maxLength * 0.8) {
            counterElement.style.color = '#ffc107';
        } else {
            counterElement.style.color = '#6c757d';
        }
    }
    
    // Validar campos al cargar la página si ya tienen valores
    validateNombreRol();
    validateDescripcionRol();
    validateEstadoRol();
});
</script>
