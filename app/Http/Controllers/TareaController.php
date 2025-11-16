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
            'sort' => 'nullable|string|in:id_tarea,nombre,estado,fecha_creacion',
            'direction' => 'nullable|string|in:asc,desc'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $query = Tarea::with(['usuarioAsignado', 'usuarioCreador', 'documentos']);

        // Aplicar filtros
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

        // Restringir a tareas asignadas si no es SuperAdmin
        if (!auth()->user()->tieneRol('SuperAdmin')) {
            $query->where('fk_id_usuario_asignado', auth()->id());
        }

        // Ordenamiento
        $sort = $request->input('sort', 'fecha_creacion');
        $direction = $request->input('direction', 'asc');
        $query->orderBy($sort, $direction);

        $tareas = $query->paginate(10);
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

        return view('tareas.tareas', compact('tareas', 'responsables', 'estadisticas', 'tiposDocumento', 'sort', 'direction'));
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
            'fecha_creacion' => 'required|date|before_or_equal:today',
            'fecha_vencimiento' => 'nullable|date|after_or_equal:fecha_creacion',
            'documento' => 'nullable|file|max:'.self::MAX_FILE_SIZE.'|mimes:'.implode(',', self::ALLOWED_MIMES),
            'fk_id_tipo' => 'nullable|required_with:documento|exists:tipo_documento,id_tipo',
            'descripcion_documento' => 'nullable|string|max:200'
        ], [
            'nombre.required' => 'El nombre de la tarea es obligatorio.',
            'nombre.max' => 'El nombre no debe exceder los 100 caracteres.',
            'nombre.regex' => 'El nombre contiene caracteres no permitidos.',
            'descripcion.max' => 'La descripción no debe exceder los 500 caracteres.',
            'descripcion.regex' => 'La descripción contiene caracteres no permitidos.',
            'fk_id_usuario_asignado.required' => 'Debe asignar un responsable.',
            'fk_id_usuario_asignado.exists' => 'El usuario asignado no existe.',
            'fecha_creacion.required' => 'La fecha de creación es obligatoria.',
            'fecha_creacion.before_or_equal' => 'La fecha de creación no puede ser futura.',
            'fecha_vencimiento.after_or_equal' => 'La fecha de vencimiento debe ser igual o posterior a la fecha de creación.',
            'documento.max' => 'El archivo no debe superar los 10MB.',
            'documento.mimes' => 'El tipo de archivo no está permitido.',
            'fk_id_tipo.required_with' => 'Debe seleccionar un tipo de documento cuando sube un archivo.',
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
                'estado' => 'Pendiente', // Estado por defecto
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

            // Registrar en bitácora
            $this->registrarBitacora('crear_tarea', 'Tarea', $tarea->id_tarea, [], $tarea->toArray());

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
        return view('tareas.edit', compact('tarea', 'responsables'));
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
            'fecha_creacion' => 'required|date',
            'fecha_vencimiento' => 'nullable|date|after_or_equal:fecha_creacion',
        ], [
            'nombre.required' => 'El nombre de la tarea es obligatorio.',
            'nombre.max' => 'El nombre no debe exceder los 100 caracteres.',
            'nombre.regex' => 'El nombre contiene caracteres no permitidos.',
            'descripcion.max' => 'La descripción no debe exceder los 500 caracteres.',
            'descripcion.regex' => 'La descripción contiene caracteres no permitidos.',
            'fk_id_usuario_asignado.required' => 'Debe asignar un responsable.',
            'fk_id_usuario_asignado.exists' => 'El usuario asignado no existe.',
            'fecha_creacion.required' => 'La fecha de creación es obligatoria.',
            'fecha_vencimiento.after_or_equal' => 'La fecha de vencimiento debe ser igual o posterior a la fecha de creación.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $tarea = Tarea::findOrFail($id);

        // Guardar datos antes de la actualización
        $datos_antes = $tarea->toArray();

        DB::beginTransaction();
        try {
            $tarea->update([
                'nombre' => strip_tags($request->nombre),
                'descripcion' => $request->descripcion ? strip_tags($request->descripcion) : null,
                'fk_id_usuario_asignado' => $request->fk_id_usuario_asignado,
                'fecha_creacion' => $request->fecha_creacion,
                'fecha_vencimiento' => $request->fecha_vencimiento,
            ]);

            DB::commit();

            // Registrar en bitácora
            $this->registrarBitacora('editar_tarea', 'Tarea', $tarea->id_tarea, $datos_antes, $tarea->toArray());

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

            // Guardar datos antes de eliminar
            $datos_antes = $tarea->toArray();

            foreach ($tarea->documentos as $documento) {
                if ($documento->ruta_archivo && Storage::disk('public')->exists($documento->ruta_archivo)) {
                    Storage::disk('public')->delete($documento->ruta_archivo);
                }
                $documento->delete();
            }

            $tarea->delete();

            DB::commit();

            // Registrar en bitácora
            $this->registrarBitacora('eliminar_tarea', 'Tarea', $id, $datos_antes, []);

            return redirect()->route('tareas.index')->with('success', 'Tarea eliminada correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors('Error al eliminar la tarea: ' . $e->getMessage());
        }
    }

    public function cambiarEstado(Request $request, $id)
    {
        $tarea = Tarea::findOrFail($id);

        // Validar permiso para cambiar estado
        if ($tarea->fk_id_usuario_asignado != auth()->id() && !auth()->user()->tieneRol('SuperAdmin')) {
            abort(403, 'No tienes permiso para cambiar el estado de esta tarea.');
        }

        $validator = Validator::make($request->all(), [
            'estado' => 'required|string|in:Pendiente,En Proceso,Completada,Rechazada'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        // Guardar datos antes del cambio
        $datos_antes = $tarea->toArray();

        $tarea->estado = $request->estado;
        $tarea->save();

        // Registrar en bitácora
        $this->registrarBitacora('cambiar_estado_tarea', 'Tarea', $tarea->id_tarea, $datos_antes, $tarea->toArray());

        return back()->with('success', 'Estado de la tarea actualizado correctamente.');
    }

    public function delegar(Request $request, $id)
    {
        $tarea = Tarea::findOrFail($id);

        // Validar permiso para delegar
        if ($tarea->fk_id_usuario_asignado != auth()->id() && !auth()->user()->tieneRol('SuperAdmin')) {
            abort(403, 'No tienes permiso para delegar esta tarea.');
        }

        $validator = Validator::make($request->all(), [
            'nuevo_responsable' => 'required|integer|exists:usuario,id_usuario'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        // Guardar datos antes de la delegación
        $datos_antes = $tarea->toArray();

        $tarea->fk_id_usuario_asignado = $request->nuevo_responsable;
        $tarea->save();

        // Registrar en bitácora
        $this->registrarBitacora('delegar_tarea', 'Tarea', $tarea->id_tarea, $datos_antes, $tarea->toArray());

        return back()->with('success', 'Tarea delegada correctamente.');
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
            'descripcion' => 'nullable|string|max:200'
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

            // Registrar en bitácora
            $this->registrarBitacora('subir_documento_tarea', 'Tarea', $request->id_tarea, [], ['documento_id' => $documento->id_documento]);

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

            // Guardar datos antes de eliminar
            $datos_antes = $documento->toArray();

            if ($documento->ruta_archivo && Storage::disk('public')->exists($documento->ruta_archivo)) {
                Storage::disk('public')->delete($documento->ruta_archivo);
            }

            $documento->delete();

            DB::commit();

            // Registrar en bitácora
            $this->registrarBitacora('eliminar_documento_tarea', 'DocumentoAdministrativo', $id, $datos_antes, []);

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

    // Método auxiliar para registrar en bitácora (si existe en tu controlador base)
    protected function registrarBitacora($accion, $tabla, $registroId, $datosAntes = [], $datosDespues = [])
    {
        // Implementación del registro en bitácora según tu sistema
        // Este método puede variar dependiendo de tu implementación específica
    }
}