@extends('adminlte::page')

@section('title', 'Pagos Terna - Administrador')

@section('content_header')
<div class="elegant-header">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h1 class="mb-0"><i class="fas fa-file-invoice-dollar mr-2 text-primary"></i> Pagos Terna</h1>
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
            <form action="{{ route('terna.admin.index') }}" method="GET">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Buscar por código</label>
                            <input type="text" name="codigo" class="form-control" placeholder="TERNA-001" value="{{ request('codigo') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Estado</label>
                            <select name="estado" class="form-control">
                                <option value="">Todos</option>
                                <option value="en_revision" {{ request('estado') == 'en_revision' ? 'selected' : '' }}>En revisión</option>
                                <option value="pendiente_pago" {{ request('estado') == 'pendiente_pago' ? 'selected' : '' }}>Pendiente pago</option>
                                <option value="pagado" {{ request('estado') == 'pagado' ? 'selected' : '' }}>Pagado</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Responsable</label>
                            <input type="text" name="responsable" class="form-control" placeholder="Nombre responsable" value="{{ request('responsable') }}">
                        </div>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter mr-1"></i> Filtrar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Resumen de estados -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card card-state bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="mb-0">En revisión</h5>
                            <h2 class="mb-0">{{ $counts['en_revision'] }}</h2>
                        </div>
                        <i class="fas fa-clock fa-3x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-state bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="mb-0">Pendiente pago</h5>
                            <h2 class="mb-0">{{ $counts['pendiente_pago'] }}</h2>
                        </div>
                        <i class="fas fa-file-invoice-dollar fa-3x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-state bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="mb-0">Pagados</h5>
                            <h2 class="mb-0">{{ $counts['pagado'] }}</h2>
                        </div>
                        <i class="fas fa-check-circle fa-3x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-state bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="mb-0">Retrasados</h5>
                            <h2 class="mb-0">{{ $counts['retrasados'] }}</h2>
                        </div>
                        <i class="fas fa-exclamation-triangle fa-3x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-elegant">
        <div class="card-header d-flex align-items-center">
            <h5 class="card-title mb-0"><i class="fas fa-list mr-2 text-muted"></i> Procesos de Pago</h5>
            <div class="ml-auto">
                @if($retrasados > 0)
                <span class="badge badge-danger mr-2">
                    {{ $retrasados }} retrasados
                </span>
                @endif
                <a href="{{ route('terna.admin.create') }}" class="btn btn-success btn-elegant">
                    <i class="fas fa-plus mr-1"></i> Nuevo Proceso
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-borderless">
                    <thead class="thead-elegant">
                        <tr>
                            <th>
                                <a href="{{ route('terna.admin.index', ['sort' => 'codigo', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}">
                                    Código {!! request('sort') == 'codigo' ? (request('direction') == 'asc' ? '▲' : '▼') : '' !!}
                                </a>
                            </th>
                            <th>
                                <a href="{{ route('terna.admin.index', ['sort' => 'descripcion', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}">
                                    Descripción {!! request('sort') == 'descripcion' ? (request('direction') == 'asc' ? '▲' : '▼') : '' !!}
                                </a>
                            </th>
                            <th>
                                <a href="{{ route('terna.admin.index', ['sort' => 'fecha_defensa', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}">
                                    Fecha Defensa {!! request('sort') == 'fecha_defensa' ? (request('direction') == 'asc' ? '▲' : '▼') : '' !!}
                                </a>
                            </th>
                            <th>
                                <a href="{{ route('terna.admin.index', ['sort' => 'estado', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}">
                                    Estado {!! request('sort') == 'estado' ? (request('direction') == 'asc' ? '▲' : '▼') : '' !!}
                                </a>
                            </th>
                            <th>Responsable</th>
                            <th>Fecha Límite</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($procesos as $proceso)
                            <tr class="table-row">
                                <td class="font-weight-bold">{{ $proceso->codigo }}</td>
                                <td>{{ $proceso->descripcion }}</td>
                                <td>{{ $proceso->fecha_defensa->format('d/m/Y') }}</td>
                                <td>
                                    <span class="badge badge-state-{{ $proceso->estado }}">
                                        {{ ucfirst(str_replace('_', ' ', $proceso->estado)) }}
                                    </span>
                                </td>
                                <td>{{ $proceso->responsable }}</td>
                                <td>
                                    {{ $proceso->fecha_limite->format('d/m/Y H:i') }}
                                    @if($proceso->estado == 'en_revision' && $proceso->fecha_limite < now())
                                        <span class="badge badge-danger ml-2">Retrasado</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-actions" role="group">
                                        <a href="{{ route('terna.admin.show', $proceso->id) }}" class="btn btn-sm btn-action" data-toggle="tooltip" title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('terna.admin.edit', $proceso->id) }}" class="btn btn-sm btn-action" data-toggle="tooltip" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('terna.admin.destroy', $proceso->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-action" data-toggle="tooltip" title="Eliminar" onclick="return confirm('¿Está seguro de eliminar este proceso?')">
                                                <i class="fas fa-trash-alt text-danger"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                        <h5>No se encontraron procesos</h5>
                                        <p class="text-muted">Parece que aún no hay procesos registrados</p>
                                        <a href="{{ route('terna.admin.create') }}" class="btn btn-primary mt-2">
                                            <i class="fas fa-plus mr-1"></i> Crear primer proceso
                                        </a>
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
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    /* Estilos copiados de la plantilla */
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
    
    .btn-success.btn-elegant {
        background: linear-gradient(135deg, #00b09b, #96c93d);
        border: none;
    }
    
    .btn-outline-secondary.btn-elegant {
        border: 1px solid #dee2e6;
        color: #6c757d;
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
    
    .badge-state-iniciado {
        background-color: #e3f2fd;
        color: #1976d2;
        padding: 6px 12px;
        border-radius: 50px;
        font-weight: 500;
    }
    
    .badge-state-en_revision {
        background-color: #fff8e1;
        color: #f57c00;
        padding: 6px 12px;
        border-radius: 50px;
        font-weight: 500;
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
    
    .badge-state-cancelado {
        background-color: #ffebee;
        color: #d32f2f;
        padding: 6px 12px;
        border-radius: 50px;
        font-weight: 500;
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
    
    .alert-elegant-danger {
        background: linear-gradient(135deg, #ffebee, #ffcdd2);
        color: #d32f2f;
        border: none;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
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
     /* Nuevos estilos para filtros y resumen */
    .card-state {
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }
    
    .card-state .card-body {
        padding: 20px;
    }
    
    .card-state h5 {
        font-size: 1rem;
        opacity: 0.9;
    }
    
    .card-state h2 {
        font-weight: 700;
        font-size: 2.2rem;
    }
    
    .filter-card .form-group {
        margin-bottom: 15px;
    }
    
    .filter-card label {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 5px;
    }
    
    .pagination .page-item.active .page-link {
        background: linear-gradient(135deg, #3a7bd5, #00d2ff);
        border-color: #3a7bd5;
    }
    
    .pagination .page-link {
        color: #3a7bd5;
        border-radius: 8px;
        margin: 0 3px;
    }
</style>
@stop

@section('js')
<script>
    $(document).ready(function() {
        // Inicializar tooltips
        $('[data-toggle="tooltip"]').tooltip({
            placement: 'top',
            trigger: 'hover'
        });
        
        // Animación para las filas de la tabla
        $('.table-row').each(function(i) {
            $(this).delay(i * 50).animate({
                opacity: 1
            }, 300);
        });
        
        // Prevenir doble envío de formularios
        $('form').submit(function() {
            $(this).find('button[type="submit"]').prop('disabled', true);
        });
    });
</script>
@stop