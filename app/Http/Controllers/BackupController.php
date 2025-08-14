<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

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
            $filePath = storage_path('app/backups/' . $filename);
            
            // Crear directorio si no existe
            if (!Storage::exists('backups')) {
                Storage::makeDirectory('backups');
            }
            
            // Comando para generar el backup
            $command = sprintf(
                'mysqldump --user=%s --password=%s --host=%s %s > %s',
                config('database.connections.mysql.username'),
                config('database.connections.mysql.password'),
                config('database.connections.mysql.host'),
                config('database.connections.mysql.database'),
                $filePath
            );

            $process = Process::fromShellCommandline($command);
            $process->run();

            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }

            return redirect()->route('backup.index')->with('success', 'Backup creado correctamente: ' . $filename);
        } catch (\Exception $e) {
            return redirect()->route('backup.index')->with('error', 'Error al crear backup: ' . $e->getMessage());
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
            return redirect()->route('backup.index')->with('error', 'Archivo no encontrado');
        }

        unlink($filePath);
        return redirect()->route('backup.index')->with('success', 'Backup eliminado: ' . $filename);
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
                        'path' => $filePath,
                    ];
                }
            }
            
            // Ordenar por fecha de modificación (más reciente primero)
            usort($files, function ($a, $b) {
                return $b['modified'] - $a['modified'];
            });
        }

        return $files;
    }
}