@extends('adminlte::page')

@section('title', 'Crear Objeto')

@section('content_header')
<div class="elegant-header">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h1 class="mb-0"><i class="fas fa-cube mr-2 text-primary"></i> Crear Objeto</h1>
            <p class="mb-0">Universidad Nacional Autónoma de Honduras - Posgrado de la Facultad de Ciencias Económicas Administrativas y Contables</p>
        </div>
        <div class="d-flex align-items-center">
            <!-- Notificaciones -->
            <div class="notifications-dropdown ml-3">
                <button class="btn btn-notification" type="button" id="notifDropdown" data-toggle="dropdown">
                    <i class="fas fa-bell"></i>
                    <span class="badge badge-danger" id="notifCounter">{{ auth()->user()->notificacionesNoLeidas->count() }}</span>
                </button>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="notifDropdown">
                    <div class="dropdown-header">Notificaciones Recientes</div>
                    @if(auth()->user()->notificaciones->count() > 0)
                        @foreach(auth()->user()->notificaciones as $notificacion)
                            <a class="dropdown-item d-flex justify-content-between align-items-center {{ $notificacion->estado == 'no_leida' ? 'unread' : '' }}" 
                            href="{{ route('notificaciones.show', $notificacion->id_notificacion) }}">
                                <div>
                                    <div class="font-weight-bold">{{ $notificacion->titulo }}</div>
                                    <small class="text-muted">
                                        De: {{ optional(optional($notificacion->acuse)->remitente)->nombres ?? 'N/A' }} {{ optional(optional($notificacion->acuse)->remitente)->apellidos ?? '' }}
                                    </small>
                                    <div class="small text-muted">{{ $notificacion->fecha ? $notificacion->fecha->format('d/m/Y H:i') : now()->format('d/m/Y H:i') }}</div>
                                </div>
                                @if($notificacion->estado == 'no_leida')
                                    <span class="badge badge-danger">Nuevo</span>
                                @endif
                            </a>
                        @endforeach
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-bell-slash fa-2x mb-2 text-muted"></i>
                            <p class="text-muted">No hay notificaciones</p>
                        </div>
                    @endif
                </div>
            </div>
            <div class="header-icon ml-3">
                <i class="fas fa-cubes"></i>
            </div>
        </div>
    </div>
</div>
@stop

