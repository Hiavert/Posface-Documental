<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tarea;
use App\Models\User;
use App\Models\DocumentoAdministrativo;
use App\Models\TipoDocumento;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TareaController extends Controller
{
    // Validaciones para crear/actualizar tareas
    protected $validationRules = [
        'nombre' => 'required|string|max:100|regex:/^[a-zA-Z0-9\sáéíóúÁÉÍÓÚñÑ.,;:()\-]+$/',
        'descripcion' => 'nullable|string|max:500|regex:/^[a-zA-Z0-9\sáéíóúÁÉÍÓÚñÑ.,;:()\-]+$/',
        'fk_id_usuario_asignado' => 'required|exists:usuarios,id_usuario',
        'fk_id_usuario_creador' => 'required|exists:usuarios,id_usuario',
        'estado' => 'required|string|in:Pendiente,En Proceso,Completada,Rechazada',
        'fecha_creacion' => 'required|date|before_or_equal:today',
        'fecha_vencimiento' => 'nullable|date|after_or_equal:fecha_creacion',
    ];

    protected $validationMessages = [
        'nombre.required' => 'El nombre de la tarea es obligatorio.',
        'nombre.max' => 'El nombre no debe exceder los 100 caracteres.',
        'nombre.regex' => 'El nombre contiene caracteres no permitidos.',
        'descripcion.max' => 'La descripción no debe exceder los 500 caracteres.',
        'descripcion.regex' => 'La descripción contiene caracteres no permitidos.',
        'fk_id_usuario_asignado.required' => 'Debe asignar un responsable.',
        'fk_id_usuario_asignado.exists' => 'El usuario asignado no existe.',
        'fk_id_usuario_creador.required' => 'Debe indicar el creador de la tarea.',
        'fk_id_usuario_creador.exists' => 'El usuario creador no existe.',
        'estado.required' => 'El estado es obligatorio.',
        'estado.in' => 'El estado seleccionado no es válido.',
        'fecha_creacion.required' => 'La fecha de creación es obligatoria.',
        'fecha_creacion.before_or_equal' => 'La fecha de creación no puede ser futura.',
        'fecha_vencimiento.after_or_equal' => 'La fecha de vencimiento debe ser igual o posterior a la fecha de creación.',
    ];

    // Validaciones para filtros
    protected $filterValidationRules = [
        'estado' => 'nullable|string|in:Pendiente,En Proceso,Completada,Rechazada',
        'responsable' => 'nullable|exists:usuarios,id_usuario',
        'fecha_inicio' => 'nullable|date',
        'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
        'tipo_documento' => 'nullable|exists:tipo_documento,id_tipo'
    ];

    protected $filterValidationMessages = [
        'estado.in' => 'Estado de filtrado no válido.',
        'responsable.exists' => 'El responsable seleccionado no existe.',
        'fecha_fin.after_or_equal' => 'La fecha final debe ser mayor o igual a la fecha inicial.',
        'tipo_documento.exists' => 'El tipo de documento seleccionado no existe.'
    ];

    /**
     * Mostrar lista de tareas con filtros
     */
    public function index(Request $request)
    {
        try {
            $request->validate($this->filterValidationRules, $this->filterValidationMessages);

            $query = Tarea::with([
                'usuarioAsignado:id_usuario,nombres,apellidos,email',
                'usuarioCreador:id_usuario,nombres,apellidos',
                'documentos' => function($q) {
                    $q->select('id_documento', 'nombre_documento', 'ruta_archivo', 'fecha_subida', 'fk_id_tipo')
                      ->with('tipoDocumento:id_tipo,nombre_tipo');
                }
            ]);

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
                $query->where(function($q) {
                    $q->where('fk_id_usuario_asignado', auth()->id())
                      ->orWhere('fk_id_usuario_creador', auth()->id());
                });
            }

            $tareas = $query->orderBy('fecha_creacion', 'desc')->get();

            $estadisticas = [
                'pendientes' => $tareas->where('estado', 'Pendiente')->count(),
                'en_proceso' => $tareas->where('estado', 'En Proceso')->count(),
                'completadas' => $tareas->where('estado', 'Completada')->count(),
                'rechazadas' => $tareas->where('estado', 'Rechazada')->count(),
            ];

            $tareas->each(function($tarea) {
                $tarea->total_documentos = $tarea->documentos->count();
                $tarea->tipo_documento = $tarea->documentos->isNotEmpty() 
                    ? $tarea->documentos->first()->tipoDocumento->nombre_tipo ?? 'Sin tipo'
                    : 'Sin documento';
                $tarea->responsable = $tarea->usuarioAsignado;
            });

            $responsables = User::select('id_usuario', 'nombres', 'apellidos')
                               ->orderBy('nombres')
                               ->get();

            $tiposDocumento = TipoDocumento::select('id_tipo', 'nombre_tipo')
                                         ->orderBy('nombre_tipo')
                                         ->get();

            return view('tareas.tareas', compact('tareas', 'responsables', 'tiposDocumento', 'estadisticas'));

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return back()->with('error', 'Error al cargar las tareas: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar formulario para crear nueva tarea
     */
    public function create()
    {
        if (!auth()->user() || !method_exists(auth()->user(), 'puedeAgregar') || !auth()->user()->puedeAgregar('TareasDocumentales')) {
            return back()->with('error', 'No tienes permisos para crear tareas.');
        }

        $responsables = User::select('id_usuario', 'nombres', 'apellidos')
                           ->orderBy('nombres')
                           ->get();

        return view('tareas.create', compact('responsables'));
    }

    /**
     * Crear nueva tarea
     */
    public function store(Request $request)
    {
        if (!auth()->user() || !method_exists(auth()->user(), 'puedeAgregar') || !auth()->user()->puedeAgregar('TareasDocumentales')) {
            return back()->with('error', 'No tienes permisos para crear tareas.');
        }

        $validated = $request->validate($this->validationRules, $this->validationMessages);
        $validated['fk_id_usuario_creador'] = auth()->id();

        $tarea = Tarea::create($validated);

        return redirect()->route('tareas.index')
            ->with('success', 'Tarea creada correctamente con ID: TD-' . str_pad($tarea->id_tarea, 5, '0', STR_PAD_LEFT));
    }

    /**
     * Ver detalle de una tarea
     */
    public function show($id)
    {
        if (!auth()->user() || !method_exists(auth()->user(), 'puedeVer') || !auth()->user()->puedeVer('TareasDocumentales')) {
            return back()->with('error', 'No tienes permisos para ver tareas.');
        }

        if (!is_numeric($id) || $id <= 0) {
            return back()->with('error', 'ID de tarea no válido.');
        }

        $tarea = Tarea::with([
            'usuarioAsignado:id_usuario,nombres,apellidos,email',
            'usuarioCreador:id_usuario,nombres,apellidos',
            'documentos.tipoDocumento'
        ])->findOrFail($id);

        if (!auth()->user()->tieneRol('SuperAdmin') &&
            $tarea->fk_id_usuario_asignado !== auth()->id() &&
            $tarea->fk_id_usuario_creador !== auth()->id()) {
            return back()->with('error', 'No tienes permisos para ver esta tarea.');
        }

        return view('tareas.show', compact('tarea'));
    }

    /**
     * Mostrar formulario para editar tarea
     */
    public function edit($id)
    {
        if (!auth()->user() || !method_exists(auth()->user(), 'puedeEditar') || !auth()->user()->puedeEditar('TareasDocumentales')) {
            return back()->with('error', 'No tienes permisos para editar tareas.');
        }

        if (!is_numeric($id) || $id <= 0) {
            return back()->with('error', 'ID de tarea no válido.');
        }

        $tarea = Tarea::with(['usuarioAsignado', 'usuarioCreador'])->findOrFail($id);

        if (!auth()->user()->tieneRol('SuperAdmin') &&
            $tarea->fk_id_usuario_creador !== auth()->id()) {
            return back()->with('error', 'Solo puedes editar tareas que hayas creado.');
        }

        $responsables = User::select('id_usuario', 'nombres', 'apellidos')
                           ->orderBy('nombres')
                           ->get();

        return view('tareas.edit', compact('tarea', 'responsables'));
    }

    /**
     * Actualizar tarea existente
     */
    public function update(Request $request, $id)
    {
        if (!auth()->user() || !method_exists(auth()->user(), 'puedeEditar') || !auth()->user()->puedeEditar('TareasDocumentales')) {
            return back()->with('error', 'No tienes permisos para editar tareas.');
        }

        if (!is_numeric($id) || $id <= 0) {
            return back()->with('error', 'ID de tarea no válido.');
        }

        $validated = $request->validate($this->validationRules, $this->validationMessages);

        $tarea = Tarea::findOrFail($id);

        if (!auth()->user()->tieneRol('SuperAdmin') &&
            $tarea->fk_id_usuario_creador !== auth()->id()) {
            return back()->with('error', 'Solo puedes editar tareas que hayas creado.');
        }

        $validated['fk_id_usuario_creador'] = $tarea->fk_id_usuario_creador;

        $tarea->update($validated);

        return redirect()->route('tareas.index')
            ->with('success', 'Tarea actualizada correctamente.');
    }

    /**
     * Eliminar tarea
     */
    public function destroy($id)
    {
        if (!auth()->user() || !method_exists(auth()->user(), 'puedeEliminar') || !auth()->user()->puedeEliminar('TareasDocumentales')) {
            return back()->with('error', 'No tienes permisos para eliminar tareas.');
        }

        if (!is_numeric($id) || $id <= 0) {
            return back()->with('error', 'ID de tarea no válido.');
        }

        $tarea = Tarea::with('documentos')->findOrFail($id);

        if (!auth()->user()->tieneRol('SuperAdmin') &&
            $tarea->fk_id_usuario_creador !== auth()->id()) {
            return back()->with('error', 'Solo puedes eliminar tareas que hayas creado.');
        }

        DB::beginTransaction();

        foreach ($tarea->documentos as $documento) {
            if ($documento->ruta_archivo && Storage::disk('public')->exists($documento->ruta_archivo)) {
                Storage::disk('public')->delete($documento->ruta_archivo);
            }
            DB::table('tarea_documento')->where('fk_id_documento', $documento->id_documento)->delete();
            $documento->delete();
        }

        $tarea->delete();

        DB::commit();

        return redirect()->route('tareas.index')
            ->with('success', 'Tarea y documentos asociados eliminados correctamente.');
    }

    /**
     * Cargar documento a una tarea
     */
    public function upload(Request $request)
    {
        if (!auth()->user() || !method_exists(auth()->user(), 'puedeAgregar') || !auth()->user()->puedeAgregar('TareasDocumentales')) {
            return back()->with('error', 'No tienes permisos para cargar documentos.');
        }

        $validated = $request->validate($this->uploadValidationRules, $this->uploadValidationMessages);

        $tarea = Tarea::findOrFail($validated['id_tarea']);

        if (!auth()->user()->tieneRol('SuperAdmin') &&
            $tarea->fk_id_usuario_asignado !== auth()->id() &&
            $tarea->fk_id_usuario_creador !== auth()->id()) {
            return back()->with('error', 'No tienes permisos para cargar documentos a esta tarea.');
        }

        DB::beginTransaction();

        $file = $request->file('documento');
        $originalName = $file->getClientOriginalName();
        $fileName = time() . '_' . $originalName;
        $path = $file->storeAs('tareas_documentos', $fileName, 'public');

        $documento = DocumentoAdministrativo::create([
            'nombre_documento' => $originalName,
            'descripcion' => $request->input('descripcion', null),
            'ruta_archivo' => $path,
            'fecha_subida' => now(),
            'fk_id_usuario' => auth()->id(),
            'fk_id_tipo' => $validated['fk_id_tipo'],
        ]);

        DB::table('tarea_documento')->insert([
            'fk_id_tarea' => $validated['id_tarea'],
            'fk_id_documento' => $documento->id_documento,
            'fecha_asociacion' => now(),
            'observaciones' => null,
        ]);

        DB::commit();

        return redirect()->route('tareas.index')
            ->with('success', 'Documento "' . $originalName . '" cargado correctamente.');
    }

    /**
     * Eliminar documento de una tarea
     */
    public function eliminarDocumento($id)
    {
        if (!auth()->user() || !method_exists(auth()->user(), 'puedeEliminar') || !auth()->user()->puedeEliminar('TareasDocumentales')) {
            return back()->with('error', 'No tienes permisos para eliminar documentos.');
        }

        if (!is_numeric($id) || $id <= 0) {
            return back()->with('error', 'ID de documento no válido.');
        }

        $documento = DocumentoAdministrativo::findOrFail($id);

        if (!auth()->user()->tieneRol('SuperAdmin') &&
            $documento->fk_id_usuario !== auth()->id()) {
            return back()->with('error', 'Solo puedes eliminar documentos que hayas subido.');
        }

        DB::beginTransaction();

        if ($documento->ruta_archivo && Storage::disk('public')->exists($documento->ruta_archivo)) {
            Storage::disk('public')->delete($documento->ruta_archivo);
        }

        DB::table('tarea_documento')->where('fk_id_documento', $id)->delete();

        $documento->delete();

        DB::commit();

        return back()->with('success', 'Documento eliminado correctamente.');
    }
}
