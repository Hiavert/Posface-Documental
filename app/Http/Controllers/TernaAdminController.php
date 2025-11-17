<?php

namespace App\Http\Controllers;

use App\Models\PagoTerna;
use App\Models\DocumentoTerna;
use App\Models\IntegranteTerna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Notifications\NuevoProcesoTernaNotification;
use App\Models\User;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class TernaAdminController extends Controller
{
    public function index()
    {
        $sort = request('sort', 'id');
        $direction = request('direction', 'desc');
        
        $query = PagoTerna::where('id_administrador', auth()->id());
        
        // Filtros
        if(request('codigo')) {
            $query->where('codigo', 'like', '%'.request('codigo').'%');
        }
        
        if(request('estado')) {
            $query->where('estado', request('estado'));
        }
        
        if(request('responsable')) {
            $query->where('responsable', 'like', '%'.request('responsable').'%');
        }
        
        $procesos = $query->orderBy($sort, $direction)
                        ->paginate(10);
        
        // Contadores para el resumen
        $counts = [
            'en_revision' => PagoTerna::where('id_administrador', auth()->id())
                                ->where('estado', 'en_revision')
                                ->count(),
            'pendiente_pago' => PagoTerna::where('id_administrador', auth()->id())
                                ->where('estado', 'pendiente_pago')
                                ->count(),
            'pagado' => PagoTerna::where('id_administrador', auth()->id())
                                ->where('estado', 'pagado')
                                ->count(),
            'retrasados' => PagoTerna::where('id_administrador', auth()->id())
                                ->where('estado', 'en_revision')
                                ->where('fecha_limite', '<', now())
                                ->count(),
        ];
        
        $retrasados = $counts['retrasados'];
                
        return view('terna.admin.index', compact('procesos', 'retrasados', 'counts'));
    }

    public function create()
    {
        $asistentes = User::whereHas('roles', function($query) {
            $query->where('nombre_rol', 'Asistente de Terna');
        })->get();
        
        $integrantes = IntegranteTerna::all();
        
        return view('terna.admin.create', compact('asistentes', 'integrantes'));
    }

    public function store(Request $request)
    {
        $today = Carbon::today()->toDateString();
        
        $request->validate([
            'descripcion' => 'required|string|max:30|regex:/^[a-zA-Z0-9\sáéíóúÁÉÍÓÚñÑ.,;:¿?¡!()\-]+$/',
            'fecha_defensa' => 'required|date|after_or_equal:'.$today,
            'id_asistente' => 'required|exists:usuario,id_usuario',
            'responsable' => 'required|string|max:30|regex:/^[a-zA-Z\sáéíóúÁÉÍÓÚñÑ]+$/',
            'fecha_limite' => 'required|date|after_or_equal:'.$today,
            'estudiante_nombre' => 'required|string|max:100',
            'estudiante_cuenta' => 'required|string|max:20|regex:/^[0-9]+$/',
            'estudiante_carrera' => 'required|string|max:50',
            'metodologo_id' => 'required|exists:integrantes_terna,id',
            'tecnico1_id' => 'required|exists:integrantes_terna,id',
            'tecnico2_id' => 'required|exists:integrantes_terna,id',
            // Documentos ahora son opcionales (archivo o enlace)
            'documento_fisico' => 'nullable|file|mimes:pdf|max:2048',
            'documento_fisico_enlace' => 'nullable|url',
            'solvencia_cobranza' => 'nullable|file|mimes:pdf|max:2048',
            'solvencia_cobranza_enlace' => 'nullable|url',
            'acta_graduacion' => 'nullable|file|mimes:pdf|max:2048',
            'acta_graduacion_enlace' => 'nullable|url',
        ], [
            'fecha_defensa.after_or_equal' => 'La fecha de defensa debe ser igual o posterior a hoy.',
            'fecha_limite.after_or_equal' => 'La fecha límite debe ser igual o posterior a hoy.',
            'descripcion.regex' => 'La descripción solo puede contener letras, números y signos de puntuación.',
            'responsable.regex' => 'El responsable solo puede contener letras y espacios.',
            'estudiante_cuenta.regex' => 'El número de cuenta solo puede contener números.',
            'documento_fisico_enlace.url' => 'El enlace del documento físico debe ser una URL válida.',
            'solvencia_cobranza_enlace.url' => 'El enlace de solvencia de cobranza debe ser una URL válida.',
            'acta_graduacion_enlace.url' => 'El enlace del acta de graduación debe ser una URL válida.',
        ]);

        $this->validateDocumentPresence($request);

        // Obtener último ID para código secuencial
        $ultimoId = PagoTerna::max('id') ?? 0;
        
        $pagoTerna = PagoTerna::create([
            'codigo' => 'TERNA-' . ($ultimoId + 1),
            'descripcion' => $request->descripcion,
            'fecha_defensa' => $request->fecha_defensa,
            'responsable' => $request->responsable,
            'fecha_limite' => $request->fecha_limite,
            'id_administrador' => auth()->id(),
            'id_asistente' => $request->id_asistente,
            'estudiante_nombre' => $request->estudiante_nombre,
            'estudiante_cuenta' => $request->estudiante_cuenta,
            'estudiante_carrera' => $request->estudiante_carrera,
            'metodologo_id' => $request->metodologo_id,
            'tecnico1_id' => $request->tecnico1_id,
            'tecnico2_id' => $request->tecnico2_id,
        ]);

        $this->subirDocumentos($pagoTerna, $request);

        $pagoTerna->update([
            'estado' => 'en_revision',
            'fecha_envio_admin' => now()
        ]);

        // Notificar al asistente
        $asistente = User::find($request->id_asistente);
        if ($asistente) {
            $asistente->notify(new NuevoProcesoTernaNotification($pagoTerna));
        }
        
        // Registrar en bitácora
        $this->registrarBitacora('crear_proceso_terna', 'PagoTerna', $pagoTerna->id, [], $pagoTerna->toArray());
        
        return redirect()->route('terna.admin.index')->with('success', 'Proceso creado y enviado al asistente');
    }

    private function validateDocumentPresence(Request $request)
    {
        $documentos = [
            'documento_fisico' => $request->documento_fisico_enlace,
            'solvencia_cobranza' => $request->solvencia_cobranza_enlace,
            'acta_graduacion' => $request->acta_graduacion_enlace,
        ];

        foreach ($documentos as $archivo => $enlace) {
            if (!$request->hasFile($archivo) && empty($enlace)) {
                throw ValidationException::withMessages([
                    $archivo => 'Debe proporcionar un archivo PDF o un enlace para este documento'
                ]);
            }
        }
    }

    private function subirDocumentos($pagoTerna, $request)
    {
        $documentos = [
            [
                'tipo' => 'documento_fisico', 
                'file' => $request->file('documento_fisico'),
                'enlace' => $request->documento_fisico_enlace
            ],
            [
                'tipo' => 'solvencia_cobranza', 
                'file' => $request->file('solvencia_cobranza'),
                'enlace' => $request->solvencia_cobranza_enlace
            ],
            [
                'tipo' => 'acta_graduacion', 
                'file' => $request->file('acta_graduacion'),
                'enlace' => $request->acta_graduacion_enlace
            ],
        ];

        foreach ($documentos as $doc) {
            $path = null;
            $tipo_archivo = 'archivo';
            
            if ($doc['file']) {
                $nombreResponsable = Str::slug($pagoTerna->responsable);
                $nombreOriginal = $doc['file']->getClientOriginalName();
                $nombreArchivo = Str::slug(pathinfo($nombreOriginal, PATHINFO_FILENAME)) 
                         . '.' . $doc['file']->getClientOriginalExtension();
                
                $path = $doc['file']->storeAs(
                    '',
                    $nombreArchivo,
                    'documentos_terna'
                );
            } elseif ($doc['enlace']) {
                $path = $doc['enlace'];
                $tipo_archivo = 'enlace';
            } else {
                continue;
            }

            DocumentoTerna::create([
                'pago_terna_id' => $pagoTerna->id,
                'tipo' => $doc['tipo'],
                'ruta_archivo' => $path,
                'tipo_archivo' => $tipo_archivo
            ]);
        }
    }

    public function show($id)
    {
        $pagoTerna = PagoTerna::with('documentos', 'administrador', 'asistente', 'metodologo', 'tecnico1', 'tecnico2')->findOrFail($id);
        
        // Obtener identidades de los integrantes
        $identidades = [
            'metodologo' => $pagoTerna->metodologo->ruta_identidad,
            'tecnico1' => $pagoTerna->tecnico1->ruta_identidad,
            'tecnico2' => $pagoTerna->tecnico2->ruta_identidad,
        ];
        
        return view('terna.admin.show', compact('pagoTerna', 'identidades'));
    }

    public function edit($id)
    {
        $pagoTerna = PagoTerna::findOrFail($id);
        $asistentes = User::whereHas('roles', function($query) {
            $query->where('nombre_rol', 'Asistente de Terna');
        })->get();
        
        $integrantes = IntegranteTerna::all();
        
        return view('terna.admin.edit', compact('pagoTerna', 'asistentes', 'integrantes'));
    }

    public function update(Request $request, $id)
    {
        $today = Carbon::today()->toDateString();
        
        $request->validate([
            'descripcion' => 'required|string|max:30|regex:/^[a-zA-Z0-9\sáéíóúÁÉÍÓÚñÑ.,;:¿?¡!()\-]+$/',
            'fecha_defensa' => 'required|date|after_or_equal:'.$today,
            'id_asistente' => 'required|exists:usuario,id_usuario',
            'responsable' => 'required|string|max:30|regex:/^[a-zA-Z\sáéíóúÁÉÍÓÚñÑ]+$/',
            'fecha_limite' => 'required|date|after_or_equal:'.$today,
            'estudiante_nombre' => 'required|string|max:100',
            'estudiante_cuenta' => 'required|string|max:20|regex:/^[0-9]+$/',
            'estudiante_carrera' => 'required|string|max:50',
            'metodologo_id' => 'required|exists:integrantes_terna,id',
            'tecnico1_id' => 'required|exists:integrantes_terna,id',
            'tecnico2_id' => 'required|exists:integrantes_terna,id',
        ], [
            'fecha_defensa.after_or_equal' => 'La fecha de defensa debe ser igual o posterior a hoy.',
            'fecha_limite.after_or_equal' => 'La fecha límite debe ser igual o posterior a hoy.',
            'descripcion.regex' => 'La descripción solo puede contener letras, números y signos de puntuación.',
            'responsable.regex' => 'El responsable solo puede contener letras y espacios.',
            'estudiante_cuenta.regex' => 'El número de cuenta solo puede contener números.',
        ]);

        $pagoTerna = PagoTerna::findOrFail($id);

        // Guardar datos antes de la actualización
        $datos_antes = $pagoTerna->toArray();

        $pagoTerna->update($request->all());

        // Registrar en bitácora
        $this->registrarBitacora('editar_proceso_terna', 'PagoTerna', $pagoTerna->id, $datos_antes, $pagoTerna->toArray());

        return redirect()->route('terna.admin.index')->with('success', 'Proceso actualizado correctamente');
    }

    public function destroy($id)
    {
        $pagoTerna = PagoTerna::findOrFail($id);
        
        // Guardar datos antes de eliminar
        $datos_antes = $pagoTerna->toArray();

        // Eliminar documentos asociados
        foreach ($pagoTerna->documentos as $documento) {
            if ($documento->tipo_archivo === 'archivo') {
                Storage::disk('documentos_terna')->delete($documento->ruta_archivo);
            }
            $documento->delete();
        }
        
        $pagoTerna->delete();
        
        // Registrar en bitácora
        $this->registrarBitacora('eliminar_proceso_terna', 'PagoTerna', $id, $datos_antes, []);

        return redirect()->route('terna.admin.index')->with('success', 'Proceso eliminado correctamente');
    }

    public function marcarPagado($id)
    {
        $pagoTerna = PagoTerna::findOrFail($id);

        // Guardar datos antes de marcar como pagado
        $datos_antes = $pagoTerna->toArray();

        $pagoTerna->update([
            'estado' => 'pagado',
            'fecha_pago' => now()
        ]);

        // Registrar en bitácora
        $this->registrarBitacora('marcar_pagado_terna', 'PagoTerna', $pagoTerna->id, $datos_antes, $pagoTerna->toArray());

        return back()->with('success', 'Pago marcado como realizado');
    }
    
    public function storeIntegrante(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'cuenta' => 'required|string|max:20|regex:/^[0-9]+$/',
            'identidad' => 'nullable|file|mimes:pdf|max:2048',
        ], [
            'cuenta.regex' => 'El número de cuenta solo puede contener números.',
        ]);

        $integrante = new IntegranteTerna();
        $integrante->nombre = $request->nombre;
        $integrante->cuenta = $request->cuenta;

        if ($request->hasFile('identidad')) {
            $file = $request->file('identidad');
            $fileName = Str::slug($request->nombre) . '_identidad.' . $file->getClientOriginalExtension();
            $path = $file->storeAs(
                'integrantes_identidad',
                $fileName,
                'public'
            );
            $integrante->ruta_identidad = $path;
        }

        $integrante->save();

        // Registrar en bitácora
        $this->registrarBitacora('crear_integrante_terna', 'IntegranteTerna', $integrante->id, [], $integrante->toArray());

        return response()->json($integrante);
    }

    // MÉTODO NUEVO AGREGADO PARA DESCARGAR DOCUMENTOS
    public function descargarDocumento($id)
    {
        $documento = DocumentoTerna::findOrFail($id);
        $pagoTerna = $documento->pagoTerna;
        
        // Verificar permisos
        if ($pagoTerna->id_administrador != auth()->id() && 
            $pagoTerna->id_asistente != auth()->id() && 
            !auth()->user()->tieneRol('SuperAdmin')) {
            abort(403, 'No tienes permiso para descargar este documento.');
        }

        // Registrar en bitácora
        $this->registrarBitacora('descargar_documento_terna', 'PagoTerna', $pagoTerna->id, [], [
            'documento_tipo' => $documento->tipo,
            'documento_id' => $documento->id,
            'tipo_archivo' => $documento->tipo_archivo
        ]);

        if ($documento->tipo_archivo === 'archivo') {
            return Storage::disk('documentos_terna')->download($documento->ruta_archivo);
        } else {
            return redirect($documento->ruta_archivo);
        }
    }
}
