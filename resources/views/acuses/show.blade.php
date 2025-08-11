@extends('adminlte::page')

@section('title', 'Detalles del Acuse')

@section('content_header')
<div class="unah-header">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h1 class="mb-0"><i class="fas fa-file-contract mr-2 text-primary"></i> Detalles del Acuse</h1>
            <p class="subtitle">Universidad Nacional Autónoma de Honduras - Posgrado en Informática Administrativa</p>
        </div>
        <div class="header-icon">
            <i class="fas fa-info-circle"></i>
        </div>
    </div>
</div>
@stop

@section('content')
<div class="container-fluid">
    <div class="card card-elegant">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-info-circle mr-2 text-muted"></i> 
                Acuse AR-{{ str_pad($acuse->id_acuse, 5, '0', STR_PAD_LEFT) }}
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5><i class="fas fa-user-tag mr-2"></i> Información General</h5>
                    <div class="info-container p-3 rounded">
                        <div class="info-item d-flex justify-content-between py-2">
                            <span class="font-weight-bold">Título:</span>
                            <span>{{ $acuse->titulo }}</span>
                        </div>
                        <div class="info-item d-flex justify-content-between py-2">
                            <span class="font-weight-bold">Descripción:</span>
                            <span>{{ $acuse->descripcion ?? 'N/A' }}</span>
                        </div>
                        <div class="info-item d-flex justify-content-between py-2">
                            <span class="font-weight-bold">Remitente:</span>
                            <span>{{ $acuse->remitente->nombres }} {{ $acuse->remitente->apellidos }}</span>
                        </div>
                        <div class="info-item d-flex justify-content-between py-2">
                            <span class="font-weight-bold">Destinatario:</span>
                            <span>{{ $acuse->destinatario->nombres }} {{ $acuse->destinatario->apellidos }}</span>
                        </div>
                        <div class="info-item d-flex justify-content-between py-2">
                            <span class="font-weight-bold">Estado:</span>
                            <span class="badge badge-state-{{ $acuse->estado }}">
                                {{ ucfirst($acuse->estado) }}
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <h5><i class="fas fa-calendar-alt mr-2"></i> Fechas</h5>
                    <div class="info-container p-3 rounded">
                        <div class="info-item d-flex justify-content-between py-2">
                            <span class="font-weight-bold">Envío:</span>
                            <span>{{ $acuse->fecha_envio->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="info-item d-flex justify-content-between py-2">
                            <span class="font-weight-bold">Recepción:</span>
                            <span>{{ $acuse->fecha_recepcion ? $acuse->fecha_recepcion->format('d/m/Y H:i') : 'Pendiente' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <h5 class="mt-4"><i class="fas fa-box-open mr-2"></i> Elementos</h5>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>Tipo</th>
                            <th>Nombre</th>
                            <th>Cantidad</th>
                            <th>Descripción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($acuse->elementos as $elemento)
                            <tr class="table-row">
                                <td>{{ $elemento->tipo->nombre }}</td>
                                <td>{{ $elemento->nombre }}</td>
                                <td>{{ $elemento->cantidad }}</td>
                                <td>{{ $elemento->descripcion ?? 'N/A' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            @if($acuse->adjuntos->count() > 0)
            <div class="mt-4">
                <h5><i class="fas fa-paperclip mr-2"></i> Archivos Adjuntos</h5>
                <div class="row">
                    @foreach($acuse->adjuntos as $adjunto)
                    <div class="col-md-3 mb-3">
                        <div class="card card-file">
                            <div class="card-body text-center py-4">
                                @if($adjunto->tipo == 'imagen')
                                <div class="position-relative">
                                    <button class="btn btn-preview p-0 w-100" 
                                            data-toggle="modal" 
                                            data-target="#imagePreviewModal"
                                            data-src="{{ asset('storage/' . $adjunto->ruta) }}"
                                            data-name="{{ $adjunto->nombre_archivo }}">
                                        <img src="{{ asset('storage/' . $adjunto->ruta) }}" 
                                             class="card-img-top" 
                                             alt="{{ $adjunto->nombre_archivo }}"
                                             style="height: 150px; object-fit: cover;">
                                        <div class="preview-overlay">
                                            <i class="fas fa-search-plus fa-2x"></i>
                                        </div>
                                    </button>
                                </div>
                                @else
                                <button class="btn btn-preview" 
                                        data-toggle="modal" 
                                        data-target="#pdfPreviewModal"
                                        data-src="{{ asset('storage/' . $adjunto->ruta) }}"
                                        data-name="{{ $adjunto->nombre_archivo }}">
                                    <i class="fas fa-file-pdf fa-3x text-danger"></i>
                                </button>
                                @endif
                            </div>
                            <div class="card-footer">
                                <small class="text-truncate d-block">{{ $adjunto->nombre_archivo }}</small>
                                <div class="d-flex justify-content-between">
                                    <button class="btn btn-sm btn-link btn-preview" 
                                            data-toggle="modal" 
                                            data-target="{{ $adjunto->tipo == 'imagen' ? '#imagePreviewModal' : '#pdfPreviewModal' }}"
                                            data-src="{{ asset('storage/' . $adjunto->ruta) }}"
                                            data-name="{{ $adjunto->nombre_archivo }}">
                                        <i class="fas fa-eye mr-1"></i> Vista Previa
                                    </button>
                                    <a href="{{ route('acuses.adjunto.descargar', $adjunto->id_adjunto) }}" class="btn btn-sm btn-link">
                                        <i class="fas fa-download mr-1"></i> Descargar
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
        <div class="card-footer text-right">
            <a href="{{ route('acuses.index') }}" class="btn btn-outline-secondary btn-elegant">
                <i class="fas fa-arrow-left mr-1"></i> Volver
            </a>
        </div>
    </div>
</div>

<!-- Modal para vista previa de imágenes -->
<div class="modal fade" id="imagePreviewModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imagePreviewTitle">Vista Previa</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <img id="imagePreview" src="" class="img-fluid" alt="Vista previa">
            </div>
        </div>
    </div>
</div>

<!-- Modal para vista previa de PDF -->
<div class="modal fade" id="pdfPreviewModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pdfPreviewTitle">Vista Previa de PDF</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="embed-responsive embed-responsive-16by9">
                    <iframe id="pdfPreviewFrame" class="embed-responsive-item" src="" frameborder="0"></iframe>
                </div>
            </div>
            <div class="modal-footer">
                <a id="pdfDownloadBtn" href="#" class="btn btn-primary">
                    <i class="fas fa-download mr-1"></i> Descargar
                </a>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<style>
    .info-container {
        background-color: #f8f9fc;
        border-radius: 10px;
    }
    
    .info-item {
        border-bottom: 1px solid #eaeef5;
    }
    
    .info-item:last-child {
        border-bottom: none;
    }
    .unah-header {
        background: linear-gradient(135deg, #0b2e59, #1a5a8d);
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        color: white;
        margin-bottom: 25px;
    }
    .card-file {
        border-radius: 10px;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    
    .card-file:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 15px rgba(0,0,0,0.1);
    }
    
    .badge-state-enviado {
        background-color: #e3f2fd;
        color: #1976d2;
        padding: 5px 12px;
        border-radius: 50px;
        font-weight: 500;
    }
    
    .badge-state-recibido {
        background-color: #e8f5e9;
        color: #388e3c;
        padding: 5px 12px;
        border-radius: 50px;
        font-weight: 500;
    }
    
    .badge-state-pendiente {
        background-color: #fff8e1;
        color: #f57c00;
        padding: 5px 12px;
        border-radius: 50px;
        font-weight: 500;
    }
    
    /* Estilos para vista previa */
    .btn-preview {
        position: relative;
        border: none;
        background: transparent;
        cursor: pointer;
        padding: 0;
        transition: all 0.2s;
    }
    
    .btn-preview:hover {
        transform: scale(1.05);
    }
    
    .preview-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s;
    }
    
    .btn-preview:hover .preview-overlay {
        opacity: 1;
    }
    
    .preview-overlay i {
        color: white;
    }
    
    .card-file .card-footer .btn-link {
        padding: 0.25rem 0.5rem;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Configurar modal para imágenes
        $('#imagePreviewModal').on('show.bs.modal', function(e) {
            const button = $(e.relatedTarget);
            const src = button.data('src');
            const name = button.data('name');
            
            $('#imagePreview').attr('src', src);
            $('#imagePreviewTitle').text(`Vista Previa: ${name}`);
        });
        
        // Configurar modal para PDF
        $('#pdfPreviewModal').on('show.bs.modal', function(e) {
            const button = $(e.relatedTarget);
            const src = button.data('src');
            const name = button.data('name');
            
            // Configurar el iframe con el PDF
            $('#pdfPreviewFrame').attr('src', src);
            $('#pdfPreviewTitle').text(`Vista Previa: ${name}`);
            $('#pdfDownloadBtn').attr('href', src);
        });
        
        // Limpiar el iframe al cerrar el modal
        $('#pdfPreviewModal').on('hidden.bs.modal', function() {
            $('#pdfPreviewFrame').attr('src', '');
        });
    });
</script>
@stop