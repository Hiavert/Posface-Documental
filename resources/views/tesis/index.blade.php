@extends('adminlte::page')

@section('title', 'Gestión Documental de Tesis')

@section('content_header')
    <div class="elegant-header" role="banner">
        <h1><i class="fas fa-book mr-2" aria-hidden="true"></i> Gestión Documental de Tesis</h1>
        <p class="mb-0">Universidad Nacional Autónoma de Honduras - Posgrado de la Facultad de Ciencias Económicas Administrativas y Contables</p>
    </div>
@stop

@section('content')
<div class="container-fluid">

    <!-- Filtros -->
    <div class="card card-elegant mb-4">
        <div class="card-header">
            <h2 class="card-title mb-0 h5"><i class="fas fa-filter mr-2 text-muted" aria-hidden="true"></i> Filtros</h2>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label for="filtro-tipo" class="form-label font-weight-bold">Tipo de Tesis</label>
                    <select class="form-control form-control-elegant" id="filtro-tipo" aria-describedby="tipoHelp">
                        <option value="">Todas</option>
                        @foreach($tiposTesis as $tipo)
                            <option value="{{ $tipo->id_tipo_tesis }}">{{ $tipo->nombre }}</option>
                        @endforeach
                    </select>
                    <small id="tipoHelp" class="form-text text-muted sr-only">Seleccione el tipo de tesis para filtrar</small>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="filtro-region" class="form-label font-weight-bold">Región/Departamento</label>
                    <select class="form-control form-control-elegant" id="filtro-region" aria-describedby="regionHelp">
                        <option value="">Todas</option>
                        @foreach($regiones as $region)
                            <option value="{{ $region->id_region }}">{{ $region->nombre }}</option>
                        @endforeach
                    </select>
                    <small id="regionHelp" class="form-text text-muted sr-only">Seleccione la región o departamento para filtrar</small>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="filtro-autor" class="form-label font-weight-bold">Autor</label>
                    <input type="text" class="form-control form-control-elegant" id="filtro-autor" 
                           placeholder="Nombre del autor" maxlength="50" 
                           oninput="validarYFiltrarAutor(this)"
                           onkeypress="return permitirCaracteresAutor(event)"
                           aria-describedby="autorHelp autorError">
                    <small id="autorHelp" class="form-text text-muted">Máximo 50 caracteres, solo letras y espacios. Máximo 3 letras iguales consecutivas.</small>
                    <div class="invalid-feedback" id="autorError" role="alert"></div>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="filtro-cuenta" class="form-label font-weight-bold">Número de Cuenta</label>
                    <input type="text" class="form-control form-control-elegant" id="filtro-cuenta" 
                           placeholder="Número de cuenta" maxlength="12"
                           oninput="validarYFiltrarCuenta(this)"
                           onkeypress="return permitirCaracteresCuenta(event)"
                           aria-describedby="cuentaHelp cuentaError">
                    <small id="cuentaHelp" class="form-text text-muted">12 caracteres, solo números y guión. No se permiten letras ni otros caracteres especiales.</small>
                    <div class="invalid-feedback" id="cuentaError" role="alert"></div>
                </div>
            </div>
            <div class="d-flex justify-content-end">
                <button class="btn btn-outline-secondary btn-elegant mr-2" id="btn-restablecer" aria-label="Restablecer filtros">
                    <i class="fas fa-redo mr-1" aria-hidden="true"></i> Restablecer
                </button>
                <button class="btn btn-primary btn-elegant mr-2" id="btn-filtrar" aria-label="Aplicar filtros">
                    <i class="fas fa-filter mr-1" aria-hidden="true"></i> Filtrar
                </button>
                @if(auth()->user()->puedeAgregar('GestionTesis'))
                <button class="btn btn-success btn-elegant" id="btn-subir-tesis" aria-label="Subir nueva tesis">
                    <i class="fas fa-upload mr-1" aria-hidden="true"></i> Subir Tesis
                </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Herramientas de tabla -->
    <div class="card card-elegant mb-4">
        <div class="card-header">
            <h2 class="card-title mb-0 h5"><i class="fas fa-table mr-2 text-muted" aria-hidden="true"></i> Herramientas de Tabla</h2>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <label for="busqueda" class="sr-only">Buscar en la tabla</label>
                    <div class="input-group">
                        <span class="input-group-text" aria-hidden="true"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control form-control-elegant" id="busqueda" 
                               placeholder="Buscar por título, autor o cuenta..." maxlength="50"
                               oninput="validarYFiltrarBusqueda(this)"
                               onkeypress="return permitirCaracteresBusqueda(event)"
                               aria-describedby="busquedaHelp busquedaError">
                    </div>
                    <small id="busquedaHelp" class="form-text text-muted">Máximo 50 caracteres, solo letras y espacios. Máximo 3 letras iguales consecutivas.</small>
                    <div class="invalid-feedback" id="busquedaError" role="alert"></div>
                </div>
                <div class="col-md-6 text-right">
                    <button class="btn btn-outline-primary btn-elegant" id="btn-exportar" aria-label="Exportar tesis seleccionadas">
                        <i class="fas fa-file-export mr-1" aria-hidden="true"></i> Exportar Seleccionados
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Tesis -->
    <div class="card card-elegant">
        <div class="card-header">
            <h2 class="card-title mb-0 h5"><i class="fas fa-folder-open mr-2 text-muted" aria-hidden="true"></i> Tesis Registradas</h2>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" aria-describedby="tablaDescripcion">
                    <caption id="tablaDescripcion" class="sr-only">Lista de tesis registradas con opciones para filtrar, ordenar y realizar acciones</caption>
                    <thead class="thead-elegant">
                        <tr>
                            <th scope="col" width="50">
                                <label for="select-all" class="sr-only">Seleccionar todas las tesis</label>
                                <input type="checkbox" id="select-all" aria-label="Seleccionar todas las tesis">
                            </th>
                            <th scope="col" width="70" data-sort="id_tesis">
                                <button type="button" class="btn btn-link p-0 border-0 text-white font-weight-bold" onclick="ordenarColumna('id_tesis')">
                                    ID <i class="fas fa-sort" aria-hidden="true"></i>
                                </button>
                            </th>
                            <th scope="col" data-sort="titulo">
                                <button type="button" class="btn btn-link p-0 border-0 text-white font-weight-bold" onclick="ordenarColumna('titulo')">
                                    Título <i class="fas fa-sort" aria-hidden="true"></i>
                                </button>
                            </th>
                            <th scope="col" data-sort="fk_id_tipo_tesis">
                                <button type="button" class="btn btn-link p-0 border-0 text-white font-weight-bold" onclick="ordenarColumna('fk_id_tipo_tesis')">
                                    Tipo <i class="fas fa-sort" aria-hidden="true"></i>
                                </button>
                            </th>
                            <th scope="col" data-sort="autor">
                                <button type="button" class="btn btn-link p-0 border-0 text-white font-weight-bold" onclick="ordenarColumna('autor')">
                                    Autor <i class="fas fa-sort" aria-hidden="true"></i>
                                </button>
                            </th>
                            <th scope="col" data-sort="numero_cuenta">
                                <button type="button" class="btn btn-link p-0 border-0 text-white font-weight-bold" onclick="ordenarColumna('numero_cuenta')">
                                    Número de Cuenta <i class="fas fa-sort" aria-hidden="true"></i>
                                </button>
                            </th>
                            <th scope="col" data-sort="fk_id_region">
                                <button type="button" class="btn btn-link p-0 border-0 text-white font-weight-bold" onclick="ordenarColumna('fk_id_region')">
                                    Región/Depto <i class="fas fa-sort" aria-hidden="true"></i>
                                </button>
                            </th>
                            <th scope="col" width="100" data-sort="fecha_defensa">
                                <button type="button" class="btn btn-link p-0 border-0 text-white font-weight-bold" onclick="ordenarColumna('fecha_defensa')">
                                    Fecha Defensa <i class="fas fa-sort" aria-hidden="true"></i>
                                </button>
                            </th>
                            <th scope="col" width="150">Documento</th>
                            <th scope="col" width="120">Acciones</th>
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
            <nav aria-label="Navegación de páginas">
                <ul class="pagination pagination-sm m-0 float-right" id="pagination">
                    <!-- La paginación se cargará dinámicamente -->
                </ul>
            </nav>
        </div>
    </div>

