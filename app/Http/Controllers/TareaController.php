<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tarea;
use App\Models\User;
use App\Models\DocumentoAdministrativo;
use App\Models\TipoDocumento;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TareaController extends Controller
{
    // Validaciones comunes
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

    public function index(Request $request)
    {
        try {
            // Validar parámetros de filtrado
            $request->validate([
                'estado' => 'nullable|string|in:Pendiente,En Proceso,Completada,Rechazada',
                'responsable' => 'nullable|exists:usuarios,id_usuario',
                'fecha_inicio' => 'nullable|date',
                'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
                'tipo_documento' => 'nullable|exists:tipo_documento,id_tipo'
            ], [
                'estado.in' => 'Estado de filtrado no válido.',
                'responsable.exists' => 'El responsable seleccionado no existe.',
                'fecha_fin.after_or_equal' => 'La fecha final debe ser mayor o igual a la fecha inicial.',
                'tipo_documento.exists' => 'El tipo de documento seleccionado no existe.'
            ]);

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

            if (!auth()->user()->tieneRol('SuperAdmin')) {
                $query->where('fk_id_usuario_asignado', auth()->id());
            }

            $tareas = $query->get();
            $responsables = User::all();
            $tiposDocumento = TipoDocumento::all();

            return view('tareas.tareas', compact('tareas', 'responsables', 'tiposDocumento'));

        } catch (\Exception $e) {
            Log::error('Error en index de TareaController: ' . $e->getMessage());
            return back()->with('error', 'Error al cargar las tareas: ' . $e->getMessage());
        }
    }

    public function create()
    {
        try {
            if (!auth()->user()->puedeAgregar('TareasDocumentales')) {
                throw new \Exception('No tienes permisos para crear tareas.');
            }

            $responsables = User::all();
            return view('tareas.create', compact('responsables'));

        } catch (\Exception $e) {
            Log::error('Error en create de TareaController: ' . $e->getMessage());
            return back()->with('error', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            if (!auth()->user()->puedeAgregar('TareasDocumentales')) {
                throw new \Exception('No tienes permisos para crear tareas.');
            }

            $validated = $request->validate($this->validationRules, $this->validationMessages);

            $tarea = Tarea::create($validated);

            return redirect()->route('tareas.index')
                ->with('success', 'Tarea creada correctamente.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Error de validación en store: ' . json_encode($e->errors()));
            return back()->withErrors($e->errors())->withInput();
            
        } catch (\Exception $e) {
            Log::error('Error en store: ' . $e->getMessage());
            return back()->with('error', 'Error al crear la tarea: ' . $e->getMessage())->withInput();
        }
    }

   public function show($id)
{
    try {
        if (!auth()->user()->puedeVer('TareasDocumentales')) {
            throw new \Exception('No tienes permisos para ver tareas.');
        }

        // Validar ID
        if (!is_numeric($id)) {
            throw new \Exception('ID de tarea no válido.');
        }

        $tarea = Tarea::with(['usuarioAsignado', 'usuarioCreador', 'documentos'])
                    ->findOrFail($id);
                    
        return view('tareas.show', compact('tarea'));

    } catch (\Exception $e) {
        Log::error('Error en show: ' . $e->getMessage());
        return back()->with('error', 'Error al mostrar la tarea: ' . $e->getMessage());
    }
}
    public function edit($id)
    {
        try {
            if (!auth()->user()->puedeEditar('TareasDocumentales')) {
                throw new \Exception('No tienes permisos para editar tareas.');
            }

            // Validar ID
            if (!is_numeric($id)) {
                throw new \Exception('ID de tarea no válido.');
            }

            $tarea = Tarea::findOrFail($id);
            $responsables = User::all();
            
            return view('tareas.edit', compact('tarea', 'responsables'));

        } catch (\Exception $e) {
            Log::error('Error en edit: ' . $e->getMessage());
            return back()->with('error', 'Error al cargar la tarea para edición: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            if (!auth()->user()->puedeEditar('TareasDocumentales')) {
                throw new \Exception('No tienes permisos para editar tareas.');
            }

            // Validar ID
            if (!is_numeric($id)) {
                throw new \Exception('ID de tarea no válido.');
            }

            $validated = $request->validate($this->validationRules, $this->validationMessages);

            $tarea = Tarea::findOrFail($id);
            $tarea->update($validated);

            return redirect()->route('tareas.index')
                ->with('success', 'Tarea actualizada correctamente.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Error de validación en update: ' . json_encode($e->errors()));
            return back()->withErrors($e->errors())->withInput();
            
        } catch (\Exception $e) {
            Log::error('Error en update: ' . $e->getMessage());
            return back()->with('error', 'Error al actualizar la tarea: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            if (!auth()->user()->puedeEliminar('TareasDocumentales')) {
                throw new \Exception('No tienes permisos para eliminar tareas.');
            }

            // Validar ID
            if (!is_numeric($id)) {
                throw new \Exception('ID de tarea no válido.');
            }

            $tarea = Tarea::findOrFail($id);            
            $tarea->delete();

            return redirect()->route('tareas.index')
                ->with('success', 'Tarea eliminada correctamente.');

        } catch (\Exception $e) {
            Log::error('Error en destroy: ' . $e->getMessage());
            return back()->with('error', 'Error al eliminar la tarea: ' . $e->getMessage());
        }
    }

    public function upload(Request $request)
    {
        try {
            if (!auth()->user()->puedeAgregar('TareasDocumentales')) {
                throw new \Exception('No tienes permisos para cargar documentos.');
            }

            $validated = $request->validate([
                'id_tarea' => 'required|exists:tareas,id_tarea',
                'documento' => 'required|file|mimes:pdf,jpg,jpeg,png,gif|max:2048',
                'fk_id_tipo' => 'required|exists:tipo_documento,id_tipo',
                'descripcion' => 'nullable|string|max:200|regex:/^[a-zA-Z0-9\sáéíóúÁÉÍÓÚñÑ.,;:()\-]+$/'
            ], [
                'id_tarea.required' => 'Debe seleccionar una tarea.',
                'id_tarea.exists' => 'La tarea seleccionada no existe.',
                'documento.required' => 'Debe seleccionar un archivo.',
                'documento.mimes' => 'El documento debe ser PDF, JPG, PNG o GIF.',
                'documento.max' => 'El archivo no debe superar los 2MB.',
                'fk_id_tipo.required' => 'Debe seleccionar un tipo de documento.',
                'fk_id_tipo.exists' => 'El tipo de documento seleccionado no existe.',
                'descripcion.max' => 'La descripción no debe exceder los 200 caracteres.',
                'descripcion.regex' => 'La descripción contiene caracteres no permitidos.'
            ]);

            DB::beginTransaction();

            $file = $request->file('documento');
            $path = $file->store('tareas_documentos', 'public');

            $documento = DocumentoAdministrativo::create([
                'nombre_documento' => $file->getClientOriginalName(),
                'descripcion' => $request->input('descripcion', null),
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

            return redirect()->route('tareas.index')
                ->with('success', 'Documento cargado correctamente.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return back()->withErrors($e->errors())->withInput();
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al cargar el documento: ' . $e->getMessage());
        }
    }

    public function eliminarDocumento($id)
    {
        try {
            if (!auth()->user()->puedeEliminar('TareasDocumentales')) {
                throw new \Exception('No tienes permisos para eliminar documentos.');
            }

            // Validar ID
            if (!is_numeric($id)) {
                throw new \Exception('ID de documento no válido.');
            }

            $documento = DocumentoAdministrativo::findOrFail($id);

            if ($documento->ruta_archivo && Storage::disk('public')->exists($documento->ruta_archivo)) {
                Storage::disk('public')->delete($documento->ruta_archivo);
            }

            DB::table('tarea_documento')->where('fk_id_documento', $id)->delete();
            $documento->delete();

            return back()->with('success', 'Documento eliminado correctamente.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar el documento: ' . $e->getMessage());
        }
    }
}
