@extends('adminlte::page')

@section('title', 'Gestión de Backups')

@section('content_header')
<div class="elegant-header">
    <h1><i class="fas fa-database mr-2"></i> Gestión de Backups</h1>
    <p class="mb-0">UNAH - Posgrado FCEA</p>
</div>
@stop

@section('content')
<div class="container-fluid">

    <!-- Botón Crear Backup -->
    <div class="card card-elegant mb-4">
        <div class="card-header"><h5 class="mb-0">Acciones</h5></div>
        <div class="card-body">
            <div class="d-flex justify-content-end">
                <button class="btn btn-primary btn-elegant" id="btn-crear-backup">
                    <i class="fas fa-database mr-1"></i> Crear Backup
                </button>
            </div>
        </div>
    </div>

    <!-- Tabla de Backups -->
    <div class="card card-elegant">
        <div class="card-header"><h5 class="mb-0">Historial de Backups</h5></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="thead-elegant">
                        <tr>
                            <th>#</th>
                            <th>Archivo</th>
                            <th>Tamaño (MB)</th>
                            <th>Creado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($backupFiles as $i => $file)
                        <tr>
                            <td>{{ $i+1 }}</td>
                            <td>{{ $file['name'] }}</td>
                            <td>{{ round($file['size']/1024/1024, 2) }}</td>
                            <td>{{ date('Y-m-d H:i:s', $file['modified']) }}</td>
                            <td>
                                <a href="{{ route('backup.download', $file['name']) }}" class="btn btn-info btn-sm" title="Descargar">
                                    <i class="fas fa-download"></i>
                                </a>
                                <button class="btn btn-danger btn-sm btn-eliminar" data-filename="{{ $file['name'] }}" title="Eliminar">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center">No hay backups disponibles</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- Modal Confirmación Eliminar -->
<div class="modal fade" id="modal-confirm-delete" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-elegant text-white">
                <h5 class="modal-title">Confirmar Eliminación</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                ¿Eliminar el archivo <strong id="filename-to-delete"></strong>?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="btn-confirm-delete">Eliminar</button>
            </div>
        </div>
    </div>
</div>

@stop

@section('js')
<script>
$(document).ready(function(){
    // Crear backup
    $('#btn-crear-backup').click(function(){
        window.location.href = "{{ route('backup.create') }}";
    });

    // Modal eliminar
    $('.btn-eliminar').click(function(){
        const filename = $(this).data('filename');
        $('#filename-to-delete').text(filename);
        $('#modal-confirm-delete').data('filename', filename).modal('show');
    });

    $('#btn-confirm-delete').click(function(){
        const filename = $('#modal-confirm-delete').data('filename');
        window.location.href = "{{ url('backup/delete') }}/" + filename;
    });
});
</script>
@stop
