@extends('adminlte::page')

@section('title', 'Crear Proceso de Pago Terna')

@section('content_header')
<div class="elegant-header">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h1 class="mb-0"><i class="fas fa-plus-circle mr-2 text-primary"></i> Nuevo Proceso de Pago</h1>
            <p class="subtitle">Administración de pagos de terna</p>
        </div>
        <div class="header-icon ml-3">
            <i class="fas fa-file-invoice-dollar"></i>
        </div>
    </div>
</div>
@stop

@section('content')
<div class="container-fluid">
    <div class="card card-elegant">
        <div class="card-header">
            <h5 class="card-title mb-0">Información del Proceso</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('terna.admin.store') }}" method="POST" enctype="multipart/form-data" id="pago-form">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Descripción</label>
                            <input type="text" class="form-control" name="descripcion" 
                                maxlength="100"
                                oninput="sanitizeTitulo(this)"
                                required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Fecha de Defensa</label>
                            <input type="date" class="form-control" name="fecha_defensa" 
                                min="{{ now()->toDateString() }}" 
                                required>
                            <small class="text-muted">Debe ser igual o posterior a hoy</small>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Responsable</label>
                            <input type="text" class="form-control" name="responsable"
                                maxlength="50"
                                oninput="sanitizeAutor(this)"
                                required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Fecha Límite</label>
                            <input type="datetime-local" class="form-control" name="fecha_limite" 
                                min="{{ now()->format('Y-m-d\TH:i') }}" 
                                required>
                            <small class="text-muted">Debe ser igual o posterior a hoy</small>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Asistente Asignado</label>
                            <select class="form-control" name="id_asistente" required>
                                <option value="">Seleccionar asistente</option>
                                @foreach($asistentes as $asistente)
                                    <option value="{{ $asistente->id_usuario }}">
                                        {{ $asistente->nombres }} {{ $asistente->apellidos }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <h5><i class="fas fa-user-graduate mr-2"></i> Información del Estudiante</h5>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Nombre Completo</label>
                                <input type="text" class="form-control" name="estudiante_nombre" 
                                    maxlength="50"
                                    oninput="sanitizeAutor(this)"
                                    required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Número de Cuenta</label>
                                <input type="text" class="form-control" name="estudiante_cuenta" 
                                    pattern="[0-9]{1,20}"
                                    title="Solo números (máximo 20 dígitos)"
                                    maxlength="20"
                                    oninput="sanitizeNumeroCuenta(this)"
                                    required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Carrera</label>
                                <input type="text" class="form-control" name="estudiante_carrera" 
                                    pattern="[a-zA-Z\sáéíóúÁÉÍÓÚñÑ]+"
                                    title="Solo letras y espacios"
                                    required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <h5><i class="fas fa-users mr-2"></i> Integrantes de Terna</h5>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Metodólogo</label>
                                <select class="form-control" name="metodologo_id" required>
                                    <option value="">Seleccionar metodólogo</option>
                                    @foreach($integrantes as $integrante)
                                        <option value="{{ $integrante->id }}">
                                            {{ $integrante->nombre }} - {{ $integrante->cuenta }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Técnico 1</label>
                                <select class="form-control" name="tecnico1_id" required>
                                    <option value="">Seleccionar técnico</option>
                                    @foreach($integrantes as $integrante)
                                        <option value="{{ $integrante->id }}">
                                            {{ $integrante->nombre }} - {{ $integrante->cuenta }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Técnico 2</label>
                                <select class="form-control" name="tecnico2_id" required>
                                    <option value="">Seleccionar técnico</option>
                                    @foreach($integrantes as $integrante)
                                        <option value="{{ $integrante->id }}">
                                            {{ $integrante->nombre }} - {{ $integrante->cuenta }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="text-right mb-3">
                        <button type="button" class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#modalAgregarIntegrante">
                            <i class="fas fa-plus mr-1"></i> Agregar Nuevo Integrante
                        </button>
                    </div>
                </div>

                <div class="mt-4">
                    <h5><i class="fas fa-file-pdf mr-2"></i> Documentos Requeridos</h5>
                    
                    <div class="document-section mb-4">
                        <h6 class="text-muted">Documento Físico</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" name="documento_fisico" id="documento_fisico">
                                    <label class="custom-file-label" for="documento_fisico">Subir PDF</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="text" class="form-control" name="documento_fisico_enlace" placeholder="O ingresar enlace">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="document-section mb-4">
                        <h6 class="text-muted">Solvencia de Cobranza</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" name="solvencia_cobranza" id="solvencia_cobranza">
                                    <label class="custom-file-label" for="solvencia_cobranza">Subir PDF</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="text" class="form-control" name="solvencia_cobranza_enlace" placeholder="O ingresar enlace">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="document-section mb-4">
                        <h6 class="text-muted">Acta de Graduación</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" name="acta_graduacion" id="acta_graduacion">
                                    <label class="custom-file-label" for="acta_graduacion">Subir PDF</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="text" class="form-control" name="acta_graduacion_enlace" placeholder="O ingresar enlace">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('terna.admin.index') }}" class="btn btn-outline-secondary btn-elegant mr-2">
                        <i class="fas fa-times mr-1"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary btn-elegant">
                        <i class="fas fa-paper-plane mr-1"></i> Enviar al Asistente
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalAgregarIntegrante" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Agregar Nuevo Integrante</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Nombre Completo</label>
                    <input type="text" class="form-control" id="integrante_nombre" 
                        maxlength="50"
                        oninput="sanitizeAutor(this)">
                </div>
                <div class="form-group">
                    <label>Número de Cuenta</label>
                    <input type="text" class="form-control" id="integrante_cuenta" 
                        pattern="[0-9]{1,20}"
                        title="Solo números (máximo 20 dígitos)"
                        maxlength="20"
                        oninput="sanitizeNumeroCuenta(this)">
                </div>
                <div class="form-group">
                    <label>Identidad (PDF opcional)</label>
                    <input type="file" class="form-control" id="integrante_identidad">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnGuardarIntegrante">Guardar</button>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    /* Estilos copiados de la plantilla */
    body {
        background-color: #f8f9fc;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
    }
    
    .elegant-header .subtitle {
        font-size: 1rem;
        opacity: 0.85;
    }
    
    .elegant-header .header-icon {
        font-size: 2.5rem;
        opacity: 0.9;
    }

</style>
@stop

@section('js')
<script>
    // Función para sanitizar campos de título y descripción
    function sanitizeTitulo(input) {
        let value = input.value;
        
        // 1. Eliminar caracteres no permitidos (solo guiones, puntos y comas)
        value = value.replace(/[^a-zA-Z0-9\sáéíóúÁÉÍÓÚñÑ\-.,]/g, '');
        
        // 2. Eliminar repeticiones de letras más de 3 veces consecutivas
        value = value.replace(/(.)\1{3,}/g, '$1$1$1');
        
        // 3. Limitar a 100 caracteres
        if (value.length > 100) {
            value = value.substring(0, 100);
        }
        
        input.value = value;
    }

    // Función para sanitizar campos de autor
    function sanitizeAutor(input) {
        let value = input.value;
        
        // 1. Eliminar caracteres no permitidos (solo guiones, puntos y comas)
        value = value.replace(/[^a-zA-Z0-9\sáéíóúÁÉÍÓÚñÑ\-.,]/g, '');
        
        // 2. Eliminar repeticiones de letras más de 3 veces consecutivas
        value = value.replace(/(.)\1{3,}/g, '$1$1$1');
        
        // 3. Limitar a 50 caracteres
        if (value.length > 50) {
            value = value.substring(0, 50);
        }
        
        input.value = value;
    }

    // Función para sanitizar número de cuenta
    function sanitizeNumeroCuenta(input) {
        // Solo permitir números y limitar a 20 dígitos
        input.value = input.value.replace(/\D/g, '').substring(0, 20);
    }

    $(document).ready(function() {
        $('.custom-file-input').on('change', function() {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass("selected").html(fileName);
        });
        
        $('form').submit(function() {
            $(this).find('button[type="submit"]').prop('disabled', true);
        });
        
        $('input[name="fecha_defensa"]').attr('min', new Date().toISOString().split('T')[0]);
        
        const now = new Date();
        const timezoneOffset = now.getTimezoneOffset() * 60000;
        const localISOTime = new Date(now - timezoneOffset).toISOString().slice(0, 16);
        $('input[name="fecha_limite"]').attr('min', localISOTime);

        $('#btnGuardarIntegrante').click(function() {
            const formData = new FormData();
            formData.append('nombre', $('#integrante_nombre').val());
            formData.append('cuenta', $('#integrante_cuenta').val());
            
            if ($('#integrante_identidad')[0].files[0]) {
                formData.append('identidad', $('#integrante_identidad')[0].files[0]);
            }
            
            $.ajax({
                url: "{{ route('terna.integrantes.store') }}",
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $('select[name="metodologo_id"], select[name="tecnico1_id"], select[name="tecnico2_id"]').append(
                        `<option value="${response.id}">${response.nombre} - ${response.cuenta}</option>`
                    );
                    
                    $('#modalAgregarIntegrante').modal('hide');
                    resetModal();
                    toastr.success('Integrante agregado correctamente');
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON.message || 'Error al agregar integrante');
                }
            });
        });

        function resetModal() {
            $('#integrante_nombre').val('');
            $('#integrante_cuenta').val('');
            $('#integrante_identidad').val('');
        }
    });
</script>
@stop
