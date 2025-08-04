@extends('adminlte::page')

@section('title', 'Rastreo de Acuse')

@section('content_header')
<div class="unah-header">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h1 class="mb-0"><i class="fas fa-search-location mr-2 text-info"></i> Rastreo de Acuse</h1>
            <p class="subtitle">Universidad Nacional Autónoma de Honduras - Posgrado en Informática Administrativa</p>
        </div>
        <div class="header-icon">
            <i class="fas fa-map-marked-alt"></i>
        </div>
    </div>
</div>
@stop

@section('content')
<div class="container-fluid">
    <div class="card card-elegant">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-route mr-2 text-muted"></i> 
                Historial de AR-{{ str_pad($acuse->id_acuse, 5, '0', STR_PAD_LEFT) }}
            </h5>
        </div>
        <div class="card-body">
            <div class="timeline">
                @foreach($historial as $evento)
                <div class="timeline-step mb-4">
                    <div class="timeline-content card">
                        <div class="card-header bg-white d-flex justify-content-between">
                            <div>
                                <span class="badge badge-state-{{ $evento['tipo'] == 'creacion' ? 'enviado' : 'recibido' }}">
                                    {{ ucfirst($evento['accion']) }}
                                </span>
                            </div>
                            <span class="text-muted">
                                {{ $evento['fecha']->format('d/m/Y H:i') }}
                            </span>
                        </div>
                        <div class="card-body">
                            <div class="d-flex mb-3">
                                <div class="avatar-sm mr-3">
                                    <div class="avatar-initials bg-primary text-white">
                                        {{ substr($evento['remitente']->nombres, 0, 1) }}{{ substr($evento['remitente']->apellidos, 0, 1) }}
                                    </div>
                                </div>
                                <div>
                                    <strong>De:</strong> 
                                    {{ $evento['remitente']->nombres }} {{ $evento['remitente']->apellidos }}
                                    <div class="small text-muted">Remitente</div>
                                </div>
                            </div>
                            
                            <div class="d-flex">
                                <div class="avatar-sm mr-3">
                                    <div class="avatar-initials bg-info text-white">
                                        {{ substr($evento['destinatario']->nombres, 0, 1) }}{{ substr($evento['destinatario']->apellidos, 0, 1) }}
                                    </div>
                                </div>
                                <div>
                                    <strong>Para:</strong> 
                                    {{ $evento['destinatario']->nombres }} {{ $evento['destinatario']->apellidos }}
                                    <div class="small text-muted">Destinatario</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            <div class="card-footer">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <strong>Estado actual:</strong> 
                        <span class="badge badge-state-{{ $acuse->estado }}">
                            {{ ucfirst($acuse->estado) }}
                        </span>
                    </div>
                    <div>
                        <strong>En posesión de:</strong> 
                        {{ $poseedorActual->nombres }} {{ $poseedorActual->apellidos }}
                    </div>
                </div>
            </div>
            
            <div class="mt-4 text-right">
                <a href="{{ route('acuses.index') }}" class="btn btn-outline-secondary btn-elegant">
                    <i class="fas fa-arrow-left mr-1"></i> Volver
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    .timeline {
        position: relative;
        padding-left: 40px;
    }
    
    .timeline-step {
        position: relative;
    }
    
    .timeline-step:before {
        content: '';
        position: absolute;
        left: -25px;
        top: 20px;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background: #3a7bd5;
        border: 3px solid white;
        z-index: 2;
    }
    
    .timeline-step:after {
        content: '';
        position: absolute;
        left: -18px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #eaeef5;
    }
    
    .timeline-step:last-child:after {
        height: 20px;
    }
    
    .timeline-content {
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 3px 10px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
    }
    
    .timeline-content:hover {
        transform: translateX(5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    }
    .unah-header {
        background: linear-gradient(135deg, #0b2e59, #1a5a8d);
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        color: white;
        margin-bottom: 25px;
    }
    .avatar-initials {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 16px;
    }
</style>
@endsection