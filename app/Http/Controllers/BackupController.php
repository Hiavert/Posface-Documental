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
            $backupDir = storage_path('app/backups');

            // Crear carpeta si no existe
            if (!is_dir($backupDir)) {
                mkdir($backupDir, 0775, true);
            }

            $filePath = $backupDir . DIRECTORY_SEPARATOR . $filename;

            // Dump de todas las tablas
            $tables = DB::select('SHOW TABLES');
            $sqlDump = '';

            foreach ($tables as $tableObj) {
                $table = array_values((array)$tableObj)[0];
                $createTable = DB::select("SHOW CREATE TABLE `$table`")[0]->{'Create Table'};
                $sqlDump .= $createTable . ";\n\n";

                $rows = DB::table($table)->get();
                foreach ($rows as $row) {
                    $columns = implode('`,`', array_keys((array)$row));
                    $values  = implode("','", array_map(fn($v) => addslashes($v), (array)$row));
                    $sqlDump .= "INSERT INTO `$table` (`$columns`) VALUES ('$values');\n";
                }
                $sqlDump .= "\n\n";
            }

            file_put_contents($filePath, $sqlDump);

            return response()->json(['success' => true, 'message' => 'Backup creado correctamente', 'filename' => $filename]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al crear backup: ' . $e->getMessage()]);
        }
    }

    public function downloadBackup($filename)
    {
        $filePath = storage_path('app/backups/' . $filename);

        if (!file_exists($filePath)) {
            return redirect()->route('backup.index')->with('error', 'Archivo no encontrado');
        }

        return Response::download($filePath, $filename, [
            'Content-Type' => 'application/sql',
        ]);
    }

    public function deleteBackup($filename)
    {
        $filePath = storage_path('app/backups/' . $filename);

        if (!file_exists($filePath)) {
            return response()->json(['success' => false, 'message' => 'Archivo no encontrado']);
        }

        unlink($filePath);
        return response()->json(['success' => true, 'message' => 'Backup eliminado: ' . $filename]);
    }

    private function getBackupFiles()
    {
        $files = [];
        $path = storage_path('app/backups');

        if (is_dir($path)) {
            $fileList = scandir($path);
            foreach ($fileList as $file) {
                if ($file !== '.' && $file !== '..') {
                    $filePath = $path . DIRECTORY_SEPARATOR . $file;
                    $files[] = [
                        'name' => $file,
                        'size' => filesize($filePath),
                        'modified' => filemtime($filePath),
                    ];
                }
            }

            usort($files, fn($a, $b) => $b['modified'] - $a['modified']);
        }

        return $files;
    }
}
