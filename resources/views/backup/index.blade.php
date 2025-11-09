@extends('adminlte::page')

@section('title', 'Gestión de Backups')

@section('content_header')
<div class="elegant-header">
    <h1><i class="fas fa-database mr-2"></i> Gestión de Backups</h1>
    <p class="mb-0">Universidad Nacional Autónoma de Honduras - Posgrado de la Facultad de Ciencias Económicas Administrativas y Contables</p>
</div>
@stop

@section('content')
<div class="container-fluid">

    <div class="card card-elegant mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0"><i class="fas fa-plus-circle mr-2 text-muted"></i> Acciones</h5>
        </div>
        <div class="card-body">
            <div class="d-flex justify-content-end">
                <button class="btn btn-primary btn-elegant" id="btn-crear-backup">
                    <i class="fas fa-database mr-1"></i> Crear Nuevo Backup
                </button>
            </div>
        </div>
    </div>

    <div class="card card-elegant">
        <div class="card-header">
            <h5 class="card-title mb-0"><i class="fas fa-history mr-2 text-muted"></i> Historial de Backups</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="thead-elegant">
                        <tr>
                            <th>#</th>
                            <th>Nombre del Archivo</th>
                            <th>Tamaño</th>
                            <th>Fecha de Creación</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($backupFiles as $index => $file)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $file['name'] }}</td>
                            <td>{{ round($file['size']/1024/1024, 2) }} MB</td>
                            <td>{{ date('Y-m-d H:i:s', $file['modified']) }}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('backup.download', $file['name']) }}" class="btn btn-info" title="Descargar">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    <button class="btn btn-danger btn-eliminar" data-filename="{{ $file['name'] }}" title="Eliminar">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">No hay backups disponibles</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- Modal de confirmación -->
<div class="modal fade" id="modal-confirm-delete" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-elegant">
                <h5 class="modal-title text-white">Confirmar Eliminación</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                ¿Está seguro de eliminar <strong id="filename-to-delete"></strong>?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="btn-confirm-delete">Eliminar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de carga mejorado -->
<div class="modal fade" id="modal-carga" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="background: rgba(255, 255, 255, 0.95); border-radius: 15px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);">
            <div class="modal-body text-center py-5">
                <div class="loading-spinner mb-4">
                    <div class="spinner-border text-primary" style="width: 4rem; height: 4rem; border-width: 0.3em;"></div>
                </div>
                <h4 class="text-primary mb-3" id="titulo-carga">Procesando</h4>
                <div class="loading-message">
                    <p class="text-muted mb-2" id="mensaje-carga">Por favor espere...</p>
                    <p class="text-success font-weight-bold" id="mensaje-exito" style="display: none;">
                        <i class="fas fa-check-circle mr-2"></i>
                        <span id="texto-exito"></span>
                    </p>
                </div>
                <div class="progress mt-3" style="height: 6px; display: none;" id="progress-bar-container">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 100%"></div>
                </div>
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

    /* Estilos mejorados para el modal de carga */
    .loading-spinner {
        animation: pulse 1.5s ease-in-out infinite alternate;
    }

    @keyframes pulse {
        0% {
            transform: scale(1);
        }
        100% {
            transform: scale(1.05);
        }
    }

    .modal-backdrop.show {
        opacity: 0.85;
        background: linear-gradient(135deg, #0b2e59, #1a5a8d);
    }

    #modal-carga .modal-content {
        animation: slideIn 0.3s ease-out;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-50px) scale(0.9);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }
</style>
@stop

