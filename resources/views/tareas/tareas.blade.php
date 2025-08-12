<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Gestión de Tareas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 2rem;
            background-color: #f4f6f8;
            color: #333;
        }
        h1 {
            text-align: center;
            margin-bottom: 1.5rem;
            color: #2c3e50;
        }
        form.filtros {
            margin-bottom: 1.5rem;
            background: white;
            padding: 1rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgb(0 0 0 / 0.1);
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            justify-content: center;
        }
        form.filtros label {
            font-weight: bold;
            margin-right: 0.3rem;
        }
        form.filtros input,
        form.filtros select {
            padding: 0.3rem 0.5rem;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 6px rgb(0 0 0 / 0.1);
        }
        thead {
            background-color: #2980b9;
            color: white;
        }
        thead th {
            padding: 0.75rem;
            text-align: left;
        }
        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tbody td {
            padding: 0.6rem 0.8rem;
            vertical-align: middle;
            border-bottom: 1px solid #ddd;
        }
        tbody td.estado-Pendiente {
            color: #d35400;
            font-weight: 600;
        }
        tbody td.estado-En\ Proceso {
            color: #2980b9;
            font-weight: 600;
        }
        tbody td.estado-Completada {
            color: #27ae60;
            font-weight: 600;
        }
        tbody td.estado-Rechazada {
            color: #c0392b;
            font-weight: 600;
        }
        .acciones a,
        .acciones form button {
            margin-right: 0.5rem;
            padding: 0.3rem 0.6rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9rem;
            text-decoration: none;
            color: white;
        }
        .acciones a.ver {
            background-color: #3498db;
        }
        .acciones a.editar {
            background-color: #f39c12;
        }
        .acciones form button.eliminar {
            background-color: #e74c3c;
        }
        .success-message {
            background-color: #2ecc71;
            color: white;
            padding: 0.8rem 1rem;
            border-radius: 6px;
            margin-bottom: 1rem;
            max-width: 400px;
            margin-left: auto;
            margin-right: auto;
            text-align: center;
        }
    </style>
</head>
<body>
    <h1>Gestión de Tareas</h1>

    @if(session('success'))
        <div class="success-message">{{ session('success') }}</div>
    @endif

    <form method="GET" action="{{ route('tareas.index') }}" class="filtros">
        <label for="estado">Estado:</label>
        <select name="estado" id="estado">
            <option value="">Todos</option>
            <option value="Pendiente" @if(request('estado') == 'Pendiente') selected @endif>Pendiente</option>
            <option value="En Proceso" @if(request('estado') == 'En Proceso') selected @endif>En Proceso</option>
            <option value="Completada" @if(request('estado') == 'Completada') selected @endif>Completada</option>
            <option value="Rechazada" @if(request('estado') == 'Rechazada') selected @endif>Rechazada</option>
        </select>

        <label for="responsable">Responsable:</label>
        <select name="responsable" id="responsable">
            <option value="">Todos</option>
            @foreach($responsables as $responsable)
                <option value="{{ $responsable->id_usuario }}" @if(request('responsable') == $responsable->id_usuario) selected @endif>
                    {{ $responsable->nombre }}
                </option>
            @endforeach
        </select>

        <label for="fecha_inicio">Fecha Inicio:</label>
        <input type="date" id="fecha_inicio" name="fecha_inicio" value="{{ request('fecha_inicio') }}"/>

        <label for="fecha_fin">Fecha Fin:</label>
        <input type="date" id="fecha_fin" name="fecha_fin" value="{{ request('fecha_fin') }}"/>

        <label for="tipo_documento">Tipo Documento:</label>
        <select name="tipo_documento" id="tipo_documento">
            <option value="">Todos</option>
            @foreach($tiposDocumento as $tipo)
                <option value="{{ $tipo->id_tipo }}" @if(request('tipo_documento') == $tipo->id_tipo) selected @endif>
                    {{ $tipo->nombre_tipo }}
                </option>
            @endforeach
        </select>

        <button type="submit" style="background:#2980b9; color:white; border:none; padding:0.4rem 0.8rem; border-radius:4px; cursor:pointer;">Filtrar</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Responsable</th>
                <th>Creador</th>
                <th>Estado</th>
                <th>Fecha Creación</th>
                <th>Fecha Vencimiento</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tareas as $tarea)
                <tr>
                    <td>{{ $tarea->nombre }}</td>
                    <td>{{ $tarea->descripcion }}</td>
                    <td>{{ $tarea->usuarioAsignado->nombre ?? 'N/A' }}</td>
                    <td>{{ $tarea->usuarioCreador->nombre ?? 'N/A' }}</td>
                    <td class="estado-{{ str_replace(' ', '\ ', $tarea->estado) }}">{{ $tarea->estado }}</td>
                    <td>{{ \Carbon\Carbon::parse($tarea->fecha_creacion)->format('d/m/Y') }}</td>
                    <td>{{ $tarea->fecha_vencimiento ? \Carbon\Carbon::parse($tarea->fecha_vencimiento)->format('d/m/Y') : '-' }}</td>
                    <td class="acciones">
                        <a href="{{ route('tareas.show', $tarea->id_tarea) }}" class="ver" title="Ver">Ver</a>
                        <a href="{{ route('tareas.edit', $tarea->id_tarea) }}" class="editar" title="Editar">Editar</a>
                        <form method="POST" action="{{ route('tareas.destroy', $tarea->id_tarea) }}" style="display:inline;" onsubmit="return confirm('¿Estás seguro de eliminar esta tarea?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="eliminar" title="Eliminar">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" style="text-align:center;">No se encontraron tareas.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
