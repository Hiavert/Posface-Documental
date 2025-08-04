@extends('adminlte::page')

@section('title', 'Gestión de Documentos')

@section('content_header')
<div class="elegant-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-0"><i class="fas fa-file-contract mr-2 text-primary"></i> Gestión de Documentos</h1>
            <p class="subtitle">Universidad Nacional Autónoma de Honduras - Posgrado en Informática Administrativa</p>
        </div>
        <div>
            <a href="{{ route('documentos.create') }}" class="btn btn-primary">
                <i class="fas fa-plus mr-1"></i> Nuevo Documento
            </a>
        </div>
    </div>
</div>
@stop

@section('content')
<div class="container-fluid">
    <!-- Filtros -->
    <div class="card card-elegant mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0"><i class="fas fa-filter mr-2 text-muted"></i>Filtros</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('documentos.gestor') }}">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Tipo de Documento</label>
                        <select class="form-control form-control-elegant" name="tipo">
                            <option value="">Todos</option>
                            <option value="oficio" {{ request('tipo') == 'oficio' ? 'selected' : '' }}>Oficio</option>
                            <option value="circular" {{ request('tipo') == 'circular' ? 'selected' : '' }}>Circular</option>
                            <option value="memorandum" {{ request('tipo') == 'memorandum' ? 'selected' : '' }}>Memorándum</option>
                            <option value="resolucion" {{ request('tipo') == 'resolucion' ? 'selected' : '' }}>Resolución</option>
                            <option value="acuerdo" {{ request('tipo') == 'acuerdo' ? 'selected' : '' }}>Acuerdo</option>
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
                        <label class="form-label">Fecha</label>
                        <input type="date" class="form-control form-control-elegant" name="fecha" value="{{ request('fecha') }}">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-8 mb-3">
                        <label class="form-label">Palabras Clave</label>
                        <input type="text" class="form-control form-control-elegant" name="busqueda" placeholder="Buscar por asunto o contenido" value="{{ request('busqueda') }}">
                    </div>
                    <div class="col-md-4 mb-3 d-flex align-items-end">
                        <div class="d-flex w-100">
                            <a href="{{ route('documentos.gestor') }}" class="btn btn-outline-secondary btn-elegant w-50 mr-2">
                                <i class="fas fa-redo mr-1"></i> Limpiar
                            </a>
                            <button type="submit" class="btn btn-primary btn-elegant w-50">
                                <i class="fas fa-search mr-1"></i> Buscar
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
            <h5 class="card-title mb-0"><i class="fas fa-list mr-2 text-muted"></i> Documentos Registrados</h5>
            <div class="ml-auto">
                <span class="badge badge-light">{{ $documentos->total() }} registros</span>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-borderless">
                    <thead class="thead-elegant">
                        <tr>
                            <th>ID</th>
                            <th>Tipo</th>
                            <th>Remitente</th>
                            <th>Destinatario</th>
                            <th>Asunto</th>
                            <th>Fecha</th>
                            <th class="text-center">Acciones</th>
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
                                <div class="btn-group">
                                    <a href="{{ route('documentos.historial', $documento) }}" class="btn btn-sm btn-action" title="Historial de envíos">
                                        <i class="fas fa-history text-info"></i>
                                    </a>
                                    <button class="btn btn-sm btn-action" title="Reenviar" data-toggle="modal" data-target="#modalReenviar{{ $documento->id }}">
                                        <i class="fas fa-paper-plane text-primary"></i>
                                    </button>
                                    <a href="{{ route('documentos.show', $documento) }}" class="btn btn-sm btn-action" title="Ver detalles">
                                        <i class="fas fa-eye text-info"></i>
                                    </a>
                                    <a href="{{ route('documentos.descargar', $documento) }}" class="btn btn-sm btn-action" title="Descargar">
                                        <i class="fas fa-download text-success"></i>
                                    </a>
                                    <a href="{{ route('documentos.edit', $documento) }}" class="btn btn-sm btn-action" title="Editar">
                                        <i class="fas fa-edit text-warning"></i>
                                    </a>
                                    <form action="{{ route('documentos.destroy', $documento) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-action" title="Eliminar" onclick="return confirm('¿Estás seguro de eliminar este documento?')">
                                            <i class="fas fa-trash-alt text-danger"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <h5>No se encontraron documentos</h5>
                                    <p class="text-muted">Aún no has registrado ningún documento</p>
                                    <a href="{{ route('documentos.create') }}" class="btn btn-primary mt-2">
                                        <i class="fas fa-plus mr-1"></i> Crear primer documento
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
                <div class="pagination-custom">
                    {{ $documentos->appends(request()->query())->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modales de reenvío para cada documento -->
@foreach($documentos as $documento)
<div class="modal fade" id="modalReenviar{{ $documento->id }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: #0b2e59; color: white;">
                <h5 class="modal-title">
                    <i class="fas fa-paper-plane mr-2"></i> Reenviar Documento
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('documentos.reenviar.store', $documento) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <p class="mb-0"><strong>Documento:</strong> {{ $documento->numero ?? 'DOC-' . $documento->id }} - {{ ucfirst($documento->tipo) }}</p>
                        <p class="mb-0"><strong>Asunto:</strong> {{ $documento->asunto }}</p>
                    </div>
                    
                    <div class="form-group">
                        <label>Seleccionar Destinatario(s)</label>
                        <select class="form-control select2" name="destinatarios[]" multiple required style="width: 100%;">
                            @foreach(App\Models\User::where('id_usuario', '!=', Auth::id())->get() as $usuario)
                                <option value="{{ $usuario->id_usuario }}">{{ $usuario->nombres }} {{ $usuario->apellidos }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Mensaje Adicional</label>
                        <textarea class="form-control" name="mensaje" rows="3" placeholder="Agregar un mensaje (opcional)"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Reenviar Documento</button>
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

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Inicializar Select2
            $('.select2').select2({
                placeholder: "Seleccionar destinatarios",
                allowClear: true
            });
        });
    </script>
@stop