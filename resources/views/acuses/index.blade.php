@extends('adminlte::page')

@section('title', 'Gestión de Acuses de Recibo')

@section('content_header')
<div class="elegant-header">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h1 class="mb-0"><i class="fas fa-file-contract mr-2 text-primary"></i> Gestión de Acuses de Recibo</h1>
            <p class="subtitle">Universidad Nacional Autónoma de Honduras - Posgrado en Informática Administrativa</p>
        </div>
        <div class="d-flex align-items-center">
            <!-- Notificaciones movidas aquí -->
            <div class="notifications-dropdown ml-3">
                <button class="btn btn-notification" type="button" id="notifDropdown" data-toggle="dropdown">
                    <i class="fas fa-bell"></i>
                    <span class="badge badge-danger" id="notifCounter">{{ auth()->user()->notificacionesNoLeidas->count() }}</span>
                </button>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="notifDropdown">
                    <div class="dropdown-header">Notificaciones Recientes</div>
                    @if(auth()->user()->notificaciones->count() > 0)
                        @foreach(auth()->user()->notificaciones as $notificacion)
                            <a class="dropdown-item d-flex justify-content-between align-items-center {{ $notificacion->estado == 'no_leida' ? 'unread' : '' }}" 
                               href="{{ route('notificaciones.show', $notificacion->id_notificacion) }}">
                                <div>
                                    <div class="font-weight-bold">{{ $notificacion->titulo }}</div>
                                    <small class="text-muted">De: {{ $notificacion->acuse->remitente->nombres }} {{ $notificacion->acuse->remitente->apellidos }}</small>
                                    <div class="small text-muted">{{ $notificacion->fecha ? $notificacion->fecha->format('d/m/Y H:i') : now()->format('d/m/Y H:i') }}</div>
                                </div>
                                @if($notificacion->estado == 'no_leida')
                                    <span class="badge badge-danger">Nuevo</span>
                                @endif
                            </a>
                        @endforeach
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-bell-slash fa-2x mb-2 text-muted"></i>
                            <p class="text-muted">No hay notificaciones</p>
                        </div>
                    @endif
                </div>
            </div>
            <div class="header-icon ml-3">
                <i class="fas fa-envelope-open-text"></i>
            </div>
        </div>
    </div>
</div>
@stop