</div>

<!-- Modal para Subir/Editar Tesis -->
<div class="modal fade" id="modal-tesis" tabindex="-1" role="dialog" aria-labelledby="modal-titulo" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-elegant">
                <h3 class="modal-title text-white h5" id="modal-titulo">Subir Nueva Tesis</h3>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form-tesis" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="id-tesis">
                    <div class="form-group">
                        <label for="titulo" class="form-label font-weight-bold">Título *</label>
                        <input type="text" class="form-control form-control-elegant" id="titulo" name="titulo" required maxlength="80">
                        <small class="form-text text-muted">Máximo 80 caracteres</small>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tipo_tesis" class="form-label font-weight-bold">Tipo de Tesis *</label>
                                <select class="form-control form-control-elegant" id="tipo_tesis" name="tipo_tesis" required>
                                    @foreach($tiposTesis as $tipo)
                                        <option value="{{ $tipo->id_tipo_tesis }}">{{ $tipo->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="region" class="form-label font-weight-bold">Región/Departamento *</label>
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
                                <label for="autor" class="form-label font-weight-bold">Autor *</label>
                                <input type="text" class="form-control form-control-elegant" id="autor" name="autor" 
                                       required maxlength="80"
                                       oninput="validarYFiltrarAutorModal(this)"
                                       onkeypress="return permitirCaracteresAutor(event)">
                                <small class="form-text text-muted">Solo letras y espacios, máximo 80 caracteres</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="numero_cuenta" class="form-label font-weight-bold">Número de Cuenta *</label>
                                <input type="text" class="form-control form-control-elegant" id="numero_cuenta" name="numero_cuenta" 
                                       required maxlength="13"
                                       oninput="validarYFiltrarCuentaModal(this)"
                                       onkeypress="return permitirCaracteresCuenta(event)">
                                <small class="form-text text-muted">Solo números, máximo 13 dígitos</small>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="fecha_defensa" class="form-label font-weight-bold">Fecha de Defensa *</label>
                        <input type="date" class="form-control form-control-elegant" id="fecha_defensa" name="fecha_defensa" required>
                    </div>
                    <div class="form-group">
                        <label for="documento" class="form-label font-weight-bold">Documento (PDF, máximo 30MB) *</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="documento" name="documento" accept=".pdf" aria-describedby="documentoHelp">
                            <label class="custom-file-label" for="documento">Seleccionar archivo</label>
                        </div>
                        <small id="documentoHelp" class="form-text text-muted">Solo archivos PDF, tamaño máximo 30MB</small>
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
<div class="modal fade" id="modal-detalles" tabindex="-1" role="dialog" aria-labelledby="detallesTitulo" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-elegant">
                <h3 class="modal-title text-white h5" id="detallesTitulo">Detalles de Subida</h3>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="font-weight-bold">Responsable:</label>
                    <p id="detalle-responsable"></p>
                </div>
                <div class="form-group">
                    <label class="font-weight-bold">Fecha de Subida:</label>
                    <p id="detalle-fecha-subida"></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Vista Previa del PDF -->
<div class="modal fade" id="modal-preview" tabindex="-1" role="dialog" aria-labelledby="previewTitulo" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-elegant">
                <h3 class="modal-title text-white h5" id="previewTitulo">Vista Previa de Tesis</h3>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="embed-responsive embed-responsive-16by9">
                    <iframe id="preview-iframe" class="embed-responsive-item" src="" title="Vista previa del documento PDF"></iframe>
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

<script>
// =============================================
// VALIDACIONES PARA FILTROS Y CAMPOS DE TEXTO
// =============================================

// Función para permitir solo caracteres válidos en campo de autor
function permitirCaracteresAutor(event) {
    const charCode = event.which ? event.which : event.keyCode;
    const charStr = String.fromCharCode(charCode);
    
    // Permitir letras (con acentos), espacios y ñ
    const regex = /^[A-Za-záéíóúÁÉÍÓÚñÑ\s]$/;
    
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

// Función para permitir solo caracteres válidos en campo de número de cuenta
function permitirCaracteresCuenta(event) {
    const charCode = event.which ? event.which : event.keyCode;
    const charStr = String.fromCharCode(charCode);
    
    // Permitir solo números y guión
    const regex = /^[0-9\-]$/;
    
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

// Función para permitir solo caracteres válidos en campo de búsqueda
function permitirCaracteresBusqueda(event) {
    const charCode = event.which ? event.which : event.keyCode;
    const charStr = String.fromCharCode(charCode);
    
    // Permitir letras (con acentos), espacios, números y algunos caracteres básicos para búsqueda
    const regex = /^[A-Za-záéíóúÁÉÍÓÚñÑ0-9\s\-_.,!?]$/;
    
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

// Función para validar y filtrar el campo de autor en tiempo real
function validarYFiltrarAutor(input) {
    let valor = input.value;
    const errorElement = document.getElementById('autorError');
    
    // Remover clases de validación previas
    input.classList.remove('is-invalid', 'is-valid');
    errorElement.textContent = '';
    input.removeAttribute('aria-invalid');
    
    // Filtrar caracteres no permitidos (solo letras, espacios y acentos)
    valor = valor.replace(/[^A-Za-záéíóúÁÉÍÓÚñÑ\s]/g, '');
    
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
    
    // Si está vacío, no validar
    if (valor === '') {
        return true;
    }
    
    // Si pasa todas las validaciones
    input.classList.add('is-valid');
    return true;
}

// Función para validar y filtrar el campo de número de cuenta en tiempo real
function validarYFiltrarCuenta(input) {
    let valor = input.value;
    const errorElement = document.getElementById('cuentaError');
    
    // Remover clases de validación previas
    input.classList.remove('is-invalid', 'is-valid');
    errorElement.textContent = '';
    input.removeAttribute('aria-invalid');
    
    // Filtrar caracteres no permitidos (solo números y guión)
    valor = valor.replace(/[^0-9\-]/g, '');
    
    // Limitar a 12 caracteres
    if (valor.length > 12) {
        valor = valor.substring(0, 12);
    }
    
    // Actualizar el valor del input
    if (input.value !== valor) {
        input.value = valor;
    }
    
    // Si está vacío, no validar
    if (valor === '') {
        return true;
    }
    
    // Si pasa todas las validaciones
    input.classList.add('is-valid');
    return true;
}

// Función para validar y filtrar el campo de búsqueda en tiempo real
function validarYFiltrarBusqueda(input) {
    let valor = input.value;
    const errorElement = document.getElementById('busquedaError');
    
    // Remover clases de validación previas
    input.classList.remove('is-invalid', 'is-valid');
    errorElement.textContent = '';
    input.removeAttribute('aria-invalid');
    
    // Validar que no haya más de 3 letras iguales consecutivas (solo para letras)
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
    
    // Si está vacío, no validar
    if (valor === '') {
        return true;
    }
    
    // Si pasa todas las validaciones
    input.classList.add('is-valid');
    return true;
}

// Funciones para validación en el modal
function validarYFiltrarAutorModal(input) {
    let valor = input.value;
    
    // Filtrar caracteres no permitidos (solo letras, espacios y acentos)
    valor = valor.replace(/[^A-Za-záéíóúÁÉÍÓÚñÑ\s]/g, '');
    
    // Validar que no haya más de 3 letras iguales consecutivas
    const regexRepetidas = /([A-Za-z])\1{3,}/g;
    if (regexRepetidas.test(valor)) {
        valor = valor.replace(regexRepetidas, (match) => {
            return match.substring(0, 3);
        });
    }
    
    // Limitar a 80 caracteres
    if (valor.length > 80) {
        valor = valor.substring(0, 80);
    }
    
    // Actualizar el valor del input
    if (input.value !== valor) {
        input.value = valor;
    }
}

function validarYFiltrarCuentaModal(input) {
    let valor = input.value;
    
    // Filtrar caracteres no permitidos (solo números)
    valor = valor.replace(/[^0-9]/g, '');
    
    // Limitar a 13 caracteres
    if (valor.length > 13) {
        valor = valor.substring(0, 13);
    }
    
    // Actualizar el valor del input
    if (input.value !== valor) {
        input.value = valor;
    }
}

// Función para ordenar columnas (accesible)
function ordenarColumna(columna) {
    // Esta función se integrará con la lógica existente de ordenamiento
    const event = new Event('click');
    document.querySelector(`th[data-sort="${columna}"]`).dispatchEvent(event);
}

// Aplicar validación inicial si hay valores en los campos
document.addEventListener('DOMContentLoaded', function() {
    const autorInput = document.getElementById('filtro-autor');
    const cuentaInput = document.getElementById('filtro-cuenta');
    const busquedaInput = document.getElementById('busqueda');
    
    if (autorInput && autorInput.value) {
        validarYFiltrarAutor(autorInput);
    }
    
    if (cuentaInput && cuentaInput.value) {
        validarYFiltrarCuenta(cuentaInput);
    }
    
    if (busquedaInput && busquedaInput.value) {
        validarYFiltrarBusqueda(busquedaInput);
    }
});
</script>

<script>
// =============================================
// CÓDIGO JQUERY EXISTENTE (MANTENIDO)
// =============================================
$(document).ready(function() {
    // Variables globales
    let tesisData = {};
    let currentPage = 1;
    const itemsPerPage = 10;
    const storagePath = "{{ asset('storage') }}/";
    let sortColumn = 'id_tesis';
    let sortDirection = 'asc';
    
    // Rutas corregidas según tu web.php
    const routes = {
        list: "{{ route('tesis.list') }}",
        store: "{{ route('tesis.store') }}",
        update: (id) => `{{ url('tesis') }}/${id}`,
        destroy: (id) => `{{ url('tesis') }}/${id}`,
        exportar: "{{ route('tesis.exportar') }}",
        download: (filename) => "{{ url('storage/tesis') }}/" + filename,
        preview: (filename) => "{{ url('storage/tesis') }}/" + filename
    };

    // Inicializar
    cargarTesis();
    inicializarEventos();

    function cargarTesis() {
        showLoading(true);
        
        const filtros = {
            tipo: $('#filtro-tipo').val(),
            region: $('#filtro-region').val(),
            responsable: $('#filtro-autor').val(),
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
            tr.append(`<td><input type="checkbox" class="select-item" value="${tesis.id_tesis}" aria-label="Seleccionar tesis ${tesis.id_tesis}"></td>`);
            tr.append(`<td>${tesis.id_tesis}</td>`);
            tr.append(`<td title="${tesis.titulo}">${tesis.titulo}</td>`);
            tr.append(`<td>${tesis.tipo ? tesis.tipo.nombre : 'N/A'}</td>`);
            tr.append(`<td>${tesis.autor}</td>`);
            tr.append(`<td>${tesis.numero_cuenta}</td>`);
            tr.append(`<td>${tesis.region ? tesis.region.nombre : 'N/A'}</td>`);
            tr.append(`<td>${fechaDefensa}</td>`);

            // Documento y acciones
            if (tesis.ruta_archivo) {
                const previewUrl = routes.preview(tesis.ruta_archivo);
                const downloadUrl = routes.download(tesis.ruta_archivo);
                
                tr.append(`<td>
                    <div class="btn-group btn-group-sm" role="group" aria-label="Acciones para documento">
                        <button class="btn btn-info btn-preview" 
                            data-url="${previewUrl}" 
                            aria-label="Vista previa de tesis ${tesis.titulo}">
                            <i class="fas fa-eye" aria-hidden="true"></i>
                        </button>
                        <a href="${downloadUrl}" class="btn btn-secondary" aria-label="Descargar tesis ${tesis.titulo}" target="_blank">
                            <i class="fas fa-download" aria-hidden="true"></i>
                        </a>
                        <button class="btn btn-primary btn-detalles" 
                            data-responsable="${tesis.usuario ? tesis.usuario.usuario : 'N/A'}" 
                            data-fecha="${fechaSubida}" 
                            aria-label="Ver detalles de subida">
                            <i class="fas fa-info-circle" aria-hidden="true"></i>
                        </button>
                    </div>
                </td>`);
            } else {
                tr.append('<td>-</td>');
            }

            tr.append(`<td>
                <div class="btn-group btn-group-sm" role="group" aria-label="Acciones para tesis">
                    @if(auth()->user()->puedeEditar('GestionTesis'))
                    <button class="btn btn-success btn-editar" data-id="${tesis.id_tesis}" aria-label="Editar tesis ${tesis.titulo}">
                        <i class="fas fa-edit" aria-hidden="true"></i>
                    </button>
                    @endif
                    @if(auth()->user()->puedeEliminar('GestionTesis'))
                    <button class="btn btn-danger btn-eliminar" data-id="${tesis.id_tesis}" aria-label="Eliminar tesis ${tesis.titulo}">
                        <i class="fas fa-trash-alt" aria-hidden="true"></i>
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
            const prevLink = $('<a>').addClass('page-link').attr('href', '#').html('<span aria-hidden="true">&laquo;</span><span class="sr-only">Anterior</span>').data('page', tesisData.current_page - 1);
            prevLi.append(prevLink);
            pagination.append(prevLi);

            // Números de página
            for (let i = 1; i <= tesisData.last_page; i++) {
                const li = $('<li>').addClass('page-item').toggleClass('active', i === tesisData.current_page);
                const link = $('<a>').addClass('page-link').attr('href', '#').text(i).data('page', i);
                link.attr('aria-label', `Página ${i}`);
                li.append(link);
                pagination.append(li);
            }

            // Botón Siguiente
            const nextLi = $('<li>').addClass('page-item').toggleClass('disabled', tesisData.current_page === tesisData.last_page);
            const nextLink = $('<a>').addClass('page-link').attr('href', '#').html('<span aria-hidden="true">&raquo;</span><span class="sr-only">Siguiente</span>').data('page', tesisData.current_page + 1);
            nextLi.append(nextLink);
            pagination.append(nextLi);
        }
    }

    // Helper functions
    function showLoading(show) {
        if (show) {
            $('#tabla-tesis').html('<tr><td colspan="11" class="text-center"><i class="fas fa-spinner fa-spin" aria-hidden="true"></i> Cargando tesis...</td></tr>');
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
            $('#filtro-autor, #filtro-cuenta, #busqueda').val('');
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
            form.action = routes.exportar;
            
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
        
        // Mostrar vista previa en modal
        $(document).on('click', '.btn-preview', function() {
            const previewUrl = $(this).data('url');
            $('#preview-iframe').attr('src', previewUrl);
            $('#modal-preview').modal('show');
        });
        
        // Al cerrar el modal de vista previa, vaciar el iframe
        $('#modal-preview').on('hidden.bs.modal', function () {
            $('#preview-iframe').attr('src', '');
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
        
        // Cambiar label del archivo seleccionado
        $('#documento').on('change', function() {
            const fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').html(fileName);
        });
    }
});
</script>
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
    
    /* Estilo para el modal de vista previa */
    #modal-preview .modal-dialog {
        max-width: 90%;
        height: 90vh;
    }

    #modal-preview .modal-content {
        height: 100%;
    }

    #modal-preview .modal-body {
        padding: 0;
        height: calc(100% - 120px); /* Resta el header y footer */
    }

    .embed-responsive {
        height: 100%;
    }

    /* Estilos para validación */
    .is-valid {
        border-color: #28a745 !important;
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25) !important;
    }
    
    .is-invalid {
        border-color: #dc3545 !important;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
    }
    
    .invalid-feedback {
        display: block;
        font-size: 0.875rem;
        margin-top: 0.25rem;
        color: #dc3545;
        font-weight: 500;
    }
    
    /* Mejoras de accesibilidad */
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
    
    /* Mejorar contraste en enlaces */
    a {
        color: #2c5aa0;
    }
    
    a:hover {
        color: #1e3f73;
    }
    
    /* Mejorar contraste en textos */
    .text-muted {
        color: #6b7280 !important;
    }
    
    .badge-danger {
        background-color: #dc3545;
        color: white;
    }
    
    /* Focus visible para mejor accesibilidad */
    .btn:focus,
    .form-control:focus,
    .form-check-input:focus {
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        outline: 2px solid #0056b3;
        outline-offset: 2px;
    }
    
    .pagination .page-link:focus {
        z-index: 3;
        outline: 2px solid #0056b3;
        outline-offset: 2px;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
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
        
        #modal-preview .modal-dialog {
            max-width: 95%;
            height: 80vh;
            margin: 5px;
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
