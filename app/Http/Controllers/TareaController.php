<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tarea;
use App\Models\User;
use App\Models\DocumentoAdministrativo;
use App\Models\TipoDocumento;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Bitacora;
use App\Notifications\TareaAsignadaNotification;

class TareaController extends Controller
{
    public function index(Request $request)
    {
        $query = Tarea::with(['usuarioAsignado', 'usuarioCreador', 'documentos']);

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('responsable')) {
            $query->where('fk_id_usuario_asignado', $request->responsable);
        }

        if ($request->filled('fecha_inicio')) {
            $query->whereDate('fecha_creacion', '>=', $request->fecha_inicio);
        }

        if ($request->filled('fecha_fin')) {
            $query->whereDate('fecha_creacion', '<=', $request->fecha_fin);
        }

        if ($request->filled('tipo_documento')) {
            $query->whereHas('documentos', function ($q) use ($request) {
                $q->where('fk_id_tipo', $request->tipo_documento);
            });
        }

        if (!auth()->user()->tieneRol('SuperAdmin')) {
            $query->where('fk_id_usuario_asignado', auth()->id());
        }

        $tareas = $query->get();
        $responsables = User::all();
        $estadisticas = [
            'pendientes' => Tarea::where('estado', 'Pendiente')->count(),
            'en_proceso' => Tarea::where('estado', 'En Proceso')->count(),
            'completadas' => Tarea::where('estado', 'Completada')->count(),
            'rechazadas' => Tarea::where('estado', 'Rechazada')->count(),
        ];
        $tiposDocumento = TipoDocumento::all();

        if ($request->ajax()) {
            return view('tareas.tabla', compact('tareas'))->render();
        }

