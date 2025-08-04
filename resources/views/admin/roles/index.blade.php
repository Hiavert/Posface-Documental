@extends('adminlte::page')

@section('title', 'Gestión de Roles')

@section('content_header')
    <div class="unah-header">
        <h1 class="mb-0"><i class="fas fa-user-tag mr-2"></i> Gestión de Roles</h1>
        <p class="mb-0">Universidad Nacional Autónoma de Honduras - Posgrado en Informática Administrativa</p>
    </div>
    @if (session('success'))
        <div class="alert alert-success" id="successAlert">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger" id="errorAlert">
            {{ session('error') }}
        </div>
    @endif
@stop

@section('content')
    <div class="container-fluid">
        <!-- Botones de acción -->
        <div class="row mb-3">
            <div class="col-md-12">
                @if(Auth::user()->puedeAgregar('Roles'))
                    <a href="{{ route('roles.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus mr-1"></i> Nuevo Rol
                    </a>
                @endif
            </div>
        </div>

        <!-- Listado de roles -->
        <div class="card">
            <div class="card-header card-header-unah">
                <h3 class="card-title"><i class="fas fa-list mr-2"></i> Roles del Sistema</h3>
                <div class="card-tools">
                    <div class="input-group input-group-sm" style="width: 200px;">
                        <input type="text" name="table_search" id="table_search" class="form-control float-right" placeholder="Buscar roles..." autocomplete="off" onkeyup="filterRoles()">
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover text-nowrap">
                        <thead class="bg-light">
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Estado</th>
                                <th>Usuarios Asignados</th>
                                <th>Permisos</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($roles as $rol)
                            <tr>
                                <td>{{ $rol->id_rol }}</td>
                                <td>
                                    <strong>{{ $rol->nombre_rol }}</strong>
                                </td>
                                <td>{{ $rol->descripcion_rol ?? 'Sin descripción' }}</td>
                                <td>
                                    @if($rol->estado_rol == '1')
                                        <span class="badge badge-success">Activo</span>
                                    @else
                                        <span class="badge badge-danger">Inactivo</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-info">{{ $rol->usuarios->count() }} usuarios</span>
                                </td>
                                <td>
                                    <span class="badge badge-secondary">{{ $rol->accesos->count() }} permisos</span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        @if(Auth::user()->puedeEditar('Roles'))
                                            <a href="{{ route('roles.edit', $rol->id_rol) }}" 
                                               class="btn btn-sm btn-outline-primary" 
                                               title="Editar rol">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('roles.permisos', $rol->id_rol) }}" 
                                               class="btn btn-sm btn-outline-info" 
                                               title="Gestionar permisos">
                                                <i class="fas fa-key"></i>
                                            </a>
                                        @endif
                                        @if(Auth::user()->puedeEditar('Roles') && $rol->usuarios->count() == 0)
                                            @if($rol->estado_rol == '1')
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-warning" 
                                                        onclick="cambiarEstado({{ $rol->id_rol }}, '0')"
                                                        title="Desactivar rol">
                                                    <i class="fas fa-ban"></i>
                                                </button>
                                            @else
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-success" 
                                                        onclick="cambiarEstado({{ $rol->id_rol }}, '1')"
                                                        title="Activar rol">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            @endif
                                        @endif
                                        @if(Auth::user()->puedeEliminar('Roles') && $rol->usuarios->count() == 0)
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-danger" 
                                                    onclick="eliminarRol({{ $rol->id_rol }})"
                                                    title="Eliminar rol">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    No hay roles registrados
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulario oculto para cambiar estado -->
    <form id="formCambiarEstado" method="POST" style="display: none;">
        @csrf
        @method('PUT')
        <input type="hidden" name="estado_rol" id="estadoRol">
    </form>

    <!-- Formulario oculto para eliminar -->
    <form id="formEliminar" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
@stop

@section('js')
<script>
    function filterRoles() {
        let input = document.getElementById("table_search");
        let filter = input.value.toLowerCase();
        let table = document.querySelector(".table-responsive table");
        let tr = table ? table.getElementsByTagName("tr") : [];
        
        for (let i = 1; i < tr.length; i++) {
            let visible = false;
            let td = tr[i].getElementsByTagName("td");
            if (td.length > 0) {
                for (let j = 0; j < td.length - 2; j++) { // -2 para no buscar en permisos y acciones
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

    function cambiarEstado(id, estado) {
        const accion = estado == '1' ? 'activar' : 'desactivar';
        if (confirm(`¿Estás seguro de que deseas ${accion} este rol?`)) {
            const form = document.getElementById('formCambiarEstado');
            form.action = `/roles/${id}/estado`;
            document.getElementById('estadoRol').value = estado;
            form.submit();
        }
    }

    function eliminarRol(id) {
        if (confirm('¿Estás seguro de que deseas eliminar este rol? Esta acción no se puede deshacer.')) {
            const form = document.getElementById('formEliminar');
            form.action = `/roles/${id}`;
            form.submit();
        }
    }

    $(document).ready(function() {
        // Ocultar notificaciones después de 3 segundos
        setTimeout(function() {
            $('#successAlert, #errorAlert').fadeOut('slow');
        }, 3000);
    });
</script>
@stop 
<style>
    .unah-header {
        background: linear-gradient(135deg, #0b2e59, #1a5a8d);
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        color: white;
        margin-bottom: 25px;
    }
</style>