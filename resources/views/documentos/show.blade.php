@extends('adminlte::page')

@section('title', 'Detalles del Documento')

@section('content_header')
<div class="elegant-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-0"><i class="fas fa-file-alt mr-2 text-primary"></i> Detalles del Documento</h1>
            <p class="subtitle">Documento: {{ $documento->numero ?? 'DOC-' . $documento->id }}</p>
        </div>
    </div>
</div>
@stop

@section('content')
<div class="container-fluid">
    <div class="card card-elegant">
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-8">
                    <h3>{{ $documento->asunto }}</h3>
                    <p class="text-muted">{{ $documento->numero ?? 'DOC-' . $documento->id }} - {{ ucfirst($documento->tipo) }}</p>
                </div>
                <div class="col-md-4 text-right">
                    <button class="btn btn-primary preview-btn" 
                            data-file-url="{{ asset('storage/' . $documento->archivo_path) }}"
                            data-file-name="{{ basename($document极狐o->archivo_path) }}">
                        <i class="fas fa-eye mr-1"></i> Vista Previa
                    </button>
                    <a href="{{ route('documentos.descargar', $documento) }}" class="btn btn-primary">
                        <i class="fas fa-download mr-1"></i> Descargar
                    </a>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="info-group">
                        <label>Remitente:</label>
                        <p>{{ $documento->remitente }}</p>
                    </div>
                    <div class="info-group">
                        <label>Destinatario:</label>
                        <p>{{ $documento->destinatario }}</p>
                    </div>
                    <div class="info-group">
                        <label>Fecha del Documento:</label>
                        <p>{{ $documento->fecha_documento->format('d/m/Y') }}</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-group">
                        <label>Registrado por:</label>
                        <p>{{ $documento->user ? $documento->user->nombres . ' ' . $documento->user->apellidos : 'Usuario no disponible' }}</p>
                    </div>
                    <div class="info-group">
                        <label>Fecha de Registro:</label>
                        <p>{{ $documento->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>
            
            <div class="info-group mt-4">
                <label>Descripción:</label>
                <p>{{ $documento->descripcion ?? 'Sin descripción adicional' }}</p>
            </div>
            
            <div class="mt-4">
                <h5><i class="fas fa-paperclip mr-2"></i> Documento Adjunto</h5>
                <div class="d-flex align-items-center mt-2">
                    <i class="fas fa-file-pdf fa-2x text-danger mr-3"></i>
                    <div>
                        <p class="mb-0">{{ basename($documento->archivo_path) }}</p>
                        <small class="text-muted">{{ Storage::disk('public')->size($documento->archivo_path) / 1024 }} KB</small>
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-end mt-4">
                <a href="{{ route('documentos.gestor') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left mr-1"></i> Volver
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Modal para vista previa -->
<div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewTitle">Vista Previa</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="previewContent">
                <!-- Contenido dinámico -->
            </div>
            <div class="modal-footer">
                <a id="downloadBtn" href="#" class="btn btn-primary" download>
                    <i class="fas fa-download mr-1"></i> Descargar
                </a>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
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
        
        .info-group {
            margin-bottom: 1.5rem;
        }
        
        .info-group label {
            font-weight: 600;
            color: #0b2e59;
            margin-bottom: 0.25rem;
            display: block;
        }
        
        .info-group p {
            margin-bottom: 0;
            padding: 0.5rem;
            background-color: #f8f9fa;
            border-radius: 4px;
        }
    </style>
@stop

@section('js')
<script>
    $(document).ready(function() {
        $('.preview-btn').click(function() {
            const fileUrl = $(this).data('file-url');
            const fileName = $(this).data('file-name');
            const fileExt = fileName.split('.').pop().toLowerCase();
            
            $('#previewTitle').text('Vista Previa: ' + fileName);
            $('#downloadBtn').attr('href', fileUrl);
            
            if (fileExt === 'pdf') {
                $('#previewContent').html(`
                    <div class="embed-responsive embed-responsive-16by9">
                        <iframe class="embed-responsive-item" src="${fileUrl}"></iframe>
                    </div>
                `);
            } else {
                $('#previewContent').html(`
                    <div class="text-center">
                        <img src="${fileUrl}" class="img-fluid" alt="Vista previa">
                    </div>
                `);
            }
            
            $('#previewModal').modal('show');
        });
    });
</script>
@stop