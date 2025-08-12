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

        // Filtros
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
            $query->whereHas('documentos', function($q) use ($request) {
                $q->where('fk_id_tipo', $request->tipo_documento);
            });
        }

        // Solo el superusuario puede ver todas las tareas, los demás solo las asignadas
        if (!auth()->user()->tieneRol('SuperAdmin')) {
            $query->where('fk_id_usuario_asignado', auth()->id());
        }

        // Ordenamiento
        $sort = $request->get('sort', 'id_tarea');
        $direction = $request->get('direction', 'asc');
        
        if ($sort == 'id_tarea') {
            $query->orderBy('id_tarea', $direction);
        } elseif ($sort == 'nombre') {
            $query->orderBy('nombre', $direction);
        } elseif ($sort == 'responsable') {
            $query->orderBy('fk_id_usuario_asignado', $direction);
        } elseif ($sort == 'estado') {
            $query->orderBy('estado', $direction);
        } elseif ($sort == 'fecha_creacion') {
            $query->orderBy('fecha_creacion', $direction);
        }

        $tareas = $query->paginate(10);
        $responsables = User::all();
        $estadisticas = [
            'pendientes' => Tarea::where('estado', 'Pendiente')->count(),
            'en_proceso' => Tarea::where('estado', 'En Proceso')->count(),
            'completadas' => Tarea::where('estado', 'Completada')->count(),
            'rechazadas' => Tarea::where('estado', 'Rechazada')->count(),
        ];
        $tiposDocumento = TipoDocumento::all();

        return view('tareas.tareas', compact('tareas', 'responsables', 'estadisticas', 'tiposDocumento'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->puedeAgregar('TareasDocumentales')) {
            abort(403, 'No tienes permisos para crear tareas.');
        }
        
        $request->validate([
            'nombre' => 'required|string|max:100',
            'fk_id_usuario_asignado' => 'required|exists:usuario,id_usuario',
            'fk_id_tipo' => 'required|exists:tipo_documento,id_tipo',
            'documento' => 'required|file|mimes:pdf,jpg,jpeg,png,gif|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $tarea = Tarea::create([
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'fk_id_usuario_asignado' => $request->fk_id_usuario_asignado,
                'fk_id_usuario_creador' => auth()->id(),
                'estado' => 'Pendiente',
                'fecha_creacion' => now(),
                'fecha_vencimiento' => $request->fecha_vencimiento,
            ]);

            // Subir documento
            $file = $request->file('documento');
            $path = $file->store('tareas_documentos', 'public');
            
            $documento = DocumentoAdministrativo::create([
                'nombre_documento' => $file->getClientOriginalName(),
                'ruta_archivo' => $path,
                'fecha_subida' => now(),
                'fk_id_usuario' => auth()->id(),
                'fk_id_tipo' => $request->fk_id_tipo,
            ]);
            
            DB::table('tarea_documento')->insert([
                'fk_id_tarea' => $tarea->id_tarea,
                'fk_id_documento' => $documento->id_documento,
                'fecha_asociacion' => now(),
            ]);

            // Registrar en bitácora
            $this->registrarBitacora('crear', 'Tarea', $tarea->id_tarea, null, $tarea->toArray());
            
            // Notificar al usuario asignado
            $usuario = User::find($request->fk_id_usuario_asignado);
            if ($usuario) {
                $usuario->notify(new TareaAsignadaNotification($tarea));
            }

            DB::commit();
            return redirect()->route('tareas.index')->with('success', 'Tarea creada correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors('Error al crear la tarea: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        if (!auth()->user()->puedeEditar('TareasDocumentales')) {
            abort(403, 'No tienes permisos para editar tareas.');
        }
        
        $request->validate([
            'nombre' => 'required|string|max:100',
            'fk_id_usuario_asignado' => 'required|exists:usuario,id_usuario',
        ]);

        DB::beginTransaction();
        try {
            $tarea = Tarea::findOrFail($id);
            $datos_antes = $tarea->toArray();
            
            $tarea->update([
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'fk_id_usuario_asignado' => $request->fk_id_usuario_asignado,
                'fecha_vencimiento' => $request->fecha_vencimiento,
            ]);

            $this->registrarBitacora('editar', 'Tarea', $tarea->id_tarea, $datos_antes, $tarea->toArray());
            
            DB::commit();
            return redirect()->route('tareas.index')->with('success', 'Tarea actualizada correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors('Error al actualizar la tarea: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        if (!auth()->user()->puedeEliminar('TareasDocumentales')) {
            abort(403, 'No tienes permisos para eliminar tareas.');
        }
        
        DB::beginTransaction();
        try {
            $tarea = Tarea::findOrFail($id);
            $datos_antes = $tarea->toArray();
            
            // Eliminar documentos asociados
            foreach ($tarea->documentos as $documento) {
                if (Storage::disk('public')->exists($documento->ruta_archivo)) {
                    Storage::disk('public')->delete($documento->ruta_archivo);
                }
                $documento->delete();
            }
            
            $tarea->delete();
            
            $this->registrarBitacora('eliminar', 'Tarea', $id, $datos_antes, null);
            
            DB::commit();
            return redirect()->route('tareas.index')->with('success', 'Tarea eliminada correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors('Error al eliminar la tarea: ' . $e->getMessage());
        }
    }

    public function cambiarEstado(Request $request, $id)
    {
        if (!auth()->user()->puedeEditar('TareasDocumentales')) {
            abort(403, 'No tienes permisos para cambiar el estado de tareas.');
        }
        
        $request->validate([
            'estado' => 'required|string|max:50',
        ]);

        $tarea = Tarea::findOrFail($id);
        $estadoAnterior = $tarea->estado;
        
        $tarea->update([
            'estado' => $request->estado,
        ]);

        // Registrar cambio de estado en bitácora
        $this->registrarBitacora('cambiar_estado', 'Tarea', $tarea->id_tarea, 
            ['estado' => $estadoAnterior], 
            ['estado' => $request->estado, 'comentario' => $request->comentario]
        );

        return redirect()->route('tareas.index')->with('success', 'Estado de tarea actualizado correctamente.');
    }

    public function agregarDocumento(Request $request)
    {
        if (!auth()->user()->puedeAgregar('TareasDocumentales')) {
            abort(403, 'No tienes permisos para agregar documentos.');
        }
        
        $request->validate([
            'tarea_id' => 'required|exists:tareas,id_tarea',
            'fk_id_tipo' => 'required|exists:tipo_documento,id_tipo',
            'documento' => 'required|file|mimes:pdf,jpg,jpeg,png,gif|max:2048',
        ]);

        DB::beginTransaction();
        try {
            // Subir documento
            $file = $request->file('documento');
            $path = $file->store('tareas_documentos', 'public');
            
            $documento = DocumentoAdministrativo::create([
                'nombre_documento' => $file->getClientOriginalName(),
                'ruta_archivo' => $path,
                'fecha_subida' => now(),
                'fk_id_usuario' => auth()->id(),
                'fk_id_tipo' => $request->fk_id_tipo,
            ]);
            
            DB::table('tarea_documento')->insert([
                'fk_id_tarea' => $request->tarea_id,
                'fk_id_documento' => $documento->id_documento,
                'fecha_asociacion' => now(),
            ]);

            $this->registrarBitacora('agregar_documento', 'Tarea', $request->tarea_id, 
                null, 
                ['documento_id' => $documento->id_documento]
            );
            
            DB::commit();
            return redirect()->route('tareas.index')->with('success', 'Documento agregado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors('Error al agregar documento: ' . $e->getMessage());
        }
    }

    public function eliminarDocumento($id)
    {
        if (!auth()->user()->puedeEliminar('TareasDocumentales')) {
            abort(403, 'No tienes permisos para eliminar documentos.');
        }
        
        DB::beginTransaction();
        try {
            $documento = DocumentoAdministrativo::findOrFail($id);
            $tareaId = $documento->tareas()->first()->id_tarea;
            
            if ($documento->ruta_archivo && Storage::disk('public')->exists($documento->ruta_archivo)) {
                Storage::disk('public')->delete($documento->ruta_archivo);
            }
            
            DB::table('tarea_documento')->where('fk_id_documento', $id)->delete();
            $documento->delete();
            
            $this->registrarBitacora('eliminar_documento', 'Documento', $id, 
                null, 
                ['tarea_id' => $tareaId]
            );
            
            DB::commit();
            return back()->with('success', 'Documento eliminado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors('Error al eliminar documento: ' . $e->getMessage());
        }
    }

    public function historialBitacora($id)
    {
        $tarea = Tarea::with('documentos')->findOrFail($id);
        $documentosIds = $tarea->documentos->pluck('id_documento')->toArray();
        
        $historialTarea = Bitacora::where('modulo', 'Tarea')
            ->where('registro_id', $id)
            ->orderBy('created_at', 'asc')
            ->get();
        
        $historialDocs = Bitacora::where('modulo', 'Documento')
            ->whereIn('registro_id', $documentosIds)
            ->orderBy('created_at', 'asc')
            ->get();
        
        $eventos = [];
        
        // Eventos de tarea
        foreach ($historialTarea as $item) {
            $usuario = $item->usuario_nombre ?? 'Sistema';
            $fecha = $item->created_at ? $item->created_at->format('d/m/Y H:i') : '';
            
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
                    $comentario = $item->datos_despues['comentario'] ?? '';
                    $msg = "Estado cambiado de '$antes' a '$despues' por $usuario el $fecha.";
                    if ($comentario) $msg .= " Comentario: $comentario";
                    $eventos[] = $msg;
                    break;
                case 'eliminar':
                    $eventos[] = "Tarea eliminada por $usuario el $fecha.";
                    break;
                case 'agregar_documento':
                    $eventos[] = "Documento agregado por $usuario el $fecha.";
                    break;
            }
        }
        
        // Eventos de documentos
        foreach ($historialDocs as $item) {
            $usuario = $item->usuario_nombre ?? 'Sistema';
            $fecha = $item->created_at ? $item->created_at->format('d/m/Y H:i') : '';
            
            switch ($item->accion) {
                case 'eliminar_documento':
                    $eventos[] = "Documento eliminado por $usuario el $fecha.";
                    break;
            }
        }
        
        // Ordenar eventos por fecha
        usort($eventos, function($a, $b) {
            preg_match('/el (\d{2}\/\d{2}\/\d{4} \d{2}:\d{2})/', $a, $ma);
            preg_match('/el (\d{2}\/\d{2}\/\d{4} \d{2}:\d{2})/', $b, $mb);
            
            $fa = isset($ma[1]) ? \DateTime::createFromFormat('d/m/Y H:i', $ma[1]) : null;
            $fb = isset($mb[1]) ? \DateTime::createFromFormat('d/m/Y H:i', $mb[1]) : null;
            
            if ($fa && $fb) return $fa <=> $fb;
            return 0;
        });
        
        return response()->json(['historial' => $eventos]);
    }

    protected function registrarBitacora($accion, $modulo, $registroId, $datosAntes, $datosDespues)
    {
        Bitacora::create([
            'accion' => $accion,
            'modulo' => $modulo,
            'registro_id' => $registroId,
            'datos_antes' => $datosAntes,
            'datos_despues' => $datosDespues,
            'usuario_id' => auth()->id(),
            'usuario_nombre' => auth()->user()->nombres . ' ' . auth()->user()->apellidos,
            'created_at' => now(),
        ]);
    }
}