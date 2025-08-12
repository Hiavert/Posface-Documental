@extends('adminlte::page')

@section('title', 'Gestión de Tareas Documentales')

@section('content_header')
<div class="elegant-header">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h1 class="mb-0"><i class="fas fa-tasks mr-2 text-primary"></i> Gestión de Tareas Documentales</h1>
            <p class="subtitle">Universidad Nacional Autónoma de Honduras - Posgrado en Informática Administrativa</p>
        </div>
        <div class="d-flex align-items-center">
            <!-- Notificaciones -->
            <div class="notifications-dropdown ml-3">
                <button class="btn btn-notification" type="button" id="notifDropdown" data-toggle="dropdown">
                    <i class="fas fa-bell"></i>
                    <span class="badge badge-danger" id="notifCounter">3</span>
                </button>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="notifDropdown">
                    <div class="dropdown-header">Notificaciones Recientes</div>
                    <a class="dropdown-item d-flex justify-content-between align-items-center unread" href="#">
                        <div>
                            <div class="font-weight-bold">Nueva tarea asignada</div>
                            <small class="text-muted">De: José Villalobos</small>
                            <div class="small text-muted">Hace 2 minutos</div>
                        </div>
                        <span class="badge badge-danger">Nuevo</span>
                    </a>
                    <a class="dropdown-item d-flex justify-content-between align-items-center" href="#">
                        <div>
                            <div class="font-weight-bold">Tarea completada</div>
                            <small class="text-muted">De: Hola Mundo</small>
                            <div class="small text-muted">Hace 1 hora</div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="header-icon ml-3">
                <i class="fas fa-clipboard-list"></i>
            </div>
        </div>
    </div>
</div>

@if (session('success'))
    <div class="alert-container">
        <div class="alert alert-elegant-success alert-dismissible fade show">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    </div>
@endif

@if (session('error'))
    <div class="alert-container">
        <div class="alert alert-elegant-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    </div>
@endif
@stop

