@extends('adminlte::page')

@section('title', 'Gestión de Objetos')

@section('content_header')
<div class="elegant-header">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h1 class="mb-0"><i class="fas fa-cube mr-2 text-primary"></i> Gestión de Objetos</h1>
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

    <!-- Tabla de objetos -->
    <div class="card card-elegant">
        <div class="card-header d-flex align-items-center">
            <h5 class="card-title mb-0"><i class="fas fa-list mr-2 text-muted"></i> Listado de Objetos</h5>
            <div class="ml-auto">
                <span class="badge badge-light">{{ $objetos->count() }} registros</span>
            </div>
        </div>
        <div class="card-body">
            <div class="d-flex justify-content-end mb-4">
                @if(auth()->user()->puedeAgregar('objeto'))

                <a href="{{ route('objetos.create') }}" class="btn btn-success btn-elegant">
                    <i class="fas fa-plus mr-1"></i> Nuevo Objeto
                </a>
                @endif
            </div>
            <div class="table-responsive">
                <table class="table table-hover table-borderless">
                    <thead class="thead-elegant">
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Tipo</th>
                            <th>Descripción</th>
                            <th>Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($objetos as $objeto)
                            <tr class="table-row">
                                <td class="font-weight-bold">OBJ-{{ str_pad($objeto->id_objeto, 5, '0', STR_PAD_LEFT) }}</td>
                                <td>{{ $objeto->nombre_objeto }}</td>
                                <td>
                                    <span class="badge badge-type-{{ strtolower($objeto->tipo_objeto) }}">
                                        {{ $objeto->tipo_objeto }}
                                    </span>
                                </td>
                                <td>{{ $objeto->descripcion_objeto ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge badge-state-{{ $objeto->estado_objeto == '1' ? 'activo' : 'inactivo' }}">
                                        {{ $objeto->estado_objeto == '1' ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-actions" role="group">
                                        @if(auth()->user()->puedeEditar('objeto'))
                                            <a href="{{ route('objetos.edit', $objeto->id_objeto) }}" class="btn btn-sm btn-action" data-toggle="tooltip" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endif
                                        <form action="{{ route('objetos.destroy', $objeto->id_objeto) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            @if(auth()->user()->puedeEliminar('objeto'))
                                                <button type="submit" class="btn btn-sm btn-action" data-toggle="tooltip" title="Eliminar" onclick="return confirm('¿Está seguro de eliminar este objeto?')">
                                                    <i class="fas fa-trash-alt text-danger"></i>
                                                </button>
                                            @endif
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="fas fa-cubes fa-3x text-muted mb-3"></i>
                                        <h5>No se encontraron objetos</h5>
                                        <p class="text-muted">Parece que aún no hay objetos registrados en el sistema</p>
                                        <a href="{{ route('objetos.create') }}" class="btn btn-primary mt-2">
                                            <i class="fas fa-plus mr-1"></i> Crear primer objeto
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
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
    
    /* Tabla */
    .table-borderless {
        border-collapse: separate;
        border-spacing: 0 8px;
    }
    
    .thead-elegant {
        background-color: #f8f9fc;
    }
    
    .thead-elegant th {
        border: none;
        color: #6c757d;
        font-weight: 600;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 12px 15px;
    }
    
    .table-row {
        background-color: white;
        border-radius: 10px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.03);
        transition: all 0.3s ease;
    }
    
    .table-row:hover {
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        transform: translateY(-2px);
    }
    
    .table-row td {
        padding: 16px 15px;
        vertical-align: middle;
        border-top: none;
        border-bottom: 1px solid #f0f4f8;
    }
    
    .table-row:first-child td:first-child {
        border-radius: 10px 0 0 10px;
    }
    
    .table-row:first-child td:last-child {
        border-radius: 0 10px 10px 0;
    }
    
    /* Badges de estado */
    .badge-state-activo {
        background-color: #e8f5e9;
        color: #388e3c;
        padding: 6px 12px;
        border-radius: 50px;
        font-weight: 500;
    }
    
    .badge-state-inactivo {
        background-color: #ffebee;
        color: #d32f2f;
        padding: 6px 12px;
        border-radius: 50px;
        font-weight: 500;
    }
    
    /* Badges de tipo */
    .badge-type-módulo {
        background-color: #e3f2fd;
        color: #1976d2;
        padding: 6px 12px;
        border-radius: 50px;
        font-weight: 500;
    }
    
    .badge-type-función {
        background-color: #f3e5f5;
        color: #9c27b0;
        padding: 6px 12px;
        border-radius: 50px;
        font-weight: 500;
    }
    
    .badge-type-reporte {
        background-color: #fff8e1;
        color: #f57c00;
        padding: 6px 12px;
        border-radius: 50px;
        font-weight: 500;
    }
    
    /* Botones de acción */
    .btn-action {
        background: transparent;
        border: none;
        color: #6c757d;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }
    
    .btn-action:hover {
        background-color: #f0f4f8;
        color: #3a7bd5;
        transform: scale(1.1);
    }
    
    /* Alertas */
    .alert-container {
        position: fixed;
        top: 80px;
        right: 20px;
        z-index: 1050;
        width: 350px;
    }
    
    .alert-elegant-success {
        background: linear-gradient(135deg, #e8f5e9, #c8e6c9);
        color: #388e3c;
        border: none;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
    }
    
    .alert-elegant-danger {
        background: linear-gradient(135deg, #ffebee, #ffcdd2);
        color: #d32f2f;
        border: none;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
    }
    
    /* Estado vacío */
    .empty-state {
        padding: 40px 0;
        text-align: center;
    }
    
    .empty-state i {
        font-size: 4rem;
        opacity: 0.3;
        margin-bottom: 20px;
    }
    
    .empty-state h5 {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 10px;
    }
    
    .empty-state p {
        color: #6c757d;
    }
    
    /* Paginación */
    .pagination-custom .pagination {
        margin: 0;
    }
    
    .pagination-custom .page-item .page-link {
        border: none;
        border-radius: 8px;
        margin: 0 3px;
        color: #6c757d;
        transition: all 0.3s ease;
    }
    
    .pagination-custom .page-item.active .page-link {
        background: linear-gradient(135deg, #3a7bd5, #00d2ff);
        color: white;
        box-shadow: 0 4px 10px rgba(58, 123, 213, 0.25);
    }
    
    .pagination-custom .page-item .page-link:hover {
        background-color: #f0f4f8;
        color: #3a7bd5;
    }
    
    /* Formularios */
    .form-control-elegant {
        border: 1px solid #eaeef5;
        border-radius: 8px;
        padding: 10px 15px;
        transition: all 0.3s ease;
        height: calc(1.5em + 1rem + 2px);
    }
    
    .form-control-elegant:focus {
        border-color: #3a7bd5;
        box-shadow: 0 0 0 0.2rem rgba(58, 123, 213, 0.15);
    }
    
    /* Notificaciones dropdown */
    .notifications-dropdown .dropdown-menu {
        border: none;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        width: 350px;
        max-height: 400px;
        overflow-y: auto;
        padding: 0;
    }
    
    .dropdown-header {
        background-color: #f8f9fc;
        padding: 12px 20px;
        font-weight: 600;
        color: #2c3e50;
        border-radius: 12px 12px 0 0;
        border-bottom: 1px solid #eaeef5;
    }
    
    .dropdown-item {
        padding: 12px 20px;
        border-bottom: 1px solid #f0f4f8;
        transition: all 0.2s ease;
    }
    
    .dropdown-item.unread {
        background-color: #f0f8ff;
        border-left: 3px solid #3a7bd5;
    }
    
    .dropdown-item:hover {
        background-color: #f8f9fc;
    }
</style>
@stop

@section('js')
<script>
    $(document).ready(function() {
        // Inicializar tooltips
        $('[data-toggle="tooltip"]').tooltip({
            placement: 'top',
            trigger: 'hover'
        });
        
        // Animación para las filas de la tabla
        $('.table-row').each(function(i) {
            $(this).delay(i * 50).animate({
                opacity: 1
            }, 300);
        });
        
        // Animación para botón de notificaciones
        $('#notifDropdown').hover(function() {
            $(this).find('i').css('transform', 'rotate(15deg)');
        }, function() {
            $(this).find('i').css('transform', 'rotate(0deg)');
        });
    });
</script>
@stop