@section('content')
<div class="container-fluid">
    <!-- Notificaciones -->
    @if(session('success') || session('error'))
    <div class="alert-container">
        @if(session('success'))
        <div class="alert alert-elegant-success alert-dismissible fade show">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endif
        @if(session('error'))
        <div class="alert alert-elegant-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endif
    </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card card-elegant">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-plus-circle mr-2 text-muted"></i> Formulario de Creación
                    </h5>
                </div>
                <form action="{{ route('objetos.store') }}" method="POST" id="formCrearObjeto">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label for="nombre_objeto">Nombre del Objeto</label>
                            <input type="text" class="form-control form-control-elegant" id="nombre_objeto" name="nombre_objeto" 
                                   required maxlength="100" 
                                   placeholder="Ingrese el nombre del objeto"
                                   pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ\s.,\-]+"
                                   title="Solo letras, espacios y caracteres como . , -">
                            <div class="invalid-feedback" id="nombre_objeto_error"></div>
                        </div>
                        <div class="form-group">
                            <label for="tipo_objeto">Tipo de Objeto</label>
                            <select class="form-control form-control-elegant" id="tipo_objeto" name="tipo_objeto" required>
                                <option value="">Seleccione un tipo</option>
                                <option value="Módulo">Módulo</option>
                                <option value="Función">Función</option>
                                <option value="Reporte">Reporte</option>
                            </select>
                            <div class="invalid-feedback" id="tipo_objeto_error"></div>
                        </div>
                        <div class="form-group">
                            <label for="descripcion_objeto">Descripción</label>
                            <textarea class="form-control form-control-elegant" id="descripcion_objeto" name="descripcion_objeto" 
                                      rows="3" maxlength="255" 
                                      placeholder="Ingrese una descripción del objeto (opcional)"></textarea>
                        </div>
                    </div>
                    <div class="card-footer text-right">
                        <a href="{{ route('objetos.index') }}" class="btn btn-outline-secondary btn-elegant">
                            <i class="fas fa-times mr-1"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-success btn-elegant">
                            <i class="fas fa-save mr-1"></i> Guardar Objeto
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
 /* Estilos generales */
    body {
        background-color: #f8f9fc;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    
    /* Encabezado */
    .elegant-header {
        background: linear-gradient(135deg, #0b2e59, #1a5a8d);
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        color: white;
        margin-bottom: 25px;
    }
    
    .elegant-header h1 {
        font-weight: 600;
        font-size: 1.8rem;
        margin-bottom: 0.2rem;
        letter-spacing: -0.5px;
    }
    
    .elegant-header .subtitle {
        font-size: 1rem;
        opacity: 0.85;
    }
    
    .elegant-header .header-icon {
        font-size: 2.5rem;
        opacity: 0.9;
    }
    
    /* Tarjetas */
    .card-elegant {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        margin-bottom: 25px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .card-elegant:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
    }
    
    .card-header {
        background-color: white;
        border-bottom: 1px solid #eaeef5;
        border-radius: 12px 12px 0 0 !important;
        padding: 18px 25px;
    }
    
    .card-title {
        font-weight: 600;
        color: #2c3e50;
        font-size: 1.1rem;
    }
    
    .card-body {
        padding: 25px;
    }
    
    /* Botones */
    .btn-elegant {
        border-radius: 8px;
        font-weight: 500;
        padding: 8px 16px;
        transition: all 0.3s ease;
    }
    
    .btn-primary.btn-elegant {
        background: linear-gradient(135deg, #3a7bd5, #00d2ff);
        border: none;
    }
    
    .btn-success.btn-elegant {
        background: linear-gradient(135deg, #00b09b, #96c93d);
        border: none;
    }
    
    .btn-outline-secondary.btn-elegant {
        border: 1px solid #dee2e6;
        color: #6c757d;
    }
    
    .btn-notification {
        background: #f8f9fc;
        border: 1px solid #eaeef5;
        border-radius: 50%;
        width: 42px;
        height: 42px;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        transition: all 0.3s ease;
    }
    
    .btn-notification:hover {
        background: #eef2f7;
        transform: rotate(10deg);
    }
</style>
@stop

@section('js')
<script>
    $(document).ready(function() {
        // Validación en tiempo real para el nombre
        $('#nombre_objeto').on('input', function() {
            let nombre = $(this).val();
            let regex = /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s.,\-]+$/;
            
            if (!regex.test(nombre)) {
                $(this).addClass('is-invalid');
                $('#nombre_objeto_error').text('Solo se permiten letras, espacios y caracteres como . , -');
            } else if (nombre.length > 100) {
                $(this).addClass('is-invalid');
                $('#nombre_objeto_error').text('El nombre no puede exceder los 100 caracteres');
            } else {
                $(this).removeClass('is-invalid');
                $('#nombre_objeto_error').text('');
            }
        });

        // Validación en tiempo real para el tipo
        $('#tipo_objeto').change(function() {
            if ($(this).val() === '') {
                $(this).addClass('is-invalid');
                $('#tipo_objeto_error').text('Seleccione un tipo');
            } else {
                $(this).removeClass('is-invalid');
                $('#tipo_objeto_error').text('');
            }
        });

        // Validación al enviar el formulario
        $('#formCrearObjeto').on('submit', function(e) {
            let valid = true;
            let nombre = $('#nombre_objeto').val();
            let tipo = $('#tipo_objeto').val();

            if (nombre === '') {
                $('#nombre_objeto').addClass('is-invalid');
                $('#nombre_objeto_error').text('El nombre es obligatorio.');
                valid = false;
            }

            if (tipo === '') {
                $('#tipo_objeto').addClass('is-invalid');
                $('#tipo_objeto_error').text('Seleccione un tipo.');
                valid = false;
            }

            if (!valid) {
                e.preventDefault();
            }
        });
    });
</script>
@stop
