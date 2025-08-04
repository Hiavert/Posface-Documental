@extends('adminlte::page')

@section('title', 'Bienvenido al Sistema de Gestión Documental')

@section('content_header')
    <div class="welcome-header text-center py-5">
        <div class="header-content">
            <h1 class="display-4 mb-3"><i class="fas fa-book-open mr-3"></i> Sistema de Gestión Documental</h1>
            <p class="lead mb-0">Universidad Nacional Autónoma de Honduras</p>
            <p class="subtitle">POSFACE</p>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <!-- Mensaje de Bienvenida -->
        <div class="row justify-content-center mb-5">
            <div class="col-md-8">
                <div class="card welcome-card">
                    <div class="card-body p-5 text-center">
                        <div class="welcome-icon mb-4">
                            <i class="fas fa-hands-helping"></i>
                        </div>
                        <h2 class="card-title">¡Bienvenido, {{ Auth::user()->nombres }}!</h2>
                        <p class="card-text lead">
                            Estás en el sistema de gestión documental del POSFACE – UNAH.
                            Accedé a los módulos habilitados según tus permisos.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- 🔹 Módulos Disponibles -->
        <div class="row mb-5">
            <div class="col-md-12 text-center mb-4">
                <h2><i class="fas fa-th-large mr-2 text-info"></i> Módulos Disponibles</h2>
                <p class="text-muted">Accede a las herramientas según tus permisos</p>
            </div>
            
            @foreach($modulosDisponibles as $modulo)
            <div class="col-6 col-sm-4 col-md-3 mb-3">
                <a href="{{ route($modulo['ruta']) }}" class="card-link">
                    <div class="module-card small-card">
                        <div class="module-icon bg-{{ $modulo['color'] }}">
                            <i class="{{ $modulo['icono'] }} pulse"></i>
                        </div>
                        <div class="module-content">
                            <h5>{{ $modulo['nombre'] }}</h5>
                            <p>{{ $modulo['descripcion'] }}</p>
                        </div>
                        <div class="module-footer">
                            <span class="badge badge-{{ $modulo['color'] }}">
                                <i class="fas fa-lock-open mr-1"></i> Acceso permitido
                            </span>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>

        <!-- Mensaje de ayuda -->
        <div class="row justify-content-center">
            <div class="col-md-8 text-center">
                <div class="alert alert-light">
                    <p class="mb-0"><i class="fas fa-info-circle mr-2"></i> Si necesitas acceso a más módulos, contacta al administrador del sistema.</p>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
<style>
    /* Fondo general */
    body {
        background-color: #f8f9fc;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    /* Encabezado */
    .welcome-header {
        background: linear-gradient(135deg, #0b2e59, #1a5a8d);
        color: white;
        padding: 40px 20px;
    }

    .welcome-header h1 { font-size: 2.5rem; font-weight: 600; }
    .welcome-header .lead { font-size: 1.3rem; opacity: 0.9; }

    /* Tarjeta bienvenida */
    .welcome-card {
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    .welcome-icon { font-size: 4rem; color: #0b2e59; }

    /* 🔹 Tarjetas de módulos */
    .module-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
        height: 100%;
    }

    .module-card:hover {
        transform: translateY(-5px) scale(1.03);
        box-shadow: 0 6px 20px rgba(0,0,0,0.15);
    }

    .small-card {
        max-width: 200px;
        margin: auto;
        font-size: 0.85rem;
    }

    .module-icon {
        height: 70px;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 1.8rem;
        color: white;
    }

    .module-content { padding: 10px; text-align: center; }
    .module-content h5 { font-size: 1rem; font-weight: bold; color: #0b2e59; }
    .module-content p { color: #6c757d; margin-bottom: 5px; }

    .module-footer {
        background: #f8f9fa;
        text-align: center;
        padding: 5px;
        border-top: 1px solid #eee;
    }

    .card-link { text-decoration: none; }
    .card-link:hover { text-decoration: none; }

    /* 🔹 Gradientes personalizados */
    .bg-success { background: linear-gradient(135deg, #28a745, #4cd964); }
    .bg-lime { background: linear-gradient(135deg, #a3e635, #84cc16); }
    .bg-purple { background: linear-gradient(135deg, #6f42c1, #9f7aea); }
    .bg-info { background: linear-gradient(135deg, #17a2b8, #36d1dc); }
    .bg-primary { background: linear-gradient(135deg, #007bff, #3399ff); }
    .bg-orange { background: linear-gradient(135deg, #ff7b00, #ffb347); }
    .bg-warning { background: linear-gradient(135deg, #ffc107, #ffde59); }
    .bg-cyan { background: linear-gradient(135deg, #00c6ff, #0072ff); }
    .bg-danger { background: linear-gradient(135deg, #dc3545, #ff6b81); }
    .bg-indigo { background: linear-gradient(135deg, #6610f2, #9d4edd); }
    .bg-teal { background: linear-gradient(135deg, #20c997, #2dd4bf); }

    /* ✨ Efecto pulse en íconos */
    .pulse {
        animation: pulseAnim 1.5s infinite;
    }
    @keyframes pulseAnim {
        0% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.1); opacity: 0.9; }
        100% { transform: scale(1); opacity: 1; }
    }
</style>
@stop

@section('js')
<script>
    $(document).ready(function() {
        console.log("💫 Dashboard cargado con animaciones");
        $('.welcome-card').hide().fadeIn(800);
    });
</script>
@stop
