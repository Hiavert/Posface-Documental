@extends('adminlte::page')

@section('title', 'Pagos Terna - Asistente')

@section('content_header')
<div class="elegant-header">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h1 class="mb-0"><i class="fas fa-file-invoice-dollar mr-2 text-primary"></i> Pagos Terna</h1>
            <p class="subtitle">Asistente de pagos de terna</p>
        </div>
        <div class="d-flex align-items-center">
            <div class="header-icon ml-3">
                <i class="fas fa-user-headset"></i>
            </div>
        </div>
    </div>
</div>
@stop

@section('content')
<div class="container-fluid">
    @if(session('success'))
    <div class="alert-container">
        <div class="alert alert-elegant-success alert-dismissible fade show">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    </div>
    @endif

    <!-- Filtros de búsqueda -->
    <div class="card card-elegant mb-4">
        <div class="card-body">
            <form action="{{ route('terna.asistente.index') }}" method="GET">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Buscar por código</label>
                            <input type="text" name="codigo" class="form-control" placeholder="TERNA-001" value="{{ request('codigo') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Responsable</label>
                            <input type="text" name="responsable" class="form-control" placeholder="Nombre responsable" value="{{ request('responsable') }}">
                        </div>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter mr-1"></i> Filtrar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card card-elegant">
        <div class="card-header">
            <h5 class="card-title mb-0"><i class="fas fa-list mr-2 text-muted"></i> Procesos Asignados</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-borderless">
                    <thead class="thead-elegant">
                        <tr>
                            <th>
                                <a href="{{ route('terna.asistente.index', ['sort' => 'codigo', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}">
                                    Código {!! request('sort') == 'codigo' ? (request('direction') == 'asc' ? '▲' : '▼') : '' !!}
                                </a>
                            </th>
                            <th>
                                <a href="{{ route('terna.asistente.index', ['sort' => 'descripcion', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}">
                                    Descripción {!! request('sort') == 'descripcion' ? (request('direction') == 'asc' ? '▲' : '▼') : '' !!}
                                </a>
                            </th>
                            <th>
                                <a href="{{ route('terna.asistente.index', ['sort' => 'fecha_defensa', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}">
                                    Fecha Defensa {!! request('sort') == 'fecha_defensa' ? (request('direction') == 'asc' ? '▲' : '▼') : '' !!}
                                </a>
                            </th>
                            <th>
                                <a href="{{ route('terna.asistente.index', ['sort' => 'fecha_envio_admin', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}">
                                    Fecha Recepción {!! request('sort') == 'fecha_envio_admin' ? (request('direction') == 'asc' ? '▲' : '▼') : '' !!}
                                </a>
                            </th>
                            <th>
                                <a href="{{ route('terna.asistente.index', ['sort' => 'fecha_limite', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}">
                                    Fecha Límite {!! request('sort') == 'fecha_limite' ? (request('direction') == 'asc' ? '▲' : '▼') : '' !!}
                                </a>
                            </th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($procesos as $proceso)
                            <tr class="table-row">
                                <td class="font-weight-bold">{{ $proceso->codigo }}</td>
                                <td>{{ $proceso->descripcion }}</td>
                                <td>{{ $proceso->fecha_defensa->format('d/m/Y') }}</td>
                                <td>{{ $proceso->fecha_envio_admin->format('d/m/Y H:i') }}</td>
                                <td>
                                    {{ $proceso->fecha_limite->format('d/m/Y H:i') }}
                                    @if($proceso->fecha_limite < now())
                                        <span class="badge badge-danger ml-2">Retrasado</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('terna.asistente.show', $proceso->id) }}" class="btn btn-primary btn-elegant">
                                        <i class="fas fa-upload mr-1"></i> Completar
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                        <h5>No tienes procesos asignados</h5>
                                        <p class="text-muted">Actualmente no hay procesos pendientes de completar</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Paginación -->
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                    Mostrando {{ $procesos->firstItem() }} - {{ $procesos->lastItem() }} de {{ $procesos->total() }} registros
                </div>
                <div>
                    {{ $procesos->appends(request()->query())->links() }}
                </div>
            </div>
            
            <div class="d-flex justify-content-start mt-4">
                <a href="{{ URL::previous() }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left mr-1"></i> Volver
                </a>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    body {
        background-color: #f8f9fc;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    
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
    
    .card-title {
        font-weight: 600;
        color: #2c3e50;
        font-size: 1.1rem;
    }
    
    .card-body {
        padding: 25px;
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
    
    .table-borderless {
        border-collapse: separate;
        border-spacing: 0 8px;
    }
    
    .thead-elegant {
        background-color: #f8f9fc;
    }
    
    .thead-elegant th {
        border: none;
        color: #6c757d;
        font-weight: 600;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 12px 15px;
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
    
    .table-row td {
        padding: 16px 15px;
        vertical-align: middle;
        border-top: none;
        border-bottom: 1px solid #f0f4f8;
    }
    
    .table-row:first-child td:first-child {
        border-radius: 10px 0 0 10px;
    }
    
    .table-row:first-child td:last-child {
        border-radius: 0 10px 10px 0;
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
    
    .alert-container {
        position: fixed;
        top: 80px;
        right: 20px;
        z-index: 1050;
        width: 350px;
    }
    
    .alert-elegant-success {
        background: linear-gradient(135deg, #e8f5e9, #c8e6c9);
        color: #388e3c;
        border: none;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
    }
</style>
@stop