@section('content')
<div class="container-fluid">
    <!-- Filtros -->
    <div class="card card-elegant mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0"><i class="fas fa-filter mr-2 text-muted"></i>Filtros de Búsqueda</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('tareas.index') }}" id="filtroTareas">
                <div class="row">
                    <div class="col-md-2 mb-3">
                        <label class="form-label">Estado de la Tarea</label>
                        <select class="form-control form-control-elegant" name="estado">
                            <option value="">Todos</option>
                            <option value="Pendiente" {{ request('estado') == 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
                            <option value="En Proceso" {{ request('estado') == 'En Proceso' ? 'selected' : '' }}>En Proceso</option>
                            <option value="Completada" {{ request('estado') == 'Completada' ? 'selected' : '' }}>Completada</option>
                            <option value="Rechazada" {{ request('estado') == 'Rechazada' ? 'selected' : '' }}>Rechazada</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label">Responsable</label>
                        <select class="form-control form-control-elegant" name="responsable">
                            <option value="">Todos</option>
                            @foreach($responsables as $responsable)
                                <option value="{{ $responsable->id_usuario }}" {{ request('responsable') == $responsable->id_usuario ? 'selected' : '' }}>
                                    {{ $responsable->nombres }} {{ $responsable->apellidos }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label">Tipo de Documento</label>
                        <select class="form-control form-control-elegant" name="tipo_documento">
                            <option value="">Todos</option>
                            @foreach($tiposDocumento as $tipo)
                                <option value="{{ $tipo->id_tipo }}" {{ request('tipo_documento') == $tipo->id_tipo ? 'selected' : '' }}>{{ $tipo->nombre_tipo }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label">Fecha Inicio</label>
                        <input type="date" class="form-control form-control-elegant" name="fecha_inicio" value="{{ request('fecha_inicio') }}">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label">Fecha Fin</label>
                        <input type="date" class="form-control form-control-elegant" name="fecha_fin" value="{{ request('fecha_fin') }}">
                    </div>
                    <div class="col-md-2 mb-3 d-flex align-items-end">
                        <div class="w-100">
                            <button class="btn btn-primary btn-elegant mr-2 mb-2" type="submit">
                                <i class="fas fa-filter mr-1"></i> Filtrar
                            </button>
                            <a href="#" id="btnRestablecer" class="btn btn-outline-secondary btn-elegant mb-2">
                                <i class="fas fa-redo mr-1"></i> Restablecer
                            </a>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-end mt-2">
                    @if(Auth::user()->puedeAgregar('TareasDocumentales'))
                    <button type="button" class="btn btn-success btn-elegant" data-toggle="modal" data-target="#modalNuevaTarea">
                        <i class="fas fa-plus mr-1"></i> Nueva Tarea
                    </button>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Estadísticas elegantes -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stats-card stats-pending">
                <div class="stats-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stats-content">
                    <div class="stats-number">{{ $estadisticas['pendientes'] ?? 0 }}</div>
                    <div class="stats-label">Pendientes</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card stats-process">
                <div class="stats-icon">
                    <i class="fas fa-spinner"></i>
                </div>
                <div class="stats-content">
                    <div class="stats-number">{{ $estadisticas['en_proceso'] ?? 0 }}</div>
                    <div class="stats-label">En Proceso</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card stats-completed">
                <div class="stats-icon">
                    <i class="fas fa-check"></i>
                </div>
                <div class="stats-content">
                    <div class="stats-number">{{ $estadisticas['completadas'] ?? 0 }}</div>
                    <div class="stats-label">Completadas</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card stats-rejected">
                <div class="stats-icon">
                    <i class="fas fa-times"></i>
                </div>
                <div class="stats-content">
                    <div class="stats-number">{{ $estadisticas['rechazadas'] ?? 0 }}</div>
                    <div class="stats-label">Rechazadas</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de tareas -->
    <div class="card card-elegant">
        <div class="card-header d-flex align-items-center">
            <h5 class="card-title mb-0"><i class="fas fa-list mr-2 text-muted"></i> Tareas Documentales</h5>
            <div class="ml-auto">
                <span class="badge badge-light">{{ $tareas->count() }} registros</span>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-borderless">
                    <thead class="thead-elegant">
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Responsable</th>
                            <th>Estado</th>
                            <th>Fecha de Creación</th>
                            <th>Documentos</th>
                            <th>Tipo de Documento</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tareas as $tarea)
                            <tr class="table-row">
                                <td class="font-weight-bold">TD-{{ str_pad($tarea->id_tarea, 5, '0', STR_PAD_LEFT) }}</td>
                                <td>{{ $tarea->nombre }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm mr-2">
                                            @if($tarea->responsable)
                                                <div class="avatar-initials bg-primary text-white">
                                                    {{ substr($tarea->responsable->nombres, 0, 1) }}{{ substr($tarea->responsable->apellidos, 0, 1) }}
                                                </div>
                                            @else
                                                <div class="avatar-initials bg-secondary text-white">ND</div>
                                            @endif
                                        </div>
                                        <div>
                                            {{ $tarea->responsable->nombres ?? 'No asignado' }} {{ $tarea->responsable->apellidos ?? '' }}
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-state-{{ strtolower(str_replace(' ', '-', $tarea->estado)) }}">
                                        {{ $tarea->estado }}
                                    </span>
                                </td>
                                <td>{{ $tarea->fecha_creacion ? \Carbon\Carbon::parse($tarea->fecha_creacion)->format('d/m/Y H:i') : 'N/A' }}</td>
                                <td>
                                    @if($tarea->documentos && $tarea->documentos->count() > 0)
                                        <i class="fas fa-file-pdf text-danger" title="{{ $tarea->documentos->count() }} documento(s)"></i>
                                        <span class="ml-1">{{ $tarea->documentos->count() }}</span>
                                    @else
                                        <i class="fas fa-file-times text-muted" title="Sin documentos"></i>
                                        <span class="ml-1 text-muted">0</span>
                                    @endif
                                </td>
                                <td>
                                    @if($tarea->documentos && $tarea->documentos->first())
                                        <span class="badge badge-info">{{ $tarea->documentos->first()->tipoDocumento->nombre_tipo ?? 'N/A' }}</span>
                                    @else
                                        <span class="text-muted">Sin documento</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-actions" role="group">
                                        <button class="btn btn-sm btn-action" data-toggle="modal" data-target="#modalDetalleTarea"
                                                data-id="{{ $tarea->id_tarea }}"
                                                data-nombre="{{ $tarea->nombre }}"
                                                data-responsable="{{ $tarea->responsable->nombres ?? 'No asignado' }} {{ $tarea->responsable->apellidos ?? '' }}"
                                                data-estado="{{ $tarea->estado }}"
                                                data-fecha="{{ $tarea->fecha_creacion ? \Carbon\Carbon::parse($tarea->fecha_creacion)->format('d/m/Y') : 'N/A' }}"
                                                data-descripcion="{{ $tarea->descripcion ?? 'Sin descripción' }}"
                                                title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        
                                        @if(Auth::user()->puedeEditar('TareasDocumentales'))
                                        <button class="btn btn-sm btn-action" data-toggle="modal" data-target="#modalNuevaTarea"
                                                data-id="{{ $tarea->id_tarea }}"
                                                data-nombre="{{ $tarea->nombre }}"
                                                data-responsable="{{ $tarea->fk_id_usuario_asignado }}"
                                                data-estado="{{ $tarea->estado }}"
                                                data-fecha="{{ $tarea->fecha_creacion }}"
                                                data-descripcion="{{ $tarea->descripcion }}"
                                                title="Editar">
                                            <i class="fas fa-edit text-warning"></i>
                                        </button>
                                        @endif
                                        
                                        <button class="btn btn-sm btn-action" data-toggle="modal" data-target="#modalCargarDocumento"
                                                data-id="{{ $tarea->id_tarea }}" title="Cargar documento">
                                            <i class="fas fa-upload text-success"></i>
                                        </button>
                                        
                                        @if(Auth::user()->puedeEliminar('TareasDocumentales'))
                                        <form action="{{ route('tareas.destroy', $tarea->id_tarea) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-action" title="Eliminar" onclick="return confirm('¿Está seguro de eliminar esta tarea?')">
                                                <i class="fas fa-trash-alt text-danger"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="fas fa-tasks fa-3x text-muted mb-3"></i>
                                        <h5>No se encontraron tareas</h5>
                                        <p class="text-muted">Parece que aún no hay tareas registradas en el sistema</p>
                                        @if(Auth::user()->puedeAgregar('TareasDocumentales'))
                                        <button type="button" class="btn btn-primary mt-2" data-toggle="modal" data-target="#modalNuevaTarea">
                                            <i class="fas fa-plus mr-1"></i> Crear primera tarea
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal para nueva/editar tarea -->
<div class="modal fade" id="modalNuevaTarea" tabindex="-1" role="dialog" aria-labelledby="modalNuevaTareaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form method="POST" id="formTarea" action="{{ route('tareas.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header" style="background: #0b2e59; color: white;" id="modalTareaHeader">
                    <h5 class="modal-title" id="modalNuevaTareaLabel">
                        <i class="fas fa-plus mr-2"></i> Nueva Tarea
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="nombreTarea">Nombre de la tarea</label>
                        <input type="text" class="form-control form-control-elegant" id="nombreTarea" name="nombre" required>
                    </div>
                    <div class="form-group">
                        <label for="responsableTarea">Responsable</label>
                        <select class="form-control form-control-elegant" id="responsableTarea" name="fk_id_usuario_asignado" required>
                            <option value="">Seleccione</option>
                            @foreach($responsables as $responsable)
                                <option value="{{ $responsable->id_usuario }}">{{ $responsable->nombres }} {{ $responsable->apellidos }}</option>
                            @endforeach
                        </select>
                    </div>
                    <input type="hidden" name="fk_id_usuario_creador" value="{{ auth()->id() }}">
                    <div class="form-group">
                        <label for="descripcionTarea">Descripción</label>
                        <textarea class="form-control form-control-elegant" id="descripcionTarea" name="descripcion" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="estadoTarea">Estado</label>
                        <select class="form-control form-control-elegant" id="estadoTarea" name="estado" required>
                            <option value="Pendiente">Pendiente</option>
                            <option value="En Proceso">En Proceso</option>
                            <option value="Completada">Completada</option>
                            <option value="Rechazada">Rechazada</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="fechaCreacionTarea">Fecha de Creación</label>
                        <input type="date" class="form-control form-control-elegant" id="fechaCreacionTarea" name="fecha_creacion" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="fechaVencimientoTarea">Fecha de Vencimiento</label>
                        <input type="date" class="form-control form-control-elegant" id="fechaVencimientoTarea" name="fecha_vencimiento">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnGuardarTarea">Crear Tarea</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal para cargar documento -->
<div class="modal fade" id="modalCargarDocumento" tabindex="-1" role="dialog" aria-labelledby="modalCargarDocumentoLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form method="POST" action="{{ route('tareas.upload') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id_tarea" id="idTareaDocumento">
            <div class="modal-content">
                <div class="modal-header" style="background: #0b2e59; color: white;">
                    <h5 class="modal-title" id="modalCargarDocumentoLabel">
                        <i class="fas fa-upload mr-2"></i> Cargar Documento
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="fk_id_tipo">Tipo de Documento</label>
                        <select name="fk_id_tipo" id="fk_id_tipo" class="form-control form-control-elegant" required>
                            <option value="">Seleccione...</option>
                            @foreach($tiposDocumento as $tipo)
                                <option value="{{ $tipo->id_tipo }}">{{ $tipo->nombre_tipo }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="documento">Archivo (PDF o imagen)</label>
                        <input type="file" class="form-control form-control-elegant" id="documento" name="documento" accept="application/pdf,image/*" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Subir</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Detalle Tarea -->
<div class="modal fade" id="modalDetalleTarea" tabindex="-1" role="dialog" aria-labelledby="modalDetalleTareaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: #0b2e59; color: white;">
                <h5 class="modal-title" id="modalDetalleTareaLabel">
                    <i class="fas fa-info-circle mr-2"></i> Detalle de Tarea
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <dl class="row">
                    <dt class="col-sm-4">Nombre</dt>
                    <dd class="col-sm-8" id="detalle-nombre"></dd>
                    <dt class="col-sm-4">Responsable</dt>
                    <dd class="col-sm-8" id="detalle-responsable"></dd>
                    <dt class="col-sm-4">Estado</dt>
                    <dd class="col-sm-8">
                        <span id="detalle-estado" class="badge badge-pill"></span>
                    </dd>
                    <dt class="col-sm-4">Fecha de Creación</dt>
                    <dd class="col-sm-8" id="detalle-fecha"></dd>
                    <dt class="col-sm-4">Descripción</dt>
                    <dd class="col-sm-8" id="detalle-descripcion"></dd>
                </dl>
                <!-- Tarjeta de historial -->
                <div class="card mt-3">
                    <div class="card-header p-2">
                        <strong><i class="fas fa-history mr-1"></i>Historial de acciones</strong>
                    </div>
                    <div class="card-body p-2 historial-scroll" style="max-height: 250px; overflow-y: auto; background: #f8f9fa;" id="historial-bitacora">
                        <div class="text-center text-muted">Cargando historial...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para visualizar documento -->
<div class="modal fade" id="modalVerDocumento" tabindex="-1" role="dialog" aria-labelledby="modalVerDocumentoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <span id="iconoDocumento" style="font-size:2.5rem; margin-right:10px;"></span>
                <h5 class="modal-title" id="modalVerDocumentoLabel"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center" id="contenedorDocumento">
                <!-- Aquí se carga el documento dinámicamente -->
            </div>
            <div class="modal-footer">
                <form id="formEliminarDocumento" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" id="btnEliminarDocumento" onclick="return confirm('¿Seguro que desea eliminar este documento?')">
                        <i class="fas fa-trash"></i> Eliminar
                    </button>
                </form>
                <a href="#" id="descargarDocumento" class="btn btn-primary" download target="_blank">
                    <i class="fas fa-download"></i> Descargar
                </a>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
@stop

@section('css')
<style>
    /* Estilos generales */
    body {
        background-color: #f8f9fc;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    
    /* Encabezado elegante */
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
    
    /* Tarjetas elegantes */
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
    
    /* Estadísticas elegantes */
    .stats-card {
        background: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        margin-bottom: 20px;
    }
    
    .stats-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }
    
    .stats-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        border-radius: 15px 15px 0 0;
    }
    
    .stats-pending::before {
        background: linear-gradient(135deg, #ff9800, #ffb74d);
    }
    
    .stats-process::before {
        background: linear-gradient(135deg, #2196f3, #64b5f6);
    }
    
    .stats-completed::before {
        background: linear-gradient(135deg, #4caf50, #81c784);
    }
    
    .stats-rejected::before {
        background: linear-gradient(135deg, #f44336, #e57373);
    }
    
    .stats-icon {
        font-size: 2.5rem;
        margin-bottom: 15px;
        opacity: 0.8;
    }
    
    .stats-pending .stats-icon {
        color: #ff9800;
    }
    
    .stats-process .stats-icon {
        color: #2196f3;
    }
    
    .stats-completed .stats-icon {
        color: #4caf50;
    }
    
    .stats-rejected .stats-icon {
        color: #f44336;
    }
    
    .stats-number {
        font-size: 2.5rem;
        font-weight: 700;
        color: #2c3e50;
        line-height: 1;
        margin-bottom: 5px;
    }
    
    .stats-label {
        font-weight: 600;
        color: #6c757d;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    /* Botones elegantes */
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
    
    .btn-primary.btn-elegant:hover {
        background: linear-gradient(135deg, #2968c4, #00bfef);
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(58, 123, 213, 0.3);
    }
    
    .btn-success.btn-elegant {
        background: linear-gradient(135deg, #00b09b, #96c93d);
        border: none;
    }
    
    .btn-success.btn-elegant:hover {
        background: linear-gradient(135deg, #009d8a, #85b82c);
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0, 176, 155, 0.3);
    }
    
    .btn-outline-secondary.btn-elegant {
        border: 1px solid #dee2e6;
        color: #6c757d;
        background: white;
    }
    
    .btn-outline-secondary.btn-elegant:hover {
        background: #f8f9fa;
        border-color: #6c757d;
        color: #495057;
        transform: translateY(-2px);
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
    
    .btn-notification .badge {
        position: absolute;
        top: -5px;
        right: -5px;
        min-width: 18px;
        height: 18px;
        border-radius: 50%;
        font-size: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    /* Tabla elegante */
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
    
    .table-row td:first-child {
        border-radius: 10px 0 0 10px;
    }
    
    .table-row td:last-child {
        border-radius: 0 10px 10px 0;
    }
    
    /* Badges de estado */
    .badge-state-pendiente {
        background-color: #fff8e1;
        color: #f57c00;
        padding: 6px 12px;
        border-radius: 50px;
        font-weight: 500;
    }
    
    .badge-state-en-proceso {
        background-color: #e3f2fd;
        color: #1976d2;
        padding: 6px 12px;
        border-radius: 50px;
        font-weight: 500;
    }
    
    .badge-state-completada {
        background-color: #e8f5e9;
        color: #388e3c;
        padding: 6px 12px;
        border-radius: 50px;
        font-weight: 500;
    }
    
    .badge-state-rechazada {
        background-color: #ffebee;
        color: #d32f2f;
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
        margin: 0 2px;
    }
    
    .btn-action:hover {
        background-color: #f0f4f8;
        color: #3a7bd5;
        transform: scale(1.1);
    }
    
    .btn-group-actions {
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    /* Alertas elegantes */
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
        animation: slideInRight 0.5s ease-out;
    }
    
    .alert-elegant-danger {
        background: linear-gradient(135deg, #ffebee, #ffcdd2);
        color: #d32f2f;
        border: none;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        animation: slideInRight 0.5s ease-out;
    }
    
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
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
        color: #6c757d;
    }
    
    .empty-state h5 {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 10px;
    }
    
    .empty-state p {
        color: #6c757d;
    }
    
    /* Formularios elegantes */
    .form-control-elegant {
        border: 1px solid #eaeef5;
        border-radius: 8px;
        padding: 10px 15px;
        transition: all 0.3s ease;
        height: calc(1.5em + 1rem + 2px);
        background-color: white;
    }
    
    .form-control-elegant:focus {
        border-color: #3a7bd5;
        box-shadow: 0 0 0 0.2rem rgba(58, 123, 213, 0.15);
        background-color: white;
    }
    
    .form-label {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 5px;
        font-size: 0.9rem;
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
    
    /* Modales elegantes */
    .modal-content {
        border: none;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    }
    
    .modal-header {
        border-bottom: 1px solid #eaeef5;
        border-radius: 12px 12px 0 0;
    }
    
    .modal-footer {
        border-top: 1px solid #eaeef5;
        border-radius: 0 0 12px 12px;
    }
    
    /* Animaciones */
    .table-row {
        animation: fadeInUp 0.3s ease-out;
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .stats-card {
        animation: fadeInUp 0.5s ease-out;
    }
    
    .card-elegant {
        animation: fadeInUp 0.4s ease-out;
    }
    
    /* Scrollbar personalizado */
    .historial-scroll::-webkit-scrollbar {
        width: 8px;
    }
    
    .historial-scroll::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 4px;
    }
    
    .historial-scroll::-webkit-scrollbar-track {
        background: #f8f9fa;
    }