        return view('tareas.tareas', compact('tareas', 'responsables', 'estadisticas', 'tiposDocumento'));
    }

    public function create()
    {
        if (!auth()->user()->puedeAgregar('TareasDocumentales')) {
            abort(403, 'No tienes permisos para crear tareas.');
        }

        $responsables = User::all();
        return view('tareas.create', compact('responsables'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->puedeAgregar('TareasDocumentales')) {
            abort(403, 'No tienes permisos para crear tareas.');
        }

        $request->validate([
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'fk_id_usuario_asignado' => 'required|exists:usuario,id_usuario',
            'fk_id_usuario_creador' => 'required|exists:usuario,id_usuario',
            'estado' => 'required|string|max:50',
            'fecha_creacion' => 'required|date',
            'fecha_vencimiento' => 'nullable|date|after_or_equal:fecha_creacion',
        ]);

        $tarea = Tarea::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'fk_id_usuario_asignado' => $request->fk_id_usuario_asignado,
            'fk_id_usuario_creador' => $request->fk_id_usuario_creador,
            'estado' => $request->estado,
            'fecha_creacion' => $request->fecha_creacion,
            'fecha_vencimiento' => $request->fecha_vencimiento,
        ]);

        $this->registrarBitacora('crear', 'Tarea', $tarea->id_tarea, null, $tarea->toArray());

        $usuario = User::find($request->fk_id_usuario_asignado);
        if ($usuario) {
            $usuario->notify(new TareaAsignadaNotification($tarea));
        }

        return redirect()->route('tareas.index')->with('success', 'Tarea creada correctamente.');
    }

    public function show($id)
    {
        if (!auth()->user()->puedeVer('TareasDocumentales')) {
            abort(403, 'No tienes permisos para ver tareas.');
        }

        $tarea = Tarea::with(['usuarioAsignado', 'usuarioCreador', 'documentos'])->findOrFail($id);
        return view('tareas.show', compact('tarea'));
    }

    public function edit($id)
    {
        if (!auth()->user()->puedeEditar('TareasDocumentales')) {
            abort(403, 'No tienes permisos para editar tareas.');
        }

        $tarea = Tarea::findOrFail($id);
        $responsables = User::all();
        return view('tareas.edit', compact('tarea', 'responsables'));
    }

    public function update(Request $request, $id)
    {
        if (!auth()->user()->puedeEditar('TareasDocumentales')) {
            abort(403, 'No tienes permisos para editar tareas.');
        }

        $request->validate([
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'fk_id_usuario_asignado' => 'required|exists:usuario,id_usuario',
            'fk_id_usuario_creador' => 'required|exists:usuario,id_usuario',
            'estado' => 'required|string|max:50',
            'fecha_creacion' => 'required|date',
            'fecha_vencimiento' => 'nullable|date|after_or_equal:fecha_creacion',
        ]);

        $tarea = Tarea::findOrFail($id);
        $datos_antes = $tarea->toArray();
        $estado_anterior = $tarea->estado;

        $tarea->update([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'fk_id_usuario_asignado' => $request->fk_id_usuario_asignado,
            'fk_id_usuario_creador' => $request->fk_id_usuario_creador,
            'estado' => $request->estado,
            'fecha_creacion' => $request->fecha_creacion,
            'fecha_vencimiento' => $request->fecha_vencimiento,
        ]);

        $this->registrarBitacora('editar', 'Tarea', $tarea->id_tarea, $datos_antes, $tarea->toArray());

        if ($estado_anterior !== $request->estado) {
            $this->registrarBitacora('cambiar_estado', 'Tarea', $tarea->id_tarea, ['estado' => $estado_anterior], ['estado' => $request->estado]);
        }

        return redirect()->route('tareas.index')->with('success', 'Tarea actualizada correctamente.');
    }

    public function destroy($id)
    {
        if (!auth()->user()->puedeEliminar('TareasDocumentales')) {
            abort(403, 'No tienes permisos para eliminar tareas.');
        }

        $tarea = Tarea::findOrFail($id);
        $datos_antes = $tarea->toArray();
        $tarea->delete();

        $this->registrarBitacora('eliminar', 'Tarea', $id, $datos_antes, null);

        return redirect()->route('tareas.index')->with('success', 'Tarea eliminada correctamente.');
    }

    public function upload(Request $request)
    {
        if (!auth()->user()->puedeAgregar('TareasDocumentales')) {
            abort(403, 'No tienes permisos para cargar documentos.');
        }

        $request->validate([
            'id_tarea' => 'required|exists:tareas,id_tarea',
            'documento' => 'required|file|mimes:pdf,jpg,jpeg,png,gif|max:2048',
            'fk_id_tipo' => 'required|exists:tipo_documento,id_tipo',
        ]);

        DB::beginTransaction();
        try {
            $file = $request->file('documento');
            $path = $file->store('tareas_documentos', 'public');

            $documento = DocumentoAdministrativo::create([
                'nombre_documento' => $file->getClientOriginalName(),
                'descripcion' => $request->input('descripcion'),
                'ruta_archivo' => $path,
                'fecha_subida' => now(),
                'fk_id_usuario' => auth()->id(),
                'fk_id_tipo' => $request->fk_id_tipo,
            ]);

            DB::table('tarea_documento')->insert([
                'fk_id_tarea' => $request->id_tarea,
                'fk_id_documento' => $documento->id_documento,
                'fecha_asociacion' => now(),
            ]);

            DB::commit();

            $this->registrarBitacora('cargar_documento', 'Documento', $documento->id_documento, null, $documento->toArray());
            $this->registrarBitacora('asociar_documento', 'Tarea', $request->id_tarea, null, ['id_documento' => $documento->id_documento]);

            return redirect()->route('tareas.index')->with('success', 'Documento cargado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors('Error al cargar el documento: ' . $e->getMessage());
        }
    }

    public function eliminarDocumento($id)
    {
        if (!auth()->user()->puedeEliminar('TareasDocumentales')) {
            abort(403, 'No tienes permisos para eliminar documentos.');
        }

        $documento = DocumentoAdministrativo::findOrFail($id);
        $datos_antes = $documento->toArray();

        if ($documento->ruta_archivo && Storage::disk('public')->exists($documento->ruta_archivo)) {
            Storage::disk('public')->delete($documento->ruta_archivo);
        }

        DB::table('tarea_documento')->where('fk_id_documento', $id)->delete();
        $documento->delete();

        $this->registrarBitacora('eliminar_documento', 'Documento', $id, $datos_antes, null);

        return back()->with('success', 'Documento eliminado correctamente.');
    }

    public function historialBitacora($id)
    {
        $tarea = Tarea::with('documentos')->findOrFail($id);
        $documentosIds = $tarea->documentos->pluck('id_documento')->toArray();

        $historialTarea = Bitacora::where('modulo', 'Tarea')->where('registro_id', $id)->orderBy('created_at', 'asc')->get();
        $historialDocs = Bitacora::where('modulo', 'Documento')->whereIn('registro_id', $documentosIds)->orderBy('created_at', 'asc')->get();

        $eventos = [];

        foreach ($historialTarea as $item) {
            $usuario = $item->usuario_nombre ?? 'Sistema';
            $fecha = $item->created_at->format('d/m/Y H:i');
            switch ($item->accion) {
                case 'crear':
                    $eventos[] = "Tarea creada por $usuario el $fecha.";
                    break;
                case 'editar':
                    $eventos[] = "Tarea editada por $usuario el $fecha.";
                    break;
                case 'cambiar_estado':
                    $antes = $item->datos_antes['estado'] ?? '';
                    $despues = $item->datos_despues['estado'] ?? '';
                    $eventos[] = "Estado cambiado de '$antes' a '$despues' por $usuario el $fecha.";
                    break;
                case 'eliminar':
                    $eventos[] = "Tarea eliminada por $usuario el $fecha.";
                    break;
            }
        }

        foreach ($historialDocs as $item) {
            $usuario = $item->usuario_nombre ?? 'Sistema';
            $fecha = $item->created_at->format('d/m/Y H:i');
            switch ($item->accion) {
                case 'cargar_documento':
                    $eventos[] = "Documento cargado por $usuario el $fecha.";
                    break;
                case 'eliminar_documento':
                    $eventos[] = "Documento eliminado por $usuario el $fecha.";
                    break;
            }
        }

        return response()->json(['historial' => $eventos]);
    }

    protected function registrarBitacora($accion, $modulo, $registro_id = null, $datos_antes = null, $datos_despues = null)
    {
        Bitacora::create([
            'usuario_id' => auth()->id(),
            'usuario_nombre' => auth()->user()->name ?? 'Sistema',
            'accion' => $accion,
            'modulo' => $modulo,
            'registro_id' => $registro_id,
            'datos_antes' => $datos_antes,
            'datos_despues' => $datos_despues,
            'created_at' => now(),
        ]);
    }
}
