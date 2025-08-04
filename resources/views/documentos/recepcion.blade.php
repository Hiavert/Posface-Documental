@extends('adminlte::page')

@section('title', 'Documentos Recibidos')

@section('content_header')
<div class="elegant-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-0"><i class="fas fa-inbox mr-2 text-primary"></i> Documentos Recibidos</h1>
            <p class="subtitle">Universidad Nacional Autónoma de Honduras - Posgrado en Informática Administrativa</p>
        </div>
        <div>
            <button class="btn btn-outline-secondary" data-toggle="collapse" data-target="#filtrosCollapse">
                <i class="fas fa-filter mr-1"></i> Mostrar Filtros
            </button>
        </div>
    </div>
</div>
@stop

@section('content')
<div class="container-fluid">
    <!-- Filtros desplegables -->
    <div class="collapse mb-4" id="filtrosCollapse">
        <div class="card card-elegant">
            <div class="card-body">
                <form>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Tipo de Documento</label>
                            <select class="form-control form-control-elegant" name="tipo">
                                <option value="">Todos</option>
                                <option value="oficio">Oficio</option>
                                <option value="circular">Circular</option>
                                <option value="memorandum">Memorándum</option>
                                <option value="resolucion">Resolución</option>
                                <option value="acuerdo">Acuerdo</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Remitente</label>
                            <input type="text" class="form-control form-control-elegant" placeholder="Nombre del remitente">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Fecha del Documento</label>
                            <input type="date" class="form-control form-control-elegant">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label">Palabras Clave</label>
                            <input type="text" class="form-control form-control-elegant" placeholder="Buscar por asunto o contenido">
                        </div>
                        <div class="col-md-4 mb-3 d-flex align-items-end">
                            <div class="d-flex w-100">
                                <button type="button" class="btn btn-outline-secondary btn-elegant w-50 mr-2">
                                    <i class="fas fa-redo mr-1"></i> Limpiar
                                </button>
                                <button type="submit" class="btn btn-primary btn-elegant w-50">
                                    <i class="fas fa-search mr-1"></i> Buscar
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Tabla de documentos recibidos -->
    <div class="card card-elegant">
        <div class="card-header d-flex align-items-center">
            <h5 class="card-title mb-0"><i class="fas fa-file-alt mr-2 text-muted"></i> Documentos Recibidos</h5>
            <div class="ml-auto">
                <span class="badge badge-light">{{ $documentosRecibidos->total() }} registros</span>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-borderless">
                    <thead class="thead-elegant">
                        <tr>
                            <th>#</th>
                            <th>Tipo</th>
                            <th>Nombre</th>
                            <th>Remitente</th>
                            <th>Fecha Doc</th>
                            <th>Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($documentosRecibidos as $envio)
                        <tr class="{{ $envio->leido ? '' : 'table-row-unread' }}">
                            <td>{{ $envio->id }}</td>
                            <td>{{ ucfirst($envio->documento->tipo) }}</td>
                            <td>{{ $envio->documento->asunto }}</td>
                            <td>{{ $envio->enviadoPor->nombres }} {{ $envio->enviadoPor->apellidos }}</td>
                            <td>{{ $envio->documento->fecha_documento->format('d/m/Y') }}</td>
                            <td>
                                @if($envio->leido)
                                    <span class="badge badge-success">Leído</span>
                                @else
                                    <span class="badge badge-danger">No leído</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('documentos.show', $envio->documento) }}" 
                                   class="btn btn-sm btn-action" 
                                   title="Ver detalles">
                                    <i class="fas fa-eye text-info"></i>
                                </a>
                                <a href="{{ route('documentos.descargar', $envio->documento) }}" 
                                   class="btn btn-sm btn-action" 
                                   title="Descargar">
                                    <i class="fas fa-download text-success"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <h5>No se encontraron documentos</h5>
                                    <p class="text-muted">No tienes documentos recibidos</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Paginación -->
            @if($documentosRecibidos->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                    Mostrando {{ $documentosRecibidos->firstItem() }} - {{ $documentosRecibidos->lastItem() }} de {{ $documentosRecibidos->total() }} registros
                </div>
                <div class="pagination-custom">
                    {{ $documentosRecibidos->links() }}
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
        
        .card-header {
            background-color: white;
            border-bottom: 1px solid #eaeef5;
            border-radius: 12px 12px 0 0 !important;
            padding: 18px 25px;
        }
        
        .thead-elegant {
            background: linear-gradient(135deg, #0b2e59, #1a5a8d);
            color: white;
        }
        
        .table-row-unread {
            background-color: rgba(13, 110, 253, 0.05);
            font-weight: 500;
        }
        
        .badge-danger {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .badge-success {
            background-color: #d4edda;
            color: #155724;
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
        
        .pagination-custom .page-item.active .page-link {
            background: linear-gradient(135deg, #3a7bd5, #00d2ff);
            border-color: #3a7bd5;
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
        
        .form-control-elegant {
            border: 1px solid #eaeef5;
            border-radius: 8px;
            padding: 10px 15px;
            transition: all 0.3s ease;
            height: calc(1.5em + 1rem + 2px);
        }
        
        .form-control-elegant:focus {
            border-color: #3a7bd5;
            box-shadow: 0 0 0 0.2rem rgba(58, 123, 213, 0.15);
        }
        
        .btn-elegant {
            border-radius: 8px;
            font-weight: 500;
            padding: 8px 16px;
            transition: all 0.3s ease;
        }
        
        .btn-primary.btn-elegant {
            background: linear-gradient(135deg, #3a7bd5, #00d2ff);
            border: none;
        }
        
        .btn-outline-secondary.btn-elegant {
            border: 1px solid #dee2e6;
            color: #6c757d;
        }
    </style>
@stop