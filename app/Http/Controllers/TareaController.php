<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tarea;
use App\Models\User;
use App\Models\DocumentoAdministrativo;
use App\Models\TipoDocumento;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TareaController extends Controller
{
    // Constantes para validación
    const MAX_FILE_SIZE = 10240; // 10MB
    const ALLOWED_MIMES = [
        'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 
        'jpg', 'jpeg', 'png', 'gif', 'txt', 'csv', 'zip', 'rar'
    ];

    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'estado' => ['nullable', 'string', Rule::in(['Pendiente', 'En Proceso', 'Completada', 'Rechazada'])],
            'responsable' => 'nullable|integer|exists:usuario,id_usuario',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'tipo_documento' => 'nullable|integer|exists:tipo_documento,id_tipo',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

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
            $query->whereHas('documentos', function($q) use ($request) {
                $q->where('fk_id_tipo', $request->tipo_documento);
            });
        }

        if (!auth()->user()->tieneRol('SuperAdmin')) {
            $query->where('fk_id_usuario_asignado', auth()->id());
        }

        $tareas = $query->orderBy('fecha_vencimiento', 'asc')->get();
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
        $tiposDocumento = TipoDocumento::all();
        return view('tareas.create', compact('responsables', 'tiposDocumento'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->puedeAgregar('TareasDocumentales')) {
            abort(403, 'No tienes permisos para crear tareas.');
        }

        $validator = Validator::make($request->all(), [
            'nombre' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s\-_.,;:()\/]+$/',
                'not_regex:/<[^>]*>/' 
            ],
            'descripcion' => [
                'nullable',
                'string',
                'max:500',
                'regex:/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s\-_.,;:()\/]+$/',
                'not_regex:/<[^>]*>/'
            ],
            'fk_id_usuario_asignado' => 'required|integer|exists:usuario,id_usuario',
            'estado' => 'required|string|in:Pendiente,En Proceso,Completada,Rechazada',
            'fecha_creacion' => 'required|date|before_or_equal:today',
            'fecha_vencimiento' => 'nullable|date|after_or_equal:fecha_creacion',
            'documento' => 'nullable|file|max:'.self::MAX_FILE_SIZE.'|mimes:'.implode(',', self::ALLOWED_MIMES),
            'fk_id_tipo' => 'nullable|required_with:documento|exists:tipo_documento,id_tipo',
            'descripcion_documento' => 'nullable|required_with:documento|string|max:200'
        ], [
            'nombre.required' => 'El nombre de la tarea es obligatorio.',
            'nombre.max' => 'El nombre no debe exceder los 100 caracteres.',
            'nombre.regex' => 'El nombre contiene caracteres no permitidos.',
            'descripcion.max' => 'La descripción no debe exceder los 500 caracteres.',
            'descripcion.regex' => 'La descripción contiene caracteres no permitidos.',
            'fk_id_usuario_asignado.required' => 'Debe asignar un responsable.',
            'fk_id_usuario_asignado.exists' => 'El usuario asignado no existe.',
            'estado.required' => 'El estado es obligatorio.',
            'estado.in' => 'El estado seleccionado no es válido.',
            'fecha_creacion.required' => 'La fecha de creación es obligatoria.',
            'fecha_creacion.before_or_equal' => 'La fecha de creación no puede ser futura.',
            'fecha_vencimiento.after_or_equal' => 'La fecha de vencimiento debe ser igual o posterior a la fecha de creación.',
            'documento.max' => 'El archivo no debe superar los 10MB.',
            'documento.mimes' => 'El tipo de archivo no está permitido.',
            'fk_id_tipo.required_with' => 'Debe seleccionar un tipo de documento cuando sube un archivo.',
            'descripcion_documento.required_with' => 'Debe agregar una descripción cuando sube un archivo.'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $tarea = Tarea::create([
                'nombre' => strip_tags($request->nombre),
                'descripcion' => $request->descripcion ? strip_tags($request->descripcion) : null,
                'fk_id_usuario_asignado' => $request->fk_id_usuario_asignado,
                'fk_id_usuario_creador' => auth()->id(),
                'estado' => $request->estado,
                'fecha_creacion' => $request->fecha_creacion,
                'fecha_vencimiento' => $request->fecha_vencimiento,
            ]);

            if ($request->hasFile('documento')) {
                $file = $request->file('documento');
                $path = $file->store('tareas_documentos', 'public');

                $documento = DocumentoAdministrativo::create([
                    'nombre_documento' => $file->getClientOriginalName(),
                    'descripcion' => strip_tags($request->descripcion_documento),
                    'ruta_archivo' => $path,
                    'fecha_subida' => now(),
                    'fk_id_usuario' => auth()->id(),
                    'fk_id_tipo' => $request->fk_id_tipo,
                ]);

                DB::table('tarea_documento')->insert([
                    'fk_id_tarea' => $tarea->id_tarea,
                    'fk_id_documento' => $documento->id_documento,
                    'fecha_asociacion' => now(),
                    'observaciones' => null,
                ]);
            }

            DB::commit();

            $usuario = User::find($request->fk_id_usuario_asignado);
            if ($usuario) {
                $usuario->notify(new \App\Notifications\TareaAsignadaNotification($tarea));
            }

            return redirect()->route('tareas.index')->with('success', 'Tarea creada correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors('Error al crear la tarea: ' . $e->getMessage())->withInput();
        }
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
        $tiposDocumento = TipoDocumento::all();
        return view('tareas.edit', compact('tarea', 'responsables', 'tiposDocumento'));
    }

    public function update(Request $request, $id)
    {
        if (!auth()->user()->puedeEditar('TareasDocumentales')) {
            abort(403, 'No tienes permisos para editar tareas.');
        }

        $validator = Validator::make($request->all(), [
            'nombre' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s\-_.,;:()\/]+$/',
                'not_regex:/<[^>]*>/'
            ],
            'descripcion' => [
                'nullable',
                'string',
                'max:500',
                'regex:/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s\-_.,;:()\/]+$/',
                'not_regex:/<[^>]*>/'
            ],
            'fk_id_usuario_asignado' => 'required|integer|exists:usuario,id_usuario',
            'estado' => 'required|string|in:Pendiente,En Proceso,Completada,Rechazada',
            'fecha_creacion' => 'required|date',
            'fecha_vencimiento' => 'nullable|date|after_or_equal:fecha_creacion',
            'documento' => 'nullable|file|max:'.self::MAX_FILE_SIZE.'|mimes:'.implode(',', self::ALLOWED_MIMES),
            'fk_id_tipo' => 'nullable|required_with:documento|exists:tipo_documento,id_tipo',
            'descripcion_documento' => 'nullable|required_with:documento|string|max:200'
        ], [
            'nombre.required' => 'El nombre de la tarea es obligatorio.',
            'nombre.max' => 'El nombre no debe exceder los 100 caracteres.',
            'nombre.regex' => 'El nombre contiene caracteres no permitidos.',
            'descripcion.max' => 'La descripción no debe exceder los 500 caracteres.',
            'descripcion.regex' => 'La descripción contiene caracteres no permitidos.',
            'fk_id_usuario_asignado.required' => 'Debe asignar un responsable.',
            'fk_id_usuario_asignado.exists' => 'El usuario asignado no existe.',
            'estado.required' => 'El estado es obligatorio.',
            'estado.in' => 'El estado seleccionado no es válido.',
            'fecha_creacion.required' => 'La fecha de creación es obligatoria.',
            'fecha_vencimiento.after_or_equal' => 'La fecha de vencimiento debe ser igual o posterior a la fecha de creación.',
            'documento.max' => 'El archivo no debe superar los 10MB.',
            'documento.mimes' => 'El tipo de archivo no está permitido.',
            'fk_id_tipo.required_with' => 'Debe seleccionar un tipo de documento cuando sube un archivo.',
            'descripcion_documento.required_with' => 'Debe agregar una descripción cuando sube un archivo.'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $tarea = Tarea::findOrFail($id);

        DB::beginTransaction();
        try {
            $tarea->update([
                'nombre' => strip_tags($request->nombre),
                'descripcion' => $request->descripcion ? strip_tags($request->descripcion) : null,
                'fk_id_usuario_asignado' => $request->fk_id_usuario_asignado,
                'estado' => $request->estado,
                'fecha_creacion' => $request->fecha_creacion,
                'fecha_vencimiento' => $request->fecha_vencimiento,
            ]);

            if ($request->hasFile('documento')) {
                $file = $request->file('documento');
                $path = $file->store('tareas_documentos', 'public');

                $documento = DocumentoAdministrativo::create([
                    'nombre_documento' => $file->getClientOriginalName(),
                    'descripcion' => strip_tags($request->descripcion_documento),
                    'ruta_archivo' => $path,
                    'fecha_subida' => now(),
                    'fk_id_usuario' => auth()->id(),
                    'fk_id_tipo' => $request->fk_id_tipo,
                ]);

                DB::table('tarea_documento')->insert([
                    'fk_id_tarea' => $tarea->id_tarea,
                    'fk_id_documento' => $documento->id_documento,
                    'fecha_asociacion' => now(),
                    'observaciones' => null,
                ]);
            }

            DB::commit();

            return redirect()->route('tareas.index')->with('success', 'Tarea actualizada correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors('Error al actualizar la tarea: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        if (!auth()->user()->puedeEliminar('TareasDocumentales')) {
            abort(403, 'No tienes permisos para eliminar tareas.');
        }

        $validator = Validator::make(['id' => $id], [
            'id' => 'required|integer|exists:tareas,id_tarea'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $tarea = Tarea::findOrFail($id);

            foreach ($tarea->documentos as $documento) {
                if ($documento->ruta_archivo && Storage::disk('public')->exists($documento->ruta_archivo)) {
                    Storage::disk('public')->delete($documento->ruta_archivo);
                }
                $documento->delete();
            }

            $tarea->delete();

            DB::commit();

            return redirect()->route('tareas.index')->with('success', 'Tarea eliminada correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors('Error al eliminar la tarea: ' . $e->getMessage());
        }
    }

    public function upload(Request $request)
    {
        if (!auth()->user()->puedeAgregar('TareasDocumentales')) {
            abort(403, 'No tienes permisos para cargar documentos.');
        }

        $validator = Validator::make($request->all(), [
            'id_tarea' => 'required|integer|exists:tareas,id_tarea',
            'documento' => 'required|file|max:'.self::MAX_FILE_SIZE.'|mimes:'.implode(',', self::ALLOWED_MIMES),
            'fk_id_tipo' => 'required|integer|exists:tipo_documento,id_tipo',
            'descripcion' => 'nullable|string|max:200'  // Aquí es opcional
        ], [
            'id_tarea.required' => 'Se requiere una tarea asociada.',
            'id_tarea.exists' => 'La tarea especificada no existe.',
            'documento.required' => 'Debe seleccionar un archivo.',
            'documento.max' => 'El archivo no debe superar los 10MB.',
            'documento.mimes' => 'El tipo de archivo no está permitido.',
            'fk_id_tipo.required' => 'Debe seleccionar un tipo de documento.',
            'fk_id_tipo.exists' => 'El tipo de documento seleccionado no existe.',
            'descripcion.max' => 'La descripción no debe exceder los 200 caracteres.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $file = $request->file('documento');
            $path = $file->store('tareas_documentos', 'public');

            $documento = DocumentoAdministrativo::create([
                'nombre_documento' => $file->getClientOriginalName(),
                'descripcion' => $request->descripcion ? strip_tags($request->descripcion) : null,
                'ruta_archivo' => $path,
                'fecha_subida' => now(),
                'fk_id_usuario' => auth()->id(),
                'fk_id_tipo' => $request->fk_id_tipo,
            ]);

            DB::table('tarea_documento')->insert([
                'fk_id_tarea' => $request->id_tarea,
                'fk_id_documento' => $documento->id_documento,
                'fecha_asociacion' => now(),
                'observaciones' => null,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Documento cargado correctamente.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar el documento: ' . $e->getMessage()
            ], 500);
        }
    }

    public function eliminarDocumento($id)
    {
        if (!auth()->user()->puedeEliminar('TareasDocumentales')) {
            abort(403, 'No tienes permisos para eliminar documentos.');
        }

        $validator = Validator::make(['id' => $id], [
            'id' => 'required|integer|exists:documento_administrativo,id_documento'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $documento = DocumentoAdministrativo::findOrFail($id);

            if ($documento->ruta_archivo && Storage::disk('public')->exists($documento->ruta_archivo)) {
                Storage::disk('public')->delete($documento->ruta_archivo);
            }

            $documento->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Documento eliminado correctamente.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el documento: ' . $e->getMessage()
            ], 500);
        }
    }

    // Método nuevo para historial de bitácora con creación incluida
    public function historialBitacora($id)
    {
        // Aquí debes ajustar la consulta según tu modelo Bitacora
        $historialTarea = \App\Models\Bitacora::where('modulo', 'Tarea')
            ->where('registro_id', $id)
            ->orderBy('created_at', 'asc')
            ->get();

        $eventos = [];

        // Evento de creación
        if ($historialTarea->where('accion', 'crear')->count() > 0) {
            $creacion = $historialTarea->where('accion', 'crear')->first();
            $usuario = $creacion->usuario_nombre ?? 'Sistema';
            $fecha = $creacion->created_at ? $creacion->created_at->format('d/m/Y H:i') : '';
            $eventos[] = "Tarea creada por $usuario el $fecha.";
        }

        // Aquí puedes agregar más eventos y lógica según el historial

        return response()->json(['historial' => $eventos]);
    }
}
