@extends('adminlte::page')

@section('title', 'Editar usuario')

@section('content_header')
    <div class="unah-header">
        <h1 class="mb-0"><i class="fas fa-user-edit mr-2"></i> Editar usuario</h1>
    </div>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg">
                <div class="card-body p-0">
                    <div class="col-12">
                        <div class="p-4">
                            <p class="text-posface-primary small font-weight-bold mb-1">GESTIÓN DE USUARIOS</p>
                            <h2 class="h3 font-weight-bold text-dark mb-4">Editar usuario</h2>

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

                            <form method="POST" action="{{ route('usuarios.update', $usuario->id_usuario) }}" id="user-form">
                                @csrf
                                @method('PUT')

                                <div class="form-group">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-white border-right-0">
                                                <i class="bi bi-person text-posface-primary"></i>
                                            </span>
                                        </div>
                                        <input type="text" name="nombres" class="form-control border-left-0" placeholder="Nombres" 
                                        value="{{ old('nombres', $usuario->nombres) }}" required 
                                        oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]/g, ''); validateField(this, 'nombres')" 
                                        maxlength="50" />
                                    </div>
                                    <span class="validation-hint">Solo letras y espacios</span>
                                    <span class="character-count" id="nombres-count">{{ strlen(old('nombres', $usuario->nombres)) }}/50</span>
                                    <span class="validation-status" id="nombres-status"></span>
                                </div>

                                <div class="form-group">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-white border-right-0">
                                                <i class="bi bi-person text-posface-primary"></i>
                                            </span>
                                        </div>
                                        <input type="text" name="apellidos" class="form-control border-left-0" placeholder="Apellidos" 
                                        value="{{ old('apellidos', $usuario->apellidos) }}" required 
                                        oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]/g, ''); validateField(this, 'apellidos')" 
                                        maxlength="50" />
                                    </div>
                                    <span class="validation-hint">Solo letras y espacios</span>
                                    <span class="character-count" id="apellidos-count">{{ strlen(old('apellidos', $usuario->apellidos)) }}/50</span>
                                    <span class="validation-status" id="apellidos-status"></span>
                                </div>

                                <div class="form-group">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-white border-right-0">
                                                <i class="bi bi-envelope text-posface-primary"></i>
                                            </span>
                                        </div>
                                        <input type="email" name="email" class="form-control border-left-0" placeholder="Correo electrónico" 
                                               value="{{ old('email', $usuario->email) }}" required />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-white border-right-0">
                                            <i class="bi bi-card-text text-posface-primary"></i>
                                            </span>
                                        </div>
                                        <input type="text" name="identidad" class="form-control border-left-0" placeholder="Identidad" 
                                        value="{{ old('identidad', $usuario->identidad) }}" required 
                                        oninput="this.value = this.value.replace(/[^0-9-]/g, ''); validateField(this, 'identidad')" 
                                        maxlength="15" />
                                    </div>
                                    <span class="validation-hint">Solo números y guiones</span>
                                    <span class="character-count" id="identidad-count">{{ strlen(old('identidad', $usuario->identidad)) }}/15</span>
                                    <span class="validation-status" id="identidad-status"></span>
                                </div>


                                <div class="form-group">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-white border-right-0">
                                                <i class="bi bi-person-badge text-posface-primary"></i>
                                            </span>
                                        </div>
                                        <input type="text" name="usuario" class="form-control border-left-0" placeholder="Usuario" 
                                        value="{{ old('usuario', $usuario->usuario) }}" required 
                                        oninput="this.value = this.value.replace(/[^a-zA-Z0-9.-]/g, ''); validateField(this, 'usuario')" 
                                        maxlength="30" />
                                    </div>
                                    <span class="validation-hint">Solo letras, números, puntos y guiones</span>
                                    <span class="character-count" id="usuario-count">{{ strlen(old('usuario', $usuario->usuario)) }}/30</span>
                                    <span class="validation-status" id="usuario-status"></span>
                                </div>

                                <div class="form-group">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-white border-right-0">
                                                <i class="bi bi-person-badge text-posface-primary"></i>
                                            </span>
                                        </div>
                                        <select name="rol" class="form-control border-left-0" required>
                                            <option value="">Seleccione un rol</option>
                                            @php
                                                $rolActual = old('rol');
                                                if (!$rolActual && $usuario->roles && $usuario->roles->count()) {
                                                    $rolActual = $usuario->roles->first()->id_rol;
                                                }
                                            @endphp
                                            @foreach($roles as $rol)
                                                <option value="{{ $rol->id_rol }}" {{ $rolActual == $rol->id_rol ? 'selected' : '' }}>{{ $rol->nombre_rol }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-white border-right-0">
                                                <i class="bi bi-toggle-on text-posface-primary"></i>
                                            </span>
                                        </div>
                                        <select name="estado" class="form-control border-left-0" required>
                                            <option value="0" {{ old('estado', $usuario->estado) == '0' ? 'selected' : '' }}>Inactivo</option>
                                            <option value="1" {{ old('estado', $usuario->estado) == '1' ? 'selected' : '' }}>Activo</option>
                                        </select>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-posface btn-block py-2 font-weight-bold">
                                    ACTUALIZAR USUARIO
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Mismos estilos que en crear usuario --}}
<style>
    :root {
        --posface-dark-blue: #0A225F;
        --posface-light-blue: #3E8DCF;
        --posface-white: #FFFFFF;
        --posface-text-dark: #333333;
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
        box-shadow: 0 0 0 0.2rem rgba(var(--posface-dark-blue-rgb, 10, 34, 95), 0.25);
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

    /* Estilos para mensajes de validación */
    .validation-hint {
        display: block;
        font-size: 0.8rem;
        color: #6c757d;
        margin-top: 5px;
        padding-left: 15px;
    }
    
    /* Indicadores de estado en tiempo real */
    .validation-status {
        position: absolute;
        top: 18px;
        right: 15px;
        font-size: 0.9rem;
    }
    
    .valid-status {
        color: #28a745; /* Verde para válido */
    }
    
    .invalid-status {
        color: #dc3545; /* Rojo para inválido */
    }
    
    /* Contador de caracteres */
    .character-count {
        position: absolute;
        bottom: -20px;
        right: 15px;
        font-size: 0.75rem;
        color: #6c757d;
    }
    
    /* Estilo para inputs con error */
    .input-error {
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        border-color: #dc3545 !important;
    }

    @media (max-width: 768px) {
        .bg-posface-dark {
            border-radius: 15px 15px 0 0 !important;
        }
    }
</style>

@endsection


@section('js')
    <script>
        function validateField(input, field) {
            // Actualizar contador de caracteres
            document.getElementById(`${field}-count`).textContent = `${input.value.length}/${input.maxLength}`;
            
            // Obtener elemento de estado
            const statusElement = document.getElementById(`${field}-status`);
            
            // Validar según el tipo de campo
            let isValid = false;
            
            if (field === 'nombres' || field === 'apellidos') {
                isValid = /^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/.test(input.value);
            } 
            else if (field === 'identidad') {
                isValid = /^[0-9-]+$/.test(input.value);
            }
            else if (field === 'usuario') {
                isValid = /^[a-zA-Z0-9.-]+$/.test(input.value);
            }
            
            // Actualizar estado visual
            if (input.value.length === 0) {
                statusElement.innerHTML = '';
                input.classList.remove('input-error');
            } else if (isValid) {
                statusElement.innerHTML = '<i class="fas fa-check-circle valid-status"></i>';
                input.classList.remove('input-error');
            } else {
                statusElement.innerHTML = '<i class="fas fa-times-circle invalid-status"></i>';
                input.classList.add('input-error');
            }
        }
        
        // Inicializar contadores y estados
        document.addEventListener('DOMContentLoaded', function() {
            const fields = ['nombres', 'apellidos', 'identidad', 'usuario'];
            
            fields.forEach(field => {
                const input = document.querySelector(`[name="${field}"]`);
                if (input) {
                    validateField(input, field);
                }
            });
        });
        
        // Validación al enviar el formulario
        document.getElementById('user-form').addEventListener('submit', function(e) {
            const nombres = document.querySelector('[name="nombres"]').value;
            const apellidos = document.querySelector('[name="apellidos"]').value;
            const identidad = document.querySelector('[name="identidad"]').value;
            const usuario = document.querySelector('[name="usuario"]').value;
            
            // Validar nombres
            if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/.test(nombres)) {
                alert('Los nombres solo pueden contener letras y espacios');
                e.preventDefault();
                return false;
            }
            
            // Validar apellidos
            if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/.test(apellidos)) {
                alert('Los apellidos solo pueden contener letras y espacios');
                e.preventDefault();
                return false;
            }
            
            // Validar identidad
            if (!/^[0-9-]+$/.test(identidad)) {
                alert('La identidad solo puede contener números y guiones');
                e.preventDefault();
                return false;
            }
            
            // Validar usuario
            if (!/^[a-zA-Z0-9.-]+$/.test(usuario)) {
                alert('El usuario solo puede contener letras, números, puntos y guiones bajos');
                e.preventDefault();
                return false;
            }
            
            return true;
        });
    </script>
@endsection
