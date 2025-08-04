@extends('adminlte::page')

@section('title', 'Gestión Documental de Tesis')

@section('content_header')
    <div class="elegant-header">
        <h1><i class="fas fa-book mr-2"></i> Gestión Documental de Tesis</h1>
        <p class="mb-0">Universidad Nacional Autónoma de Honduras - Posgrado en Informática Administrativa</p>
    </div>
@stop

@section('content')
<div class="container-fluid">

    <!-- Filtros -->
    <div class="card card-elegant mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0"><i class="fas fa-filter mr-2 text-muted"></i> Filtros</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label">Tipo de Tesis</label>
                    <select class="form-control form-control-elegant" id="filtro-tipo">
                        <option value="">Todas</option>
                        @foreach($tiposTesis as $tipo)
                            <option value="{{ $tipo->id_tipo_tesis }}">{{ $tipo->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Región/Departamento</label>
                    <select class="form-control form-control-elegant" id="filtro-region">
                        <option value="">Todas</option>
                        @foreach($regiones as $region)
                            <option value="{{ $region->id_region }}">{{ $region->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Autor</label>
                    <input type="text" class="form-control form-control-elegant" id="filtro-responsable" placeholder="Nombre del autor">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Número de Cuenta</label>
                    <input type="text" class="form-control form-control-elegant" id="filtro-cuenta" placeholder="Número de cuenta">
                </div>
            </div>
            <div class="d-flex justify-content-end">
                <button class="btn btn-outline-secondary btn-elegant mr-2" id="btn-restablecer">
                    <i class="fas fa-redo mr-1"></i> Restablecer
                </button>
                <button class="btn btn-primary btn-elegant mr-2" id="btn-filtrar">
                    <i class="fas fa-filter mr-1"></i> Filtrar
                </button>
                @if(auth()->user()->puedeAgregar('GestionTesis'))
                <button class="btn btn-success btn-elegant" id="btn-subir-tesis">
                    <i class="fas fa-upload mr-1"></i> Subir Tesis
                </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Herramientas de tabla -->
    <div class="card card-elegant mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0"><i class="fas fa-table mr-2 text-muted"></i> Herramientas de Tabla</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control form-control-elegant" id="busqueda" placeholder="Buscar por título, autor o cuenta...">
                    </div>
                </div>
                <div class="col-md-6 text-right">
                    <button class="btn btn-outline-primary btn-elegant" id="btn-exportar">
                        <i class="fas fa-file-export mr-1"></i> Exportar Seleccionados
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Tesis -->
    <div class="card card-elegant">
        <div class="card-header">
            <h5 class="card-title mb-0"><i class="fas fa-folder-open mr-2 text-muted"></i> Tesis Registradas</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="thead-elegant">
                        <tr>
                            <th width="50"><input type="checkbox" id="select-all"></th>
                            <th width="70" data-sort="id_tesis">ID <i class="fas fa-sort"></i></th>
                            <th data-sort="titulo">Título <i class="fas fa-sort"></i></th>
                            <th data-sort="fk_id_tipo_tesis">Tipo <i class="fas fa-sort"></i></th>
                            <th data-sort="autor">Autor <i class="fas fa-sort"></i></th>
                            <th data-sort="numero_cuenta">Número de Cuenta <i class="fas fa-sort"></i></th>
                            <th data-sort="fk_id_region">Región/Depto <i class="fas fa-sort"></i></th>
                            <th width="100" data-sort="fecha_defensa">Fecha Defensa <i class="fas fa-sort"></i></th>
                            <th width="150">Documento</th>
                            <th width="120">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tabla-tesis">
                        <!-- Los datos se cargarán dinámicamente -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Paginación -->
        <div class="card-footer clearfix bg-white">
            <ul class="pagination pagination-sm m-0 float-right" id="pagination">
                <!-- La paginación se cargará dinámicamente -->
            </ul>
        </div>
    </div>

</div>

<!-- Modal para Subir/Editar Tesis -->
<div class="modal fade" id="modal-tesis" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-elegant">
                <h5 class="modal-title text-white" id="modal-titulo">Subir Nueva Tesis</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form-tesis" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="id-tesis">
                    <div class="form-group">
                        <label for="titulo" class="form-label">Título *</label>
                        <input type="text" class="form-control form-control-elegant" id="titulo" name="titulo" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tipo_tesis" class="form-label">Tipo de Tesis *</label>
                                <select class="form-control form-control-elegant" id="tipo_tesis" name="tipo_tesis" required>
                                    @foreach($tiposTesis as $tipo)
                                        <option value="{{ $tipo->id_tipo_tesis }}">{{ $tipo->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="region" class="form-label">Región/Departamento *</label>
                                <select class="form-control form-control-elegant" id="region" name="region" required>
                                    @foreach($regiones as $region)
                                        <option value="{{ $region->id_region }}">{{ $region->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="autor" class="form-label">Autor *</label>
                                <input type="text" class="form-control form-control-elegant" id="autor" name="autor" 
                                       pattern="[a-zA-Z\sáéíóúÁÉÍÓÚñÑ]+" title="Solo letras y espacios" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="numero_cuenta" class="form-label">Número de Cuenta *</label>
                                <input type="text" class="form-control form-control-elegant" id="numero_cuenta" name="numero_cuenta" 
                                       pattern="[0-9]+" title="Solo números" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="fecha_defensa" class="form-label">Fecha de Defensa *</label>
                        <input type="date" class="form-control form-control-elegant" id="fecha_defensa" name="fecha_defensa" required>
                    </div>
                    <div class="form-group">
                        <label for="documento" class="form-label">Documento (PDF, máximo 30MB) *</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="documento" name="documento" accept=".pdf">
                            <label class="custom-file-label" for="documento">Seleccionar archivo</label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary btn-elegant" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary btn-elegant" id="btn-guardar">Guardar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Detalles -->
<div class="modal fade" id="modal-detalles" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-elegant">
                <h5 class="modal-title text-white">Detalles de Subida</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Responsable:</label>
                    <p id="detalle-responsable"></p>
                </div>
                <div class="form-group">
                    <label>Fecha de Subida:</label>
                    <p id="detalle-fecha-subida"></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de carga -->
<div class="modal fade" id="modal-carga" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content bg-transparent border-0">
            <div class="d-flex justify-content-center">
                <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                    <span class="sr-only">Cargando...</span>
                </div>
            </div>
            <div class="text-center mt-3 text-white">
                <p id="mensaje-carga">Procesando, por favor espere...</p>
            </div>
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
    
    .elegant-header p {
        font-size: 1rem;
        opacity: 0.85;
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
    
    .btn-outline-secondary.btn-elegant {
        border: 1px solid #dee2e6;
        color: #6c757d;
    }
    
    .btn-outline-secondary.btn-elegant:hover {
        background-color: #f8f9fa;
    }
    
    .btn-primary.btn-elegant {
        background: linear-gradient(135deg, #3a7bd5, #00d2ff);
        border: none;
    }
    
    .btn-primary.btn-elegant:hover {
        background: linear-gradient(135deg, #2a6bc9, #00bde3);
    }
    
    .btn-success.btn-elegant {
        background: linear-gradient(135deg, #00b09b, #96c93d);
        border: none;
    }
    
    .btn-success.btn-elegant:hover {
        background: linear-gradient(135deg, #009c87, #84b52c);
    }
    
    .btn-outline-primary.btn-elegant {
        border: 1px solid #3a7bd5;
        color: #3a7bd5;
    }
    
    .btn-outline-primary.btn-elegant:hover {
        background-color: #3a7bd5;
        color: white;
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
    
    .input-group-text {
        background-color: #f8f9fc;
        border: 1px solid #eaeef5;
        border-radius: 8px 0 0 8px;
    }
    
    /* Tabla */
    .table {
        border-collapse: separate;
        border-spacing: 0;
    }
    
    .thead-elegant {
        background-color: #0b2e59;
        color: white;
    }
    
    .table th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.5px;
        padding: 12px 15px;
        vertical-align: middle;
        cursor: pointer;
    }
    
    .table th i {
        margin-left: 5px;
        opacity: 0.7;
    }
    
    .table td {
        padding: 12px 15px;
        vertical-align: middle;
        border-bottom: 1px solid #f0f4f8;
    }
    
    .table-hover tbody tr:hover {
        background-color: #f8f9fc;
    }
    
    /* Modal */
    .modal-header.bg-elegant {
        background: linear-gradient(135deg, #0b2e59, #1a5a8d);
        border-radius: 0;
    }
    
    .modal-title {
        font-weight: 500;
    }
    
    /* Paginación */
    .pagination {
        margin: 0;
    }
    
    .page-item .page-link {
        border-radius: 8px;
        margin: 0 3px;
        color: #3a7bd5;
        border: 1px solid #dee2e6;
    }
    
    .page-item.active .page-link {
        background: linear-gradient(135deg, #3a7bd5, #00d2ff);
        border-color: #3a7bd5;
        color: white;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .d-flex.justify-content-end {
            flex-wrap: wrap;
        }
        
        .btn-elegant {
            margin-bottom: 8px;
            width: 100%;
        }
        
        .card-header, .card-body {
            padding: 15px;
        }
        
        .table-responsive {
            max-height: 400px;
        }
    }
    
    /* Animaciones */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .fade-in {
        animation: fadeIn 0.3s ease-in-out;
    }
</style>
@stop

@section('js')
<script>
$(document).ready(function() {
    // Variables globales
    let tesisData = {};
    let currentPage = 1;
    const itemsPerPage = 10;
    const storagePath = "{{ asset('storage/tesis') }}/";
    let sortColumn = 'id_tesis';
    let sortDirection = 'asc';
    
    // Rutas
    const routes = {
        list: "{{ route('tesis.list') }}",
        store: "{{ route('tesis.store') }}",
        update: (id) => `{{ url('tesis') }}/${id}`,
        destroy: (id) => `{{ url('tesis') }}/${id}`,
        download: (filename) => `{{ route('tesis.download', ['filename' => '__filename__']) }}`.replace('__filename__', filename)
    };

    // Inicializar
    cargarTesis();
    inicializarEventos();

    function cargarTesis() {
        showLoading(true);
        
        const filtros = {
            tipo: $('#filtro-tipo').val(),
            region: $('#filtro-region').val(),
            responsable: $('#filtro-responsable').val(),
            cuenta: $('#filtro-cuenta').val(),
            busqueda: $('#busqueda').val(),
            page: currentPage,
            sortColumn: sortColumn,
            sortDirection: sortDirection
        };

        $.ajax({
            url: routes.list,
            type: 'GET',
            data: filtros,
            success: function(response) {
                tesisData = response;
                renderTablaTesis();
            },
            error: function(xhr) {
                console.error('Error en la solicitud:', xhr);
                let errorMsg = 'Error en la conexión';
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMsg = xhr.responseJSON.error;
                }
                showError(errorMsg);
            },
            complete: function() {
                showLoading(false);
            }
        });
    }

    function renderTablaTesis() {
        const tbody = $('#tabla-tesis');
        tbody.empty();

        if (!tesisData.data || tesisData.data.length === 0) {
            tbody.append('<tr><td colspan="11" class="text-center">No se encontraron tesis</td></tr>');
            return;
        }

        tesisData.data.forEach(tesis => {
            const fechaDefensa = tesis.fecha_defensa ? new Date(tesis.fecha_defensa).toLocaleDateString('es-HN') : '-';
            const fechaSubida = tesis.fecha_subida ? new Date(tesis.fecha_subida).toLocaleDateString('es-HN') : '-';
            
            const tr = $('<tr class="fade-in">');
            tr.append(`<td><input type="checkbox" class="select-item" value="${tesis.id_tesis}"></td>`);
            tr.append(`<td>${tesis.id_tesis}</td>`);
            tr.append(`<td title="${tesis.titulo}">${tesis.titulo}</td>`);
            tr.append(`<td>${tesis.tipo ? tesis.tipo.nombre : 'N/A'}</td>`);
            tr.append(`<td>${tesis.autor}</td>`);
            tr.append(`<td>${tesis.numero_cuenta}</td>`);
            tr.append(`<td>${tesis.region ? tesis.region.nombre : 'N/A'}</td>`);
            tr.append(`<td>${fechaDefensa}</td>`);

            // Documento y acciones
            if (tesis.ruta_archivo) {
                const previewUrl = `{{ route('tesis.preview', ['filename' => '__filename__']) }}`.replace('__filename__', tesis.ruta_archivo);
                const downloadUrl = `{{ route('tesis.download', ['filename' => '__filename__']) }}`.replace('__filename__', tesis.ruta_archivo);
                
                tr.append(`<td>
                    <div class="btn-group btn-group-sm">
                        <a href="${previewUrl}" target="_blank" class="btn btn-info" title="Vista previa">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="${downloadUrl}" class="btn btn-secondary" title="Descargar">
                            <i class="fas fa-download"></i>
                        </a>
                        <button class="btn btn-primary btn-detalles" 
                            data-responsable="${tesis.usuario ? tesis.usuario.usuario : 'N/A'}" 
                            data-fecha="${fechaSubida}" 
                            title="Detalles">
                            <i class="fas fa-info-circle"></i>
                        </button>
                    </div>
                </td>`);
            } else {
                tr.append('<td>-</td>');
            }

            tr.append(`<td>
                <div class="btn-group btn-group-sm">
                    @if(auth()->user()->puedeEditar('GestionTesis'))
                    <button class="btn btn-success btn-editar" data-id="${tesis.id_tesis}" title="Editar">
                        <i class="fas fa-edit"></i>
                    </button>
                    @endif
                    @if(auth()->user()->puedeEliminar('GestionTesis'))
                    <button class="btn btn-danger btn-eliminar" data-id="${tesis.id_tesis}" title="Eliminar">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                    @endif
                </div>
            </td>`);
            tbody.append(tr);
        });

        renderPaginacion();
        updateSortIcons();
    }
    
    function updateSortIcons() {
        $('th i').removeClass('fa-sort-up fa-sort-down').addClass('fa-sort');
        const $th = $(`th[data-sort="${sortColumn}"]`);
        $th.find('i').removeClass('fa-sort').addClass(sortDirection === 'asc' ? 'fa-sort-up' : 'fa-sort-down');
    }

    function renderPaginacion() {
        const pagination = $('#pagination');
        pagination.empty();

        if (tesisData.last_page > 1) {
            // Botón Anterior
            const prevLi = $('<li>').addClass('page-item').toggleClass('disabled', tesisData.current_page === 1);
            const prevLink = $('<a>').addClass('page-link').attr('href', '#').text('Anterior').data('page', tesisData.current_page - 1);
            prevLi.append(prevLink);
            pagination.append(prevLi);

            // Números de página
            for (let i = 1; i <= tesisData.last_page; i++) {
                const li = $('<li>').addClass('page-item').toggleClass('active', i === tesisData.current_page);
                const link = $('<a>').addClass('page-link').attr('href', '#').text(i).data('page', i);
                li.append(link);
                pagination.append(li);
            }

            // Botón Siguiente
            const nextLi = $('<li>').addClass('page-item').toggleClass('disabled', tesisData.current_page === tesisData.last_page);
            const nextLink = $('<a>').addClass('page-link').attr('href', '#').text('Siguiente').data('page', tesisData.current_page + 1);
            nextLi.append(nextLink);
            pagination.append(nextLi);
        }
    }

    // Helper functions
    function showLoading(show) {
        if (show) {
            $('#tabla-tesis').html('<tr><td colspan="11" class="text-center"><i class="fas fa-spinner fa-spin"></i> Cargando tesis...</td></tr>');
        }
    }

    function showError(message) {
        $('#tabla-tesis').html(`<tr><td colspan="11" class="text-center text-danger">${message}</td></tr>`);
    }
    
    function showLoadingModal(message = 'Procesando, por favor espere...') {
        $('#mensaje-carga').text(message);
        $('#modal-carga').modal('show');
    }
    
    function hideLoadingModal() {
        $('#modal-carga').modal('hide');
    }
    
    function showToast(message, type = 'success') {
        const toast = $(`<div class="toast fade show" role="alert" aria-live="assertive" aria-atomic="true" style="position: fixed; top: 20px; right: 20px; min-width: 300px; z-index: 9999;">
            <div class="toast-header bg-${type} text-white">
                <strong class="me-auto">${type === 'success' ? 'Éxito' : 'Error'}</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body bg-white">
                ${message}
            </div>
        </div>`);
        
        $('body').append(toast);
        
        setTimeout(() => {
            toast.remove();
        }, 5000);
    }

    function inicializarEventos() {
        // Filtrar
        $('#btn-filtrar').click(function() {
            currentPage = 1;
            cargarTesis();
        });
        
        // Restablecer filtros
        $('#btn-restablecer').click(function() {
            $('#filtro-tipo, #filtro-region').val('');
            $('#filtro-responsable, #filtro-cuenta, #busqueda').val('');
            currentPage = 1;
            cargarTesis();
        });
        
        // Buscar al escribir
        $('#busqueda').on('input', function() {
            currentPage = 1;
            cargarTesis();
        });
        
        // Paginación
        $(document).on('click', '.page-link', function(e) {
            e.preventDefault();
            currentPage = $(this).data('page');
            cargarTesis();
        });
        
        // Seleccionar todos
        $('#select-all').change(function() {
            $('.select-item').prop('checked', this.checked);
        });
        
        // Subir nueva tesis
        $('#btn-subir-tesis').click(function() {
            $('#modal-titulo').text('Subir Nueva Tesis');
            $('#form-tesis')[0].reset();
            $('.custom-file-label').text('Seleccionar archivo');
            $('#id-tesis').val('');
            $('#modal-tesis').modal('show');
        });
        
        // Guardar tesis
        $('#btn-guardar').click(function() {
            // Validación de campos
            const titulo = $('#titulo').val();
            const autor = $('#autor').val();
            const numeroCuenta = $('#numero_cuenta').val();
            const fechaDefensa = $('#fecha_defensa').val();
            
            if (!titulo || !autor || !numeroCuenta || !fechaDefensa) {
                showToast('Por favor complete todos los campos requeridos', 'danger');
                return;
            }
            
            // Crear FormData manualmente para asegurar todos los campos
            const formData = new FormData();
            
            // Agregar todos los campos del formulario
            formData.append('titulo', titulo);
            formData.append('tipo_tesis', $('#tipo_tesis').val());
            formData.append('region', $('#region').val());
            formData.append('autor', autor);
            formData.append('numero_cuenta', numeroCuenta);
            formData.append('fecha_defensa', fechaDefensa);
            
            // Agregar archivo si existe
            const fileInput = $('#documento')[0];
            if (fileInput.files.length > 0) {
                formData.append('documento', fileInput.files[0]);
            }
            
            // Agregar el ID si estamos editando
            const id = $('#id-tesis').val();
            if (id) {
                formData.append('_method', 'PUT');
            }

            const url = id ? routes.update(id) : routes.store;
            const method = 'POST'; // Siempre POST, PUT se maneja con _method

            // Mostrar modal de carga
            showLoadingModal('Guardando tesis...');
            
            $.ajax({
                url: url,
                type: method,
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $('#modal-tesis').modal('hide');
                    currentPage = 1;
                    cargarTesis();
                    showToast('Tesis guardada correctamente');
                },
                error: function(xhr) {
                    let errorMsg = 'Error en la operación';
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        const errors = xhr.responseJSON.errors;
                        errorMsg = Object.values(errors)[0][0];
                    } else if (xhr.responseJSON && xhr.responseJSON.error) {
                        errorMsg = xhr.responseJSON.error;
                    }
                    showToast(errorMsg, 'danger');
                },
                complete: function() {
                    hideLoadingModal();
                }
            });
        });
        
        // Editar tesis
        $(document).on('click', '.btn-editar', function() {
            const id = $(this).data('id');
            const tesis = tesisData.data.find(t => t.id_tesis == id);
            
            if (tesis) {
                $('#modal-titulo').text('Editar Tesis');
                $('#id-tesis').val(tesis.id_tesis);
                $('#titulo').val(tesis.titulo);
                $('#tipo_tesis').val(tesis.fk_id_tipo_tesis);
                $('#region').val(tesis.fk_id_region);
                $('#autor').val(tesis.autor);
                $('#numero_cuenta').val(tesis.numero_cuenta);
                $('#fecha_defensa').val(tesis.fecha_defensa);
                $('.custom-file-label').text(tesis.ruta_archivo ? 'Documento actual' : 'Seleccionar archivo');
                $('#modal-tesis').modal('show');
            }
        });
        
        // Eliminar tesis
        $(document).on('click', '.btn-eliminar', function() {
            const id = $(this).data('id');
            
            if (confirm('¿Está seguro de eliminar esta tesis?')) {
                showLoadingModal('Eliminando tesis...');
                
                $.ajax({
                    url: routes.destroy(id),
                    type: 'DELETE',
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        cargarTesis();
                        showToast('Tesis eliminada correctamente');
                    },
                    error: function(xhr) {
                        showToast('Error al eliminar la tesis', 'danger');
                    },
                    complete: function() {
                        hideLoadingModal();
                    }
                });
            }
        });
        
        // Exportar tesis (ZIP)
        $('#btn-exportar').click(function() {
            const selectedIds = $('.select-item:checked').map(function() {
                return $(this).val();
            }).get();
            
            if (selectedIds.length === 0) {
                showToast('Seleccione al menos una tesis para exportar', 'danger');
                return;
            }
            
            showLoadingModal('Preparando archivos para exportar...');
            
            // Crear formulario temporal para descarga
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = "{{ route('tesis.exportar') }}";
            
            const token = document.createElement('input');
            token.type = 'hidden';
            token.name = '_token';
            token.value = "{{ csrf_token() }}";
            form.appendChild(token);
            
            const idsInput = document.createElement('input');
            idsInput.type = 'hidden';
            idsInput.name = 'ids';
            idsInput.value = JSON.stringify(selectedIds);
            form.appendChild(idsInput);
            
            document.body.appendChild(form);
            form.submit();
            
            // Ocultar modal después de un breve retraso
            setTimeout(hideLoadingModal, 2000);
        });
        
        // Mostrar detalles
        $(document).on('click', '.btn-detalles', function() {
            const responsable = $(this).data('responsable');
            const fecha = $(this).data('fecha');
            $('#detalle-responsable').text(responsable);
            $('#detalle-fecha-subida').text(fecha);
            $('#modal-detalles').modal('show');
        });
        
        // Ordenar por columna
        $(document).on('click', 'th[data-sort]', function() {
            const column = $(this).data('sort');
            if (sortColumn === column) {
                sortDirection = sortDirection === 'asc' ? 'desc' : 'asc';
            } else {
                sortColumn = column;
                sortDirection = 'asc';
            }
            currentPage = 1;
            cargarTesis();
        });
        
        // Validación en tiempo real
        $('#autor').on('input', function() {
            this.value = this.value.replace(/[^a-zA-Z\sáéíóúÁÉÍÓÚñÑ]/g, '');
        });
        
        $('#numero_cuenta').on('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
        
        // Cambiar label del archivo seleccionado
        $('#documento').on('change', function() {
            const fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').html(fileName);
        });
    }
});
</script>
@stop