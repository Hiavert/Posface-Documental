@extends('adminlte::page')

@section('title', 'Detalles del Proceso de Pago')

@section('content_header')
<div class="elegant-header">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h1 class="mb-0"><i class="fas fa-file-invoice-dollar mr-2 text-primary"></i> Proceso: {{ $pagoTerna->codigo }}</h1>
            <p class="subtitle">Administración de pagos de terna</p>
        </div>
        <div class="d-flex align-items-center">
            <div class="header-icon ml-3">
                <i class="fas fa-user-tie"></i>
            </div>
        </div>
    </div>
</div>
@stop

@section('content')
<div class="container-fluid">
    <div class="card card-elegant">
        <div class="card-header">
            <h5 class="card-title mb-0">Detalles del Proceso</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="info-item">
                        <label>Código:</label>
                        <p>{{ $pagoTerna->codigo }}</p>
                    </div>
                    <div class="info-item">
                        <label>Descripción:</label>
                        <p>{{ $pagoTerna->descripcion }}</p>
                    </div>
                    <div class="info-item">
                        <label>Fecha de Defensa:</label>
                        <p>{{ $pagoTerna->fecha_defensa->format('d/m/Y') }}</p>
                    </div>
                    <div class="info-item">
                        <label>Responsable:</label>
                        <p>{{ $pagoTerna->responsable }}</p>
                    </div>
                    <div class="info-item">
                        <label>Estudiante:</label>
                        <p>{{ $pagoTerna->estudiante_nombre }} ({{ $pagoTerna->estudiante_cuenta }})</p>
                    </div>
                    <div class="info-item">
                        <label>Carrera:</label>
                        <p>{{ $pagoTerna->estudiante_carrera }}</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-item">
                        <label>Estado:</label>
                        <span class="badge badge-state-{{ $pagoTerna->estado }}">
                            {{ ucfirst(str_replace('_', ' ', $pagoTerna->estado)) }}
                        </span>
                    </div>
                    <div class="info-item">
                        <label>Administrador:</label>
                        <p>{{ $pagoTerna->administrador->nombres }} {{ $pagoTerna->administrador->apellidos }}</p>
                    </div>
                    <div class="info-item">
                        <label>Asistente:</label>
                        <p>{{ $pagoTerna->asistente->nombres }} {{ $pagoTerna->asistente->apellidos }}</p>
                    </div>
                    <div class="info-item">
                        <label>Fecha Límite:</label>
                        <p>{{ $pagoTerna->fecha_limite->format('d/m/Y H:i') }}</p>
                    </div>
                    <div class="info-item">
                        <label>Fecha de Envío al Asistente:</label>
                        <p>{{ $pagoTerna->fecha_envio_admin ? $pagoTerna->fecha_envio_admin->format('d/m/Y H:i') : 'No enviado' }}</p>
                    </div>
                    @if($pagoTerna->fecha_pago)
                    <div class="info-item">
                        <label>Fecha de Pago:</label>
                        <p>{{ $pagoTerna->fecha_pago->format('d/m/Y H:i') }}</p>
                    </div>
                    @endif
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

            <div class="mt-4">
                <h5><i class="fas fa-file-pdf mr-2"></i> Documentos Administrador</h5>
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
            </div>

            @if($pagoTerna->estado != 'en_revision')
            <div class="mt-4">
                <h5><i class="fas fa-file-pdf mr-2"></i> Documentos Asistente</h5>
                <div class="row">
                    @foreach($pagoTerna->documentos as $documento)
                        @if(in_array($documento->tipo, ['constancia_participacion', 'orden_pago', 'propuesta_maestria']))
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
            </div>
            @endif

            @if($pagoTerna->estado == 'pendiente_pago')
            <div class="mt-4">
                <h5><i class="fas fa-id-card mr-2"></i> Identidades de los Integrantes</h5>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="card document-card shadow-sm">
                            <div class="card-body">
                                <h6 class="document-title">Metodólogo: {{ $pagoTerna->metodologo->nombre }}</h6>
                                @if($identidades['metodologo'])
                                    <div class="document-preview">
                                        <iframe src="{{ asset('storage/' . $identidades['metodologo']) }}" 
                                            width="100%" 
                                            height="200" 
                                            style="border: none; background: #f8f9fc;">
                                        </iframe>
                                    </div>
                                    <div class="mt-3 text-center">
                                        <a href="{{ asset('storage/' . $identidades['metodologo']) }}" 
                                           target="_blank" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-download mr-1"></i> Descargar
                                        </a>
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <i class="fas fa-file-excel text-muted fa-3x"></i>
                                        <p class="mt-2">No se subió identidad</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <div class="card document-card shadow-sm">
                            <div class="card-body">
                                <h6 class="document-title">Técnico 1: {{ $pagoTerna->tecnico1->nombre }}</h6>
                                @if($identidades['tecnico1'])
                                    <div class="document-preview">
                                        <iframe src="{{ asset('storage/' . $identidades['tecnico1']) }}" 
                                            width="100%" 
                                            height="200" 
                                            style="border: none; background: #f8f9fc;">
                                        </iframe>
                                    </div>
                                    <div class="mt-3 text-center">
                                        <a href="{{ asset('storage/' . $identidades['tecnico1']) }}" 
                                           target="_blank" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-download mr-1"></i> Descargar
                                        </a>
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <i class="fas fa-file-excel text-muted fa-3x"></i>
                                        <p class="mt-2">No se subió identidad</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <div class="card document-card shadow-sm">
                            <div class="card-body">
                                <h6 class="document-title">Técnico 2: {{ $pagoTerna->tecnico2->nombre }}</h6>
                                @if($identidades['tecnico2'])
                                    <div class="document-preview">
                                        <iframe src="{{ asset('storage/' . $identidades['tecnico2']) }}" 
                                            width="100%" 
                                            height="200" 
                                            style="border: none; background: #f8f9fc;">
                                        </iframe>
                                    </div>
                                    <div class="mt-3 text-center">
                                        <a href="{{ asset('storage/' . $identidades['tecnico2']) }}" 
                                           target="_blank" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-download mr-1"></i> Descargar
                                        </a>
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <i class="fas fa-file-excel text-muted fa-3x"></i>
                                        <p class="mt-2">No se subió identidad</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-4 text-center">
                <form action="{{ route('terna.admin.marcar-pagado', $pagoTerna->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="fas fa-check-circle mr-1"></i> Marcar como Pagado
                    </button>
                </form>
            </div>
            @endif

            <div class="d-flex justify-content-start mt-4">
                <a href="{{ route('terna.admin.index') }}" class="btn btn-outline-secondary">
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
    
    .badge-state-pendiente_pago {
        background-color: #e3f2fd;
        color: #1976d2;
        padding: 6px 12px;
        border-radius: 50px;
        font-weight: 500;
    }
    
    .badge-state-pagado {
        background-color: #e8f5e9;
        color: #388e3c;
        padding: 6px 12px;
        border-radius: 50px;
        font-weight: 500;
    }
</style>
@stop