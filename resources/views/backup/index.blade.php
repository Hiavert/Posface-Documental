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

    <!-- Alertas de sesión -->
    @if (session('success'))
        <div class="alert alert-elegant-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-elegant-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card card-elegant mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0"><i class="fas fa-plus-circle mr-2 text-muted"></i> Acciones</h5>
        </div>
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-0">Crea copias de seguridad de la base de datos para prevenir pérdida de datos.</p>
                </div>
                <div>
                    <button class="btn btn-primary btn-elegant" id="btn-crear-backup">
                        <i class="fas fa-database mr-1"></i> Crear Nuevo Backup
                    </button>
                </div>
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
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($backupFiles as $index => $file)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <i class="fas fa-file-code text-primary mr-2"></i>
                                {{ $file['name'] }}
                            </td>
                            <td>
                                <span class="badge badge-info">
                                    {{ number_format($file['size'] / 1024 / 1024, 2) }} MB
                                </span>
                            </td>
                            <td>
                                <i class="far fa-calendar-alt text-muted mr-1"></i>
                                {{ date('Y-m-d H:i:s', $file['modified']) }}
                            </td>
                            <td>
                                <div class="d-flex justify-content-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('backup.download', $file['name']) }}" 
                                           class="btn btn-outline-primary" 
                                           title="Descargar Backup"
                                           data-toggle="tooltip">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        <button class="btn btn-outline-danger btn-eliminar" 
                                                data-filename="{{ $file['name'] }}" 
                                                title="Eliminar Backup"
                                                data-toggle="tooltip">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">
                                <div class="empty-state">
                                    <i class="fas fa-database fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No hay backups disponibles</h5>
                                    <p class="text-muted">Crea tu primer backup para comenzar</p>
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

<!-- Modal de confirmación -->
<div class="modal fade" id="modal-confirm-delete" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle mr-2"></i> Confirmar Eliminación
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p>¿Está seguro de eliminar el siguiente archivo de backup?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-file-code mr-2"></i>
                    <strong id="filename-to-delete"></strong>
                </div>
                <p class="text-muted small">Esta acción no se puede deshacer.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i> Cancelar
                </button>
                <button type="button" class="btn btn-danger" id="btn-confirm-delete">
                    <i class="fas fa-trash-alt mr-1"></i> Eliminar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de carga -->
