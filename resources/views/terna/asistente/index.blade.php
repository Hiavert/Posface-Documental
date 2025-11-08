@extends('adminlte::page')

@section('title', 'Pagos Terna - Asistente')

@section('content_header')
<div class="elegant-header" role="banner">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h1 class="mb-0"><i class="fas fa-file-invoice-dollar mr-2 text-primary" aria-hidden="true"></i> Pagos Terna</h1>
            <p class="subtitle">Asistente de pagos de terna</p>
        </div>
        <div class="d-flex align-items-center">
            <div class="header-icon ml-3" aria-hidden="true">
                <i class="fas fa-user-headset"></i>
            </div>
        </div>
    </div>
</div>
@stop

@section('content')
<div class="container-fluid">
    @if(session('success'))
    <div class="alert-container" role="alert" aria-live="polite">
        <div class="alert alert-elegant-success alert-dismissible fade show">
            <i class="fas fa-check-circle mr-2" aria-hidden="true"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    </div>
    @endif

    <!-- Filtros de búsqueda -->
    <div class="card card-elegant mb-4">
        <div class="card-body">
            <form action="{{ route('terna.asistente.index') }}" method="GET" id="filtrosForm">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="codigo" class="font-weight-bold">Buscar por código</label>
                            <input type="text" name="codigo" id="codigo" class="form-control" 
                                   placeholder="TERNA-001" value="{{ request('codigo') }}" 
                                   maxlength="12" oninput="validarYFiltrarCodigo(this)"
                                   onkeypress="return permitirCaracteresCodigo(event)"
                                   aria-describedby="codigoHelp codigoError">
                            <small id="codigoHelp" class="form-text text-muted">
                                12 caracteres, solo letras, números y guión. Máximo 3 letras iguales consecutivas.
                            </small>
                            <div class="invalid-feedback" id="codigoError" role="alert"></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="responsable" class="font-weight-bold">Responsable</label>
                            <input type="text" name="responsable" id="responsable" class="form-control" 
                                   placeholder="Nombre responsable" value="{{ request('responsable') }}" 
                                   maxlength="50" oninput="validarYFiltrarResponsable(this)"
                                   onkeypress="return permitirCaracteresResponsable(event)"
                                   aria-describedby="responsableHelp responsableError">
                            <small id="responsableHelp" class="form-text text-muted">
                                Máximo 50 caracteres, solo letras y espacios. Máximo 3 letras iguales consecutivas.
                            </small>
                            <div class="invalid-feedback" id="responsableError" role="alert"></div>
                        </div>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100" id="btnFiltrar">
                            <i class="fas fa-filter mr-1" aria-hidden="true"></i> Filtrar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card card-elegant">
        <div class="card-header">
            <h2 class="card-title mb-0 h5"><i class="fas fa-list mr-2 text-muted" aria-hidden="true"></i> Procesos Asignados</h2>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-borderless" aria-describedby="tableDescription">
                    <caption class="sr-only">Lista de procesos asignados para pagos de terna</caption>
                    <thead class="thead-elegant">
                        <tr>
                            <th scope="col">
                                <a href="{{ route('terna.asistente.index', ['sort' => 'codigo', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}" 
                                   class="text-decoration-none">
                                    Código {!! request('sort') == 'codigo' ? (request('direction') == 'asc' ? '▲' : '▼') : '' !!}
                                </a>
                            </th>
                            <th scope="col">
                                <a href="{{ route('terna.asistente.index', ['sort' => 'descripcion', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}" 
                                   class="text-decoration-none">
                                    Descripción {!! request('sort') == 'descripcion' ? (request('direction') == 'asc' ? '▲' : '▼') : '' !!}
                                </a>
                            </th>
                            <th scope="col">
                                <a href="{{ route('terna.asistente.index', ['sort' => 'fecha_defensa', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}" 
                                   class="text-decoration-none">
                                    Fecha Defensa {!! request('sort') == 'fecha_defensa' ? (request('direction') == 'asc' ? '▲' : '▼') : '' !!}
                                </a>
                            </th>
                            <th scope="col">
                                <a href="{{ route('terna.asistente.index', ['sort' => 'fecha_envio_admin', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}" 
                                   class="text-decoration-none">
                                    Fecha Recepción {!! request('sort') == 'fecha_envio_admin' ? (request('direction') == 'asc' ? '▲' : '▼') : '' !!}
                                </a>
                            </th>
                            <th scope="col">
                                <a href="{{ route('terna.asistente.index', ['sort' => 'fecha_limite', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}" 
                                   class="text-decoration-none">
                                    Fecha Límite {!! request('sort') == 'fecha_limite' ? (request('direction') == 'asc' ? '▲' : '▼') : '' !!}
                                </a>
                            </th>
                            <th scope="col" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($procesos as $proceso)
                            <tr class="table-row">
                                <td class="font-weight-bold">{{ $proceso->codigo }}</td>
                                <td>{{ $proceso->descripcion }}</td>
                                <td>{{ $proceso->fecha_defensa->format('d/m/Y') }}</td>
                                <td>{{ $proceso->fecha_envio_admin->format('d/m/Y H:i') }}</td>
                                <td>
                                    {{ $proceso->fecha_limite->format('d/m/Y H:i') }}
                                    @if($proceso->fecha_limite < now())
                                        <span class="badge badge-danger ml-2" aria-label="Estado: Retrasado">Retrasado</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('terna.asistente.show', $proceso->id) }}" 
                                       class="btn btn-primary btn-elegant"
                                       aria-label="Completar proceso {{ $proceso->codigo }}">
                                        <i class="fas fa-upload mr-1" aria-hidden="true"></i> Completar
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="empty-state" role="status" aria-live="polite">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3" aria-hidden="true"></i>
                                        <h3 class="h5">No tienes procesos asignados</h3>
                                        <p class="text-muted">Actualmente no hay procesos pendientes de completar</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Paginación -->
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted" id="paginationInfo">
                    Mostrando {{ $procesos->firstItem() }} - {{ $procesos->lastItem() }} de {{ $procesos->total() }} registros
                </div>
                <nav aria-label="Navegación de paginación">
                    {{ $procesos->appends(request()->query())->links() }}
                </nav>
            </div>
            
            <div class="d-flex justify-content-start mt-4">
                <a href="{{ URL::previous() }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left mr-1" aria-hidden="true"></i> Volver
                </a>
            </div>
        </div>
    </div>
