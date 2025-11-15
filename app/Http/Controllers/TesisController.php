<?php

namespace App\Http\Controllers;

use App\Models\Tesis;
use App\Models\TipoTesis;
use App\Models\Region;
use App\Models\Bitacora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class TesisController extends Controller
{
    public function index()
    {
        $tiposTesis = TipoTesis::all();
        $regiones = Region::all();
        return view('tesis.index', compact('tiposTesis', 'regiones'));
    }

    public function list(Request $request)
    {
        try {
            $query = Tesis::with(['tipo', 'region', 'usuario']);

            // Aplicar filtros
            if ($request->filled('tipo')) {
                $query->where('fk_id_tipo_tesis', $request->tipo);
            }
            if ($request->filled('region')) {
                $query->where('fk_id_region', $request->region);
            }
            if ($request->filled('responsable')) {
                $query->where('autor', 'like', '%'.$request->responsable.'%');
            }
            if ($request->filled('cuenta')) {
                $query->where('numero_cuenta', 'like', '%'.$request->cuenta.'%');
            }
            if ($request->filled('busqueda')) {
                $query->where(function($q) use ($request) {
                    $q->where('titulo', 'like', '%'.$request->busqueda.'%')
                      ->orWhere('autor', 'like', '%'.$request->busqueda.'%')
                      ->orWhere('numero_cuenta', 'like', '%'.$request->busqueda.'%');
                });
            }

            // Ordenamiento
            $sortColumn = $request->input('sortColumn', 'id_tesis');
            $sortDirection = $request->input('sortDirection', 'asc');
            $query->orderBy($sortColumn, $sortDirection);

            return response()->json($query->paginate(10));
        } catch (\Exception $e) {
            Log::error('Error en list: '.$e->getMessage());
            return response()->json(['error' => 'Error al cargar datos'], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'titulo' => 'required|max:255|string',
                'tipo_tesis' => 'required|exists:tipos_tesis,id_tipo_tesis',
                'region' => 'required|exists:regiones,id_region',
                'autor' => 'required|max:255|regex:/^[\pL\s\-]+$/u',
                'numero_cuenta' => 'required|max:20|regex:/^[0-9]+$/',
                'fecha_defensa' => 'required|date|before_or_equal:today',
                'documento' => 'required|file|mimes:pdf|max:30720',
            ], [
                'autor.regex' => 'El campo autor solo puede contener letras y espacios',
                'numero_cuenta.regex' => 'El campo número de cuenta solo puede contener números',
                'fecha_defensa.before_or_equal' => 'La fecha de defensa no puede ser mayor a la fecha actual',
                'documento.mimes' => 'El documento debe ser un archivo PDF',
                'documento.max' => 'El tamaño máximo del documento es 30MB',
            ]);

            $file = $request->file('documento');
            $filename = 'tesis_'.time().'.'.$file->extension();
            $path = $file->storeAs('tesis', $filename, 'public');

            $tesis = Tesis::create([
                'titulo' => strip_tags($request->titulo),
                'fk_id_tipo_tesis' => $request->tipo_tesis,
                'fk_id_region' => $request->region,
                'autor' => strip_tags($request->autor),
                'numero_cuenta' => strip_tags($request->numero_cuenta),
                'ruta_archivo' => $filename,
                'fk_id_usuario' => auth()->id() ?: 15,
                'carrera' => 'No especificado',
                'tema' => 'No especificado',
                'anio' => now()->year,
                'fecha_defensa' => $request->fecha_defensa,
                'fecha_subida' => now()->toDateString()
            ]);

            // Registrar en bitácora
            $this->registrarBitacora('subir_tesis', $tesis->id_tesis, [], $tesis->toArray());

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Error en store: '.$e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $tesis = Tesis::findOrFail($id);
            
            // Guardar datos antes de la actualización
            $datos_antes = $tesis->toArray();
            
            $request->validate([
                'titulo' => 'required|max:255|string',
                'tipo_tesis' => 'required|exists:tipos_tesis,id_tipo_tesis',
                'region' => 'required|exists:regiones,id_region',
                'autor' => 'required|max:255|regex:/^[\pL\s\-]+$/u',
                'numero_cuenta' => 'required|max:20|regex:/^[0-9]+$/',
                'fecha_defensa' => 'required|date|before_or_equal:today',
                'documento' => 'nullable|file|mimes:pdf|max:30720',
            ], [
                'autor.regex' => 'El campo autor solo puede contener letras y espacios',
                'numero_cuenta.regex' => 'El campo número de cuenta solo puede contener números',
                'fecha_defensa.before_or_equal' => 'La fecha de defensa no puede ser mayor a la fecha actual',
                'documento.mimes' => 'El documento debe ser un archivo PDF',
                'documento.max' => 'El tamaño máximo del documento es 30MB',
            ]);

            $data = [
                'titulo' => strip_tags($request->titulo),
                'fk_id_tipo_tesis' => $request->tipo_tesis,
                'fk_id_region' => $request->region,
                'autor' => strip_tags($request->autor),
                'numero_cuenta' => strip_tags($request->numero_cuenta),
                'fecha_defensa' => $request->fecha_defensa,
            ];

            if ($request->hasFile('documento')) {
                if ($tesis->ruta_archivo) {
                    Storage::disk('public')->delete('tesis/'.$tesis->ruta_archivo);
                }
                
                $file = $request->file('documento');
                $filename = 'tesis_'.time().'.'.$file->extension();
                $path = $file->storeAs('tesis', $filename, 'public');
                $data['ruta_archivo'] = $filename;
            }

            $tesis->update($data);

            // Registrar en bitácora
            $this->registrarBitacora('editar_tesis', $tesis->id_tesis, $datos_antes, $tesis->fresh()->toArray());

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Error en update: '.$e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $tesis = Tesis::findOrFail($id);
            
            // Guardar datos antes de eliminar
            $datos_antes = $tesis->toArray();
            
            if ($tesis->ruta_archivo) {
                Storage::disk('public')->delete('tesis/'.$tesis->ruta_archivo);
            }
            
            $tesis->delete();

            // Registrar en bitácora
            $this->registrarBitacora('eliminar_tesis', $id, $datos_antes, []);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Error en destroy: '.$e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function download($filename)
    {
        // Usar Storage facade para mejor compatibilidad
        if (!Storage::disk('public')->exists('tesis/'.$filename)) {
            abort(404, "El archivo no existe: $filename");
        }

        // Obtener la tesis relacionada con el archivo
        $tesis = Tesis::where('ruta_archivo', $filename)->first();
        
        if ($tesis) {
            // Registrar en bitácora
            $this->registrarBitacora('descargar_tesis', $tesis->id_tesis, [], []);
        }
        
        return Storage::disk('public')->download('tesis/'.$filename);
    }

    public function preview($filename)
    {
        if (!Storage::disk('public')->exists('tesis/'.$filename)) {
            abort(404, "El archivo no existe: $filename");
        }

        // Obtener la tesis relacionada con el archivo
        $tesis = Tesis::where('ruta_archivo', $filename)->first();
        
        if ($tesis) {
            // Registrar en bitácora
            $this->registrarBitacora('previsualizar_tesis', $tesis->id_tesis, [], []);
        }
        
        return Storage::disk('public')->response('tesis/'.$filename, null, [
            'Content-Type' => 'application/pdf'
        ]);
    }

    /**
     * Registra una acción en la bitácora
     */
    protected function registrarBitacora($accion, $registro_id, $datos_antes = [], $datos_despues = [])
    {
        // Verificar permisos si es necesario
        // if (!auth()->user()->puedeEditar('Tesis')) {
        //     return;
        // }

        Bitacora::create([
            'user_id' => auth()->id(),
            'usuario_nombre' => auth()->user()->name ?? 'Usuario no autenticado',
            'accion' => $accion,
            'modulo' => 'Tesis',
            'registro_id' => $registro_id,
            'datos_antes' => $datos_antes,
            'datos_despues' => $datos_despues,
            'ip' => request()->ip(),
        ]);
    }
}