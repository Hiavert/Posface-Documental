<?php

namespace App\Http\Controllers;

use App\Models\Acuse;
use App\Models\Elemento;
use App\Models\Notificacion;
use App\Models\TipoElemento;
use App\Models\AcuseTransferencia;
use App\Models\AcuseAdjunto;
use App\Models\User;
use App\Notifications\AcuseEnviadoNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class AcuseController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        $query = Acuse::with(['remitente', 'destinatario', 'elementos'])
            ->whereHas('remitente')
            ->whereHas('destinatario');
            
        // Filtro para usuarios no SuperAdmin
        if (!$user->tieneRol('SuperAdmin')) {
            $query->where(function($q) use ($user) {
                $q->where('fk_id_usuario_remitente', $user->id_usuario)
                  ->orWhere('fk_id_usuario_destinatario', $user->id_usuario);
            });
        }

        // Sanitizar inputs
        $filters = [
            'estado' => strip_tags($request->estado),
            'remitente' => strip_tags($request->remitente),
            'destinatario' => strip_tags($request->destinatario),
            'elemento' => strip_tags($request->elemento),
        ];

        // Ordenamiento
        $sort = in_array($request->sort, ['id_acuse', 'titulo', 'estado', 'fecha_envio']) ? 
                $request->sort : 'fecha_envio';
                
        $direction = in_array($request->direction, ['asc', 'desc']) ? 
                    $request->direction : 'desc';
                    
        $query->orderBy($sort, $direction);

        if (!empty($filters['estado'])) {
            $query->where('estado', $filters['estado']);
        }

        if (!empty($filters['remitente'])) {
            $query->whereHas('remitente', function($q) use ($filters) {
                $q->where('nombres', 'like', '%'.$filters['remitente'].'%')
                  ->orWhere('apellidos', 'like', '%'.$filters['remitente'].'%');
            });
        }

        if (!empty($filters['destinatario'])) {
            $query->whereHas('destinatario', function($q) use ($filters) {
                $q->where('nombres', 'like', '%'.$filters['destinatario'].'%')
                  ->orWhere('apellidos', 'like', '%'.$filters['destinatario'].'%');
            });
        }

        if (!empty($filters['elemento'])) {
            $query->whereHas('elementos', function($q) use ($filters) {
                $q->where('nombre', 'like', '%'.$filters['elemento'].'%');
            });
        }

        $acuses = $query->paginate(10);
        $usuarios = User::all();
        $tiposElemento = TipoElemento::all();

        return view('acuses.index', compact('acuses', 'usuarios', 'tiposElemento', 'request'));
    }

    public function reenviarForm($id)
    {
        $acuse = Acuse::with(['elementos.tipo'])->find($id);
        
        if (!$acuse) {
            abort(404, 'Acuse no encontrado');
        }
        
        $usuarios = User::where('id_usuario', '!=', auth()->id())->get();
        
        // Verificar permisos
        $user = auth()->user();
        if (!$user->tieneRol('SuperAdmin') && 
            $acuse->fk_id_usuario_destinatario != $user->id_usuario) {
            abort(403, 'No autorizado');
        }
        
        return view('acuses.reenviar', compact('acuse', 'usuarios'));
    }

    public function reenviar(Request $request, $id)
    {
        DB::beginTransaction();
        
        try {
            $acuse = Acuse::find($id);
            
            if (!$acuse) {
                throw new \Exception('Acuse no encontrado');
            }
            
            $user = auth()->user();
            
            // Verificar permisos
            if (!$user->tieneRol('SuperAdmin') && 
                $acuse->fk_id_usuario_destinatario != $user->id_usuario) {
                throw new \Exception('No tienes permiso para reenviar este acuse');
            }
            
            // Validar destinatario
            $validator = Validator::make($request->all(), [
                'nuevo_destinatario' => [
                    'required',
                    'exists:usuario,id_usuario',
                    Rule::notIn([$user->id_usuario])
                ]
            ], [
                'nuevo_destinatario.not_in' => 'No puedes reenviar el acuse a ti mismo'
            ]);
            
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
            
            // Actualizar el acuse existente
            $acuse->fk_id_usuario_remitente = $user->id_usuario;
            $acuse->fk_id_usuario_destinatario = $request->nuevo_destinatario;
            $acuse->estado = 'pendiente';
            $acuse->fecha_envio = now();
            $acuse->fecha_recepcion = null;
            $acuse->save();
            
            // Registrar transferencia
            AcuseTransferencia::create([
                'fk_id_acuse' => $acuse->id_acuse,
                'fk_id_usuario_origen' => $user->id_usuario,
                'fk_id_usuario_destino' => $request->nuevo_destinatario,
                'fecha_transferencia' => now()
            ]);
            
            // Crear notificación
            Notificacion::create([
                'titulo' => 'Acuse reenviado',
                'mensaje' => 'Se te ha reenviado un acuse de ' . $user->nombres,
                'fk_id_usuario_destinatario' => $request->nuevo_destinatario,
                'fk_id_acuse' => $acuse->id_acuse,
                'fecha' => now(),
            ]);
            
            DB::commit();
            
            return redirect()->route('acuses.index')
                ->with('success', 'Acuse reenviado correctamente');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al reenviar el acuse: ' . $e->getMessage());
        }
    }    

    public function descargarAdjunto($id)
    {
        $adjunto = AcuseAdjunto::find($id);
        
        if (!$adjunto) {
            abort(404, 'Adjunto no encontrado');
        }
        
        $rutaCompleta = storage_path('app/public/' . $adjunto->ruta);
        
        // Verificar que el archivo existe
        if (!file_exists($rutaCompleta)) {
            abort(404, 'El archivo solicitado no existe');
        }
        
        return response()->download($rutaCompleta, $adjunto->nombre_archivo);
    }    

    public function store(Request $request)
    {
        // Validación robusta con sanitización
        $validator = Validator::make($request->all(), [
            'destinatario' => [
                'required', 
                'exists:usuario,id_usuario',
                Rule::notIn([Auth::id()])
            ],
            'titulo' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s\-\.,;:()0-9]+$/u'
            ],
            'descripcion' => [
                'nullable', 
                'string',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s\-\.,;:()0-9]+$/u'
            ],
            'elementos' => 'required|array|min:1',
            'elementos.*.fk_id_tipo' => 'required|exists:tipos_elemento,id_tipo',
            'elementos.*.nombre' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s\-\.,;:()0-9]+$/u'
            ],
            'elementos.*.descripcion' => [
                'nullable', 
                'string',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s\-\.,;:()0-9]+$/u'
            ],
            'elementos.*.cantidad' => 'nullable|integer|min:1|max:1000',
            'adjuntos_documentos.*' => 'nullable|mimes:pdf,doc,docx,xls,xlsx|max:5120',
            'adjuntos_imagenes.*' => 'nullable|image|mimes:jpeg,png,gif|max:5120'
        ], [
            'titulo.regex' => 'Solo se permiten letras, números y signos de puntuación básicos',
            'descripcion.regex' => 'Solo se permiten letras, números y signos básicos',
            'elementos.*.nombre.regex' => 'Solo se permiten letras, números y signos básicos',
            'elementos.*.descripcion.regex' => 'Solo se permiten letras, números y signos básicos',
            'elementos.*.cantidad.max' => 'La cantidad máxima permitida es 1000',
            'adjuntos_documentos.*.max' => 'El archivo no debe exceder 5MB',
            'adjuntos_imagenes.*.max' => 'La imagen no debe exceder 5MB',
            'destinatario.not_in' => 'No puedes enviar un acuse a ti mismo'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        
        DB::beginTransaction();

        try {
            $acuse = new Acuse();
            $acuse->titulo = strip_tags($request->titulo);
            $acuse->descripcion = strip_tags($request->descripcion);
            $acuse->fk_id_usuario_remitente = Auth::user()->id_usuario;
            $acuse->fk_id_usuario_destinatario = $request->destinatario;
            $acuse->estado = 'pendiente';
            $acuse->fecha_envio = now();
            $acuse->save();

            foreach ($request->elementos as $elementoData) {
                $elemento = new Elemento();
                $elemento->nombre = strip_tags($elementoData['nombre']);
                $elemento->descripcion = isset($elementoData['descripcion']) ? 
                                         strip_tags($elementoData['descripcion']) : null;
                $elemento->cantidad = $elementoData['cantidad'] ?? 1;
                $elemento->fk_id_tipo = $elementoData['fk_id_tipo'];
                $elemento->fk_id_acuse = $acuse->id_acuse;
                $elemento->save();
            }

            $notificacion = new Notificacion();
            $notificacion->titulo = 'Nuevo acuse de recibo';
            $notificacion->mensaje = 'Tienes un nuevo acuse de recibo de ' . Auth::user()->nombres;
            $notificacion->fk_id_usuario_destinatario = $request->destinatario;
            $notificacion->fk_id_acuse = $acuse->id_acuse;
            $notificacion->fecha = now();
            $notificacion->save();

            $user = User::find($request->destinatario);
            $user->notify(new AcuseEnviadoNotification(
                'Nuevo Acuse',
                'Nuevo acuse de recibo.',
                Auth::user()->nombres,
                route('acuses.index')
            ));
            
            // Guardar archivos adjuntos
            if ($request->hasFile('adjuntos_documentos')) {
                foreach ($request->file('adjuntos_documentos') as $archivo) {
                    $nombre = time() . '_' . preg_replace('/[^a-zA-Z0-9\.]/', '_', $archivo->getClientOriginalName());
                    $ruta = $archivo->storeAs('adjuntos_acuses/documentos', $nombre, 'public');
                    
                    AcuseAdjunto::create([
                        'fk_id_acuse' => $acuse->id_acuse,
                        'tipo' => 'documento',
                        'nombre_archivo' => $archivo->getClientOriginalName(),
                        'ruta' => $ruta
                    ]);
                }
            }
            
            if ($request->hasFile('adjuntos_imagenes')) {
                foreach ($request->file('adjuntos_imagenes') as $archivo) {
                    $nombre = time() . '_' . preg_replace('/[^a-zA-Z0-9\.]/', '_', $archivo->getClientOriginalName());
                    $ruta = $archivo->storeAs('adjuntos_acuses/imagenes', $nombre, 'public');
                    
                    AcuseAdjunto::create([
                        'fk_id_acuse' => $acuse->id_acuse,
                        'tipo' => 'imagen',
                        'nombre_archivo' => $archivo->getClientOriginalName(),
                        'ruta' => $ruta
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('acuses.index')->with('success', 'Acuse enviado correctamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error al crear el acuse: ' . $e->getMessage());
        }
    }

    public function rastrear($id)
    {
        $acuse = Acuse::with(['transferencias.origen', 'transferencias.destino'])
            ->whereHas('remitente')
            ->whereHas('destinatario')
            ->find($id);
        
        if (!$acuse) {
            abort(404, 'Acuse no encontrado');
        }
        
        // Verificar permisos
        $user = auth()->user();
        if (!$user->tieneRol('SuperAdmin') && 
            $acuse->fk_id_usuario_remitente != $user->id_usuario && 
            $acuse->fk_id_usuario_destinatario != $user->id_usuario) {
            abort(403, 'No autorizado');
        }
        
        $historial = collect();
        
        // Evento de creación inicial
        $historial->push([
            'fecha' => $acuse->fecha_envio,
            'tipo' => 'creacion',
            'remitente' => $acuse->remitente,
            'destinatario' => $acuse->destinatario,
            'accion' => 'Creación del acuse'
        ]);
        
        // Eventos de transferencia
        foreach ($acuse->transferencias as $transferencia) {
            $historial->push([
                'fecha' => $transferencia->fecha_transferencia,
                'tipo' => 'transferencia',
                'remitente' => $transferencia->origen,
                'destinatario' => $transferencia->destino,
                'accion' => 'Reenvío del acuse'
            ]);
        }
        
        // Ordenar por fecha (más antiguo primero)
        $historial = $historial->sortBy('fecha');
        
        // Obtener poseedor actual
        $poseedorActual = $historial->last()['destinatario'];
        
        return view('acuses.rastreo', compact('acuse', 'historial', 'poseedorActual'));
    }

    public function show($id)
    {
        $acuse = Acuse::with(['remitente', 'destinatario', 'elementos.tipo', 'adjuntos'])
            ->whereHas('remitente')
            ->whereHas('destinatario')
            ->find($id);
        
        if (!$acuse) {
            abort(404, 'Acuse no encontrado');
        }
        
        // Verificar permisos
        $user = auth()->user();
        if (!$user->tieneRol('SuperAdmin') && 
            $acuse->fk_id_usuario_remitente != $user->id_usuario && 
            $acuse->fk_id_usuario_destinatario != $user->id_usuario) {
            abort(403, 'No autorizado');
        }
        
        return view('acuses.show', compact('acuse'));
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $acuse = Acuse::find($id);
            
            if (!$acuse) {
                throw new \Exception('Acuse no encontrado');
            }
            
            // Verificar permisos
            $user = auth()->user();
            if (!$user->tieneRol('SuperAdmin') && 
                $acuse->fk_id_usuario_remitente != $user->id_usuario && 
                $acuse->fk_id_usuario_destinatario != $user->id_usuario) {
                abort(403, 'No autorizado');
            }
            
            // 1. Eliminar adjuntos (archivos físicos y registros)
            foreach ($acuse->adjuntos as $adjunto) {
                // Eliminar archivo físico
                $filePath = storage_path('app/public/' . $adjunto->ruta);
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
                // Eliminar registro
                $adjunto->delete();
            }
            
            // 2. Eliminar elementos
            Elemento::where('fk_id_acuse', $id)->delete();
            
            // 3. Eliminar transferencias
            AcuseTransferencia::where('fk_id_acuse', $id)->delete();
            
            // 4. Eliminar notificaciones relacionadas
            Notificacion::where('fk_id_acuse', $id)->delete();
            
            // 5. Finalmente eliminar el acuse
            $acuse->delete();
            
            DB::commit();
            
            return redirect()->route('acuses.index')
                ->with('success', 'Acuse eliminado correctamente');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al eliminar el acuse: ' . $e->getMessage());
        }
    }

    public function aceptar($id)
    {
        $acuse = Acuse::find($id);
        
        if (!$acuse) {
            abort(404, 'Acuse no encontrado');
        }

        // Verificar permisos
        $user = auth()->user();
        if (!$user->tieneRol('SuperAdmin') && 
            $acuse->fk_id_usuario_destinatario != $user->id_usuario) {
            abort(403, 'No autorizado');
        }

        $acuse->estado = 'recibido';
        $acuse->fecha_recepcion = now();
        $acuse->save();

        return redirect()->route('acuses.index')->with('success', 'Acuse marcado como recibido');
    }
}