@extends('adminlte::page')

@section('title', 'Editar Documento')

@section('content_header')
<div class="elegant-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-0"><i class="fas fa-edit mr-2 text-primary"></i> Editar Documento</h1>
            <p class="subtitle">Documento: {{ $documento->numero ?? 'DOC-' . $documento->id }}</p>
        </div>
    </div>
</div>
@stop

@section('content')
<div class="container-fluid">
    <div class="card card-elegant">
        <div class="card-body">
            <form action="{{ route('documentos.update', $documento) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Tipo de Documento</label>
                            <select class="form-control form-control-elegant" name="tipo" required>
                                <option value="">Seleccionar tipo</option>
                                <option value="oficio" {{ $documento->tipo == 'oficio' ? 'selected' : '' }}>Oficio</option>
                                <option value="circular" {{ $documento->tipo == 'circular' ? 'selected' : '' }}>Circular</option>
                                <option value="memorandum" {{ $documento->tipo == 'memorandum' ? 'selected' : '' }}>Memorándum</option>
                                <option value="resolucion" {{ $documento->tipo == 'resolucion' ? 'selected' : '' }}>Resolución</option>
                                <option value="acuerdo" {{ $documento->tipo == 'acuerdo' ? 'selected' : '' }}>Acuerdo</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Número de Documento</label>
                            <input type="text" class="form-control form-control-elegant" name="numero" 
                                   placeholder="Ej: OF-2023-001" value="{{ $documento->numero }}">
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Remitente</label>
                            <input type="text" class="form-control form-control-elegant" name="remitente" 
                                   placeholder="Nombre del remitente" required value="{{ $documento->remitente }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Destinatario</label>
                            <input type="text" class="form-control form-control-elegant" name="destinatario" 
                                   placeholder="Nombre del destinatario" required value="{{ $documento->destinatario }}">
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Asunto</label>
                    <input type="text" class="form-control form-control-elegant" name="asunto" 
                           placeholder="Asunto del documento" required value="{{ $documento->asunto }}">
                </div>
                
                <div class="form-group">
                    <label>Descripción</label>
                    <textarea class="form-control form-control-elegant" name="descripcion" rows="3" 
                              placeholder="Descripción adicional (opcional)">{{ $documento->descripcion }}</textarea>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Fecha del Documento</label>
                            <input type="date" class="form-control form-control-elegant" name="fecha_documento" 
                                   required value="{{ $documento->fecha_documento->format('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Adjuntar Documento (Opcional)</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="customFile" 
                                       name="archivo" accept=".pdf,.jpg,.jpeg,.png">
                                <label class="custom-file-label" for="customFile">
                                    {{ basename($documento->archivo_path) }}
                                </label>
                            </div>
                            <small class="form-text text-muted">
                                Dejar en blanco para conservar el archivo actual ({{ Storage::disk('public')->size($documento->archivo_path) / 1024 }} KB)
                            </small>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('documentos.gestor') }}" class="btn btn-outline-secondary mr-2">
                        <i class="fas fa-times mr-1"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Actualizar Documento
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('css')
    <style>
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

    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Mostrar nombre del archivo seleccionado
            $('.custom-file-input').on('change', function() {
                let fileName = $(this).val().split('\\').pop();
                $(this).next('.custom-file-label').addClass("selected").html(fileName);
            });
        });
    </script>
@stop
