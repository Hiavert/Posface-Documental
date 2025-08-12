@extends('adminlte::page')

@section('title', 'Completar Proceso de Pago')

@section('content_header')
<div class="elegant-header">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h1 class="mb-0"><i class="fas fa-file-invoice-dollar mr-2 text-primary"></i> Proceso: {{ $pagoTerna->codigo }}</h1>
            <p class="subtitle">Completar proceso de pago</p>
        </div>
        <div class="header-icon ml-3">
            <i class="fas fa-user-headset"></i>
        </div>
    </div>
</div>
@stop

@section('content')
<div class="container-fluid">
    <div class="card card-elegant">
        <div class="card-header">
            <h5 class="card-title mb-0">Documentos Recibidos</h5>
        </div>
        <div class="card-body">
            <div class="row">
                @foreach($pagoTerna->documentos as $documento)
                    @if(in_array($documento->tipo, ['documento_fisico', 'solvencia_cobranza', 'acta_graduacion']))
                    <div class="col-md-4 mb-3">
                        <div class="card document-card shadow-sm">
                            <div class="card-body">
                                <h6 class="document-title">
                                    {{ ucfirst(str_replace('_', ' ', $documento->tipo)) }}
                                </h6>
                                
                                @if($documento->tipo_archivo === 'archivo')
                                    <div class="document-preview">
                                        <iframe src="{{ asset('storage/documentos_terna/' . $documento->ruta_archivo) }}" 
                                            width="100%" 
                                            height="200" 
                                            style="border: none; background: #f8f9fc;">
                                        </iframe>
                                    </div>
                                @else
                                    <div class="text-center py-3">
                                        <i class="fas fa-link fa-3x text-primary"></i>
                                    </div>
                                @endif
                                
                                <div class="mt-3 text-center">
                                    @if($documento->tipo_archivo === 'archivo')
                                        <a href="{{ asset('storage/documentos_terna/' . $documento->ruta_archivo) }}" 
                                           target="_blank" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-download mr-1"></i> Descargar
                                        </a>
                                    @else
                                        <a href="{{ $documento->ruta_archivo }}" 
                                           target="_blank" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-external-link-alt mr-1"></i> Ver Enlace
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                @endforeach
            </div>
            
            <div class="mt-4">
                <h5><i class="fas fa-user-graduate mr-2"></i> Información del Estudiante</h5>
                <div class="info-item">
                    <label>Nombre:</label>
                    <p>{{ $pagoTerna->estudiante_nombre }}</p>
                </div>
                <div class="info-item">
                    <label>Cuenta:</label>
                    <p>{{ $pagoTerna->estudiante_cuenta }}</p>
                </div>
                <div class="info-item">
                    <label>Carrera:</label>
                    <p>{{ $pagoTerna->estudiante_carrera }}</p>
                </div>
            </div>
            
            <div class="mt-4">
                <h5><i class="fas fa-users mr-2"></i> Integrantes de Terna</h5>
                <div class="info-item">
                    <label>Metodólogo:</label>
                    <p>{{ $pagoTerna->metodologo->nombre }} ({{ $pagoTerna->metodologo->cuenta }})</p>
                </div>
                <div class="info-item">
                    <label>Técnico 1:</label>
                    <p>{{ $pagoTerna->tecnico1->nombre }} ({{ $pagoTerna->tecnico1->cuenta }})</p>
                </div>
                <div class="info-item">
                    <label>Técnico 2:</label>
                    <p>{{ $pagoTerna->tecnico2->nombre }} ({{ $pagoTerna->tecnico2->cuenta }})</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-elegant mt-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Documentos Requeridos</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('terna.asistente.completar', $pagoTerna->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Constancia de Participación</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" name="constancia_participacion" id="constancia_participacion">
                                <label class="custom-file-label" for="constancia_participacion">Subir PDF</label>
                            </div>
                            <div class="mt-2">
                                <input type="text" class="form-control" name="constancia_participacion_enlace" placeholder="O ingresar enlace">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Orden de Pago</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" name="orden_pago" id="orden_pago">
                                <label class="custom-file-label" for="orden_pago">Subir PDF</label>
                            </div>
                            <div class="mt-2">
                                <input type="text" class="form-control" name="orden_pago_enlace" placeholder="O ingresar enlace">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Propuesta de Maestrías</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" name="propuesta_maestria" id="propuesta_maestria">
                                <label class="custom-file-label" for="propuesta_maestria">Subir PDF</label>
                            </div>
                            <div class="mt-2">
                                <input type="text" class="form-control" name="propuesta_maestria_enlace" placeholder="O ingresar enlace">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('terna.asistente.index') }}" class="btn btn-outline-secondary btn-elegant mr-2">
                        <i class="fas fa-times mr-1"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary btn-elegant">
                        <i class="fas fa-check-circle mr-1"></i> Completar Proceso
                    </button>
                </div>
            </form>
            
            <div class="d-flex justify-content-start mt-4">
                <a href="{{ route('terna.asistente.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left mr-1"></i> Volver
                </a>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    .document-card {
        border-radius: 10px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: 1px solid #eaeef5;
        height: 100%;
    }
    
    .document-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    
    .document-title {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 15px;
        font-size: 1rem;
    }
    
    .document-preview {
        background-color: #f8f9fc;
        border-radius: 8px;
        overflow: hidden;
        height: 200px;
    }
    
    .btn-download {
        border-radius: 20px;
        padding: 5px 15px;
        font-size: 0.85rem;
    }
    
    .info-item {
        margin-bottom: 15px;
    }
    
    .info-item label {
        font-weight: 600;
        color: #2c3e50;
        display: block;
        margin-bottom: 5px;
    }
    
    .info-item p {
        margin: 0;
        padding: 8px 12px;
        background-color: #f8f9fc;
        border-radius: 8px;
    }
</style>
@stop

@section('js')
<script>
    $(document).ready(function() {
        $('.custom-file-input').on('change', function() {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass("selected").html(fileName);
        });
        
        $('form').submit(function() {
            $(this).find('button[type="submit"]').prop('disabled', true);
        });
    });
</script>
@stop