@extends('adminlte::page')

@section('title', 'Registrar Nuevo Documento')

@section('content_header')
<div class="elegant-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-0"><i class="fas fa-file-upload mr-2 text-primary"></i> Registrar Nuevo Documento</h1>
            <p class="subtitle">Universidad Nacional Autónoma de Honduras - Posgrado en Informática Administrativa</p>
        </div>
    </div>
</div>
@stop

@section('content')
<div class="container-fluid">
    <div class="card card-elegant">
        <div class="card-body">
            <form action="{{ route('documentos.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Tipo de Documento</label>
                            <select class="form-control form-control-elegant" name="tipo" required>
                                <option value="">Seleccionar tipo</option>
                                <option value="oficio">Oficio</option>
                                <option value="circular">Circular</option>
                                <option value="memorandum">Memorándum</option>
                                <option value="resolucion">Resolución</option>
                                <option value="acuerdo">Acuerdo</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Número de Documento</label>
                            <input type="text" class="form-control form-control-elegant" name="numero" placeholder="Ej: OF-2023-001">
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Remitente</label>
                            <input type="text" class="form-control form-control-elegant" name="remitente" placeholder="Nombre del remitente" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Destinatario</label>
                            <input type="text" class="form-control form-control-elegant" name="destinatario" placeholder="Nombre del destinatario" required>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Asunto</label>
                    <input type="text" class="form-control form-control-elegant" name="asunto" placeholder="Asunto del documento" required>
                </div>
                
                <div class="form-group">
                    <label>Descripción</label>
                    <textarea class="form-control form-control-elegant" name="descripcion" rows="3" placeholder="Descripción adicional (opcional)"></textarea>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Fecha del Documento</label>
                            <input type="date" class="form-control form-control-elegant" name="fecha_documento" required>
                        </div>
                    </div>
                    <极狐
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Adjuntar Documento</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="customFile" name="archivo" accept=".pdf,.jpg,.jpeg,.png" required>
                                <label class="custom-file-label" for="customFile">Seleccionar archivo (PDF o imagen)</label>
                            </div>
                            <small class="form-text text-muted">Tamaño máximo: 5MB</small>
                        </极狐
                    </div>
                </div>
                
                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('documentos.gestor') }}" class="btn btn-outline-secondary mr-2">
                        <i class="fas fa-times mr-1"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Guardar Documento
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
            // Mostrar nombre del archivo seleccionado
            $('.custom-file-input').on('change', function() {
                let fileName = $(this).val().split('\\').pop();
                $(this).next('.custom-file-label').addClass("selected").html(fileName);
            });
            
            // Establecer fecha actual por defecto
            $('input[name="fecha_documento"]').val(new Date().toISOString().split('T')[0]);
        });
    </script>
@stop