</div>

<script>
// Función para permitir solo caracteres válidos en código
function permitirCaracteresCodigo(event) {
    const charCode = event.which ? event.which : event.keyCode;
    const charStr = String.fromCharCode(charCode);
    
    // Permitir letras (A-Z, a-z), números (0-9) y guión (-)
    const regex = /^[A-Za-z0-9\-]$/;
    
    // Permitir teclas de control (backspace, delete, tab, etc.)
    if (charCode === 8 || charCode === 9 || charCode === 37 || charCode === 39) {
        return true;
    }
    
    if (!regex.test(charStr)) {
        event.preventDefault();
        mostrarErrorTemporal('codigoError', 'Solo se permiten letras, números y guión.');
        return false;
    }
    
    return true;
}

// Función para permitir solo caracteres válidos en responsable
function permitirCaracteresResponsable(event) {
    const charCode = event.which ? event.which : event.keyCode;
    const charStr = String.fromCharCode(charCode);
    
    // Permitir letras (con acentos), espacios y ñ
    const regex = /^[A-Za-záéíóúÁÉÍÓÚñÑ\s]$/;
    
    // Permitir teclas de control (backspace, delete, tab, etc.)
    if (charCode === 8 || charCode === 9 || charCode === 37 || charCode === 39) {
        return true;
    }
    
    if (!regex.test(charStr)) {
        event.preventDefault();
        mostrarErrorTemporal('responsableError', 'Solo se permiten letras y espacios.');
        return false;
    }
    
    return true;
}

