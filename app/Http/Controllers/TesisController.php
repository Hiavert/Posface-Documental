<?php

namespace App\Http\Controllers;

use App\Models\Tesis;
use App\Models\TipoTesis;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

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

            Tesis::create([
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

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Error en store: '.$e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function exportar(Request $request)
    {
        try {
            $selectedIds = $request->input('ids');
            
            // Convertir a array si es JSON string
            if (!is_array($selectedIds)) {
                $selectedIds = json_decode($selectedIds, true);
            }
            
            if (empty($selectedIds)) {
                return response()->json(['error' => 'No se seleccionaron tesis'], 400);
            }

            $tesis = Tesis::whereIn('id_tesis', $selectedIds)
                      ->whereNotNull('ruta_archivo')
                      ->get();
            
            if ($tesis->isEmpty()) {
                return response()->json(['error' => 'No se encontraron tesis para exportar'], 404);
            }

            // Crear directorio temporal
            $tempDir = storage_path('app/public/temp_export_' . time());
            File::makeDirectory($tempDir, 0755, true, true);
            
            // Copiar archivos al directorio temporal
            foreach ($tesis as $tesisItem) {
                $filePath = storage_path('app/public/tesis/'.$tesisItem->ruta_archivo);
                if (file_exists($filePath)) {
                    // Crear nombre seguro para el archivo
                    $safeTitle = Str::slug($tesisItem->titulo, '_');
                    $newFileName = $safeTitle . '.pdf';
                    $newFilePath = $tempDir . '/' . $newFileName;
                    
                    // Copiar archivo con nuevo nombre
                    copy($filePath, $newFilePath);
                }
            }

            // Crear archivo ZIP manualmente
            $zipFileName = 'tesis_seleccionadas_'.time().'.zip';
            $zipPath = storage_path('app/public/'.$zipFileName);
            
            // Comprimir directorio manualmente
            $this->zipFolder($tempDir, $zipPath);
            
            // Eliminar directorio temporal
            File::deleteDirectory($tempDir);
            
            return response()->download($zipPath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Log::error('Error en exportar: '.$e->getMessage());
            return response()->json(['error' => 'Error interno: '.$e->getMessage()], 500);
        }
    }

    // Método para comprimir directorio sin ZipArchive
    private function zipFolder($sourcePath, $zipPath)
    {
        // Crear archivo ZIP
        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE) === TRUE) {
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($sourcePath),
                \RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($files as $name => $file) {
                if (!$file->isDir()) {
                    $filePath = $file->getRealPath();
                    $relativePath = substr($filePath, strlen($sourcePath) + 1);
                    $zip->addFile($filePath, $relativePath);
                }
            }
            $zip->close();
            return true;
        }
        return false;
    }

    public function update(Request $request, $id)
    {
        try {
            $tesis = Tesis::findOrFail($id);
            
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
            
            if ($tesis->ruta_archivo) {
                Storage::disk('public')->delete('tesis/'.$tesis->ruta_archivo);
            }
            
            $tesis->delete();
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
        
        return Storage::disk('public')->download('tesis/'.$filename);
    }

    public function preview($filename)
    {
        if (!Storage::disk('public')->exists('tesis/'.$filename)) {
            abort(404, "El archivo no existe: $filename");
        }
        
        return Storage::disk('public')->response('tesis/'.$filename, null, [
            'Content-Type' => 'application/pdf'
        ]);
    }
}
        return Storage::disk('public')->response('tesis/'.$filename, null, [
            'Content-Type' => 'application/pdf'
        ]);
    }
}
