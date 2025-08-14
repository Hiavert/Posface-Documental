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

<!-- Modal de carga -->
<div class="modal fade" id="modal-carga" tabindex="-1" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content bg-transparent border-0 text-center">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;"></div>
            <div class="text-white mt-3" id="mensaje-carga">Procesando...</div>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
$(document).ready(function() {

    // Crear backup
    $('#btn-crear-backup').click(function() {
        $('#mensaje-carga').text('Creando backup...');
        $('#modal-carga').modal('show');

        $.get("{{ route('backup.create') }}", function(res) {
            if(res.success) {
                $('#mensaje-carga').text(res.message);
                setTimeout(()=>location.reload(), 1000);
            } else {
                alert(res.message);
                $('#modal-carga').modal('hide');
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
        $('#mensaje-carga').text('Eliminando backup...');
        $('#modal-carga').modal('show');

        $.get("{{ url('backup/delete') }}/" + filename, function(res) {
            if(res.success) {
                $('#mensaje-carga').text(res.message);
                setTimeout(()=>location.reload(), 1000);
            } else {
                alert(res.message);
                $('#modal-carga').modal('hide');
            }
        });
    });
});
</script>
@stop
