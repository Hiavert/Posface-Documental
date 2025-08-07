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
use Carbon\Carbon;

class AcuseController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        $query = Acuse::with(['remitente', 'destinatario', 'elementos']);
            
        // Filtro para usuarios no SuperAdmin
        if (!$user->tieneRol('SuperAdmin')) {
            $query->where(function($q) use ($user) {
                $q->where('fk_id_usuario_remitente', $user->id_usuario)
                  ->orWhere('fk_id_usuario_destinatario', $user->id_usuario);
            });
        }

        // Ordenamiento
        $sort = $request->get('sort', 'fecha_envio');
        $direction = $request->get('direction', 'desc');
        $query->orderBy($sort, $direction);

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('remitente')) {
            $query->whereHas('remitente', function($q) use ($request) {
                $q->where('nombres', 'like', '%'.$request->remitente.'%')
                  ->orWhere('apellidos', 'like', '%'.$request->remitente.'%');
            });
        }

        if ($request->filled('destinatario')) {
            $query->whereHas('destinatario', function($q) use ($request) {
                $q->where('nombres', 'like', '%'.$request->destinatario.'%')
                  ->orWhere('apellidos', 'like', '%'.$request->destinatario.'%');
            });
        }

        if ($request->filled('elemento')) {
            $query->whereHas('elementos', function($q) use ($request) {
                $q->where('nombre', 'like', '%'.$request->elemento.'%');
            });
        }

        $acuses = $query->paginate(10);
        $usuarios = User::all();
        $tiposElemento = TipoElemento::all();

        return view('acuses.index', compact('acuses', 'usuarios', 'tiposElemento', 'request'));
    }

    public function reenviarForm($id)
    {
        $acuse = Acuse::findOrFail($id);
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
            $acuse = Acuse::findOrFail($id);
            $user = auth()->user();
            
            // Verificar permisos
            if (!$user->tieneRol('SuperAdmin') && 
                $acuse->fk_id_usuario_destinatario != $user->id_usuario) {
                throw new \Exception('No tienes permiso para reenviar este acuse');
            }
            
            // Actualizar el acuse existente en lugar de crear uno nuevo
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
        $adjunto = AcuseAdjunto::findOrFail($id);
        $rutaCompleta = storage_path('app/public/' . $adjunto->ruta);
        
        // Verificar que el archivo existe
        if (!file_exists($rutaCompleta)) {
            abort(404, 'El archivo solicitado no existe');
        }
        
        return response()->download($rutaCompleta, $adjunto->nombre_archivo);
    }    

    public function store(Request $request)
    {
        $request->validate([
            'destinatario' => 'required|exists:usuario,id_usuario',
            'titulo' => 'required|string|max:255|regex:/^[\pL\s\-\.,;:()0-9]+$/u',
            'descripcion' => 'nullable|string|regex:/^[\pL\s\-\.,;:()0-9]+$/u',
            'elementos' => 'required|array|min:1',
            'elementos.*.fk_id_tipo' => 'required|exists:tipos_elemento,id_tipo',
            'elementos.*.nombre' => 'required|string|max:255|regex:/^[\pL\s\-\.,;:()0-9]+$/u',
            'elementos.*.descripcion' => 'nullable|string|regex:/^[\pL\s\-\.,;:()0-9]+$/u',
            'elementos.*.cantidad' => 'nullable|integer|min:1',
            'adjuntos_documentos.*' => 'nullable|mimes:pdf,doc,docx,xls,xlsx|max:5120',
            'adjuntos_imagenes.*' => 'nullable|image|mimes:jpeg,png,gif|max:5120'
        ], [
            'titulo.regex' => 'El título solo permite letras, números y signos de puntuación básicos',
            'elementos.*.nombre.regex' => 'El nombre del elemento solo permite letras, números y signos de puntuación básicos',
            'elementos.*.descripcion.regex' => 'La descripción del elemento solo permite letras, números y signos de puntuación básicos'
        ]);
        
        DB::beginTransaction();

        try {
            $acuse = new Acuse();
            $acuse->titulo = $request->titulo;
            $acuse->descripcion = $request->descripcion;
            $acuse->fk_id_usuario_remitente = Auth::user()->id_usuario;
            $acuse->fk_id_usuario_destinatario = $request->destinatario;
            $acuse->estado = 'pendiente';
            $acuse->fecha_envio = now();
            $acuse->save();

            foreach ($request->elementos as $elementoData) {
                $elemento = new Elemento();
                $elemento->nombre = $elementoData['nombre'];
                $elemento->descripcion = $elementoData['descripcion'] ?? null;
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
                route('acuses.index') // Aquí apuntás directamente al index
            ));
            // Guardar archivos adjuntos
            if ($request->hasFile('adjuntos_documentos')) {
                foreach ($request->file('adjuntos_documentos') as $archivo) {
                    $nombre = time() . '_' . $archivo->getClientOriginalName();
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
                    $nombre = time() . '_' . $archivo->getClientOriginalName();
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
        $acuse = Acuse::with(['transferencias.origen', 'transferencias.destino'])->findOrFail($id);
        
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
        $acuse = Acuse::with(['remitente', 'destinatario', 'elementos.tipo', 'adjuntos'])->findOrFail($id);
        
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
            $acuse = Acuse::findOrFail($id);
            
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
                    unlink($filePath);
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
        $acuse = Acuse::findOrFail($id);

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