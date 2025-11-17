<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

class BackupController extends Controller
{
    public function index()
    {
        $backupFiles = $this->getBackupFiles();
        return view('backup.index', compact('backupFiles'));
    }

    public function createBackup()
    {
        try {
            $filename = 'backup-' . date('Y-m-d-His') . '.sql';
            
            // Usar el disco 'public' que es el default en tu configuración
            $disk = Storage::disk('public');
            $backupDir = 'backups';

            \Log::info("Iniciando creación de backup: " . $filename);
            \Log::info("Disco: public");
            \Log::info("Directorio: " . $backupDir);

            // Crear carpeta si no existe usando Storage
            if (!$disk->exists($backupDir)) {
                \Log::info("Creando directorio: " . $backupDir);
                $disk->makeDirectory($backupDir);
            }

            $filePath = $backupDir . '/' . $filename;
            \Log::info("Ruta completa: " . $filePath);

            // Dump de todas las tablas
            $tables = DB::select('SHOW TABLES');
            $sqlDump = '';

            foreach ($tables as $tableObj) {
                $table = array_values((array)$tableObj)[0];
                
                // Obtener estructura de la tabla
                $createTable = DB::select("SHOW CREATE TABLE `$table`")[0]->{'Create Table'};
                $sqlDump .= $createTable . ";\n\n";

                // Obtener datos de la tabla
                $rows = DB::table($table)->get();
                if ($rows->count() > 0) {
                    foreach ($rows as $row) {
                        $rowArray = (array)$row;
                        $columns = implode('`,`', array_keys($rowArray));
                        $values = implode("','", array_map(function($v) {
                            return str_replace("'", "''", $v);
                        }, $rowArray));
                        $sqlDump .= "INSERT INTO `$table` (`$columns`) VALUES ('$values');\n";
                    }
                    $sqlDump .= "\n";
                }
            }

            // Guardar usando Storage
            $disk->put($filePath, $sqlDump);

            // Verificar que el archivo se creó
            if (!$disk->exists($filePath)) {
                throw new \Exception("El archivo no se creó correctamente");
            }

            $fileSize = $disk->size($filePath);
            \Log::info("Backup creado exitosamente: " . $filename . " (" . $fileSize . " bytes)");

            // Registrar en bitácora
            $this->registrarBitacora('crear_backup', 'Backup', null, [], ['filename' => $filename]);

            return response()->json([
                'success' => true, 
                'message' => 'Backup creado correctamente', 
                'filename' => $filename
            ]);

        } catch (\Exception $e) {
            \Log::error('Error al crear backup: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Error al crear backup: ' . $e->getMessage()
            ], 500);
        }
    }

    public function downloadBackup($filename)
    {
        try {
            // Usar el disco 'public' que es donde se están guardando los archivos
            $disk = Storage::disk('public');
            $filePath = 'backups/' . $filename;

            \Log::info("Intentando descargar backup: " . $filePath);
            \Log::info("Archivos en backups: " . json_encode($disk->files('backups')));

            if (!$disk->exists($filePath)) {
                \Log::error("Archivo no encontrado: " . $filePath);
                return redirect()->route('backup.index')
                    ->with('error', 'Archivo de backup no encontrado: ' . $filename);
            }

            \Log::info("Archivo encontrado, procediendo a descargar: " . $filePath);

            // Registrar en bitácora
            $this->registrarBitacora('descargar_backup', 'Backup', null, [], ['filename' => $filename]);

            // Para Railway, necesitamos usar la respuesta de descarga de Laravel
            $fileContent = $disk->get($filePath);
            $fileSize = $disk->size($filePath);

            return Response::make($fileContent, 200, [
                'Content-Type' => 'application/sql',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Content-Length' => $fileSize,
            ]);

        } catch (\Exception $e) {
            \Log::error('Error al descargar backup: ' . $e->getMessage());
            return redirect()->route('backup.index')
                ->with('error', 'Error al descargar el archivo: ' . $e->getMessage());
        }
    }

    public function deleteBackup($filename)
    {
        try {
            $disk = Storage::disk('public');
            $filePath = 'backups/' . $filename;

            if (!$disk->exists($filePath)) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Archivo no encontrado: ' . $filename
                ], 404);
            }

            // Eliminar archivo
            $disk->delete($filePath);

            // Registrar en bitácora
            $this->registrarBitacora('eliminar_backup', 'Backup', null, [], ['filename' => $filename]);

            return response()->json([
                'success' => true, 
                'message' => 'Backup eliminado correctamente: ' . $filename
            ]);

        } catch (\Exception $e) {
            \Log::error('Error al eliminar backup: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Error al eliminar backup: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getBackupFiles()
    {
        $files = [];
        $disk = Storage::disk('public');
        $backupDir = 'backups';

        try {
            if ($disk->exists($backupDir)) {
                $fileList = $disk->files($backupDir);
                
                foreach ($fileList as $file) {
                    // Solo archivos .sql
                    if (pathinfo($file, PATHINFO_EXTENSION) === 'sql') {
                        $files[] = [
                            'name' => basename($file),
                            'size' => $disk->size($file),
                            'modified' => $disk->lastModified($file),
                        ];
                    }
                }

                // Ordenar por fecha de modificación (más reciente primero)
                usort($files, function($a, $b) {
                    return $b['modified'] - $a['modified'];
                });
            }
        } catch (\Exception $e) {
            \Log::error('Error al listar backups: ' . $e->getMessage());
        }

        return $files;
    }

    /**
     * Método auxiliar para registrar en bitácora
     * Compatible con la firma del método en el Controller padre
     */
    protected function registrarBitacora($accion, $modulo, $registro_id = null, $datos_antes = null, $datos_despues = null)
    {
        try {
            // Llamar al método del padre si existe
            if (method_exists(get_parent_class(), 'registrarBitacora')) {
                parent::registrarBitacora($accion, $modulo, $registro_id, $datos_antes, $datos_despues);
            }
        } catch (\Exception $e) {
            \Log::warning('Error al registrar en bitácora: ' . $e->getMessage());
        }
    }

    /**
     * Método de diagnóstico para verificar la configuración de storage
     */
    private function diagnosticarStorage()
    {
        try {
            $disks = ['local', 'public'];
            $results = [];

            foreach ($disks as $diskName) {
                $disk = Storage::disk($diskName);
                $results[$diskName] = [
                    'root' => $disk->getAdapter()->getPathPrefix(),
                    'writable' => is_writable($disk->getAdapter()->getPathPrefix()),
                    'backups_exists' => $disk->exists('backups'),
                    'backups_files' => $disk->exists('backups') ? $disk->files('backups') : [],
                ];
            }

            \Log::info('Diagnóstico de Storage:', $results);
            return $results;

        } catch (\Exception $e) {
            \Log::error('Error en diagnóstico de storage: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }
}