// Función para validar y filtrar el campo de código en tiempo real
function validarYFiltrarCodigo(input) {
    let valor = input.value;
    const errorElement = document.getElementById('codigoError');
    
    // Remover clases de validación previas
    input.classList.remove('is-invalid', 'is-valid');
    errorElement.textContent = '';
    input.removeAttribute('aria-invalid');
    
    // Filtrar caracteres no permitidos (solo letras, números y guión)
    valor = valor.replace(/[^A-Za-z0-9\-]/g, '');
    
    // Validar que no haya más de 3 letras iguales consecutivas
    const regexRepetidas = /([A-Za-z])\1{3,}/g;
    if (regexRepetidas.test(valor)) {
        // Eliminar letras repetidas más allá de 3
        valor = valor.replace(regexRepetidas, (match) => {
            return match.substring(0, 3);
        });
    }
    
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
    
    // Validar longitud exacta de 12 caracteres
    if (valor.length !== 12) {
        input.classList.add('is-invalid');
        input.setAttribute('aria-invalid', 'true');
        errorElement.textContent = 'El código debe tener exactamente 12 caracteres.';
        return false;
    }
    
    // Si pasa todas las validaciones
    input.classList.add('is-valid');
    return true;
}

// Función para validar y filtrar el campo de responsable en tiempo real
function validarYFiltrarResponsable(input) {
    let valor = input.value;
    const errorElement = document.getElementById('responsableError');
    
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

// Función para mostrar errores temporales
function mostrarErrorTemporal(elementId, mensaje) {
    const errorElement = document.getElementById(elementId);
    errorElement.textContent = mensaje;
    errorElement.style.display = 'block';
    
    setTimeout(() => {
        errorElement.textContent = '';
        errorElement.style.display = 'none';
    }, 2000);
}

// Validar formulario antes de enviar
document.getElementById('filtrosForm').addEventListener('submit', function(e) {
    const codigoInput = document.getElementById('codigo');
    const responsableInput = document.getElementById('responsable');
    
    const codigoValido = validarYFiltrarCodigo(codigoInput);
    const responsableValido = validarYFiltrarResponsable(responsableInput);
    
    if (!codigoValido || !responsableValido) {
        e.preventDefault();
        
        // Enfocar el primer campo con error
        if (!codigoValido) {
            codigoInput.focus();
        } else if (!responsableValido) {
            responsableInput.focus();
        }
    }
});

// Aplicar validación inicial si hay valores en los campos
document.addEventListener('DOMContentLoaded', function() {
    const codigoInput = document.getElementById('codigo');
    const responsableInput = document.getElementById('responsable');
    
    if (codigoInput.value) {
        validarYFiltrarCodigo(codigoInput);
    }
    
    if (responsableInput.value) {
        validarYFiltrarResponsable(responsableInput);
    }
    
    // Mejorar accesibilidad de la paginación
    const paginationLinks = document.querySelectorAll('.pagination a');
    paginationLinks.forEach(link => {
        link.setAttribute('aria-label', `Página ${link.textContent.trim()}`);
    });
});
</script>
@stop

@section('css')
<style>
    body {
        background-color: #f8f9fc;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        line-height: 1.6;
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
        color: #ffffff;
    }
    
    .elegant-header .subtitle {
        font-size: 1rem;
        opacity: 0.9;
        color: #e6f2ff;
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
        color: #ffffff;
    }
    
    .btn-primary.btn-elegant:hover {
        background: linear-gradient(135deg, #2a6bc4, #00c2ef);
        color: #ffffff;
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
        color: #4a5568;
        font-weight: 600;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 12px 15px;
        background-color: #f8f9fc;
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
        color: #2d3748;
    }
    
    .table-row:first-child td:first-child {
        border-radius: 10px 0 0 10px;
    }
    
    .table-row:first-child td:last-child {
        border-radius: 0 10px 10px 0;
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
        color: #1b5e20;
        border: none;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
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
</style>
@stop