<div class="modal fade" id="modal-carga" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="background: rgba(255, 255, 255, 0.98); border-radius: 15px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);">
            <div class="modal-body text-center py-5">
                <div class="loading-spinner mb-4">
                    <div class="spinner-border text-primary" style="width: 4rem; height: 4rem; border-width: 0.3em;"></div>
                </div>
                <h4 class="text-primary mb-3" id="titulo-carga">Procesando</h4>
                <div class="loading-message">
                    <p class="text-muted mb-2" id="mensaje-carga">Por favor espere...</p>
                    <p class="font-weight-bold" id="mensaje-exito" style="display: none;">
                        <i class="fas fa-check-circle mr-2 text-success"></i>
                        <span id="texto-exito"></span>
                    </p>
                    <p class="font-weight-bold" id="mensaje-error" style="display: none;">
                        <i class="fas fa-exclamation-circle mr-2 text-danger"></i>
                        <span id="texto-error"></span>
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
    
    /* Botones */
    .btn-elegant {
        border-radius: 8px;
        font-weight: 500;
        padding: 8px 16px;
        transition: all 0.3s ease;
    }
    
    .btn-primary.btn-elegant {
        background: linear-gradient(135deg, #1a5a8d, #0b2e59);
        border: none;
    }
    
    .btn-primary.btn-elegant:hover {
        background: linear-gradient(135deg, #0b2e59, #1a5a8d);
        transform: translateY(-2px);
    }
    
    /* Tabla */
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
    
    .table > tbody > tr:hover {
        background-color: #f8fafc;
    }
    
    /* Estado vacío */
    .empty-state {
        padding: 40px 0;
        text-align: center;
    }
    
    .empty-state i {
        font-size: 3rem;
        opacity: 0.5;
        margin-bottom: 15px;
    }
    
    /* Alertas */
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

    /* Estilos para el modal de carga */
    .loading-spinner {
        animation: pulse 1.5s ease-in-out infinite alternate;
    }

    @keyframes pulse {
        0% { transform: scale(1); }
        100% { transform: scale(1.05); }
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

    /* Badges */
    .badge {
        font-weight: 500;
        padding: 0.5em 0.8em;
    }
</style>
@stop

@section('js')
<script>
$(document).ready(function() {
    // Inicializar tooltips
    $('[data-toggle="tooltip"]').tooltip();

    // Crear backup
    $('#btn-crear-backup').click(function() {
        const $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Creando...');

        showLoadingModal('Creando Backup', 'Estamos creando el backup de la base de datos...');

        $.ajax({
            url: "{{ route('backup.create') }}",
            type: 'GET',
            success: function(response) {
                if(response.success) {
                    showSuccessModal('¡Éxito!', response.message || 'Backup creado correctamente');
                    
                    setTimeout(() => {
                        hideLoadingModal();
                        location.reload();
                    }, 2000);
                } else {
                    showErrorModal('Error', response.message || 'Error al crear el backup');
                    $btn.prop('disabled', false).html('<i class="fas fa-database mr-1"></i> Crear Nuevo Backup');
                }
            },
            error: function(xhr, status, error) {
                let errorMessage = 'Error de conexión al crear el backup';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                showErrorModal('Error', errorMessage);
                $btn.prop('disabled', false).html('<i class="fas fa-database mr-1"></i> Crear Nuevo Backup');
            }
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
        
        showLoadingModal('Eliminando Backup', 'Eliminando el archivo de backup...');

        $.ajax({
            url: "{{ url('backup/delete') }}/" + encodeURIComponent(filename),
            type: 'GET',
            success: function(response) {
                if(response.success) {
                    showSuccessModal('¡Éxito!', response.message || 'Backup eliminado correctamente');
                    
                    setTimeout(() => {
                        hideLoadingModal();
                        location.reload();
                    }, 2000);
                } else {
                    showErrorModal('Error', response.message || 'Error al eliminar el backup');
                }
            },
            error: function(xhr, status, error) {
                let errorMessage = 'Error de conexión al eliminar el backup';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                showErrorModal('Error', errorMessage);
            }
        });
    });

    // Funciones para manejar el modal de carga
    function showLoadingModal(title, message) {
        $('#titulo-carga').text(title);
        $('#mensaje-carga').text(message);
        $('#mensaje-exito').hide();
        $('#mensaje-error').hide();
        $('#progress-bar-container').show();
        $('#modal-carga').modal('show');
    }

    function showSuccessModal(title, message) {
        $('#titulo-carga').text(title);
        $('#mensaje-carga').hide();
        $('#texto-exito').text(message);
        $('#mensaje-exito').show();
        $('#mensaje-error').hide();
        $('#progress-bar-container').hide();
        
        // Cambiar el spinner por un ícono de éxito
        $('.loading-spinner').html('<i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>');
    }

    function showErrorModal(title, message) {
        $('#titulo-carga').text(title);
        $('#mensaje-carga').hide();
        $('#texto-error').text(message);
        $('#mensaje-error').show();
        $('#mensaje-exito').hide();
        $('#progress-bar-container').hide();
        
        // Cambiar el spinner por un ícono de error
        $('.loading-spinner').html('<i class="fas fa-exclamation-circle text-danger" style="font-size: 4rem;"></i>');
        
        setTimeout(() => {
            hideLoadingModal();
        }, 3000);
    }

    function hideLoadingModal() {
        $('#modal-carga').modal('hide');
        
        // Restaurar el spinner original después de un tiempo
        setTimeout(() => {
            $('.loading-spinner').html('<div class="spinner-border text-primary" style="width: 4rem; height: 4rem; border-width: 0.3em;"></div>');
        }, 500);
    }

    // Restaurar el modal cuando se cierre
    $('#modal-carga').on('hidden.bs.modal', function() {
        $('.loading-spinner').html('<div class="spinner-border text-primary" style="width: 4rem; height: 4rem; border-width: 0.3em;"></div>');
        $('#mensaje-exito').hide();
        $('#mensaje-error').hide();
        $('#mensaje-carga').show();
        $('#progress-bar-container').show();
    });
});
</script>
@stop