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

    <!-- Botón de crear backup -->
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

    <!-- Listado de Backups -->
    <div class="card card-elegant">
        <div class="card-header">
            <h5 class="card-title mb-0"><i class="fas fa-history mr-2 text-muted"></i> Historial de Backups</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="thead-elegant">
                        <tr>
                            <th width="50">#</th>
                            <th>Nombre del Archivo</th>
                            <th>Tamaño</th>
                            <th>Fecha de Creación</th>
                            <th width="200">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($backupFiles as $index => $file)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $file['name'] }}</td>
                            <td>{{ round($file['size'] / 1024 / 1024, 2) }} MB</td>
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

<!-- Modal de confirmación para eliminar -->
<div class="modal fade" id="modal-confirm-delete" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-elegant">
                <h5 class="modal-title text-white">Confirmar Eliminación</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                ¿Está seguro de eliminar el archivo <strong id="filename-to-delete"></strong>?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="btn-confirm-delete">Eliminar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de carga -->
<div class="modal fade" id="modal-carga" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content bg-transparent border-0">
            <div class="d-flex justify-content-center">
                <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                    <span class="sr-only">Cargando...</span>
                </div>
            </div>
            <div class="text-center mt-3 text-white">
                <p id="mensaje-carga">Procesando, por favor espere...</p>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    /* Mantenemos los mismos estilos que en index.blade.php */
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
    
    .elegant-header p {
        font-size: 1rem;
        opacity: 0.85;
    }
    
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
    
    .btn-primary.btn-elegant:hover {
        background: linear-gradient(135deg, #2a6bc9, #00bde3);
    }
    
    .table {
        border-collapse: separate;
        border-spacing: 0;
    }
    
    .thead-elegant {
        background-color: #0b2e59;
        color: white;
    }
    
    .table th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.5px;
        padding: 12px 15px;
        vertical-align: middle;
    }
    
    .table td {
        padding: 12px 15px;
        vertical-align: middle;
        border-bottom: 1px solid #f0f4f8;
    }
    
    .table-hover tbody tr:hover {
        background-color: #f8f9fc;
    }
</style>
@stop

@section('js')
<script>
$(document).ready(function() {
    // Crear backup
    $('#btn-crear-backup').click(function() {
        $('#mensaje-carga').text('Creando backup de la base de datos...');
        $('#modal-carga').modal('show');
        
        $.ajax({
            url: "{{ route('backup.create') }}",
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    setTimeout(function() {
                        $('#modal-carga').modal('hide');
                        location.reload();
                    }, 1500);
                }
            },
            error: function() {
                $('#modal-carga').modal('hide');
                alert('Error al crear el backup');
            }
        });
    });

    // Eliminar backup: abrir modal de confirmación
    $(document).on('click', '.btn-eliminar', function() {
        const filename = $(this).data('filename');
        $('#filename-to-delete').text(filename);
        $('#modal-confirm-delete').data('filename', filename).modal('show');
    });

    // Confirmar eliminación
    $('#btn-confirm-delete').click(function() {
        const filename = $('#modal-confirm-delete').data('filename');
        $('#modal-confirm-delete').modal('hide');
        
        $('#mensaje-carga').text('Eliminando backup...');
        $('#modal-carga').modal('show');
        
        $.ajax({
            url: "{{ url('backup/delete') }}/" + filename,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    setTimeout(function() {
                        $('#modal-carga').modal('hide');
                        location.reload();
                    }, 1000);
                }
            },
            error: function() {
                $('#modal-carga').modal('hide');
                alert('Error al eliminar el backup');
            }
        });
    });
});
</script>
@stop