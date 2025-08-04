@extends('adminlte::page')

@section('title', 'Gestión de Tareas Documentales')

@section('content_header')
    <div class="unah-header">
        <h1 class="mb-0"><i class="fas fa-tasks mr-2"></i> Gestión de Tareas Documentales</h1>
        <p class="mb-0">Universidad Nacional Autónoma de Honduras - Posgrado en Informática Administrativa</p>
    </div>
    @if (session('success'))
        <div class="alert alert-success" id="successAlert">
            {{ session('success') }}
        </div>
    @endif
@stop

@section('content')
    <div class="container-fluid">
        <!-- Filtros y búsqueda -->
        <div class="filter-box mb-2">
            <form method="GET" action="{{ route('tareas.index') }}" id="filtroTareas">
                <div class="row align-items-end">
                    <div class="col-md-2">
                        <div class="form-group mb-1">
                            <label class="small">Estado de la Tarea</label>
                            <select class="form-control form-control-sm" name="estado">
                                <option value="">Todos</option>
                                <option value="Pendiente" {{ request('estado') == 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
                                <option value="En Proceso" {{ request('estado') == 'En Proceso' ? 'selected' : '' }}>En Proceso</option>
                                <option value="Completada" {{ request('estado') == 'Completada' ? 'selected' : '' }}>Completada</option>
                                <option value="Rechazada" {{ request('estado') == 'Rechazada' ? 'selected' : '' }}>Rechazada</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group mb-1">
                            <label class="small">Responsable</label>
                            <select class="form-control form-control-sm" name="responsable">
                                <option value="">Todos</option>
                                @foreach($responsables as $responsable)
                                    <option value="{{ $responsable->id_usuario }}" {{ request('responsable') == $responsable->id_usuario ? 'selected' : '' }}>
                                        {{ $responsable->nombres }} {{ $responsable->apellidos }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group mb-1">
                            <label class="small">Tipo de Documento</label>
                            <select class="form-control form-control-sm" name="tipo_documento">
                                <option value="">Todos</option>
                                @foreach($tiposDocumento as $tipo)
                                    <option value="{{ $tipo->id_tipo }}" {{ request('tipo_documento') == $tipo->id_tipo ? 'selected' : '' }}>{{ $tipo->nombre_tipo }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group mb-1">
                            <label class="small">Fecha Inicio</label>
                            <input type="date" class="form-control form-control-sm" name="fecha_inicio" value="{{ request('fecha_inicio') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group mb-1">
                            <label class="small">Fecha Fin</label>
                            <input type="date" class="form-control form-control-sm" name="fecha_fin" value="{{ request('fecha_fin') }}">
                        </div>
                    </div>
                    <div class="col-md-2 text-right">
                        <div class="btn-group mb-1" role="group">
                            <button class="btn btn-success btn-sm btn-filtros" type="submit">
                                <i class="fas fa-filter mr-1"></i> Filtrar
                            </button>
                            <a href="#" id="btnRestablecer" class="btn btn-warning btn-sm btn-filtros">
                                <i class="fas fa-redo mr-1"></i> Restablecer
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Estadísticas rápidas -->
        <div class="row mb-4 mt-2">
            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-secondary"><i class="fas fa-clock"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Pendientes</span>
                        <span class="info-box-number">{{ $estadisticas['pendientes'] ?? 0 }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-info"><i class="fas fa-spinner"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">En Proceso</span>
                        <span class="info-box-number">{{ $estadisticas['en_proceso'] ?? 0 }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-success"><i class="fas fa-check"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Completadas</span>
                        <span class="info-box-number">{{ $estadisticas['completadas'] ?? 0 }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-primary"><i class="fas fa-user-check"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Rechazadas</span>
                        <span class="info-box-number">{{ $estadisticas['rechazadas'] ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Listado de tareas -->
        <div class="card">
            <div class="card-header card-header-unah d-flex align-items-center justify-content-between">
                <h3 class="card-title mb-0"><i class="fas fa-list mr-2"></i> Tareas Documentales</h3>
                @if(Auth::user()->puedeAgregar('TareasDocumentales'))
                <div class="ml-auto">
                    <button class="btn btn-primary btn-sm btn-filtros" type="button" data-toggle="modal" data-target="#modalNuevaTarea" id="btnNuevaTarea">
                        <i class="fas fa-plus mr-1"></i> Nueva Tarea
                    </button>
                </div>
                @endif
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover text-nowrap">
                        <thead class="bg-light">
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Responsable</th>
                                <th>Estado</th>
                                <th>Fecha de Creación</th>
                                <th>Documentos</th>
                                <th>Tipo de Documento</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @include('tareas.tabla', ['tareas' => $tareas])
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para nueva/editar tarea -->
    <div class="modal fade" id="modalNuevaTarea" tabindex="-1" role="dialog" aria-labelledby="modalNuevaTareaLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form method="POST" id="formTarea" action="{{ route('tareas.store') }}">
                @csrf
                <div class="modal-content">
                    <div class="modal-header bg-primary" id="modalTareaHeader">
                        <h5 class="modal-title" id="modalNuevaTareaLabel">Nueva Tarea</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- Campos del formulario -->
                        <div class="form-group">
                            <label for="nombreTarea">Nombre de la tarea</label>
                            <input type="text" class="form-control" id="nombreTarea" name="nombre" required>
                        </div>
                        <div class="form-group">
                            <label for="responsableTarea">Responsable</label>
                            <select class="form-control" id="responsableTarea" name="fk_id_usuario_asignado" required>
                                <option value="">Seleccione</option>
                                @foreach($responsables as $responsable)
                                    <option value="{{ $responsable->id_usuario }}">{{ $responsable->nombres }} {{ $responsable->apellidos }}</option>
                                @endforeach
                            </select>
                        </div>
                        <input type="hidden" name="fk_id_usuario_creador" value="{{ auth()->id() }}">
                        <div class="form-group">
                            <label for="descripcionTarea">Descripción</label>
                            <textarea class="form-control" id="descripcionTarea" name="descripcion" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="estadoTarea">Estado</label>
                            <select class="form-control" id="estadoTarea" name="estado" required>
                                <option value="Pendiente">Pendiente</option>
                                <option value="En Proceso">En Proceso</option>
                                <option value="Completada">Completada</option>
                                <option value="Rechazada">Rechazada</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="fechaCreacionTarea">Fecha de Creación</label>
                            <input type="date" class="form-control" id="fechaCreacionTarea" name="fecha_creacion" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="fechaVencimientoTarea">Fecha de Vencimiento</label>
                            <input type="date" class="form-control" id="fechaVencimientoTarea" name="fecha_vencimiento">
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
                    <div class="modal-header bg-success">
                        <h5 class="modal-title" id="modalCargarDocumentoLabel">Cargar Documento</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="fk_id_tipo">Tipo de Documento</label>
                            <select name="fk_id_tipo" id="fk_id_tipo" class="form-control" required>
                                <option value="">Seleccione...</option>
                                @foreach($tiposDocumento as $tipo)
                                    <option value="{{ $tipo->id_tipo }}">{{ $tipo->nombre_tipo }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="documento">Archivo (PDF o imagen)</label>
                            <input type="file" class="form-control" id="documento" name="documento" accept="application/pdf,image/*" required>
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
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-info">
                    <h5 class="modal-title" id="modalDetalleTareaLabel">Detalle de Tarea</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
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
                        <div class="card-body p-2 historial-scroll" style="max-height: 250px; overflow-y: auto; background: #f8f9fa; padding-right: 18px; border-left: none !important; box-shadow: none !important;" id="historial-bitacora">
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

@section('js')
<script>
    // Modal Detalle
    $('#modalDetalleTarea').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var tareaId = button.data('id');
        $('#detalle-nombre').text(button.data('nombre'));
        $('#detalle-responsable').text(button.data('responsable'));
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
        $('#detalle-fecha').text(button.data('fecha'));
        $('#detalle-descripcion').text(button.data('descripcion'));
        // Cargar historial por AJAX
        var historialDiv = $('#historial-bitacora');
        historialDiv.html('<div class="text-center text-muted">Cargando historial...</div>');
        $.get('/tareas/' + tareaId + '/historial', function(data) {
            if (data.historial.length === 0) {
                historialDiv.html('<div class="text-center text-muted">Sin acciones registradas.</div>');
            } else {
                var html = '<ul class="timeline list-unstyled mb-0">';
                data.historial.forEach(function(item) {
                    html += '<li class="mb-2"><i class="fas fa-circle text-info mr-2" style="font-size:0.7rem;"></i>' + item + '</li>';
                });
                html += '</ul>';
                historialDiv.html(html);
            }
        }).fail(function() {
            historialDiv.html('<div class="text-danger">Error al cargar el historial.</div>');
        });
    });

    // Modal Nueva/Editar
    $('#modalNuevaTarea').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var modal = $(this);
        var form = modal.find('form');
        var isEdit = button.data('id') ? true : false;

        // Reset form
        form.trigger('reset');
        form.find('input[name="_method"]').remove();
        $('#modalTareaHeader').removeClass('bg-warning').addClass('bg-primary');
        modal.find('.modal-title').text('Nueva Tarea');
        $('#btnGuardarTarea').text('Crear Tarea').removeClass('btn-warning').addClass('btn-primary');

        if (isEdit) {
            // Editar
            $('#modalTareaHeader').removeClass('bg-primary').addClass('bg-warning');
            modal.find('.modal-title').text('Editar Tarea');
            $('#btnGuardarTarea').text('Actualizar Tarea').removeClass('btn-primary').addClass('btn-warning');
            form.attr('action', '/tareas/' + button.data('id'));
            form.append('<input type="hidden" name="_method" value="PUT">');
            $('#nombreTarea').val(button.data('nombre'));
            $('#responsableTarea').val(button.data('responsable'));
            $('#estadoTarea').val(button.data('estado'));
            $('#fechaCreacionTarea').val(button.data('fecha'));
            $('#descripcionTarea').val(button.data('descripcion'));
        } else {
            // Nueva
            form.attr('action', '{{ route('tareas.store') }}');
            $('#fechaCreacionTarea').val('{{ date('Y-m-d') }}');
            // Limpiar selects y textarea
            $('#responsableTarea').val('');
            $('#estadoTarea').val('Pendiente');
            $('#descripcionTarea').val('');
        }
    });

    // Limpiar modal al cerrarse
    $('#modalNuevaTarea').on('hidden.bs.modal', function () {
        var form = $(this).find('form');
        form.trigger('reset');
        form.find('input[name="_method"]').remove();
        $('#modalTareaHeader').removeClass('bg-warning').addClass('bg-primary');
        $(this).find('.modal-title').text('Nueva Tarea');
        $('#btnGuardarTarea').text('Crear Tarea').removeClass('btn-warning').addClass('btn-primary');
        form.attr('action', '{{ route('tareas.store') }}');
    });

    // Modal cargar documento
    $('#modalCargarDocumento').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        $('#idTareaDocumento').val(button.data('id'));
    });

    // Visualizar documento en modal mejorado
    $(document).on('click', '.ver-documento', function(e) {
        e.preventDefault();
        var url = $(this).data('url');
        var tipo = $(this).data('tipo');
        var nombre = $(this).data('nombre');
        var icono = '';
        var html = '';
        var id = $(this).data('id'); // <-- agrega este data-id en el enlace

        if(tipo === 'imagen') {
            icono = '<i class="fas fa-file-image text-info"></i>';
            html = '<img src="' + url + '" alt="' + nombre + '" class="img-fluid">';
        } else if(tipo === 'pdf') {
            icono = '<i class="fas fa-file-pdf text-danger"></i>';
            html = '<iframe src="' + url + '" width="100%" height="500px" style="border:none;"></iframe>';
        } else {
            icono = '<i class="fas fa-file text-secondary"></i>';
            html = '<p>No es posible visualizar este tipo de archivo.</p>';
        }

        $('#iconoDocumento').html(icono);
        $('#modalVerDocumentoLabel').text(nombre);
        $('#contenedorDocumento').html(html);
        $('#descargarDocumento').attr('href', url);

        // Actualiza la acción del formulario de eliminar
        var action = "{{ route('tareas.documento.eliminar', ':id') }}";
        $('#formEliminarDocumento').attr('action', action.replace(':id', id));
    });

    // Limpiar modal al cerrarse
    $('#modalVerDocumento').on('hidden.bs.modal', function () {
        $('#iconoDocumento').html('');
        $('#modalVerDocumentoLabel').text('');
        $('#contenedorDocumento').html('');
        $('#descargarDocumento').attr('href', '#');
        $('#formEliminarDocumento').attr('action', '#');
    });

    // Solo filtrar la tabla si el input de búsqueda NO está dentro de un modal
    function filterTareas() {
        var input = document.getElementById("table_search");
        if (!input) return; // Si no existe, no hace nada y evita el error
        let filter = input.value.toLowerCase();
        let table = document.querySelector(".table-responsive table");
        let tr = table ? table.getElementsByTagName("tr") : [];
        for (let i = 1; i < tr.length; i++) { // i=1 para saltar el thead
            let visible = false;
            let td = tr[i].getElementsByTagName("td");
            if (td.length > 0) {
                for (let j = 0; j < td.length - 1; j++) { // -1 para no buscar en acciones
                    let cell = td[j];
                    if (cell) {
                        let text = cell.textContent || cell.innerText;
                        if (text.toLowerCase().indexOf(filter) > -1) {
                            visible = true;
                            break;
                        }
                    }
                }
                tr[i].style.display = visible ? "" : "none";
            }
        }
    }

    $(document).ready(function() {
        // Ocultar notificación de éxito después de 3 segundos
        setTimeout(function() {
            $('#successAlert').fadeOut('slow');
        }, 3000);

        // Interceptar solo el submit del formulario de filtros (GET)
        $('#filtroTareas').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            $.ajax({
                url: form.attr('action'),
                type: 'GET',
                data: form.serialize(),
                success: function(data) {
                    $(".table-responsive tbody").html(data);
                },
                error: function() {
                    alert('Error al filtrar las tareas.');
                }
            });
        });

        // Restablecer filtros y tabla
        $('#btnRestablecer').on('click', function(e) {
            e.preventDefault();
            var form = $(this).closest('form');
            form[0].reset();
            $('#table_search').val('');
            filterTareas();
            // También recarga la tabla completa vía AJAX
            $.ajax({
                url: form.attr('action'),
                type: 'GET',
                data: {},
                success: function(data) {
                    $(".table-responsive tbody").html(data);
                },
                error: function() {
                    alert('Error al restablecer las tareas.');
                }
            });
        });
    });
</script>
<style>
    .unah-header {
        background: linear-gradient(135deg, #0b2e59, #1a5a8d);
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        color: white;
        margin-bottom: 25px;
    }
    /* Elimina cualquier borde izquierdo en la tarjeta y el historial */
    .card, .card-body, .historial-scroll, .card .card-body, .card .historial-scroll {
        border-left: none !important;
        box-shadow: none !important;
    }
    /* Elimina línea vertical de listas tipo timeline */
    .timeline, .timeline li, .timeline:before {
        border-left: none !important;
        box-shadow: none !important;
        background: none !important;
    }
    ul.timeline {
        border-left: none !important;
        background: none !important;
        margin-left: 0 !important;
        padding-left: 0 !important;
    }
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
</style>
@endsection