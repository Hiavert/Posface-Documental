@extends('adminlte::page')

@section('title', 'Gestión de Documentos')

@section('content_header')
<div class="elegant-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-0"><i class="fas fa-file-contract mr-2 text-primary" aria-hidden="true"></i> Gestión de Documentos</h1>
            <p class="subtitle">Universidad Nacional Autónoma de Honduras - Posgrado de la Facultad de Ciencias Económicas Administrativas y Contables</p>
        </div>
        <div>
            @if(auth()->user()->puedeAgregar('Documentos'))
            <a href="{{ route('documentos.create') }}" class="btn btn-primary">
                <i class="fas fa-plus mr-1" aria-hidden="true"></i> Nuevo Documento
            </a>
            @endif
        </div>
    </div>
</div>
@stop

@section('content')
<div class="container-fluid">
    <!-- Filtros -->
    <div class="card card-elegant mb-4">
        <div class="card-header">
            <h2 class="card-title mb-0"><i class="fas fa-filter mr-2 text-muted" aria-hidden="true"></i>Filtros</h2>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('documentos.gestor') }}" id="filtrosForm" role="search" aria-label="Formulario de filtros para documentos">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="tipo_documento" class="form-label">Tipo de Documento</label>
                        <select class="form-control form-control-elegant" name="tipo" id="tipo_documento" aria-describedby="tipo_documento_help">
                            <option value="">Todos</option>
                            <option value="oficio" {{ request('tipo') == 'oficio' ? 'selected' : '' }}>Oficio</option>
                            <option value="circular" {{ request('tipo') == 'circular' ? 'selected' : '' }}>Circular</option>
                            <option value="memorandum" {{ request('tipo') == 'memorandum' ? 'selected' : '' }}>Memorándum</option>
                            <option value="resolucion" {{ request('tipo') == 'resolucion' ? 'selected' : '' }}>Resolución</option>
                            <option value="acuerdo" {{ request('tipo') == 'acuerdo' ? 'selected' : '' }}>Acuerdo</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="remitente" class="form-label">Remitente</label>
                        <input type="text" class="form-control form-control-elegant" name="remitente" 
                               id="remitente" 
                               placeholder="Nombre del remitente" 
                               value="{{ request('remitente') }}"
                               oninput="sanitizeNombre(this)"
                               maxlength="100"
                               aria-describedby="remitente_help">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="destinatario" class="form-label">Destinatario</label>
                        <input type="text" class="form-control form-control-elegant" name="destinatario" 
                               id="destinatario"
                               placeholder="Nombre del destinatario" 
                               value="{{ request('destinatario') }}"
                               oninput="sanitizeNombre(this)"
                               maxlength="100"
                               aria-describedby="destinatario_help">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="fecha" class="form-label">Fecha</label>
                        <input type="date" class="form-control form-control-elegant" name="fecha" id="fecha" value="{{ request('fecha') }}" aria-describedby="fecha_help">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-8 mb-3">
                        <label for="busqueda" class="form-label">Palabras Clave</label>
                        <input type="text" class="form-control form-control-elegant" name="busqueda" 
                               id="busqueda"
                               placeholder="Buscar por asunto o contenido" 
                               value="{{ request('busqueda') }}"
                               oninput="sanitizeBusqueda(this)"
                               aria-describedby="busqueda_help">
                        <small id="busqueda_help" class="form-text text-muted">Ingrese palabras clave para buscar en asunto o contenido</small>
                    </div>
                    <div class="col-md-4 mb-3 d-flex align-items-end">
                        <div class="d-flex w-100">
                            <button type="button" class="btn btn-outline-secondary btn-elegant w-50 mr-2" id="btnLimpiar" aria-label="Limpiar todos los filtros">
                                <i class="fas fa-redo mr-1" aria-hidden="true"></i> Limpiar
                            </button>
                            <button type="submit" class="btn btn-primary btn-elegant w-50" aria-label="Buscar documentos con los filtros aplicados">
                                <i class="fas fa-search mr-1" aria-hidden="true"></i> Buscar
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de documentos -->
    <div class="card card-elegant">
        <div class="card-header d-flex align-items-center">
            <h2 class="card-title mb-0"><i class="fas fa-list mr-2 text-muted" aria-hidden="true"></i> Documentos Registrados</h2>
            <div class="ml-auto">
                <span class="badge badge-light">{{ $documentos->total() }} registros</span>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-borderless" aria-describedby="tabla-documentos-desc">
                    <caption id="tabla-documentos-desc" class="sr-only">Lista de documentos registrados en el sistema con sus detalles y acciones disponibles</caption>
                    <thead class="thead-elegant">
                        <tr>
                            <th scope="col">
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'id', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}" aria-label="Ordenar por ID {{ request('sort') == 'id' ? (request('direction') == 'asc' ? 'ascendente' : 'descendente') : '' }}">
                                    ID {!! request('sort') == 'id' ? (request('direction') == 'asc' ? '▲' : '▼') : '' !!}
                                </a>
                            </th>
                            <th scope="col">
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'tipo', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}" aria-label="Ordenar por tipo {{ request('sort') == 'tipo' ? (request('direction') == 'asc' ? 'ascendente' : 'descendente') : '' }}">
                                    Tipo {!! request('sort') == 'tipo' ? (request('direction') == 'asc' ? '▲' : '▼') : '' !!}
                                </a>
                            </th>
                            <th scope="col">
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'remitente', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}" aria-label="Ordenar por remitente {{ request('sort') == 'remitente' ? (request('direction') == 'asc' ? 'ascendente' : 'descendente') : '' }}">
                                    Remitente {!! request('sort') == 'remitente' ? (request('direction') == 'asc' ? '▲' : '▼') : '' !!}
                                </a>
                            </th>
                            <th scope="col">
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'destinatario', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}" aria-label="Ordenar por destinatario {{ request('sort') == 'destinatario' ? (request('direction') == 'asc' ? 'ascendente' : 'descendente') : '' }}">
                                    Destinatario {!! request('sort') == 'destinatario' ? (request('direction') == 'asc' ? '▲' : '▼') : '' !!}
                                </a>
                            </th>
                            <th scope="col">Asunto</th>
                            <th scope="col">
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'fecha_documento', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}" aria-label="Ordenar por fecha {{ request('sort') == 'fecha_documento' ? (request('direction') == 'asc' ? 'ascendente' : 'descendente') : '' }}">
                                    Fecha {!! request('sort') == 'fecha_documento' ? (request('direction') == 'asc' ? '▲' : '▼') : '' !!}
                                </a>
                            </th>
                            <th scope="col" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($documentos as $documento)
                        <tr class="table-row">
                            <td>{{ $documento->numero ?? 'DOC-' . $documento->id }}</td>
                            <td>{{ ucfirst($documento->tipo) }}</td>
                            <td>{{ $documento->remitente }}</td>
                            <td>{{ $documento->destinatario }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($documento->asunto, 40) }}</td>
                            <td>{{ $documento->fecha_documento->format('d/m/Y') }}</td>
                            <td class="text-center">
                                <div class="btn-group" role="group" aria-label="Acciones para documento {{ $documento->numero }}">
                                    <a href="{{ route('documentos.historial', $documento) }}" class="btn btn-sm btn-action" 
                                       title="Historial de envíos"
                                       aria-label="Ver historial de envíos del documento {{ $documento->numero }}">
                                        <i class="fas fa-history text-info" aria-hidden="true"></i>
                                    </a>
                                    <button class="btn btn-sm btn-action" 
                                            title="Reenviar" 
                                            data-toggle="modal" 
                                            data-target="#modalReenviar{{ $documento->id }}"
                                            aria-label="Reenviar documento {{ $documento->numero }}">
                                        <i class="fas fa-paper-plane text-primary" aria-hidden="true"></i>
                                    </button>
                                    <button class="btn btn-sm btn-action preview-btn" 
                                            title="Vista Previa"
                                            data-file-url="{{ asset('storage/' . $documento->archivo_path) }}"
                                            data-file-name="{{ basename($documento->archivo_path) }}"
                                            aria-label="Ver vista previa del documento {{ $documento->numero }}">
                                        <i class="fas fa-eye text-info" aria-hidden="true"></i>
                                    </button>
                                    <a href="{{ route('documentos.descargar', $documento) }}" class="btn btn-sm btn-action" 
                                       title="Descargar"
                                       aria-label="Descargar documento {{ $documento->numero }}">
                                        <i class="fas fa-download text-success" aria-hidden="true"></i>
                                    </a>
                                    @if(auth()->user()->puedeEditar('Documentos'))
                                    <a href="{{ route('documentos.edit', $documento) }}" class="btn btn-sm btn-action" 
                                       title="Editar"
                                       aria-label="Editar documento {{ $documento->numero }}">
                                        <i class="fas fa-edit text-warning" aria-hidden="true"></i>
                                    </a>
                                    @endif
                                    @if(auth()->user()->puedeEliminar('Documentos'))
                                    <form action="{{ route('documentos.destroy', $documento) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-action" 
                                                title="Eliminar" 
                                                onclick="return confirm('¿Estás seguro de eliminar este documento?')"
                                                aria-label="Eliminar documento {{ $documento->numero }}">
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
                                    <h3>No se encontraron documentos</h3>
                                    <p class="text-muted">Aún no has registrado ningún documento</p>
                                    <a href="{{ route('documentos.create') }}" class="btn btn-primary mt-2">
                                        <i class="fas fa-plus mr-1" aria-hidden="true"></i> Crear primer documento
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Paginación -->
            @if($documentos->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                    Mostrando {{ $documentos->firstItem() }} - {{ $documentos->lastItem() }} de {{ $documentos->total() }} registros
                </div>
                <div class="pagination-custom" role="navigation" aria-label="Paginación de documentos">
                    {{ $documentos->appends(request()->query())->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal para vista previa -->
<div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-labelledby="previewTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="previewTitle">Vista Previa</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar ventana de vista previa">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="previewContent">
                <!-- Contenido dinámico -->
            </div>
            <div class="modal-footer">
                <a id="downloadBtn" href="#" class="btn btn-primary" download aria-label="Descargar documento">
                    <i class="fas fa-download mr-1" aria-hidden="true"></i> Descargar
                </a>
                <button type="button" class="btn btn-secondary" data-dismiss="modal" aria-label="Cerrar ventana">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modales de reenvío para cada documento -->
@foreach($documentos as $documento)
<div class="modal fade" id="modalReenviar{{ $documento->id }}" tabindex="-1" role="dialog" aria-labelledby="modalReenviarTitle{{ $documento->id }}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: #0b2e59; color: white;">
                <h3 class="modal-title" id="modalReenviarTitle{{ $documento->id }}">
                    <i class="fas fa-paper-plane mr-2" aria-hidden="true"></i> Reenviar Documento
                </h3>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar ventana de reenvío">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('documentos.reenviar.store', $documento) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info" role="alert">
                        <p class="mb-0"><strong>Documento:</strong> {{ $documento->numero ?? 'DOC-' . $documento->id }} - {{ ucfirst($documento->tipo) }}</p>
                        <p class="mb-0"><strong>Asunto:</strong> {{ $documento->asunto }}</p>
                    </div>
                    
                    <div class="form-group">
                        <label for="destinatarios{{ $documento->id }}">Seleccionar Destinatario(s)</label>
                        <select class="form-control select2" name="destinatarios[]" id="destinatarios{{ $documento->id }}" multiple required style="width: 100%;" aria-describedby="destinatarios_help{{ $documento->id }}">
                            @foreach(App\Models\User::where('id_usuario', '!=', Auth::id())->get() as $usuario)
                                <option value="{{ $usuario->id_usuario }}">{{ $usuario->nombres }} {{ $usuario->apellidos }}</option>
                            @endforeach
                        </select>
                        <small id="destinatarios_help{{ $documento->id }}" class="form-text text-muted">Seleccione uno o más destinatarios para reenviar el documento</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="mensaje{{ $documento->id }}">Mensaje Adicional</label>
                        <textarea class="form-control" name="mensaje" id="mensaje{{ $documento->id }}" rows="3" placeholder="Agregar un mensaje (opcional)" aria-describedby="mensaje_help{{ $documento->id }}"></textarea>
                        <small id="mensaje_help{{ $documento->id }}" class="form-text text-muted">Mensaje opcional que se incluirá en el reenvío</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" aria-label="Cancelar reenvío">Cancelar</button>
                    <button type="submit" class="btn btn-primary" aria-label="Confirmar reenvío del documento">Reenviar Documento</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@stop

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
    <style>
        .select2-container .select2-selection--multiple {
            min-height: 38px;
            border: 1px solid #ced4da;
            border-radius: 4px;
        }
        
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #0b2e59;
            border-color: #0b2e59;
            color: white;
        }
        .table th a {
            color: white;
            text-decoration: none;
            display: block;
        }
        /* Estilos del index */
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
        
        .empty-state h3 {
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Inicializar Select2
            $('.select2').select2({
                placeholder: "Seleccionar destinatarios",
                allowClear: true
            });

            // Limpiar filtros
            $('#btnLimpiar').click(function() {
                $('#filtrosForm')[0].reset();
                window.location = "{{ route('documentos.gestor') }}";
            });
            
            // Vista previa de documentos
            $('.preview-btn').click(function() {
                const fileUrl = $(this).data('file-url');
                const fileName = $(this).data('file-name');
                const fileExt = fileName.split('.').pop().toLowerCase();
                
                $('#previewTitle').text('Vista Previa: ' + fileName);
                $('#downloadBtn').attr('href', fileUrl);
                
                if (fileExt === 'pdf') {
                    $('#previewContent').html(`
                        <div class="embed-responsive embed-responsive-16by9">
                            <iframe class="embed-responsive-item" src="${fileUrl}" title="Vista previa del documento ${fileName}" aria-label="Vista previa del documento ${fileName}"></iframe>
                        </div>
                    `);
                } else {
                    $('#previewContent').html(`
                        <div class="text-center">
                            <img src="${fileUrl}" class="img-fluid" alt="Vista previa del documento ${fileName}">
                        </div>
                    `);
                }
                
                $('#previewModal').modal('show');
            });
        });
        
        // Funciones de sanitización para filtros
        function sanitizeNombre(input) {
            let value = input.value;
            value = value.replace(/[^a-zA-Z\sáéíóúÁÉÍÓÚñÑ.,-]/g, '');
            value = value.replace(/(.)\1{3,}/g, '$1$1$1');
            if (value.length > 100) {
                value = value.substring(0, 100);
            }
            input.value = value;
        }
        
        function sanitizeBusqueda(input) {
            let value = input.value;
            value = value.replace(/[^a-zA-Z0-9\sáéíóúÁÉÍÓÚñÑ.,\-¿?¡!]/g, '');
            value = value.replace(/(.)\1{3,}/g, '$1$1$1');
            input.value = value;
        }
    </script>
@stop
