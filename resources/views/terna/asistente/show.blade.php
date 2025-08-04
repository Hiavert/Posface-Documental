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
                            <div class="card-body text-center">
                                <div class="document-icon mb-3">
                                    <i class="fas fa-file-pdf fa-3x text-danger"></i>
                                </div>
                                <h6 class="document-title">{{ ucfirst(str_replace('_', ' ', $documento->tipo)) }}</h6>
                                <div class="mt-3">
                                    <a href="{{ asset('storage/documentos_terna/' . $documento->ruta_archivo) }}" target="_blank" class="btn btn-sm btn-outline-primary btn-download">
                                        <i class="fas fa-download mr-1"></i> Descargar
                                    </a>

                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                @endforeach
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
                                <input type="file" class="custom-file-input" name="constancia_participacion" required>
                                <label class="custom-file-label">Seleccionar archivo</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Orden de Pago</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" name="orden_pago" required>
                                <label class="custom-file-label">Seleccionar archivo</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Propuesta de Maestrías</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" name="propuesta_maestria" required>
                                <label class="custom-file-label">Seleccionar archivo</label>
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
    }
    
    .document-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    
    .document-title {
        font-weight: 600;
        color: #2c3e50;
    }
    
    .btn-download {
        border-radius: 20px;
        padding: 5px 15px;
        font-size: 0.85rem;
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
        
        // Prevenir doble envío
        $('form').submit(function() {
            $(this).find('button[type="submit"]').prop('disabled', true);
        });
    });
</script>
@stop