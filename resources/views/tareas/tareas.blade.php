@extends('adminlte::page')

@section('title', 'Gestión de Tareas Documentales')

@section('content_header')
<div class="elegant-header">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h1 class="mb-0"><i class="fas fa-tasks mr-2 text-primary" aria-hidden="true"></i> Gestión de Tareas Documentales</h1>
           <p class="mb-0">Universidad Nacional Autónoma de Honduras - Posgrado de la Facultad de Ciencias Económicas Administrativas y Contables</p>
        </div>
        <div class="d-flex align-items-center">
            <div class="header-icon ml-3">
                <i class="fas fa-file-alt" aria-hidden="true"></i>
            </div>
        </div>
    </div>
</div>
@stop

@section('content')
<div class="container-fluid">
    <!-- Alertas -->
    @if (session('success'))
        <div class="alert alert-elegant-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle mr-2" aria-hidden="true"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar alerta">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-elegant-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle mr-2" aria-hidden="true"></i>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar alerta">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Estadísticas rápidas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card card-stats">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="icon-bg bg-secondary">
                            <i class="fas fa-clock" aria-hidden="true"></i>
                        </div>
                        <div class="ml-3">
                            <h2 class="card-title">Pendientes</h2>
                            <p class="card-value" aria-label="{{ $estadisticas['pendientes'] ?? 0 }} tareas pendientes">{{ $estadisticas['pendientes'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-stats">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="icon-bg bg-info">
                            <i class="fas fa-spinner" aria-hidden="true"></i>
                        </div>
                        <div class="ml-3">
                            <h2 class="card-title">En Proceso</h2>
                            <p class="card-value" aria-label="{{ $estadisticas['en_proceso'] ?? 0 }} tareas en proceso">{{ $estadisticas['en_proceso'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-stats">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="icon-bg bg-success">
                            <i class="fas fa-check" aria-hidden="true"></i>
                        </div>
                        <div class="ml-3">
                            <h2 class="card-title">Completadas</h2>
                            <p class="card-value" aria-label="{{ $estadisticas['completadas'] ?? 0 }} tareas completadas">{{ $estadisticas['completadas'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-stats">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="icon-bg bg-primary">
                            <i class="fas fa-user-check" aria-hidden="true"></i>
                        </div>
                        <div class="ml-3">
                            <h2 class="card-title">Rechazadas</h2>
                            <p class="card-value" aria-label="{{ $estadisticas['rechazadas'] ?? 0 }} tareas rechazadas">{{ $estadisticas['rechazadas'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card card-elegant mb-4">
        <div class="card-header">
            <h2 class="card-title mb-0"><i class="fas fa-filter mr-2 text-muted" aria-hidden="true"></i>Filtros</h2>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('tareas.index') }}" id="filtroTareas" role="search" aria-label="Formulario de filtros para tareas documentales">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="estado_filtro" class="form-label">Estado</label>
                        <select class="form-control form-control-elegant" name="estado" id="estado_filtro" aria-describedby="estado_help">
                            <option value="">Todos</option>
                            <option value="Pendiente" {{ request('estado') == 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
                            <option value="En Proceso" {{ request('estado') == 'En Proceso' ? 'selected' : '' }}>En Proceso</option>
                            <option value="Completada" {{ request('estado') == 'Completada' ? 'selected' : '' }}>Completada</option>
                            <option value="Rechazada" {{ request('estado') == 'Rechazada' ? 'selected' : '' }}>Rechazada</option>
                        </select>
                        <small id="estado_help" class="form-text text-muted">Filtrar por estado de la tarea</small>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="responsable_filtro" class="form-label">Responsable</label>
                        <select class="form-control form-control-elegant" name="responsable" id="responsable_filtro" aria-describedby="responsable_help">
                            <option value="">Todos</option>
                            @foreach($responsables as $responsable)
                                <option value="{{ $responsable->id_usuario }}" {{ request('responsable') == $responsable->id_usuario ? 'selected' : '' }}>
                                    {{ $responsable->nombres }} {{ $responsable->apellidos }}
                                </option>
                            @endforeach
                        </select>
                        <small id="responsable_help" class="form-text text-muted">Filtrar por responsable asignado</small>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="tipo_documento_filtro" class="form-label">Tipo de Documento</label>
                        <select class="form-control form-control-elegant" name="tipo_documento" id="tipo_documento_filtro" aria-describedby="tipo_documento_help">
                            <option value="">Todos</option>
                            @foreach($tiposDocumento as $tipo)
                                <option value="{{ $tipo->id_tipo }}" {{ request('tipo_documento') == $tipo->id_tipo ? 'selected' : '' }}>{{ $tipo->nombre_tipo }}</option>
                            @endforeach
                        </select>
                        <small id="tipo_documento_help" class="form-text text-muted">Filtrar por tipo de documento</small>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="fecha_inicio_filtro" class="form-label">Fecha Inicio</label>
                        <input type="date" class="form-control form-control-elegant" name="fecha_inicio" id="fecha_inicio_filtro" value="{{ request('fecha_inicio') }}" aria-describedby="fecha_inicio_help">
                        <small id="fecha_inicio_help" class="form-text text-muted">Fecha de inicio para el filtro</small>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="fecha_fin_filtro" class="form-label">Fecha Fin</label>
                        <input type="date" class="form-control form-control-elegant" name="fecha_fin" id="fecha_fin_filtro" value="{{ request('fecha_fin') }}" aria-describedby="fecha_fin_help">
                        <small id="fecha_fin_help" class="form-text text-muted">Fecha de fin para el filtro</small>
                    </div>
                    <div class="col-md-3 mb-3 d-flex align-items-end">
                        <div class="btn-group w-100" role="group" aria-label="Acciones de filtros">
                            <button type="submit" class="btn btn-primary btn-elegant" aria-label="Aplicar filtros">
                                <i class="fas fa-filter mr-1" aria-hidden="true"></i> Aplicar
                            </button>
                            <a href="{{ route('tareas.index') }}" class="btn btn-outline-secondary btn-elegant" aria-label="Limpiar filtros">
                                <i class="fas fa-redo mr-1" aria-hidden="true"></i> Limpiar
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Listado de tareas -->
    <div class="card card-elegant">
        <div class="card-header d-flex align-items-center">
            <h2 class="card-title mb-0"><i class="fas fa-list mr-2 text-muted" aria-hidden="true"></i> Tareas Documentales</h2>
            <div class="ml-auto">
                @if(Auth::user()->puedeAgregar('TareasDocumentales'))
                <button class="btn btn-success btn-elegant" type="button" data-toggle="modal" data-target="#modalNuevaTarea" id="btnNuevaTarea" aria-label="Crear nueva tarea documental">
                    <i class="fas fa-plus mr-1" aria-hidden="true"></i> Nueva Tarea
                </button>
                @endif
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-borderless" aria-describedby="tabla-tareas-desc">
                    <caption id="tabla-tareas-desc" class="sr-only">Lista de tareas documentales con información de ID, nombre, responsable, estado, fecha de creación, documentos y acciones disponibles</caption>
                    <thead class="thead-elegant">
                        <tr>
                            <th scope="col">
                                <a href="{{ route('tareas.index', [
                                    'sort' => 'id_tarea',
                                    'direction' => ($sort == 'id_tarea' && $direction == 'asc') ? 'desc' : 'asc',
                                    'estado' => request('estado'),
                                    'responsable' => request('responsable'),
                                    'fecha_inicio' => request('fecha_inicio'),
                                    'fecha_fin' => request('fecha_fin'),
                                    'tipo_documento' => request('tipo_documento')
                                ]) }}" class="sort-link" aria-label="Ordenar por ID {{ $sort == 'id_tarea' ? ($direction == 'asc' ? 'ascendente' : 'descendente') : '' }}">
                                    ID
                                    @if ($sort == 'id_tarea')
                                        <i class="fas fa-sort-{{ $direction == 'asc' ? 'up' : 'down' }}" aria-hidden="true"></i>
                                    @else
                                        <i class="fas fa-sort" aria-hidden="true"></i>
                                    @endif
                                </a>
                            </th>
                            <th scope="col">
                                <a href="{{ route('tareas.index', [
                                    'sort' => 'nombre',
                                    'direction' => ($sort == 'nombre' && $direction == 'asc') ? 'desc' : 'asc',
                                    'estado' => request('estado'),
                                    'responsable' => request('responsable'),
                                    'fecha_inicio' => request('fecha_inicio'),
                                    'fecha_fin' => request('fecha_fin'),
                                    'tipo_documento' => request('tipo_documento')
                                ]) }}" class="sort-link" aria-label="Ordenar por nombre {{ $sort == 'nombre' ? ($direction == 'asc' ? 'ascendente' : 'descendente') : '' }}">
                                    Nombre
                                    @if ($sort == 'nombre')
                                        <i class="fas fa-sort-{{ $direction == 'asc' ? 'up' : 'down' }}" aria-hidden="true"></i>
                                    @else
                                        <i class="fas fa-sort" aria-hidden="true"></i>
                                    @endif
                                </a>
                            </th>
                            <th scope="col">Responsable</th>
                            <th scope="col">
                                <a href="{{ route('tareas.index', [
                                    'sort' => 'estado',
                                    'direction' => ($sort == 'estado' && $direction == 'asc') ? 'desc' : 'asc',
                                    'estado' => request('estado'),
                                    'responsable' => request('responsable'),
                                    'fecha_inicio' => request('fecha_inicio'),
                                    'fecha_fin' => request('fecha_fin'),
                                    'tipo_documento' => request('tipo_documento')
                                ]) }}" class="sort-link" aria-label="Ordenar por estado {{ $sort == 'estado' ? ($direction == 'asc' ? 'ascendente' : 'descendente') : '' }}">
                                    Estado
                                    @if ($sort == 'estado')
                                        <i class="fas fa-sort-{{ $direction == 'asc' ? 'up' : 'down' }}" aria-hidden="true"></i>
                                    @else
                                        <i class="fas fa-sort" aria-hidden="true"></i>
                                    @endif
                                </a>
                            </th>
                            <th scope="col">
                                <a href="{{ route('tareas.index', [
                                    'sort' => 'fecha_creacion',
                                    'direction' => ($sort == 'fecha_creacion' && $direction == 'asc') ? 'desc' : 'asc',
                                    'estado' => request('estado'),
                                    'responsable' => request('responsable'),
                                    'fecha_inicio' => request('fecha_inicio'),
                                    'fecha_fin' => request('fecha_fin'),
                                    'tipo_documento' => request('tipo_documento')
                                ]) }}" class="sort-link" aria-label="Ordenar por fecha de creación {{ $sort == 'fecha_creacion' ? ($direction == 'asc' ? 'ascendente' : 'descendente') : '' }}">
                                    Fecha Creación
                                    @if ($sort == 'fecha_creacion')
                                        <i class="fas fa-sort-{{ $direction == 'asc' ? 'up' : 'down' }}" aria-hidden="true"></i>
                                    @else
                                        <i class="fas fa-sort" aria-hidden="true"></i>
                                    @endif
                                </a>
                            </th>
                            <th scope="col">Documentos</th>
                            <th scope="col" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @include('tareas.tabla', ['tareas' => $tareas])
                    </tbody>
                </table>
            </div>
            
            @if($tareas->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                    Mostrando {{ $tareas->firstItem() }} - {{ $tareas->lastItem() }} de {{ $tareas->total() }} registros
                </div>
                <div class="pagination-custom" role="navigation" aria-label="Paginación de tareas">
                    {{ $tareas->appends(request()->query())->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal para nueva/editar tarea -->
<div class="modal fade" id="modalNuevaTarea" tabindex="-1" role="dialog" aria-labelledby="modalNuevaTareaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: #0b2e59; color: white;">
                <h3 class="modal-title" id="modalNuevaTareaLabel">
                    <i class="fas fa-plus-circle mr-2" aria-hidden="true"></i> <span id="modalTareaTitle">Nueva Tarea Documental</span>
                </h3>
                @if(auth()->user()->puedeEditar('TareasDocumentales'))
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar modal de tarea">
                    <span aria-hidden="true">&times;</span>
                </button>
                @endif
            </div>
            <form method="POST" id="formTarea" action="{{ route('tareas.store') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="tarea_id" id="tareaId">
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="nombreTarea">Nombre de la tarea *</label>
                            <input type="text" class="form-control" id="nombreTarea" name="nombre" required 
                                   maxlength="50" oninput="sanitizeNombreTarea(this)" aria-describedby="nombreTarea_help">
                            <small id="nombreTarea_help" class="form-text text-muted">Máximo 50 caracteres. Solo letras, números, espacios, puntos, comas y guiones.</small>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="responsableTarea">Responsable *</label>
                            <select class="form-control" id="responsableTarea" name="fk_id_usuario_asignado" required aria-describedby="responsableTarea_help">
                                <option value="">Seleccionar responsable</option>
                                @foreach($responsables as $responsable)
                                    <option value="{{ $responsable->id_usuario }}">{{ $responsable->nombres }} {{ $responsable->apellidos }}</option>
                                @endforeach
                            </select>
                            <small id="responsableTarea_help" class="form-text text-muted">Seleccione el responsable de la tarea</small>
                        </div>
                    </div>
                    
                    <input type="hidden" name="fk_id_usuario_creador" value="{{ auth()->id() }}">
                    
                    <div class="form-group">
                        <label for="descripcionTarea">Descripción (opcional)</label>
                        <textarea class="form-control" id="descripcionTarea" name="descripcion" rows="3" 
                                  maxlength="100" oninput="sanitizeDescripcionTarea(this)" aria-describedby="descripcionTarea_help"></textarea>
                        <small id="descripcionTarea_help" class="form-text text-muted">Máximo 100 caracteres. Solo letras, números, espacios, puntos (pueden ser dos seguidos), comas y guiones.</small>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="fechaCreacionTarea">Fecha de Creación *</label>
                            <input type="date" class="form-control" id="fechaCreacionTarea" name="fecha_creacion" value="{{ date('Y-m-d') }}" required aria-describedby="fechaCreacionTarea_help">
                            <small id="fechaCreacionTarea_help" class="form-text text-muted">Fecha en que se crea la tarea</small>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="fechaVencimientoTarea">Fecha de Vencimiento (opcional)</label>
                            <input type="date" class="form-control" id="fechaVencimientoTarea" name="fecha_vencimiento" aria-describedby="fechaVencimientoTarea_help">
                            <small id="fechaVencimientoTarea_help" class="form-text text-muted">Fecha límite para completar la tarea</small>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="documentoTarea">Documento adjunto (opcional)</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="documentoTarea" name="documento" accept="application/pdf,image/*" aria-describedby="documentoTarea_help">
                            <label class="custom-file-label" for="documentoTarea" id="documentoLabel">Seleccionar archivo</label>
                        </div>
                        <small id="documentoTarea_help" class="form-text text-muted">Tamaño máximo: 10MB</small>
                    </div>
                    
                    <div class="form-group" id="tipoDocumentoContainer">
                        <label for="fk_id_tipo">Tipo de Documento *</label>
                        <select name="fk_id_tipo" id="fk_id_tipo" class="form-control" required aria-describedby="fk_id_tipo_help">
                            <option value="">Seleccionar tipo...</option>
                            @foreach($tiposDocumento as $tipo)
                                <option value="{{ $tipo->id_tipo }}">{{ $tipo->nombre_tipo }}</option>
                            @endforeach
                        </select>
                        <small id="fk_id_tipo_help" class="form-text text-muted">Seleccione el tipo de documento</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" aria-label="Cancelar creación de tarea">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnGuardarTarea" aria-label="Guardar tarea">Guardar Tarea</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Detalle Tarea -->
<div class="modal fade" id="modalDetalleTarea" tabindex="-1" role="dialog" aria-labelledby="modalDetalleTareaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: #0b2e59; color: white;">
                <h3 class="modal-title" id="modalDetalleTareaLabel">
                    <i class="fas fa-info-circle mr-2" aria-hidden="true"></i> Detalle de Tarea
                </h3>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar modal de detalle">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-8">
                        <dl class="row mb-0">
                            <dt class="col-sm-4">Nombre</dt>
                            <dd class="col-sm-8" id="detalle-nombre"></dd>
                            
                            <dt class="col-sm-4">Responsable</dt>
                            <dd class="col-sm-8" id="detalle-responsable"></dd>
                            
                            <dt class="col-sm-4">Estado</dt>
                            <dd class="col-sm-8">
                                <span id="detalle-estado" class="badge badge-pill"></span>
                            </dd>
                            
                            <dt class="col-sm-4">Fecha Creación</dt>
                            <dd class="col-sm-8" id="detalle-fecha"></dd>
                            
                            <dt class="col-sm-4">Fecha Vencimiento</dt>
                            <dd class="col-sm-8" id="detalle-vencimiento"></dd>
                            
                            <dt class="col-sm-4">Descripción</dt>
                            <dd class="col-sm-8" id="detalle-descripcion"></dd>
                        </dl>
                    </div>
                    <div class="col-md-4 border-left">
                        <h4><i class="fas fa-file-alt mr-2" aria-hidden="true"></i> Documentos</h4>
                        <div id="detalle-documentos" class="mb-3">
                            <div class="text-center text-muted py-3">Sin documentos adjuntos</div>
                        </div>
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
                <span id="iconoDocumento" style="font-size:2.5rem; margin-right:10px;" aria-hidden="true"></span>
                <h3 class="modal-title" id="modalVerDocumentoLabel"></h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar visor de documento">
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
                    @if(auth()->user()->puedeEliminar('TareasDocumentales'))
                    <button type="submit" class="btn btn-danger" id="btnEliminarDocumento" onclick="return confirm('¿Seguro que desea eliminar este documento?')" aria-label="Eliminar documento">
                        <i class="fas fa-trash" aria-hidden="true"></i> Eliminar
                    </button>
                    @endif
                </form>
                <a href="#" id="descargarDocumento" class="btn btn-primary" download target="_blank" aria-label="Descargar documento">
                    <i class="fas fa-download" aria-hidden="true"></i> Descargar
                </a>
                <button type="button" class="btn btn-secondary" data-dismiss="modal" aria-label="Cerrar ventana">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para delegar tarea -->
<div class="modal fade" id="modalDelegarTarea" tabindex="-1" role="dialog" aria-labelledby="modalDelegarTareaLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: #0b2e59; color: white;">
                <h3 class="modal-title" id="modalDelegarTareaLabel">
                    <i class="fas fa-user-friends mr-2" aria-hidden="true"></i> Delegar Tarea
                </h3>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar modal de delegación">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" id="formDelegarTarea">
                @csrf
                <input type="hidden" name="tarea_id" id="tareaIdDelegar">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="nuevoResponsable">Seleccionar nuevo responsable</label>
                        <select class="form-control" id="nuevoResponsable" name="nuevo_responsable" required aria-describedby="nuevoResponsable_help">
                            <option value="">Seleccionar responsable</option>
                            @foreach($responsables as $responsable)
                                <option value="{{ $responsable->id_usuario }}">{{ $responsable->nombres }} {{ $responsable->apellidos }}</option>
                            @endforeach
                        </select>
                        <small id="nuevoResponsable_help" class="form-text text-muted">Seleccione el nuevo responsable para la tarea</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" aria-label="Cancelar delegación">Cancelar</button>
                    <button type="submit" class="btn btn-primary" aria-label="Confirmar delegación de tarea">Delegar</button>
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
    
    /* Tarjetas de estadísticas */
    .card-stats {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }
    
    .card-stats .card-body {
        padding: 15px;
    }
    
    .icon-bg {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
    }
    
    .bg-secondary { background-color: #6c757d; }
    .bg-info { background-color: #17a2b8; }
    .bg-success { background-color: #28a745; }
    .bg-primary { background-color: #007bff; }
    
    .card-title {
        font-size: 0.9rem;
        font-weight: 600;
        color: #6c757d;
        margin-bottom: 5px;
    }
    
    .card-value {
        font-size: 1.8rem;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 0;
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
    .badge-pill {
        padding: 0.5em 1em;
        font-weight: 500;
    }
    
    .badge-warning { background-color: #ffc107; color: #343a40; }
    .badge-info { background-color: #17a2b8; }
    .badge-success { background-color: #28a745; }
    .badge-primary { background-color: #007bff; }
    
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
    
    /* Documentos en detalle */
    .documento-item {
        display: flex;
        align-items: center;
        padding: 10px;
        margin-bottom: 8px;
        border-radius: 8px;
        background-color: #f8f9fc;
        transition: all 0.3s ease;
    }
    
    .documento-item:hover {
        background-color: #eef2f7;
    }
    
    .documento-icon {
        font-size: 1.8rem;
        margin-right: 12px;
    }
    
    .documento-info {
        flex-grow: 1;
    }
    
    .documento-info h6 {
        margin-bottom: 2px;
        font-weight: 500;
    }
    
    .documento-info p {
        margin-bottom: 0;
        font-size: 0.85rem;
        color: #6c757d;
    }
    
    /* Historial */
    .historial-scroll {
        padding-right: 18px !important;
    }
    
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
    
    .historial-item {
        padding: 8px 0;
        border-bottom: 1px dashed #eaeef5;
    }
    
    .historial-item:last-child {
        border-bottom: none;
    }
    
    .historial-item .badge {
        font-size: 0.75em;
        margin-right: 8px;
    }

    /* Estilos para accesibilidad */
    .sr-only {
        position: absolute;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        white-space: nowrap;
        border: 0;
    }
</style>
@stop

@section('js')
<script>
    // Funciones de sanitización
    function sanitizeNombreTarea(input) {
        let value = input.value;
        
        // Eliminar caracteres no permitidos
        value = value.replace(/[^a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s.,\-]/g, '');
        
        // Limitar repeticiones consecutivas a 3
        value = value.replace(/(.)\1{3,}/g, '$1$1$1');
        
        // Limitar a 50 caracteres
        if (value.length > 50) {
            value = value.substring(0, 50);
        }
        
        input.value = value;
    }

    function sanitizeDescripcionTarea(input) {
        let value = input.value;
        
        // Eliminar caracteres no permitidos (solo letras, números, espacios, puntos, comas, guiones y dos puntos)
        value = value.replace(/[^a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s.,\-:]/g, '');
        
        // Limitar repeticiones consecutivas a 3
        value = value.replace(/(.)\1{3,}/g, '$1$1$1');
        
        // Limitar a 100 caracteres
        if (value.length > 100) {
            value = value.substring(0, 100);
        }
        
        input.value = value;
    }

    $(document).ready(function() {
        // Ocultar notificación de éxito después de 5 segundos
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
        
        // Actualizar nombre de archivo en input file
        $('.custom-file-input').on('change', function() {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass("selected").html(fileName);
        });
        
        // Modal Detalle
        $('#modalDetalleTarea').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var tareaId = button.data('id');
            
            $('#detalle-nombre').text(button.data('nombre'));
            $('#detalle-responsable').text(button.data('responsable'));
            $('#detalle-fecha').text(button.data('fecha'));
            $('#detalle-vencimiento').text(button.data('vencimiento') || 'No definida');
            $('#detalle-descripcion').text(button.data('descripcion') || 'Sin descripción');
            
            // Estado destacado
            var estado = button.data('estado');
            var badgeClass = '';
            switch(estado) {
                case 'Pendiente': badgeClass = 'badge-warning'; break;
                case 'En Proceso': badgeClass = 'badge-info'; break;
                case 'Completada': badgeClass = 'badge-success'; break;
                case 'Rechazada': badgeClass = 'badge-primary'; break;
                default: badgeClass = 'badge-secondary';
            }
            $('#detalle-estado').attr('class', 'badge badge-pill ' + badgeClass).text(estado);
            
            // Documentos
            var documentos = button.data('documentos');
            var documentosHtml = '';
            
            if(documentos && documentos.length > 0) {
                documentos.forEach(function(doc) {
                    var icono = '';
                    if(doc.tipo === 'imagen') {
                        icono = '<i class="fas fa-file-image text-info documento-icon" aria-hidden="true"></i>';
                    } else if(doc.tipo === 'pdf') {
                        icono = '<i class="fas fa-file-pdf text-danger documento-icon" aria-hidden="true"></i>';
                    } else {
                        icono = '<i class="fas fa-file text-secondary documento-icon" aria-hidden="true"></i>';
                    }
                    
                    documentosHtml += `
                        <div class="documento-item">
                            ${icono}
                            <div class="documento-info">
                                <h6>${doc.nombre}</h6>
                                <p>Tipo: ${doc.tipo_documento}</p>
                            </div>
                            <button class="btn btn-sm btn-link ver-documento" 
                                data-url="${doc.url}" 
                                data-tipo="${doc.tipo}"
                                data-nombre="${doc.nombre}"
                                data-id="${doc.id}"
                                aria-label="Ver documento ${doc.nombre}">
                                <i class="fas fa-eye" aria-hidden="true"></i>
                            </button>
                        </div>
                    `;
                });
            } else {
                documentosHtml = '<div class="text-center text-muted py-3">Sin documentos adjuntos</div>';
            }
            
            $('#detalle-documentos').html(documentosHtml);
        });

        // Modal Nueva/Editar Tarea
        $('#modalNuevaTarea').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var modal = $(this);
            var form = modal.find('form');
            var isEdit = button.data('id') ? true : false;

            // Reset form
            form.trigger('reset');
            form.find('input[name="_method"]').remove();
            modal.find('#documentoLabel').text('Seleccionar archivo');
            $('#modalTareaTitle').text('Nueva Tarea Documental');
            $('#btnGuardarTarea').text('Crear Tarea');
            $('#tareaId').val('');
            
            // Mostrar campo de documento solo para crear
            $('#tipoDocumentoContainer').show();

            if (isEdit) {
                // Editar
                $('#modalTareaTitle').text('Editar Tarea Documental');
                $('#btnGuardarTarea').text('Actualizar Tarea');
                form.attr('action', '/tareas/' + button.data('id'));
                form.append('<input type="hidden" name="_method" value="PUT">');
                $('#tareaId').val(button.data('id'));
                $('#nombreTarea').val(button.data('nombre'));
                $('#responsableTarea').val(button.data('responsable'));
                $('#fechaCreacionTarea').val(button.data('fecha'));
                $('#fechaVencimientoTarea').val(button.data('vencimiento'));
                $('#descripcionTarea').val(button.data('descripcion'));
                
                // Ocultar campo de documento para edición
                $('#tipoDocumentoContainer').hide();
            }
        });

        // Visualizar documento en modal
        $(document).on('click', '.ver-documento', function(e) {
            e.preventDefault();
            var url = $(this).data('url');
            var tipo = $(this).data('tipo');
            var nombre = $(this).data('nombre');
            var id = $(this).data('id');
            var icono = '';
            var html = '';

            if(tipo === 'imagen') {
                icono = '<i class="fas fa-file-image text-info" aria-hidden="true"></i>';
                html = '<img src="' + url + '" alt="Documento: ' + nombre + '" class="img-fluid">';
            } else if(tipo === 'pdf') {
                icono = '<i class="fas fa-file-pdf text-danger" aria-hidden="true"></i>';
                html = '<iframe src="' + url + '" width="100%" height="500px" style="border:none;" title="Visor de documento PDF: ' + nombre + '"></iframe>';
            } else {
                icono = '<i class="fas fa-file text-secondary" aria-hidden="true"></i>';
                html = '<p>No es posible visualizar este tipo de archivo.</p>';
            }

            $('#iconoDocumento').html(icono);
            $('#modalVerDocumentoLabel').text(nombre);
            $('#contenedorDocumento').html(html);
            $('#descargarDocumento').attr('href', url);

            // Actualiza la acción del formulario de eliminar
            var action = "{{ route('tareas.documento.eliminar', ':id') }}";
            $('#formEliminarDocumento').attr('action', action.replace(':id', id));
            
            $('#modalVerDocumento').modal('show');
        });
        
        // Validación de fechas
        $('#fechaVencimientoTarea').on('change', function() {
            var fechaInicio = new Date($('#fechaCreacionTarea').val());
            var fechaFin = new Date($(this).val());
            
            if(fechaFin < fechaInicio) {
                alert('La fecha de vencimiento no puede ser anterior a la fecha de creación');
                $(this).val('');
            }
        });
        
        // Filtrar tabla con AJAX
        $('#filtroTareas').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            $.ajax({
                url: form.attr('action'),
                type: 'GET',
                data: form.serialize(),
                success: function(data) {
                    $(".table tbody").html(data);
                },
                error: function() {
                    alert('Error al filtrar las tareas.');
                }
            });
        });

        // Cambiar estado de tarea
        $(document).on('click', '.cambiar-estado', function(e) {
            e.preventDefault();
            var estado = $(this).data('estado');
            var tareaId = $(this).closest('tr').data('id');
            var url = "{{ route('tareas.estado', ['id' => ':id']) }}";
            url = url.replace(':id', tareaId);

            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    estado: estado
                },
                success: function(response) {
                    location.reload();
                },
                error: function() {
                    alert('Error al cambiar el estado');
                }
            });
        });

        // Modal Delegar Tarea
        $('#modalDelegarTarea').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var tareaId = button.data('id');
            var form = $(this).find('form');
            form.attr('action', "{{ route('tareas.delegar', ['id' => ':id']) }}".replace(':id', tareaId));
            $('#tareaIdDelegar').val(tareaId);
        });
    });
</script>
@stop
