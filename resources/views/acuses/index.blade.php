@extends('adminlte::page')

@section('title', 'Gestión de Acuses de Recibo')

@section('content_header')
<div class="elegant-header">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h1 class="mb-0"><i class="fas fa-file-contract mr-2 text-primary" aria-hidden="true"></i> Gestión de Acuses de Recibo</h1>
            <p class="mb-0">Universidad Nacional Autónoma de Honduras - Posgrado de la Facultad de Ciencias Económicas Administrativas y Contables</p>
        </div>
        <div class="d-flex align-items-center">
            <!-- Notificaciones movidas aquí -->
            <div class="notifications-dropdown ml-3">
                <button class="btn btn-notification" type="button" id="notifDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-label="Notificaciones">
                    <i class="fas fa-bell" aria-hidden="true"></i>
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
                                    <small class="text-muted">
                                        De: {{ optional(optional($notificacion->acuse)->remitente)->nombres ?? 'N/A' }} {{ optional(optional($notificacion->acuse)->remitente)->apellidos ?? '' }}
                                    </small>
                                    <div class="small text-muted">{{ $notificacion->fecha ? $notificacion->fecha->format('d/m/Y H:i') : now()->format('d/m/Y H:i') }}</div>
                                </div>
                                @if($notificacion->estado == 'no_leida')
                                    <span class="badge badge-danger">Nuevo</span>
                                @endif
                            </a>
                        @endforeach
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-bell-slash fa-2x mb-2 text-muted" aria-hidden="true"></i>
                            <p class="text-muted">No hay notificaciones</p>
                        </div>
                    @endif
                </div>
            </div>
            <div class="header-icon ml-3">
                <i class="fas fa-envelope-open-text" aria-hidden="true"></i>
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
        <div class="alert alert-elegant-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle mr-2" aria-hidden="true"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endif
        @if(session('error'))
        <div class="alert alert-elegant-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle mr-2" aria-hidden="true"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endif
    </div>
    @endif

    <!-- Filtros -->
    <div class="card card-elegant mb-4">
        <div class="card-header">
            <h2 class="card-title mb-0"><i class="fas fa-filter mr-2 text-muted" aria-hidden="true"></i>Filtros</h2>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('acuses.index') }}">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="estado" class="form-label">Estado</label>
                        <select class="form-control form-control-elegant" name="estado" id="estado" aria-describedby="help-estado">
                            <option value="">Todos</option>
                            <option value="enviado" {{ request('estado') == 'enviado' ? 'selected' : '' }}>Enviado</option>
                            <option value="recibido" {{ request('estado') == 'recibido' ? 'selected' : '' }}>Recibido</option>
                            <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                        </select>
                        <small id="help-estado" class="form-text text-muted">Seleccione el estado del acuse</small>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="remitente" class="form-label">Remitente</label>
                        <input type="text" class="form-control form-control-elegant" name="remitente" id="remitente"
                               placeholder="Nombre del remitente" value="{{ request('remitente') }}"
                               maxlength="50"
                               oninput="validarYFiltrarRemitente(this)"
                               onkeypress="return permitirCaracteresRemitente(event)"
                               aria-describedby="help-remitente">
                        <small id="help-remitente" class="form-text text-muted">Solo letras, espacios, puntos, comas y guiones. Máximo 50 caracteres.</small>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="destinatario" class="form-label">Destinatario</label>
                        <input type="text" class="form-control form-control-elegant" name="destinatario" id="destinatario"
                               placeholder="Nombre del destinatario" value="{{ request('destinatario') }}"
                               maxlength="50"
                               oninput="validarYFiltrarDestinatario(this)"
                               onkeypress="return permitirCaracteresDestinatario(event)"
                               aria-describedby="help-destinatario">
                        <small id="help-destinatario" class="form-text text-muted">Solo letras, espacios, puntos, comas y guiones. Máximo 50 caracteres.</small>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="elemento" class="form-label">Elemento</label>
                        <input type="text" class="form-control form-control-elegant" name="elemento" id="elemento"
                               placeholder="Nombre del elemento" value="{{ request('elemento') }}"
                               maxlength="50"
                               oninput="validarYFiltrarElemento(this)"
                               onkeypress="return permitirCaracteresElemento(event)"
                               aria-describedby="help-elemento">
                        <small id="help-elemento" class="form-text text-muted">Solo letras, espacios, puntos, comas y guiones. Máximo 50 caracteres.</small>
                    </div>
                </div>
                <div class="d-flex justify-content-end mt-2">
                    <a href="{{ route('acuses.index') }}" class="btn btn-outline-secondary btn-elegant mr-2" aria-label="Restablecer filtros">
                        <i class="fas fa-redo mr-1" aria-hidden="true"></i> Restablecer
                    </a>
                    <button type="submit" class="btn btn-primary btn-elegant mr-2" aria-label="Aplicar filtros">
                        <i class="fas fa-filter mr-1" aria-hidden="true"></i> Aplicar Filtros
                    </button>
                    @if(Auth::user()->puedeAgregar('GestionAcuses'))
                    <button type="button" class="btn btn-success btn-elegant" data-toggle="modal" data-target="#modalEnviarAcuse" aria-label="Crear nuevo acuse">
                        <i class="fas fa-plus mr-1" aria-hidden="true"></i> Nuevo Acuse
                    </button>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de acuses -->
    <div class="card card-elegant">
        <div class="card-header d-flex align-items-center">
            <h2 class="card-title mb-0"><i class="fas fa-list mr-2 text-muted" aria-hidden="true"></i> Acuses de Recibo</h2>
            <div class="ml-auto">
                <span class="badge badge-light">{{ $acuses->total() }} registros</span>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-borderless" aria-describedby="tabla-descripcion">
                    <caption id="tabla-descripcion" class="sr-only">Lista de acuses de recibo con opciones de ordenamiento y acciones</caption>
                    <thead class="thead-elegant">
                        <tr>
                            <th scope="col">
                                <a href="{{ route('acuses.index', array_merge(request()->query(), ['sort' => 'id_acuse', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc'])) }}" aria-label="Ordenar por ID">
                                    ID {!! request('sort') == 'id_acuse' ? (request('direction') == 'asc' ? '▲' : '▼') : '' !!}
                                </a>
                            </th>
                            <th scope="col">
                                <a href="{{ route('acuses.index', array_merge(request()->query(), ['sort' => 'titulo', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc'])) }}" aria-label="Ordenar por título">
                                    Título {!! request('sort') == 'titulo' ? (request('direction') == 'asc' ? '▲' : '▼') : '' !!}
                                </a>
                            </th>
                            <th scope="col">Remitente</th>
                            <th scope="col">Destinatario</th>
                            <th scope="col">
                                <a href="{{ route('acuses.index', array_merge(request()->query(), ['sort' => 'estado', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc'])) }}" aria-label="Ordenar por estado">
                                    Estado {!! request('sort') == 'estado' ? (request('direction') == 'asc' ? '▲' : '▼') : '' !!}
                                </a>
                            </th>
                            <th scope="col">
                                <a href="{{ route('acuses.index', array_merge(request()->query(), ['sort' => 'fecha_envio', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc'])) }}" aria-label="Ordenar por fecha de envío">
                                    Fecha Envío {!! request('sort') == 'fecha_envio' ? (request('direction') == 'asc' ? '▲' : '▼') : '' !!}
                                </a>
                            </th>
                            <th scope="col" class="text-center">Acciones</th>
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
                                    <div class="btn-group btn-group-actions" role="group" aria-label="Acciones para acuse {{ $acuse->id_acuse }}">
                                        <a href="{{ route('acuses.show', $acuse->id_acuse) }}" class="btn btn-sm btn-action" data-toggle="tooltip" title="Ver detalles" aria-label="Ver detalles del acuse {{ $acuse->id_acuse }}">
                                            <i class="fas fa-eye" aria-hidden="true"></i>
                                        </a>
                                        
                                        @if($acuse->estado == 'pendiente' && $acuse->fk_id_usuario_destinatario == auth()->user()->id_usuario)
                                            <form action="{{ route('acuses.aceptar', $acuse->id_acuse) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn btn-sm btn-action" data-toggle="tooltip" title="Aceptar acuse" aria-label="Aceptar acuse {{ $acuse->id_acuse }}">
                                                    <i class="fas fa-check text-success" aria-hidden="true"></i>
                                                </button>
                                            </form>
                                        @endif
                                        
                                        @if($acuse->estado == 'recibido' && $acuse->fk_id_usuario_destinatario == auth()->user()->id_usuario)
                                            <a href="{{ route('acuses.reenviar.form', $acuse->id_acuse) }}" class="btn btn-sm btn-action" data-toggle="tooltip" title="Reenviar" aria-label="Reenviar acuse {{ $acuse->id_acuse }}">
                                                <i class="fas fa-share text-warning" aria-hidden="true"></i>
                                            </a>
                                        @endif
                                        
                                        <a href="{{ route('acuses.rastrear', $acuse->id_acuse) }}" class="btn btn-sm btn-action" data-toggle="tooltip" title="Rastrear" aria-label="Rastrear acuse {{ $acuse->id_acuse }}">
                                            <i class="fas fa-search-location text-info" aria-hidden="true"></i>
                                        </a>
                                        
                                        @if(auth()->user()->puedeEliminar('GestionAcuses'))
                                            <form action="{{ route('acuses.destroy', $acuse->id_acuse) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-action" data-toggle="tooltip" title="Eliminar" aria-label="Eliminar acuse {{ $acuse->id_acuse }}" onclick="return confirm('¿Está seguro de eliminar este acuse?')">
                                                    <i class="fas fa-trash-alt text-danger" aria-hidden="true"></i>
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
                                        <i class="fas fa-inbox fa-3x text-muted mb-3" aria-hidden="true"></i>
                                        <h3>No se encontraron acuses de recibo</h3>
                                        <p class="text-muted">Parece que aún no hay acuses registrados en el sistema</p>
                                        @if(Auth::user()->puedeAgregar('GestionAcuses'))
                                        <button type="button" class="btn btn-primary mt-2" data-toggle="modal" data-target="#modalEnviarAcuse" aria-label="Crear primer acuse">
                                            <i class="fas fa-plus mr-1" aria-hidden="true"></i> Crear primer acuse
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
                <nav aria-label="Paginación de acuses">
                    <div class="pagination-custom">
                        {{ $acuses->appends(request()->query())->links() }}
                    </div>
                </nav>
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
                <h3 class="modal-title" id="modalEnviarAcuseLabel">
                    <i class="fas fa-paper-plane mr-2" aria-hidden="true"></i> Nuevo Acuse de Recibo
                </h3>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('acuses.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="destinatario">Destinatario</label>
                        <select class="form-control" name="destinatario" id="destinatario" required aria-describedby="help-destinatario-modal">
                            <option value="">Seleccionar destinatario</option>
                            @foreach($usuarios as $usuario)
                                @if($usuario->id_usuario != auth()->user()->id_usuario)
                                    <option value="{{ $usuario->id_usuario }}">
                                        {{ $usuario->nombres }} {{ $usuario->apellidos }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                        <small id="help-destinatario-modal" class="form-text text-muted">Seleccione el destinatario del acuse</small>
                    </div>
                    <div class="form-group">
                        <label for="titulo">Título</label>
                        <input type="text" class="form-control" name="titulo" id="titulo" required 
                               placeholder="Título del acuse" maxlength="50"
                               oninput="sanitizeTitulo(this)" aria-describedby="help-titulo">
                        <small id="help-titulo" class="form-text text-muted">Máximo 50 caracteres. Solo letras, números, espacios, puntos, comas y guiones.</small>
                    </div>
                    <div class="form-group">
                        <label for="descripcion">Descripción</label>
                        <textarea class="form-control" name="descripcion" id="descripcion" rows="3" 
                                  placeholder="Descripción del acuse" maxlength="100"
                                  oninput="sanitizeDescripcion(this)" aria-describedby="help-descripcion"></textarea>
                        <small id="help-descripcion" class="form-text text-muted">Máximo 100 caracteres. Solo letras, números, espacios, puntos (pueden ser dos seguidos), comas y guiones.</small>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
                        <h4 class="mb-0">
                            <i class="fas fa-file-alt mr-2" aria-hidden="true"></i> Elementos
                        </h4>
                        <div>
                            <button type="button" class="btn btn-sm btn-success" id="addElement" aria-label="Agregar nuevo elemento">
                                <i class="fas fa-plus mr-1" aria-hidden="true"></i> Agregar
                            </button>
                            <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#modalNuevoTipo" aria-label="Crear nuevo tipo de elemento">
                                <i class="fas fa-plus-circle mr-1" aria-hidden="true"></i> Nuevo Tipo
                            </button>
                        </div>
                    </div>
                    
                    <div id="elementosContainer">
                        <div class="elemento-item mb-3 border p-3 rounded">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="tipo-elemento-0">Tipo</label>
                                    <select class="form-control tipo-select" name="elementos[0][fk_id_tipo]" id="tipo-elemento-0" required aria-describedby="help-tipo-0">
                                        <option value="">Seleccionar tipo</option>
                                        @foreach($tiposElemento as $tipo)
                                            <option value="{{ $tipo->id_tipo }}">
                                                {{ $tipo->nombre }} ({{ $tipo->categoria }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <small id="help-tipo-0" class="form-text text-muted">Seleccione el tipo de elemento</small>
                                </div>
                                <div class="col-md-4">
                                    <label for="nombre-elemento-0">Nombre</label>
                                    <input type="text" class="form-control" name="elementos[0][nombre]" id="nombre-elemento-0" required 
                                           placeholder="Nombre del elemento" maxlength="50"
                                           oninput="sanitizeNombreElemento(this)" aria-describedby="help-nombre-0">
                                    <small id="help-nombre-0" class="form-text text-muted">Máximo 50 caracteres. Solo letras, números y espacios.</small>
                                </div>
                                <div class="col-md-2">
                                    <label for="cantidad-elemento-0">Cantidad</label>
                                    <input type="number" class="form-control" name="elementos[0][cantidad]" id="cantidad-elemento-0"
                                           value="1" min="1" max="999" oninput="this.value = this.value.replace(/[^0-9]/g, '')" aria-describedby="help-cantidad-0">
                                    <small id="help-cantidad-0" class="form-text text-muted">Solo números</small>
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="button" class="btn btn-danger btn-block remove-element" aria-label="Eliminar elemento">
                                        <i class="fas fa-trash" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-12">
                                    <label for="descripcion-elemento-0">Descripción</label>
                                    <textarea class="form-control" name="elementos[0][descripcion]" id="descripcion-elemento-0" rows="2" 
                                              placeholder="Descripción del elemento" maxlength="100"
                                              oninput="sanitizeDescripcionElemento(this)" aria-describedby="help-desc-elemento-0"></textarea>
                                    <small id="help-desc-elemento-0" class="form-text text-muted">Máximo 100 caracteres. Solo letras, números, espacios, puntos (pueden ser dos seguidos), comas y guiones.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Sección para adjuntos -->
                    <div class="mt-4">
                        <h4><i class="fas fa-paperclip mr-2" aria-hidden="true"></i> Documentos Adjuntos (PDF, Word, Excel)</h4>
                        <div class="form-group">
                            <label for="adjuntos_documentos">Seleccionar archivos</label>
                            <input type="file" class="form-control-file" name="adjuntos_documentos[]" id="adjuntos_documentos" multiple accept=".pdf,.doc,.docx,.xls,.xlsx" aria-describedby="help-adjuntos-doc">
                            <small id="help-adjuntos-doc" class="form-text text-muted">Solo para elementos de tipo documento</small>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <h4><i class="fas fa-image mr-2" aria-hidden="true"></i> Imágenes Adjuntas (JPG, PNG, GIF)</h4>
                        <div class="form-group">
                            <label for="adjuntos_imagenes">Seleccionar imágenes</label>
                            <input type="file" class="form-control-file" name="adjuntos_imagenes[]" id="adjuntos_imagenes" multiple accept="image/*" aria-describedby="help-adjuntos-img">
                            <small id="help-adjuntos-img" class="form-text text-muted">Solo para elementos de tipo objeto o kit</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" aria-label="Cancelar operación">Cancelar</button>
                    <button type="submit" class="btn btn-primary" aria-label="Enviar acuse">Enviar Acuse</button>
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
                <h3 class="modal-title" id="modalNuevoTipoLabel">
                    <i class="fas fa-plus-circle mr-2" aria-hidden="true"></i> Nuevo Tipo de Elemento
                </h3>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('tipos.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="nombre_tipo">Nombre</label>
                        <input type="text" class="form-control" name="nombre" id="nombre_tipo" required 
                               placeholder="Nombre del tipo" maxlength="50"
                               oninput="sanitizeNombreTipo(this)" aria-describedby="help-nombre-tipo">
                        <small id="help-nombre-tipo" class="form-text text-muted">Máximo 50 caracteres. Solo letras y espacios.</small>
                    </div>
                    <div class="form-group">
                        <label for="categoria_tipo">Categoría</label>
                        <select class="form-control" name="categoria" id="categoria_tipo" required aria-describedby="help-categoria-tipo">
                            <option value="">Seleccionar categoría</option>
                            <option value="documento">Documento</option>
                            <option value="objeto">Objeto</option>
                            <option value="kit">Kit</option>
                        </select>
                        <small id="help-categoria-tipo" class="form-text text-muted">Seleccione la categoría del tipo</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" aria-label="Cancelar operación">Cancelar</button>
                    <button type="submit" class="btn btn-primary" aria-label="Guardar tipo">Guardar Tipo</button>
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
    
    .empty-state p {
        color: #6c757d;
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

    /* Clase para contenido visualmente oculto pero accesible */
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
                            <label for="tipo-elemento-${elementoCount}">Tipo</label>
                            <select class="form-control tipo-select" name="elementos[${elementoCount}][fk_id_tipo]" id="tipo-elemento-${elementoCount}" required aria-describedby="help-tipo-${elementoCount}">
                                <option value="">Seleccionar tipo</option>
                                @foreach($tiposElemento as $tipo)
                                    <option value="{{ $tipo->id_tipo }}">
                                        {{ $tipo->nombre }} ({{ $tipo->categoria }})
                                    </option>
                                @endforeach
                            </select>
                            <small id="help-tipo-${elementoCount}" class="form-text text-muted">Seleccione el tipo de elemento</small>
                        </div>
                        <div class="col-md-4">
                            <label for="nombre-elemento-${elementoCount}">Nombre</label>
                            <input type="text" class="form-control" name="elementos[${elementoCount}][nombre]" id="nombre-elemento-${elementoCount}" required 
                                   placeholder="Nombre del elemento" maxlength="50"
                                   oninput="sanitizeNombreElemento(this)" aria-describedby="help-nombre-${elementoCount}">
                            <small id="help-nombre-${elementoCount}" class="form-text text-muted">Máximo 50 caracteres. Solo letras, números y espacios.</small>
                        </div>
                        <div class="col-md-2">
                            <label for="cantidad-elemento-${elementoCount}">Cantidad</label>
                            <input type="number" class="form-control" name="elementos[${elementoCount}][cantidad]" id="cantidad-elemento-${elementoCount}"
                                   value="1" min="1" max="999" oninput="this.value = this.value.replace(/[^0-9]/g, '')" aria-describedby="help-cantidad-${elementoCount}">
                            <small id="help-cantidad-${elementoCount}" class="form-text text-muted">Solo números</small>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" class="btn btn-danger btn-block remove-element" aria-label="Eliminar elemento">
                                <i class="fas fa-trash" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-12">
                            <label for="descripcion-elemento-${elementoCount}">Descripción</label>
                            <textarea class="form-control" name="elementos[${elementoCount}][descripcion]" id="descripcion-elemento-${elementoCount}" rows="2" 
                                      placeholder="Descripción del elemento" maxlength="100"
                                      oninput="sanitizeDescripcionElemento(this)" aria-describedby="help-desc-elemento-${elementoCount}"></textarea>
                            <small id="help-desc-elemento-${elementoCount}" class="form-text text-muted">Máximo 100 caracteres. Solo letras, números, espacios, puntos (pueden ser dos seguidos), comas y guiones.</small>
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
        
        // Validación para campos numéricos
        $('body').on('input', 'input[type="number"]', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });

        // Aplicar validación inicial si hay valores en los campos de filtro
        document.addEventListener('DOMContentLoaded', function() {
            const remitenteInput = document.getElementById('remitente');
            const destinatarioInput = document.getElementById('destinatario');
            const elementoInput = document.getElementById('elemento');
            
            if (remitenteInput && remitenteInput.value) {
                validarYFiltrarRemitente(remitenteInput);
            }
            
            if (destinatarioInput && destinatarioInput.value) {
                validarYFiltrarDestinatario(destinatarioInput);
            }
            
            if (elementoInput && elementoInput.value) {
                validarYFiltrarElemento(elementoInput);
            }
        });
    });

    // =============================================
    // VALIDACIONES PARA FILTROS
    // =============================================

    // Función para permitir solo caracteres válidos en campo de remitente
    function permitirCaracteresRemitente(event) {
        const charCode = event.which ? event.which : event.keyCode;
        const charStr = String.fromCharCode(charCode);
        
        // Permitir letras (con acentos), espacios, puntos, comas y guiones
        const regex = /^[A-Za-záéíóúÁÉÍÓÚñÑ\s.,\-]$/;
        
        // Permitir teclas de control (backspace, delete, tab, flechas, etc.)
        if (charCode === 8 || charCode === 9 || charCode === 37 || charCode === 39 || charCode === 46) {
            return true;
        }
        
        if (!regex.test(charStr)) {
            event.preventDefault();
            return false;
        }
        
        return true;
    }

    // Función para permitir solo caracteres válidos en campo de destinatario
    function permitirCaracteresDestinatario(event) {
        const charCode = event.which ? event.which : event.keyCode;
        const charStr = String.fromCharCode(charCode);
        
        // Permitir letras (con acentos), espacios, puntos, comas y guiones
        const regex = /^[A-Za-záéíóúÁÉÍÓÚñÑ\s.,\-]$/;
        
        // Permitir teclas de control (backspace, delete, tab, flechas, etc.)
        if (charCode === 8 || charCode === 9 || charCode === 37 || charCode === 39 || charCode === 46) {
            return true;
        }
        
        if (!regex.test(charStr)) {
            event.preventDefault();
            return false;
        }
        
        return true;
    }

    // Función para permitir solo caracteres válidos en campo de elemento
    function permitirCaracteresElemento(event) {
        const charCode = event.which ? event.which : event.keyCode;
        const charStr = String.fromCharCode(charCode);
        
        // Permitir letras (con acentos), espacios, puntos, comas y guiones
        const regex = /^[A-Za-záéíóúÁÉÍÓÚñÑ\s.,\-]$/;
        
        // Permitir teclas de control (backspace, delete, tab, flechas, etc.)
        if (charCode === 8 || charCode === 9 || charCode === 37 || charCode === 39 || charCode === 46) {
            return true;
        }
        
        if (!regex.test(charStr)) {
            event.preventDefault();
            return false;
        }
        
        return true;
    }

    // Función para validar y filtrar el campo de remitente en tiempo real
    function validarYFiltrarRemitente(input) {
        let valor = input.value;
        
        // Filtrar caracteres no permitidos (solo letras, espacios, puntos, comas y guiones)
        valor = valor.replace(/[^A-Za-záéíóúÁÉÍÓÚñÑ\s.,\-]/g, '');
        
        // Validar que no haya más de 3 letras iguales consecutivas
        const regexRepetidas = /([A-Za-z])\1{3,}/g;
        if (regexRepetidas.test(valor)) {
            // Eliminar letras repetidas más allá de 3
            valor = valor.replace(regexRepetidas, (match) => {
                return match.substring(0, 3);
            });
        }
        
        // Limitar a 50 caracteres
        if (valor.length > 50) {
            valor = valor.substring(0, 50);
        }
        
        // Actualizar el valor del input
        if (input.value !== valor) {
            input.value = valor;
        }
    }

    // Función para validar y filtrar el campo de destinatario en tiempo real
    function validarYFiltrarDestinatario(input) {
        let valor = input.value;
        
        // Filtrar caracteres no permitidos (solo letras, espacios, puntos, comas y guiones)
        valor = valor.replace(/[^A-Za-záéíóúÁÉÍÓÚñÑ\s.,\-]/g, '');
        
        // Validar que no haya más de 3 letras iguales consecutivas
        const regexRepetidas = /([A-Za-z])\1{3,}/g;
        if (regexRepetidas.test(valor)) {
            // Eliminar letras repetidas más allá de 3
            valor = valor.replace(regexRepetidas, (match) => {
                return match.substring(0, 3);
            });
        }
        
        // Limitar a 50 caracteres
        if (valor.length > 50) {
            valor = valor.substring(0, 50);
        }
        
        // Actualizar el valor del input
        if (input.value !== valor) {
            input.value = valor;
        }
    }

    // Función para validar y filtrar el campo de elemento en tiempo real
    function validarYFiltrarElemento(input) {
        let valor = input.value;
        
        // Filtrar caracteres no permitidos (solo letras, espacios, puntos, comas y guiones)
        valor = valor.replace(/[^A-Za-záéíóúÁÉÍÓÚñÑ\s.,\-]/g, '');
        
        // Validar que no haya más de 3 letras iguales consecutivas
        const regexRepetidas = /([A-Za-z])\1{3,}/g;
        if (regexRepetidas.test(valor)) {
            // Eliminar letras repetidas más allá de 3
            valor = valor.replace(regexRepetidas, (match) => {
                return match.substring(0, 3);
            });
        }
        
        // Limitar a 50 caracteres
        if (valor.length > 50) {
            valor = valor.substring(0, 50);
        }
        
        // Actualizar el valor del input
        if (input.value !== valor) {
            input.value = valor;
        }
    }

    // Funciones de sanitización existentes
    function sanitizeTitulo(input) {
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

    function sanitizeDescripcion(input) {
        let value = input.value;
        
        // Eliminar caracteres no permitidos (solo letras, números, espacios, puntos, comas y guiones)
        value = value.replace(/[^a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s.,\-]/g, '');
        
        // Limitar repeticiones consecutivas a 3
        value = value.replace(/(.)\1{3,}/g, '$1$1$1');
        
        // Limitar a 100 caracteres
        if (value.length > 100) {
            value = value.substring(0, 100);
        }
        
        input.value = value;
    }

    function sanitizeNombreTipo(input) {
        let value = input.value;
        
        // Eliminar caracteres no permitidos (solo letras y espacios)
        value = value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '');
        
        // Limitar repeticiones consecutivas a 3
        value = value.replace(/(.)\1{3,}/g, '$1$1$1');
        
        // Limitar a 50 caracteres
        if (value.length > 50) {
            value = value.substring(0, 50);
        }
        
        input.value = value;
    }

    function sanitizeNombreElemento(input) {
        let value = input.value;
        
        // Eliminar caracteres no permitidos (solo letras, números y espacios)
        value = value.replace(/[^a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s]/g, '');
        
        // Limitar repeticiones consecutivas a 3
        value = value.replace(/(.)\1{3,}/g, '$1$1$1');
        
        // Limitar a 50 caracteres
        if (value.length > 50) {
            value = value.substring(0, 50);
        }
        
        input.value = value;
    }

    function sanitizeDescripcionElemento(input) {
        let value = input.value;
        
        // Eliminar caracteres no permitidos (solo letras, números, espacios, puntos, comas y guiones)
        value = value.replace(/[^a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s.,\-]/g, '');
        
        // Limitar repeticiones consecutivas a 3
        value = value.replace(/(.)\1{3,}/g, '$1$1$1');
        
        // Limitar a 100 caracteres
        if (value.length > 100) {
            value = value.substring(0, 100);
        }
        
        input.value = value;
    }
</script>
@stop
