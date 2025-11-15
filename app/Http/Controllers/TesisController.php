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
$success = $this->zipFolder($tempDir, $zipPath);

if (!$success || !file_exists($zipPath)) {
    // Limpiar archivos temporales
    File::deleteDirectory($tempDir);
    if (file_exists($zipPath)) {
        unlink($zipPath);
    }
    Log::error('Error al crear archivo comprimido. Verifique permisos de escritura.');
    return response()->json(['error' => 'No se pudo crear el archivo comprimido. Contacte al administrador.'], 500);
}

 // Método para comprimir directorio sin ZipArchive
    private function zipFolder($sourcePath, $zipPath)
{
    // Verificar si podemos usar comandos del sistema
    if (function_exists('shell_exec')) {
        // Intentar con comando zip
        $zipCheck = shell_exec('which zip 2>/dev/null');
        if (!empty($zipCheck)) {
            $command = "cd " . escapeshellarg($sourcePath) . " && zip -r " . escapeshellarg($zipPath) . " . 2>/dev/null";
            exec($command, $output, $returnVar);
            
            if ($returnVar === 0 && file_exists($zipPath)) {
                return true;
            }
        }
        
        // Intentar con tar + gzip
        $tarCheck = shell_exec('which tar 2>/dev/null');
        if (!empty($tarCheck)) {
            $tarPath = $zipPath . '.tar.gz';
            $command = "cd " . escapeshellarg($sourcePath) . " && tar -czf " . escapeshellarg($tarPath) . " . 2>/dev/null";
            exec($command, $output, $returnVar);
            
            if ($returnVar === 0 && file_exists($tarPath)) {
                // Renombrar para mantener extensión .zip
                rename($tarPath, $zipPath);
                return true;
            }
        }
    }
    
    // Si todo falla, crear un ZIP manual con PHP puro
    return $this->createZipManual($sourcePath, $zipPath);
}

// Método para crear ZIP manualmente con PHP
private function createZipManual($sourcePath, $zipPath)
{
    $files = [];
    
    // Recoger todos los archivos
    $iterator = new \RecursiveIteratorIterator(
        new \RecursiveDirectoryIterator($sourcePath),
        \RecursiveIteratorIterator::LEAVES_ONLY
    );
    
    foreach ($iterator as $file) {
        if (!$file->isDir()) {
            $filePath = $file->getRealPath();
            $relativePath = str_replace($sourcePath . '/', '', $filePath);
            $files[$relativePath] = $filePath;
        }
    }
    
    if (empty($files)) {
        Log::error('No se encontraron archivos para comprimir en: ' . $sourcePath);
        return false;
    }
    
    // Crear archivo ZIP manual
    $zipContent = $this->buildZipContent($files);
    
    if (file_put_contents($zipPath, $zipContent) !== false) {
        return true;
    }
    
    Log::error('No se pudo escribir el archivo ZIP en: ' . $zipPath);
    return false;
}

// Construir contenido ZIP manualmente
private function buildZipContent($files)
{
    $zipContent = '';
    $centralDirectory = '';
    $offset = 0;
    
    foreach ($files as $relativePath => $filePath) {
        // Leer contenido del archivo
        $fileContent = file_get_contents($filePath);
        if ($fileContent === false) {
            continue;
        }
        
        $compressedSize = strlen($fileContent);
        $uncompressedSize = strlen($fileContent);
        $crc = crc32($fileContent);
        
        // Header local del archivo
        $localHeader = $this->buildLocalFileHeader(
            $relativePath,
            $fileContent,
            $crc,
            $compressedSize,
            $uncompressedSize
        );
        
        $zipContent .= $localHeader;
        
        // Entrada del directorio central
        $centralDirectory .= $this->buildCentralDirectoryHeader(
            $relativePath,
            $fileContent,
            $crc,
            $compressedSize,
            $uncompressedSize,
            $offset
        );
        
        $offset += strlen($localHeader);
    }
    
    // Final del directorio central
    $endOfCentralDirectory = $this->buildEndOfCentralDirectory(count($files), strlen($centralDirectory), $offset);
    
    return $zipContent . $centralDirectory . $endOfCentralDirectory;
}

// Construir header local de archivo
private function buildLocalFileHeader($filename, $content, $crc, $compressedSize, $uncompressedSize)
{
    $filenameLength = strlen($filename);
    $header = pack('VvvvVVVVvv', 
        0x04034b50, // Signature
        10,         // Version needed
        0,          // Flags
        0,          // Compression method (0 = store)
        0,          // Mod time
        0,          // Mod date
        $crc,       // CRC32
        $compressedSize, // Compressed size
        $uncompressedSize, // Uncompressed size
        $filenameLength, // Filename length
        0           // Extra field length
    );
    
    return $header . $filename . $content;
}

// Construir entrada del directorio central
private function buildCentralDirectoryHeader($filename, $content, $crc, $compressedSize, $uncompressedSize, $offset)
{
    $filenameLength = strlen($filename);
    $header = pack('VvvvvVVVVvvvvvVV', 
        0x02014b50, // Signature
        20,         // Version made by
        10,         // Version needed
        0,          // Flags
        0,          // Compression method
        0,          // Mod time
        0,          // Mod date
        $crc,       // CRC32
        $compressedSize, // Compressed size
        $uncompressedSize, // Uncompressed size
        $filenameLength, // Filename length
        0,          // Extra field length
        0,          // File comment length
        0,          // Disk number start
        0,          // Internal file attributes
        0,          // External file attributes
        $offset     // Relative offset of local header
    );
    
    return $header . $filename;
}

// Construir fin del directorio central
private function buildEndOfCentralDirectory($fileCount, $centralDirectorySize, $offset)
{
    return pack('VvvvvVVv', 
        0x06054b50, // Signature
        0,          // Disk number
        0,          // Disk number start
        $fileCount, // Entries in this disk
        $fileCount, // Total entries
        $centralDirectorySize, // Central directory size
        $offset,    // Offset of central directory
        0           // Comment length
    );
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
