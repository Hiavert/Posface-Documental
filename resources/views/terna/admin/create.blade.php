@extends('adminlte::page')

@section('title', 'Crear Proceso de Pago Terna')

@section('content_header')
<div class="elegant-header">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h1 class="mb-0"><i class="fas fa-plus-circle mr-2 text-primary"></i> Nuevo Proceso de Pago</h1>
            <p class="subtitle">Administración de pagos de terna</p>
        </div>
        <div class="header-icon ml-3">
            <i class="fas fa-file-invoice-dollar"></i>
        </div>
    </div>
</div>
@stop

@section('content')
<div class="container-fluid">
    <div class="card card-elegant">
        <div class="card-header">
            <h5 class="card-title mb-0">Información del Proceso</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('terna.admin.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Descripción</label>
                            <input type="text" class="form-control" name="descripcion" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Fecha de Defensa</label>
                            <input type="date" class="form-control" name="fecha_defensa" required>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Responsable</label>
                            <input type="text" class="form-control" name="responsable" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Fecha Límite</label>
                            <input type="datetime-local" class="form-control" name="fecha_limite" required>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Asistente Asignado</label>
                            <select class="form-control" name="id_asistente" required>
                                <option value="">Seleccionar asistente</option>
                                @foreach($asistentes as $asistente)
                                    <option value="{{ $asistente->id_usuario }}">
                                        {{ $asistente->nombres }} {{ $asistente->apellidos }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <h5><i class="fas fa-file-pdf mr-2"></i> Documentos Requeridos</h5>
                    <div class="form-group">
                        <label>Documento Físico</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" name="documento_fisico" required>
                            <label class="custom-file-label">Seleccionar archivo</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Solvencia de Cobranza</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" name="solvencia_cobranza" required>
                            <label class="custom-file-label">Seleccionar archivo</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Acta de Graduación</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" name="acta_graduacion" required>
                            <label class="custom-file-label">Seleccionar archivo</label>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('terna.admin.index') }}" class="btn btn-outline-secondary btn-elegant mr-2">
                        <i class="fas fa-times mr-1"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary btn-elegant">
                        <i class="fas fa-paper-plane mr-1"></i> Enviar al Asistente
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
    $(document).ready(function() {
        $('.custom-file-input').on('change', function() {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass("selected").html(fileName);
        });
        
        // Prevenir doble envío
        $('form').submit(function() {
            $(this).find('button[type="submit"]').prop('disabled', true);
        });
    });
</script>
@stop