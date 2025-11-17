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
            
            // Usar el disco 'local' que en Railway apunta al storage temporal
            $disk = Storage::disk('local');
            $backupDir = 'backups';

            // Crear carpeta si no existe usando Storage
            if (!$disk->exists($backupDir)) {
                $disk->makeDirectory($backupDir);
            }

            $filePath = $backupDir . '/' . $filename;

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

            // Registrar en bitácora - usando la firma correcta del método padre
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
            $disk = Storage::disk('local');
            $filePath = 'backups/' . $filename;

            if (!$disk->exists($filePath)) {
                return redirect()->route('backup.index')
                    ->with('error', 'Archivo de backup no encontrado: ' . $filename);
            }

            // Registrar en bitácora - usando la firma correcta del método padre
            $this->registrarBitacora('descargar_backup', 'Backup', null, [], ['filename' => $filename]);

            // Descargar usando Storage
            return $disk->download($filePath, $filename, [
                'Content-Type' => 'application/sql',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"'
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
            $disk = Storage::disk('local');
            $filePath = 'backups/' . $filename;

            if (!$disk->exists($filePath)) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Archivo no encontrado: ' . $filename
                ], 404);
            }

            // Eliminar archivo
            $disk->delete($filePath);

            // Registrar en bitácora - usando la firma correcta del método padre
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
        $disk = Storage::disk('local');
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
}