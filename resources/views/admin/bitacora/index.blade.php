@extends('adminlte::page')

@section('title', 'Bitácora del Sistema')

@section('content_header')
    <div class="unah-header">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h1 class="mb-0"><i class="fas fa-clipboard-list mr-2"></i> Bitácora del Sistema</h1>
                <p class="subtitle">Universidad Nacional Autónoma de Honduras - Posgrado en Informática Administrativa</p>
            </div>
            <div class="header-icon">
                <i class="fas fa-history"></i>
            </div>
        </div>
    </div>
@stop

@section('content')
<div class="container-fluid">
    <div class="card card-elegant mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0"><i class="fas fa-filter mr-2 text-muted"></i> Filtros</h5>
        </div>
        <div class="card-body">
            <form method="GET">
                <div class="row">
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Usuario</label>
                        <input type="text" name="usuario" class="form-control form-control-elegant" placeholder="Buscar usuario" value="{{ request('usuario') }}">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Módulo</label>
                        <select name="modulo" class="form-control form-control-elegant">
                            <option value="">Todos los módulos</option>
                            @foreach($modulos as $modulo)
                                <option value="{{ $modulo }}" {{ request('modulo') == $modulo ? 'selected' : '' }}>{{ $modulo }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Acción</label>
                        <select name="accion" class="form-control form-control-elegant">
                            <option value="">Todas las acciones</option>
                            @foreach($acciones as $accion)
                                <option value="{{ $accion }}" {{ request('accion') == $accion ? 'selected' : '' }}>{{ $accion }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Fecha</label>
                        <input type="date" name="fecha" class="form-control form-control-elegant" value="{{ request('fecha') }}">
                    </div>
                </div>
                <div class="d-flex justify-content-end mt-2">
                    <a href="{{ route('bitacora.index') }}" class="btn btn-outline-secondary btn-elegant mr-2">
                        <i class="fas fa-redo mr-1"></i> Limpiar
                    </a>
                    <button class="btn btn-primary btn-elegant" type="submit">
                        <i class="fas fa-search mr-1"></i> Filtrar
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <div class="card card-elegant">
        <div class="card-header d-flex align-items-center">
            <h5 class="card-title mb-0"><i class="fas fa-history mr-2 text-muted"></i> Registros de Bitácora</h5>
            <div class="ml-auto">
                <span class="badge badge-light">{{ $bitacoras->total() }} registros</span>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-borderless">
                    <thead class="thead-elegant">
                        <tr>
                            <th>Fecha/Hora</th>
                            <th>Usuario</th>
                            <th>Acción</th>
                            <th>Módulo</th>
                            <th>ID Registro</th>
                            <th>IP</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bitacoras as $log)
                            <tr class="table-row">
                                <td>{{ $log->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm mr-2">
                                            <div class="avatar-initials bg-primary text-white">
                                                {{ substr($log->usuario_nombre, 0, 1) }}
                                            </div>
                                        </div>
                                        <div>{{ $log->usuario_nombre }}</div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-action-{{ strtolower($log->accion) }}">
                                        {{ $log->accion }}
                                    </span>
                                </td>
                                <td>{{ $log->modulo }}</td>
                                <td>{{ $log->registro_id }}</td>
                                <td>
                                    <span class="badge badge-light">
                                        {{ $log->ip }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="fas fa-file-alt fa-3x mb-3 text-muted"></i>
                                        <h5>No se encontraron registros</h5>
                                        <p class="text-muted">No hay registros en la bitácora con los filtros aplicados</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($bitacoras->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                    Mostrando {{ $bitacoras->firstItem() }} - {{ $bitacoras->lastItem() }} de {{ $bitacoras->total() }} registros
                </div>
                <div class="pagination-custom">
                    {{ $bitacoras->appends(request()->query())->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@stop 

@section('css')
<style>
    .unah-header {
        background: linear-gradient(135deg, #0b2e59, #1a5a8d);
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        color: white;
        margin-bottom: 25px;
    }
    
    .unah-header .subtitle {
        font-size: 1rem;
        opacity: 0.85;
    }
    
    .header-icon {
        font-size: 2.5rem;
        opacity: 0.9;
    }
    
    /* Tarjetas */
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
    
    /* Tabla */
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
    
    /* Badges de acción */
    .badge-action-crear {
        background-color: #e8f5e9;
        color: #388e3c;
        padding: 6px 12px;
        border-radius: 50px;
        font-weight: 500;
    }
    
    .badge-action-editar {
        background-color: #fff8e1;
        color: #f57c00;
        padding: 6px 12px;
        border-radius: 50px;
        font-weight: 500;
    }
    
    .badge-action-eliminar {
        background-color: #ffebee;
        color: #d32f2f;
        padding: 6px 12px;
        border-radius: 50px;
        font-weight: 500;
    }
    
    .badge-action-inicio {
        background-color: #e3f2fd;
        color: #1976d2;
        padding: 6px 12px;
        border-radius: 50px;
        font-weight: 500;
    }
    
    .badge-action-otro {
        background-color: #f3e5f5;
        color: #7b1fa2;
        padding: 6px 12px;
        border-radius: 50px;
        font-weight: 500;
    }
    
    /* Avatar de usuarios */
    .avatar-sm {
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .avatar-initials {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 14px;
    }
    
    /* Botones */
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
    
    /* Estado vacío */
    .empty-state {
        padding: 40px 0;
        text-align: center;
    }
    
    .empty-state i {
        font-size: 3rem;
        opacity: 0.3;
        margin-bottom: 15px;
    }
    
    .empty-state h5 {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 10px;
    }
    
    /* Paginación */
    .pagination-custom .pagination {
        margin: 0;
    }
    
    .pagination-custom .page-item .page-link {
        border: none;
        border-radius: 8px;
        margin: 0 3px;
        color: #6c757d;
        transition: all 0.3s ease;
    }
    
    .pagination-custom .page-item.active .page-link {
        background: linear-gradient(135deg, #3a7bd5, #00d2ff);
        color: white;
        box-shadow: 0 4px 10px rgba(58, 123, 213, 0.25);
    }
    
    .pagination-custom .page-item .page-link:hover {
        background-color: #f0f4f8;
        color: #3a7bd5;
    }
    
    /* Formularios */
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
    
    /* Responsive */
    @media (max-width: 768px) {
        .card-header {
            padding: 15px;
        }
        
        .card-body {
            padding: 15px;
        }
        
        .table-row td {
            padding: 12px 8px;
            font-size: 0.85rem;
        }
        
        .table-row td:nth-child(3),
        .table-row td:nth-child(5) {
            display: none;
        }
        
        .thead-elegant th:nth-child(3),
        .thead-elegant th:nth-child(5) {
            display: none;
        }
        
        .form-control-elegant {
            padding: 8px 12px;
        }
    }
</style>
@stop