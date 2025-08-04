@extends('adminlte::page')

@section('title', 'Gestión de Usuarios')

@section('content_header')
    <div class="elegant-header">
        <div class="d-flex align-items-center">
            <div>
                <h1 class="mb-0"><i class="bi bi-people mr-2"></i> Gestión de Usuarios</h1>
                <p class="mb-0">Universidad Nacional Autónoma de Honduras - Posgrado en Informática Administrativa</p>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        @if(Auth::user()->puedeAgregar('Usuarios'))
            <a href="{{ route('usuarios.create') }}" class="btn btn-primary mb-4">
                <i class="bi bi-plus-circle mr-1"></i> Nuevo Usuario
            </a>
        @endif
        
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="bi bi-check-circle-fill mr-2"></i> {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        
        <div class="card card-elegant">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="bi bi-people mr-2"></i> Lista de Usuarios</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="thead-elegant">
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Usuario</th>
                                <th>Email</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($usuarios as $usuario)
                                <tr>
                                    <td>{{ $usuario->id_usuario }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle mr-3">
                                                <span>{{ substr($usuario->nombres, 0, 1) }}{{ substr($usuario->apellidos, 0, 1) }}</span>
                                            </div>
                                            <div>
                                                <strong>{{ $usuario->nombres }} {{ $usuario->apellidos }}</strong>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $usuario->usuario }}</td>
                                    <td>{{ $usuario->email }}</td>
                                    <td>
                                        @if($usuario->estado)
                                            <span class="badge bg-success">Activo</span>
                                        @else
                                            <span class="badge bg-danger">Inactivo</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex" style="gap: 8px;">
                                            @if(Auth::user()->puedeEditar('Usuarios'))
                                                <a href="{{ route('usuarios.edit', $usuario->id_usuario) }}" class="btn btn-sm btn-outline-primary" title="Editar">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                            @endif
                                            @if(Auth::user()->puedeEliminar('Usuarios'))
                                                <form action="{{ route('usuarios.cambiar-estado', $usuario->id_usuario) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="btn btn-sm btn-outline-secondary" title="{{ $usuario->estado ? 'Inactivar' : 'Activar' }}">
                                                        <i class="bi bi-{{ $usuario->estado ? 'power' : 'arrow-clockwise' }}"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('usuarios.destroy', $usuario->id_usuario) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                        onclick="return confirm('¿Estás seguro de eliminar este usuario? Esta acción no se puede deshacer.')"
                                                        title="Eliminar">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @if($usuarios->hasPages())
            <div class="card-footer clearfix">
                <div class="float-right">
                    {{ $usuarios->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>
@stop 

@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
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
        
        /* Tarjeta */
        .card-elegant {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 25px;
            overflow: hidden;
        }
        
        .card-header {
            background-color: white;
            border-bottom: 1px solid #eaeef5;
            border-radius: 10px 10px 0 0 !important;
            padding: 15px 20px;
        }
        
        .card-title {
            font-weight: 600;
            color: #2c3e50;
            font-size: 1.1rem;
            margin-bottom: 0;
        }
        
        /* Tabla */
        .table {
            border-collapse: separate;
            border-spacing: 0;
            margin-bottom: 0;
        }
        
        .thead-elegant {
            background-color: #f8f9fc;
        }
        
        .thead-elegant th {
            font-weight: 600;
            color: #0b2e59;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            padding: 15px;
            border-bottom: 1px solid #eaeef5;
        }
        
        .table td .btn-sm {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            padding: 0;
        }

        .table td .btn-sm i {
            font-size: 1rem;
        }
        
        .table-hover tbody tr:hover {
            background-color: #f8f9fc;
        }
        
        /* Avatar */
        .avatar-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #0b2e59;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 1rem;
        }
        
        /* Botones de acción */
        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
            border-radius: 4px;
        }
        
        .btn-outline-primary {
            border-color: #0b2e59;
            color: #0b2e59;
        }
        
        .btn-outline-primary:hover {
            background-color: #0b2e59;
            color: white;
        }
        
        .btn-outline-secondary {
            border-color: #6c757d;
            color: #6c757d;
        }
        
        .btn-outline-secondary:hover {
            background-color: #6c757d;
            color: white;
        }
        
        .btn-outline-danger {
            border-color: #dc3545;
            color: #dc3545;
        }
        
        .btn-outline-danger:hover {
            background-color: #dc3545;
            color: white;
        }
        
        /* Paginación */
        .pagination .page-link {
            border-radius: 8px;
            margin: 0 3px;
            color: #0b2e59;
            border: 1px solid #eaeef5;
        }
        
        .pagination .page-item.active .page-link {
            background: linear-gradient(135deg, #0b2e59, #1a5a8d);
            border-color: #0b2e59;
            color: white;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .d-flex {
                flex-wrap: wrap;
                gap: 5px !important;
            }
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Inicializar tooltips
            $('[title]').tooltip({
                placement: 'top',
                trigger: 'hover'
            });
        });
    </script>
@stop