@section('content')
<div class="container-fluid">
    <!-- Notificaciones -->
    @if(session('success') || session('error'))
    <div class="alert-container">
        @if(session('success'))
        <div class="alert alert-elegant-success alert-dismissible fade show">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endif
        @if(session('error'))
        <div class="alert alert-elegant-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endif
    </div>
    @endif

    <!-- Filtros -->
    <div class="card card-elegant mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0"><i class="fas fa-filter mr-2 text-muted"></i>Filtros</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('acuses.index') }}">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Estado</label>
                        <select class="form-control form-control-elegant" name="estado">
                            <option value="">Todos</option>
                            <option value="enviado" {{ request('estado') == 'enviado' ? 'selected' : '' }}>Enviado</option>
                            <option value="recibido" {{ request('estado') == 'recibido' ? 'selected' : '' }}>Recibido</option>
                            <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Remitente</label>
                        <input type="text" class="form-control form-control-elegant" name="remitente" placeholder="Nombre del remitente" value="{{ request('remitente') }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Destinatario</label>
                        <input type="text" class="form-control form-control-elegant" name="destinatario" placeholder="Nombre del destinatario" value="{{ request('destinatario') }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Elemento</label>
                        <input type="text" class="form-control form-control-elegant" name="elemento" placeholder="Nombre del elemento" value="{{ request('elemento') }}">
                    </div>
                </div>
                <div class="d-flex justify-content-end mt-2">
                    <a href="{{ route('acuses.index') }}" class="btn btn-outline-secondary btn-elegant mr-2">
                        <i class="fas fa-redo mr-1"></i> Restablecer
                    </a>
                    <button type="submit" class="btn btn-primary btn-elegant mr-2">
                        <i class="fas fa-filter mr-1"></i> Aplicar Filtros
                    </button>
                    @if(Auth::user()->puedeAgregar('GestionAcuses'))
                    <button type="button" class="btn btn-success btn-elegant" data-toggle="modal" data-target="#modalEnviarAcuse">
                        <i class="fas fa-plus mr-1"></i> Nuevo Acuse
                    </button>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de acuses -->
    <div class="card card-elegant">
        <div class="card-header d-flex align-items-center">
            <h5 class="card-title mb-0"><i class="fas fa-list mr-2 text-muted"></i> Acuses de Recibo</h5>
            <div class="ml-auto">
                <span class="badge badge-light">{{ $acuses->total() }} registros</span>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-borderless">
                    <thead class="thead-elegant">
                        <tr>
                            <th>
                                <a href="{{ route('acuses.index', array_merge(request()->query(), ['sort' => 'id_acuse', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc'])) }}">
                                    ID {!! request('sort') == 'id_acuse' ? (request('direction') == 'asc' ? '▲' : '▼') : '' !!}
                                </a>
                            </th>
                            <th>
                                <a href="{{ route('acuses.index', array_merge(request()->query(), ['sort' => 'titulo', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc'])) }}">
                                    Título {!! request('sort') == 'titulo' ? (request('direction') == 'asc' ? '▲' : '▼') : '' !!}
                                </a>
                            </th>
                            <th>Remitente</th>
                            <th>Destinatario</th>
                            <th>
                                <a href="{{ route('acuses.index', array_merge(request()->query(), ['sort' => 'estado', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc'])) }}">
                                    Estado {!! request('sort') == 'estado' ? (request('direction') == 'asc' ? '▲' : '▼') : '' !!}
                                </a>
                            </th>
                            <th>
                                <a href="{{ route('acuses.index', array_merge(request()->query(), ['sort' => 'fecha_envio', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc'])) }}">
                                    Fecha Envío {!! request('sort') == 'fecha_envio' ? (request('direction') == 'asc' ? '▲' : '▼') : '' !!}
                                </a>
                            </th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($acuses as $acuse)
                            <tr class="table-row">
                                <td class="font-weight-bold">AR-{{ str_pad($acuse->id_acuse, 5, '0', STR_PAD_LEFT) }}</td>
                                <td>{{ $acuse->titulo }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm mr-2">
                                            @if($acuse->remitente)
                                                <div class="avatar-initials bg-primary text-white">
                                                    {{ substr($acuse->remitente->nombres, 0, 1) }}{{ substr($acuse->remitente->apellidos, 0, 1) }}
                                                </div>
                                            @else
                                                ND
                                            @endif
                                        </div>
                                        <div>
                                            {{ $acuse->remitente->nombres ?? 'No disponible' }} {{ $acuse->remitente->apellidos ?? '' }}
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm mr-2">
                                            @if($acuse->destinatario)
                                                <div class="avatar-initials bg-info text-white">
                                                    {{ substr($acuse->destinatario->nombres, 0, 1) }}{{ substr($acuse->destinatario->apellidos, 0, 1) }}
                                                </div>
                                            @else
                                                ND
                                            @endif
                                        </div>
                                        <div>
                                            {{ $acuse->destinatario->nombres ?? 'No disponible' }} {{ $acuse->destinatario->apellidos ?? '' }}
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-state-{{ $acuse->estado }}">
                                        {{ ucfirst($acuse->estado) }}
                                    </span>
                                </td>
                                <td>{{ $acuse->fecha_envio->format('d/m/Y H:i') }}</td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-actions" role="group">
                                        <a href="{{ route('acuses.show', $acuse->id_acuse) }}" class="btn btn-sm btn-action" data-toggle="tooltip" title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        @if($acuse->estado == 'pendiente' && $acuse->fk_id_usuario_destinatario == auth()->user()->id_usuario)
                                            <form action="{{ route('acuses.aceptar', $acuse->id_acuse) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn btn-sm btn-action" data-toggle="tooltip" title="Aceptar acuse">
                                                    <i class="fas fa-check text-success"></i>
                                                </button>
                                            </form>
                                        @endif
                                        
                                        @if($acuse->estado == 'recibido' && $acuse->fk_id_usuario_destinatario == auth()->user()->id_usuario)
                                            <a href="{{ route('acuses.reenviar.form', $acuse->id_acuse) }}" class="btn btn-sm btn-action" data-toggle="tooltip" title="Reenviar">
                                                <i class="fas fa-share text-warning"></i>
                                            </a>
                                        @endif
                                        
                                        <a href="{{ route('acuses.rastrear', $acuse->id_acuse) }}" class="btn btn-sm btn-action" data-toggle="tooltip" title="Rastrear">
                                            <i class="fas fa-search-location text-info"></i>
                                        </a>
                                        
                                        @if(auth()->user()->puedeEliminar('GestionAcuses'))
                                            <form action="{{ route('acuses.destroy', $acuse->id_acuse) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-action" data-toggle="tooltip" title="Eliminar" onclick="return confirm('¿Está seguro de eliminar este acuse?')">
                                                    <i class="fas fa-trash-alt text-danger"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                        <h5>No se encontraron acuses de recibo</h5>
                                        <p class="text-muted">Parece que aún no hay acuses registrados en el sistema</p>
                                        @if(Auth::user()->puedeAgregar('GestionAcuses'))
                                        <button type="button" class="btn btn-primary mt-2" data-toggle="modal" data-target="#modalEnviarAcuse">
                                            <i class="fas fa-plus mr-1"></i> Crear primer acuse
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($acuses->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                    Mostrando {{ $acuses->firstItem() }} - {{ $acuses->lastItem() }} de {{ $acuses->total() }} registros
                </div>
                <div class="pagination-custom">
                    {{ $acuses->appends(request()->query())->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal para nuevo acuse -->
<div class="modal fade" id="modalEnviarAcuse" tabindex="-1" role="dialog" aria-labelledby="modalEnviarAcuseLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background: #0b2e59; color: white;">
                    <h5 class="modal-title" id="modalEnviarAcuseLabel">
                        <i class="fas fa-paper-plane mr-2"></i> Nuevo Acuse de Recibo
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('acuses.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Destinatario</label>
                            <select class="form-control" name="destinatario" required>
                                <option value="">Seleccionar destinatario</option>
                                @foreach($usuarios as $usuario)
                                    @if($usuario->id_usuario != auth()->user()->id_usuario)
                                        <option value="{{ $usuario->id_usuario }}">
                                            {{ $usuario->nombres }} {{ $usuario->apellidos }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Título</label>
                            <input type="text" class="form-control" name="titulo" required placeholder="Título del acuse">
                        </div>
                        <div class="form-group">
                            <label>Descripción</label>
                            <textarea class="form-control" name="descripcion" rows="3" placeholder="Descripción del acuse"></textarea>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
                            <h5 class="mb-0">
                                <i class="fas fa-file-alt mr-2"></i> Elementos
                            </h5>
                            <div>
                                <button type="button" class="btn btn-sm btn-success" id="addElement">
                                    <i class="fas fa-plus mr-1"></i> Agregar
                                </button>
                                <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#modalNuevoTipo">
                                    <i class="fas fa-plus-circle mr-1"></i> Nuevo Tipo
                                </button>
                            </div>
                        </div>
                        
                        <div id="elementosContainer">
                            <div class="elemento-item mb-3 border p-3 rounded">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label>Tipo</label>
                                        <select class="form-control tipo-select" name="elementos[0][fk_id_tipo]" required>
                                            <option value="">Seleccionar tipo</option>
                                            @foreach($tiposElemento as $tipo)
                                                <option value="{{ $tipo->id_tipo }}">
                                                    {{ $tipo->nombre }} ({{ $tipo->categoria }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label>Nombre</label>
                                        <input type="text" class="form-control" name="elementos[0][nombre]" required placeholder="Nombre del elemento">
                                    </div>
                                    <div class="col-md-2">
                                        <label>Cantidad</label>
                                        <input type="number" class="form-control" name="elementos[0][cantidad]" value="1" min="1">
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end">
                                        <button type="button" class="btn btn-danger btn-block remove-element">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-12">
                                        <label>Descripción</label>
                                        <textarea class="form-control" name="elementos[0][descripcion]" rows="2" placeholder="Descripción del elemento"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Sección para adjuntos -->
                        <div class="mt-4">
                            <h5><i class="fas fa-paperclip mr-2"></i> Documentos Adjuntos (PDF, Word, Excel)</h5>
                            <div class="form-group">
                                <input type="file" class="form-control-file" name="adjuntos_documentos[]" multiple accept=".pdf,.doc,.docx,.xls,.xlsx">
                                <small class="form-text text-muted">Solo para elementos de tipo documento</small>
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <h5><i class="fas fa-image mr-2"></i> Imágenes Adjuntas (JPG, PNG, GIF)</h5>
                            <div class="form-group">
                                <input type="file" class="form-control-file" name="adjuntos_imagenes[]" multiple accept="image/*">
                                <small class="form-text text-muted">Solo para elementos de tipo objeto o kit</small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Enviar Acuse</button>
                    </div>
                </form>
            </div>
        </div>
</div>

<!-- Modal para nuevo tipo de elemento -->
<div class="modal fade" id="modalNuevoTipo" tabindex="-1" role="dialog" aria-labelledby="modalNuevoTipoLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background: #0b2e59; color: white;">
                    <h5 class="modal-title" id="modalNuevoTipoLabel">
                        <i class="fas fa-plus-circle mr-2"></i> Nuevo Tipo de Elemento
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('tipos.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nombre</label>
                            <input type="text" class="form-control" name="nombre" required placeholder="Nombre del tipo">
                        </div>
                        <div class="form-group">
                            <label>Categoría</label>
                            <select class="form-control" name="categoria" required>
                                <option value="">Seleccionar categoría</option>
                                <option value="documento">Documento</option>
                                <option value="objeto">Objeto</option>
                                <option value="kit">Kit</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Tipo</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('css')
<style>
    /* Estilos generales */
    body {
        background-color: #f8f9fc;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    
    /* Encabezado */
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
    
    .btn-success.btn-elegant {
        background: linear-gradient(135deg, #00b09b, #96c93d);
        border: none;
    }
    
    .btn-outline-secondary.btn-elegant {
        border: 1px solid #dee2e6;
        color: #6c757d;
    }
    
    .btn-notification {
        background: #f8f9fc;
        border: 1px solid #eaeef5;
        border-radius: 50%;
        width: 42px;
        height: 42px;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        transition: all 0.3s ease;
    }
    
    .btn-notification:hover {
        background: #eef2f7;
        transform: rotate(10deg);
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
    
    /* Badges de estado */
    .badge-state-enviado {
        background-color: #e3f2fd;
        color: #1976d2;
        padding: 6px 12px;
        border-radius: 50px;
        font-weight: 500;
    }
    
    .badge-state-recibido {
        background-color: #e8f5e9;
        color: #388e3c;
        padding: 6px 12px;
        border-radius: 50px;
        font-weight: 500;
    }
    
    .badge-state-pendiente {
        background-color: #fff8e1;
        color: #f57c00;
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
    
    /* Botones de acción */
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
    
    /* Alertas */
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
    
    /* Estado vacío */
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
    
    /* Notificaciones dropdown */
    .notifications-dropdown .dropdown-menu {
        border: none;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        width: 350px;
        max-height: 400px;
        overflow-y: auto;
        padding: 0;
    }
    
    .dropdown-header {
        background-color: #f8f9fc;
        padding: 12px 20px;
        font-weight: 600;
        color: #2c3e50;
        border-radius: 12px 12px 0 0;
        border-bottom: 1px solid #eaeef5;
    }
    
    .dropdown-item {
        padding: 12px 20px;
        border-bottom: 1px solid #f0f4f8;
        transition: all 0.2s ease;
    }
    
    .dropdown-item.unread {
        background-color: #f0f8ff;
        border-left: 3px solid #3a7bd5;
    }
    
    .dropdown-item:hover {
        background-color: #f8f9fc;
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
        
        // Animación para botón de notificaciones
        $('#notifDropdown').hover(function() {
            $(this).find('i').css('transform', 'rotate(15deg)');
        }, function() {
            $(this).find('i').css('transform', 'rotate(0deg)');
        });

        // Controlador para agregar elementos
        let elementoCount = 1;
        $('#addElement').click(function(e) {
            e.preventDefault();
            let newElement = `
                <div class="elemento-item mb-3 border p-3 rounded">
                    <div class="row">
                        <div class="col-md-4">
                            <label>Tipo</label>
                            <select class="form-control tipo-select" name="elementos[${elementoCount}][fk_id_tipo]" required>
                                <option value="">Seleccionar tipo</option>
                                @foreach($tiposElemento as $tipo)
                                    <option value="{{ $tipo->id_tipo }}">
                                        {{ $tipo->nombre }} ({{ $tipo->categoria }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label>Nombre</label>
                            <input type="text" class="form-control" name="elementos[${elementoCount}][nombre]" required placeholder="Nombre del elemento">
                        </div>
                        <div class="col-md-2">
                            <label>Cantidad</label>
                            <input type="number" class="form-control" name="elementos[${elementoCount}][cantidad]" value="1" min="1">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" class="btn btn-danger btn-block remove-element">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-12">
                            <label>Descripción</label>
                            <textarea class="form-control" name="elementos[${elementoCount}][descripcion]" rows="2" placeholder="Descripción del elemento"></textarea>
                        </div>
                    </div>
                </div>
            `;
            $('#elementosContainer').append(newElement);
            elementoCount++;
        });

        // Eliminar elemento
        $(document).on('click', '.remove-element', function() {
            $(this).closest('.elemento-item').remove();
        });
    });
</script>
@stop