@section('js')
<script>
$(document).ready(function() {

    // Crear backup
    $('#btn-crear-backup').click(function() {
        $('#titulo-carga').text('Creando Backup');
        $('#mensaje-carga').text('Estamos creando el backup de la base de datos...');
        $('#mensaje-exito').hide();
        $('#mensaje-carga').show();
        $('#progress-bar-container').show();
        $('#modal-carga').modal('show');

        $.get("{{ route('backup.create') }}", function(res) {
            if(res.success) {
                $('#titulo-carga').text('¡Éxito!');
                $('#mensaje-carga').hide();
                $('#texto-exito').text(res.message || 'Backup creado correctamente');
                $('#mensaje-exito').show();
                $('#progress-bar-container').hide();
                
                // Cambiar el spinner por un ícono de éxito
                $('.loading-spinner').html('<i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>');
                
                setTimeout(() => {
                    $('#modal-carga').modal('hide');
                    location.reload();
                }, 1500);
            } else {
                mostrarError(res.message || 'Error al crear el backup');
            }
        }).fail(function() {
            mostrarError('Error de conexión al crear el backup');
        });
    });

    // Eliminar backup
    $(document).on('click', '.btn-eliminar', function() {
        const filename = $(this).data('filename');
        $('#filename-to-delete').text(filename);
        $('#modal-confirm-delete').data('filename', filename).modal('show');
    });

    $('#btn-confirm-delete').click(function() {
        const filename = $('#modal-confirm-delete').data('filename');
        $('#modal-confirm-delete').modal('hide');
        
        // Restaurar el spinner original
        $('.loading-spinner').html('<div class="spinner-border text-primary" style="width: 4rem; height: 4rem; border-width: 0.3em;"></div>');
        
        $('#titulo-carga').text('Eliminando Backup');
        $('#mensaje-carga').text('Eliminando el archivo de backup...');
        $('#mensaje-exito').hide();
        $('#mensaje-carga').show();
        $('#progress-bar-container').show();
        $('#modal-carga').modal('show');

        $.get("{{ url('backup/delete') }}/" + filename, function(res) {
            if(res.success) {
                $('#titulo-carga').text('¡Éxito!');
                $('#mensaje-carga').hide();
                $('#texto-exito').text(res.message || 'Backup eliminado correctamente');
                $('#mensaje-exito').show();
                $('#progress-bar-container').hide();
                
                // Cambiar el spinner por un ícono de éxito
                $('.loading-spinner').html('<i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>');
                
                setTimeout(() => {
                    $('#modal-carga').modal('hide');
                    location.reload();
                }, 1500);
            } else {
                mostrarError(res.message || 'Error al eliminar el backup');
            }
        }).fail(function() {
            mostrarError('Error de conexión al eliminar el backup');
        });
    });

    // Función para mostrar errores
    function mostrarError(mensaje) {
        $('#titulo-carga').text('Error');
        $('#mensaje-carga').hide();
        $('#texto-exito').html('<i class="fas fa-exclamation-triangle mr-2"></i>' + mensaje);
        $('#mensaje-exito').removeClass('text-success').addClass('text-danger');
        $('#mensaje-exito').show();
        $('#progress-bar-container').hide();
        
        // Cambiar el spinner por un ícono de error
        $('.loading-spinner').html('<i class="fas fa-exclamation-circle text-danger" style="font-size: 4rem;"></i>');
        
        setTimeout(() => {
            $('#modal-carga').modal('hide');
            // Restaurar el estado original del modal
            setTimeout(() => {
                $('.loading-spinner').html('<div class="spinner-border text-primary" style="width: 4rem; height: 4rem; border-width: 0.3em;"></div>');
                $('#mensaje-exito').removeClass('text-danger').addClass('text-success');
            }, 500);
        }, 2000);
    }

    // Restaurar el modal cuando se cierre
    $('#modal-carga').on('hidden.bs.modal', function() {
        $('.loading-spinner').html('<div class="spinner-border text-primary" style="width: 4rem; height: 4rem; border-width: 0.3em;"></div>');
        $('#mensaje-exito').removeClass('text-danger').addClass('text-success').hide();
        $('#mensaje-carga').show();
        $('#progress-bar-container').show();
    });
});
</script>
@stop
