@php use Carbon\Carbon; @endphp
@forelse($tareas as $tarea)
    <tr>
        <td>{{ $tarea->id_tarea }}</td>
        <td>{{ $tarea->nombre }}</td>
        <td>
            {{ $tarea->usuarioAsignado ? $tarea->usuarioAsignado->nombres . ' ' . $tarea->usuarioAsignado->apellidos : 'Sin asignar' }}
        </td>
        <td>
            @php
                $badge = [
                    'Pendiente' => 'bg-warning',
                    'En Proceso' => 'bg-info',
                    'Completada' => 'bg-success',
                    'Rechazada' => 'bg-primary'
                ][$tarea->estado] ?? 'bg-secondary';
            @endphp
            <span class="badge {{ $badge }}">{{ $tarea->estado }}</span>
        </td>
        <td>{{ Carbon::parse($tarea->fecha_creacion)->format('d/m/Y') }}</td>
        <td>
            @if($tarea->documentos->count())
                @foreach($tarea->documentos as $doc)
                    @php
                        $ext = strtolower(pathinfo($doc->nombre_documento, PATHINFO_EXTENSION));
                        $isImage = in_array($ext, ['jpg','jpeg','png','gif','bmp','webp']);
                        $isPdf = $ext === 'pdf';
                    @endphp
                    <a href="#"
                       class="ver-documento"
                       data-toggle="modal"
                       data-target="#modalVerDocumento"
                       data-url="{{ asset('storage/' . $doc->ruta_archivo) }}"
                       data-tipo="{{ $isImage ? 'imagen' : ($isPdf ? 'pdf' : 'otro') }}"
                       data-nombre="{{ $doc->nombre_documento }}"
                       data-id="{{ $doc->id_documento }}">
                        @if($isImage)
                            <i class="fas fa-file-image text-info" style="font-size:2rem"></i>
                        @elseif($isPdf)
                            <i class="fas fa-file-pdf text-danger" style="font-size:2rem"></i>
                        @else
                            <i class="fas fa-file text-secondary" style="font-size:2rem"></i>
                        @endif
                    </a>
                @endforeach
            @else
                <i class="fas fa-ban text-muted" style="font-size:2rem"></i>
            @endif
        </td>
        <td>
            @if($tarea->documentos->count())
                @foreach($tarea->documentos as $doc)
                    <span class="badge badge-info">{{ $doc->tipo->nombre_tipo ?? 'Sin tipo' }}</span><br>
                @endforeach
            @else
                <span class="text-muted">Sin documento</span>
            @endif
        </td>
        <td>
            @php
                $user = Auth::user();
            @endphp
            <!-- Ver detalles -->
            @if($user->puedeVer('TareasDocumentales'))
                <button class="btn btn-sm btn-outline-primary" title="Ver detalles"
                    data-toggle="modal"
                    data-target="#modalDetalleTarea"
                    data-id="{{ $tarea->id_tarea }}"
                    data-nombre="{{ $tarea->nombre }}"
                    data-responsable="{{ $tarea->usuarioAsignado ? $tarea->usuarioAsignado->nombres . ' ' . $tarea->usuarioAsignado->apellidos : 'Sin asignar' }}"
                    data-estado="{{ $tarea->estado }}"
                    data-fecha="{{ $tarea->fecha_creacion }}"
                    data-descripcion="{{ $tarea->descripcion }}">
                    <i class="fas fa-eye"></i>
                </button>
            @endif
            <!-- Editar -->
            @if($user->puedeEditar('TareasDocumentales'))
                <button class="btn btn-sm btn-outline-warning" title="Editar"
                    data-toggle="modal"
                    data-target="#modalNuevaTarea"
                    data-id="{{ $tarea->id_tarea }}"
                    data-nombre="{{ $tarea->nombre }}"
                    data-responsable="{{ $tarea->fk_id_usuario_asignado }}"
                    data-estado="{{ $tarea->estado }}"
                    data-fecha="{{ $tarea->fecha_creacion }}"
                    data-descripcion="{{ $tarea->descripcion }}"
                    data-editar="1">
                    <i class="fas fa-edit"></i>
                </button>
            @endif
            <!-- Cargar Documentos -->
            @if($user->puedeAgregar('TareasDocumentales'))
                <button class="btn btn-sm btn-outline-success" title="Cargar Documentos"
                    data-toggle="modal"
                    data-target="#modalCargarDocumento"
                    data-id="{{ $tarea->id_tarea }}">
                    <i class="fas fa-upload"></i>
                </button>
            @endif
            <!-- Eliminar -->
            @if($user->puedeEliminar('TareasDocumentales'))
                <form action="{{ route('tareas.destroy', $tarea->id_tarea) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger" title="Eliminar" onclick="return confirm('¿Está seguro de eliminar esta tarea?')">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
            @endif
        </td>
    </tr>
@empty
    <tr>
        <td colspan="8" class="text-center">No hay tareas registradas.</td>
    </tr>
@endforelse
