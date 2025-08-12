{{-- tabla.blade.php --}}
@php use Carbon\Carbon; @endphp
@forelse($tareas as $tarea)
    <tr class="table-row" data-id="{{ $tarea->id_tarea }}">
        <td class="font-weight-bold">T-{{ str_pad($tarea->id_tarea, 5, '0', STR_PAD_LEFT) }}</td>
        <td>{{ $tarea->nombre }}</td>
        <td>
            <div class="d-flex align-items-center">
                <div class="avatar-sm mr-2">
                    <div class="avatar-initials bg-info text-white">
                        {{ substr($tarea->usuarioAsignado->nombres ?? 'N', 0, 1) }}{{ substr($tarea->usuarioAsignado->apellidos ?? 'A', 0, 1) }}
                    </div>
                </div>
                <div>
                    {{ $tarea->usuarioAsignado ? $tarea->usuarioAsignado->nombres . ' ' . $tarea->usuarioAsignado->apellidos : 'Sin asignar' }}
                </div>
            </div>
        </td>
        <td>
            @php
                $badge = [
                    'Pendiente' => 'badge-warning',
                    'En Proceso' => 'badge-info',
                    'Completada' => 'badge-success',
                    'Rechazada' => 'badge-primary'
                ][$tarea->estado] ?? 'badge-secondary';
            @endphp
            <span class="badge {{ $badge }}">{{ $tarea->estado }}</span>
        </td>
        <td>{{ Carbon::parse($tarea->fecha_creacion)->format('d/m/Y') }}</td>
        <td>
            @if($tarea->documentos->count())
                <div class="d-flex">
                    @foreach($tarea->documentos as $doc)
                        @php
                            $ext = strtolower(pathinfo($doc->nombre_documento, PATHINFO_EXTENSION));
                            $isImage = in_array($ext, ['jpg','jpeg','png','gif','bmp','webp']);
                            $isPdf = $ext === 'pdf';
                        @endphp
                        <a href="#" class="ver-documento mr-2"
                           data-toggle="tooltip" title="{{ $doc->nombre_documento }}"
                           data-url="{{ asset('storage/' . $doc->ruta_archivo) }}"
                           data-tipo="{{ $isImage ? 'imagen' : ($isPdf ? 'pdf' : 'otro') }}"
                           data-nombre="{{ $doc->nombre_documento }}"
                           data-id="{{ $doc->id_documento }}">
                            @if($isImage)
                                <i class="fas fa-file-image text-info" style="font-size:1.5rem"></i>
                            @elseif($isPdf)
                                <i class="fas fa-file-pdf text-danger" style="font-size:1.5rem"></i>
                            @else
                                <i class="fas fa-file text-secondary" style="font-size:1.5rem"></i>
                            @endif
                        </a>
                    @endforeach
                </div>
            @else
                <i class="fas fa-ban text-muted" style="font-size:1.5rem"></i>
            @endif
        </td>
        <td class="text-center">
            @php
                $user = Auth::user();
            @endphp
            <!-- Ver detalles -->
            <button class="btn btn-sm btn-action" title="Ver detalles"
                data-toggle="modal"
                data-target="#modalDetalleTarea"
                data-id="{{ $tarea->id_tarea }}"
                data-nombre="{{ $tarea->nombre }}"
                data-responsable="{{ $tarea->usuarioAsignado ? $tarea->usuarioAsignado->nombres . ' ' . $tarea->usuarioAsignado->apellidos : 'Sin asignar' }}"
                data-estado="{{ $tarea->estado }}"
                data-fecha="{{ Carbon::parse($tarea->fecha_creacion)->format('d/m/Y') }}"
                data-vencimiento="{{ $tarea->fecha_vencimiento ? Carbon::parse($tarea->fecha_vencimiento)->format('d/m/Y') : '' }}"
                data-descripcion="{{ $tarea->descripcion }}"
                data-documentos="{{ $tarea->documentos->map(function($doc) {
                    $ext = strtolower(pathinfo($doc->nombre_documento, PATHINFO_EXTENSION));
                    return [
                        'id' => $doc->id_documento,
                        'nombre' => $doc->nombre_documento,
                        'url' => asset('storage/' . $doc->ruta_archivo),
                        'tipo' => in_array($ext, ['jpg','jpeg','png','gif','bmp','webp']) ? 'imagen' : ($ext === 'pdf' ? 'pdf' : 'otro'),
                        'tipo_documento' => $doc->tipo->nombre_tipo ?? 'Sin tipo'
                    ];
                })->toJson() }}">
                <i class="fas fa-eye"></i>
            </button>
            
            <!-- Editar -->
            @if($user->puedeEditar('TareasDocumentales'))
                <button class="btn btn-sm btn-action" title="Editar"
                    data-toggle="modal"
                    data-target="#modalNuevaTarea"
                    data-id="{{ $tarea->id_tarea }}"
                    data-nombre="{{ $tarea->nombre }}"
                    data-responsable="{{ $tarea->fk_id_usuario_asignado }}"
                    data-fecha="{{ $tarea->fecha_creacion }}"
                    data-vencimiento="{{ $tarea->fecha_vencimiento }}"
                    data-descripcion="{{ $tarea->descripcion }}">
                    <i class="fas fa-edit"></i>
                </button>
            @endif
            
            <!-- Eliminar -->
            @if($user->puedeEliminar('TareasDocumentales'))
                <form action="{{ route('tareas.destroy', $tarea->id_tarea) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-action" title="Eliminar" onclick="return confirm('¿Está seguro de eliminar esta tarea?')">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </form>
            @endif

            <!-- Cambiar estado -->
            @if($tarea->fk_id_usuario_asignado == auth()->id() || auth()->user()->tieneRol('SuperAdmin'))
                <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-action dropdown-toggle" data-toggle="dropdown" title="Cambiar estado">
                        <i class="fas fa-exchange-alt"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item cambiar-estado" href="#" data-estado="Pendiente">Pendiente</a>
                        <a class="dropdown-item cambiar-estado" href="#" data-estado="En Proceso">En Proceso</a>
                        <a class="dropdown-item cambiar-estado" href="#" data-estado="Completada">Completada</a>
                        <a class="dropdown-item cambiar-estado" href="#" data-estado="Rechazada">Rechazada</a>
                    </div>
                </div>
            @endif

            <!-- Delegar tarea -->
            @if($tarea->fk_id_usuario_asignado == auth()->id() || auth()->user()->tieneRol('SuperAdmin'))
                <button class="btn btn-sm btn-action" title="Delegar tarea" data-toggle="modal" data-target="#modalDelegarTarea" data-id="{{ $tarea->id_tarea }}">
                    <i class="fas fa-user-friends"></i>
                </button>
            @endif
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="text-center py-5">
            <div class="empty-state">
                <i class="fas fa-tasks fa-3x text-muted mb-3"></i>
                <h5>No se encontraron tareas documentales</h5>
                <p class="text-muted">Parece que aún no hay tareas registradas en el sistema</p>
                @if(Auth::user()->puedeAgregar('TareasDocumentales'))
                <button type="button" class="btn btn-primary mt-2" data-toggle="modal" data-target="#modalNuevaTarea">
                    <i class="fas fa-plus mr-1"></i> Crear primera tarea
                </button>
                @endif
            </div>
        </td>
    </tr>
@endforelse