@extends('adminlte::page')

@section('title', 'Historial de Envíos')

@section('content_header')
<div class="elegant-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-0"><i class="fas fa-history mr-2 text-info"></i> Historial de Envíos</h1>
            <p class="subtitle mb-0">Documento: {{ $documento->numero ?? 'DOC-' . $documento->id }} - {{ $documento->asunto }}</p>
        </div>
        <div>
            <a href="{{ route('documentos.gestor') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Volver
            </a>
        </div>
    </div>
</div>
@stop

@section('content')
<div class="container-fluid">
    <div class="card card-elegant">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-borderless">
                    <thead class="thead-elegant">
                        <tr>
                            <th>Destinatario</th>
                            <th>Enviado por</th>
                            <th>Fecha Envío</th>
                            <th>Estado</th>
                            <th>Fecha Lectura</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($envios as $envio)
                        <tr class="table-row">
                            <td>{{ $envio->destinatario->nombres }} {{ $envio->destinatario->apellidos }}</td>
                            <td>{{ $envio->enviadoPor->nombres }} {{ $envio->enviadoPor->apellidos }}</td>
                            <td>{{ $envio->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                @if($envio->leido)
                                    <span class="badge badge-success">Leído</span>
                                @else
                                    <span class="badge badge-warning">No leído</span>
                                @endif
                            </td>
                            <td>{{ $envio->fecha_leido ? $envio->fecha_leido->format('d/m/Y H:i') : 'N/A' }}</td>
                            <td>
                                <a href="{{ route('documentos.show', $envio->documento) }}" class="btn btn-sm btn-action" title="Ver detalles">
                                    <i class="fas fa-eye text-info"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <h5>No se encontraron envíos</h5>
                                    <p class="text-muted">Este documento no ha sido enviado a ningún usuario</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Paginación -->
            @if($envios->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                    Mostrando {{ $envios->firstItem() }} - {{ $envios->lastItem() }} de {{ $envios->total() }} registros
                </div>
                <div class="pagination-custom">
                    {{ $envios->links() }}
                </div>
            </div>
            @endif
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
        
        .thead-elegant {
            background: linear-gradient(135deg, #0b2e59, #1a5a8d);
            color: white;
        }
        
        .btn-action {
            background: transparent;
            border: none;
            color: #6c757d;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }
        
        .btn-action:hover {
            background-color: #f0f4f8;
            color: #3a7bd5;
            transform: scale(1.1);
        }
        
        .badge-success {
            background-color: #d4edda;
            color: #155724;
        }
        
        .badge-warning {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .empty-state {
            padding: 40px 0;
            text-align: center;
        }
        
        .empty-state i {
            font-size: 4rem;
            opacity: 0.3;
            margin-bottom: 20px;
        }
        
        .empty-state h5 {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 10px;
        }
        
        .empty-state p {
            color: #6c757d;
        }
        
        .table-row {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.03);
            transition: all 0.3s ease;
        }
        
        .table-row:hover {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transform: translateY(-2px);
        }
        
        .pagination-custom .page-item.active .page-link {
            background: linear-gradient(135deg, #3a7bd5, #00d2ff);
            border-color: #3a7bd5;
        }
    </style>
@stop