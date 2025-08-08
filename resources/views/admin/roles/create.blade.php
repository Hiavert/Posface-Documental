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
                        <form action="{{ route('roles.store') }}" method="POST" id="roleForm">
                            @csrf
                            
                            <div class="form-group">
                                <label for="nombre_rol">Nombre del Rol *</label>
                                <input type="text" class="form-control @error('nombre_rol') is-invalid @enderror" 
                                       id="nombre_rol" name="nombre_rol" value="{{ old('nombre_rol') }}" 
                                       placeholder="Ej: Administrador, Usuario, Supervisor" required
                                       oninput="sanitizeNombre(this)">
                                @error('nombre_rol')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                                <small class="text-muted float-right char-counter"><span id="nombre_counter">0</span>/50</small>
                            </div>

                            <div class="form-group">
                                <label for="descripcion_rol">Descripción</label>
                                <textarea class="form-control @error('descripcion_rol') is-invalid @enderror" 
                                          id="descripcion_rol" name="descripcion_rol" rows="3" 
                                          placeholder="Descripción del rol y sus responsabilidades"
                                          oninput="sanitizeDescripcion(this)">{{ old('descripcion_rol') }}</textarea>
                                @error('descripcion_rol')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                                <small class="text-muted float-right char-counter"><span id="desc_counter">0</span>/100</small>
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
    
    .error-message {
        display: none;
        color: #dc3545;
        font-size: 0.875em;
        margin-top: 5px;
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
    
    .char-counter {
        font-size: 0.8rem;
        margin-top: 5px;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar contadores
    document.getElementById('nombre_counter').textContent = 
        document.getElementById('nombre_rol').value.length;
    document.getElementById('desc_counter').textContent = 
        document.getElementById('descripcion_rol').value.length;

    // Funciones de sanitización para nombre
    window.sanitizeNombre = function(input) {
        let value = input.value;
        
        // Eliminar caracteres especiales y dígitos (solo permite letras y espacios)
        let newValue = value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '');
        
        // Limitar repeticiones consecutivas a 3
        newValue = newValue.replace(/(.)\1{3,}/g, '$1$1$1');
        
        // Limitar a 50 caracteres
        if (newValue.length > 50) {
            newValue = newValue.substring(0, 50);
        }
        
        // Actualizar valor y contador
        input.value = newValue;
        document.getElementById('nombre_counter').textContent = newValue.length;
    };

    // Funciones de sanitización para descripción
    window.sanitizeDescripcion = function(input) {
        let value = input.value;
        
        // Eliminar dígitos y caracteres no permitidos (solo permite letras, espacios, guiones, comas y puntos)
        let newValue = value.replace(/[0-9]/g, '');
        newValue = newValue.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s\-,.]/g, '');
        
        // Limitar repeticiones consecutivas a 3
        newValue = newValue.replace(/(.)\1{3,}/g, '$1$1$1');
        
        // Limitar a 100 caracteres
        if (newValue.length > 100) {
            newValue = newValue.substring(0, 100);
        }
        
        // Actualizar valor y contador
        input.value = newValue;
        document.getElementById('desc_counter').textContent = newValue.length;
    };

    // Validación al enviar el formulario
    const form = document.getElementById('roleForm');
    form.addEventListener('submit', function(e) {
        let isValid = true;
        const nombreInput = document.getElementById('nombre_rol');
        const descInput = document.getElementById('descripcion_rol');
        
        // Validar nombre
        if (nombreInput.value.trim() === '') {
            isValid = false;
        }
        
        // Validar descripción (solo longitud)
        if (descInput.value.length > 100) {
            isValid = false;
        }
        
        if (!isValid) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Error en el formulario',
                text: 'Por favor verifique los datos ingresados',
                confirmButtonColor: '#0b2e59'
            });
        }
    });
});
</script>
