@extends('adminlte::page')

@section('title', 'Permisos del Rol')

@section('content_header')
    <div class="unah-header">
        <h1 class="mb-0"><i class="fas fa-key mr-2"></i> Permisos del Rol: {{ $rol->nombre_rol }}</h1>
        <p class="mb-0">{{ $rol->descripcion_rol }}</p>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-shield-alt mr-2"></i> Configuración de Permisos</h3>
                <div class="card-tools">
                    <a href="{{ route('roles.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left mr-1"></i> Volver a Roles
                    </a>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('roles.actualizar-permisos', $rol->id_rol) }}" method="POST">
                    @csrf
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-1"></i>
                        <strong>Instrucciones:</strong> Marca las casillas correspondientes a los permisos que deseas asignar a este rol.
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="bg-primary">
                                <tr>
                                    <th style="width: 50%;">Objeto del Sistema</th>
                                    <th style="width: 15%;">Tipo</th>
                                    <th style="width: 35%;" class="text-center">Permisos</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($objetos as $objeto)
                                @php
                                    $acceso = $accesos->get($objeto->id_objeto);
                                @endphp
                                <tr>
                                    <td>
                                        <strong>{{ $objeto->nombre_objeto }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $objeto->descripcion_objeto }}</small>
                                    </td>
                                    <td>
                                        <span class="badge badge-info">{{ $objeto->tipo_objeto }}</span>
                                    </td>
                                    <td>
                                        <div class="row">
                                            <div class="col-3">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input permiso-checkbox" 
                                                           id="ver_{{ $objeto->id_objeto }}"
                                                           name="permisos[{{ $objeto->id_objeto }}][]" 
                                                           value="ver"
                                                           {{ $acceso && $acceso->permiso_ver ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="ver_{{ $objeto->id_objeto }}">
                                                        <i class="fas fa-eye text-primary"></i>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input permiso-checkbox" 
                                                           id="editar_{{ $objeto->id_objeto }}"
                                                           name="permisos[{{ $objeto->id_objeto }}][]" 
                                                           value="editar"
                                                           {{ $acceso && $acceso->permiso_editar ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="editar_{{ $objeto->id_objeto }}">
                                                        <i class="fas fa-edit text-warning"></i>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input permiso-checkbox" 
                                                           id="agregar_{{ $objeto->id_objeto }}"
                                                           name="permisos[{{ $objeto->id_objeto }}][]" 
                                                           value="agregar"
                                                           {{ $acceso && $acceso->permiso_agregar ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="agregar_{{ $objeto->id_objeto }}">
                                                        <i class="fas fa-plus text-success"></i>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                        <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input permiso-checkbox" 
                                                           id="eliminar_{{ $objeto->id_objeto }}"
                                                           name="permisos[{{ $objeto->id_objeto }}][]" 
                                                           value="eliminar"
                                                           {{ $acceso && $acceso->permiso_eliminar ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="eliminar_{{ $objeto->id_objeto }}">
                                                        <i class="fas fa-trash text-danger"></i>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                        No hay objetos del sistema configurados. 
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6><i class="fas fa-lightbulb mr-1"></i> Información de Permisos:</h6>
                                    <ul class="mb-0">
                                        <li><strong>Ver:</strong> Permite visualizar y navegar por la funcionalidad</li>
                                        <li><strong>Editar:</strong> Permite modificar registros existentes</li>
                                        <li><strong>Agregar:</strong> Permite crear nuevos registros</li>
                                        <li><strong>Eliminar:</strong> Permite eliminar registros</li>
                                        <li><strong>SuperAdmin:</strong> Siempre tiene todos los permisos</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6><i class="fas fa-cog mr-1"></i> Acciones:</h6>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save mr-1"></i> Guardar Permisos
                                    </button>
                                    <a href="{{ route('roles.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times mr-1"></i> Cancelar
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .unah-header {
        background: linear-gradient(135deg, #0b2e59, #1a5a8d);
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        color: white;
        margin-bottom: 25px;
        }
        .custom-control-label {
            font-size: 0.85rem;
        }
        .custom-control-label i {
            margin-right: 0.25rem;
        }
    </style>
@stop

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @foreach($objetos as $objeto)
            const verCheckbox_{{ $objeto->id_objeto }} = document.getElementById('ver_{{ $objeto->id_objeto }}');
            const editarCheckbox_{{ $objeto->id_objeto }} = document.getElementById('editar_{{ $objeto->id_objeto }}');
            const agregarCheckbox_{{ $objeto->id_objeto }} = document.getElementById('agregar_{{ $objeto->id_objeto }}');
            const eliminarCheckbox_{{ $objeto->id_objeto }} = document.getElementById('eliminar_{{ $objeto->id_objeto }}');
            if(verCheckbox_{{ $objeto->id_objeto }}) {
                verCheckbox_{{ $objeto->id_objeto }}.addEventListener('change', function() {
                    if(!this.checked) {
                        if(editarCheckbox_{{ $objeto->id_objeto }}) editarCheckbox_{{ $objeto->id_objeto }}.checked = false;
                        if(agregarCheckbox_{{ $objeto->id_objeto }}) agregarCheckbox_{{ $objeto->id_objeto }}.checked = false;
                        if(eliminarCheckbox_{{ $objeto->id_objeto }}) eliminarCheckbox_{{ $objeto->id_objeto }}.checked = false;
                    }
                });
            }
        @endforeach
    });
</script>
@endsection 
