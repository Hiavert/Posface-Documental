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

                            <form method="POST" action="{{ route('usuarios.update', $usuario->id_usuario) }}">
                                @csrf
                                @method('PUT')

                                <div class="form-group">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-white border-right-0">
                                                <i class="bi bi-person text-posface-primary"></i>
                                            </span>
                                        </div>
                                        <input type="text" name="nombres" class="form-control border-left-0" placeholder="Nombres" value="{{ old('nombres', $usuario->nombres) }}" required />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-white border-right-0">
                                                <i class="bi bi-person text-posface-primary"></i>
                                            </span>
                                        </div>
                                        <input type="text" name="apellidos" class="form-control border-left-0" placeholder="Apellidos" value="{{ old('apellidos', $usuario->apellidos) }}" required />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-white border-right-0">
                                                <i class="bi bi-envelope text-posface-primary"></i>
                                            </span>
                                        </div>
                                        <input type="email" name="email" class="form-control border-left-0" placeholder="Correo electrónico" value="{{ old('email', $usuario->email) }}" required />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-white border-right-0">
                                                <i class="bi bi-card-text text-posface-primary"></i>
                                            </span>
                                        </div>
                                        <input type="text" name="identidad" class="form-control border-left-0" placeholder="Identidad" value="{{ old('identidad', $usuario->identidad) }}" required />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-white border-right-0">
                                                <i class="bi bi-person-badge text-posface-primary"></i>
                                            </span>
                                        </div>
                                        <input type="text" name="usuario" class="form-control border-left-0" placeholder="Usuario" value="{{ old('usuario', $usuario->usuario) }}" required />
                                    </div>
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

    @media (max-width: 768px) {
        .bg-posface-dark {
            border-radius: 15px 15px 0 0 !important;
        }
    }
</style>

@endsection

@section('js')
    <script>
        document.documentElement.style.setProperty('--posface-dark-blue-rgb', '10, 34, 95');
    </script>
@endsection
