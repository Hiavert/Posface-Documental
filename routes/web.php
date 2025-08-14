<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TesisController;
use App\Http\Controllers\TareaController;
use App\Http\Controllers\AcuseController;
use App\Http\Controllers\ObjetoController; // Importación añadida
use App\Http\Controllers\RendimientoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TernaAsistenteController;
use App\Http\Controllers\TernaAdminController;
use App\Http\Controllers\TipoElementoController;
use App\Http\Controllers\DocumentoController;
use App\Http\Controllers\DocumentoEnvioController;
use App\Http\Controllers\NotificacionController;
use App\Http\Controllers\BackupController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('auth/login');
});

// CORRECCIÓN: Usar el controlador para el dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'can:ver-dashboard'])
    ->name('dashboard');

// CORRECCIÓN: Eliminar una de las rutas duplicadas para /espacio
Route::get('/espacio', [RendimientoController::class, 'dashboard'])
    ->middleware(['auth', 'can:ver-GestorRendimiento'])
    ->name('espacio');

// Rutas para documentos
Route::prefix('documentos')->group(function () {
    // Vista para Secretaria
    Route::get('/gestor', [DocumentoController::class, 'index'])->name('documentos.gestor');
    Route::get('/recepcion', [DocumentoController::class, 'recepcion'])->name('documentos.recepcion');

    // Rutas CRUD para documentos
    Route::get('/create', [DocumentoController::class, 'create'])->name('documentos.create');
    Route::post('/store', [DocumentoController::class, 'store'])->name('documentos.store');
    Route::get('/{documento}', [DocumentoController::class, 'show'])->name('documentos.show');
    Route::get('/descargar/{documento}', [DocumentoController::class, 'descargar'])->name('documentos.descargar');
    Route::get('/{documento}/edit', [DocumentoController::class, 'edit'])->name('documentos.edit');
    Route::put('/{documento}', [DocumentoController::class, 'update'])->name('documentos.update');
    Route::delete('/{documento}', [DocumentoController::class, 'destroy'])->name('documentos.destroy');

    // Rutas para envíos
    Route::post('/reenviar/{documento}/store', [DocumentoEnvioController::class, 'store'])->name('documentos.reenviar.store');
    Route::get('/historial/{documento}', [DocumentoEnvioController::class, 'historial'])->name('documentos.historial');
    Route::post('/marcar-leido/{envio}', [DocumentoEnvioController::class, 'marcarLeido'])->name('documentos.marcar-leido');
});

Route::middleware('auth')->group(function () {
    // Perfil con rutas y nombres unificados a /profile y profile.*
    Route::get('/profile', [ProfileController::class, 'show'])
        ->name('profile.show')
        ->middleware('can:ver-Perfil');

    Route::get('/profile/editar', [ProfileController::class, 'edit'])
        ->name('profile.edit')
        ->middleware('can:editar-Perfil');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update')
        ->middleware('can:editar-Perfil');

    Route::patch('/profile/password', [ProfileController::class, 'password'])
        ->name('profile.password')
        ->middleware('can:editar-Perfil');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy')
        ->middleware('can:eliminar-Perfil');

    // CORRECCIÓN APLICADA: Usar ObjetoController::class
    Route::resource('objetos', ObjetoController::class)->except(['show']);

    // Rutas de tareas con permisos granulares - VERSIÓN CORREGIDA
    Route::prefix('tareas')->group(function () {
        // Rutas con permisos granulares
        Route::middleware('granular.permission:TareasDocumentales,ver')->group(function () {
            Route::get('/', [TareaController::class, 'index'])->name('tareas.index');
            Route::get('/{id}', [TareaController::class, 'show'])->name('tareas.show');
        });

        Route::middleware('granular.permission:TareasDocumentales,agregar')->group(function () {
            Route::get('/create', [TareaController::class, 'create'])->name('tareas.create');
            Route::post('/', [TareaController::class, 'store'])->name('tareas.store');
            Route::post('/upload', [TareaController::class, 'upload'])->name('tareas.documento.upload');
        });

        Route::middleware('granular.permission:TareasDocumentales,editar')->group(function () {
            Route::get('/{id}/edit', [TareaController::class, 'edit'])->name('tareas.edit');
            Route::put('/{id}', [TareaController::class, 'update'])->name('tareas.update');
            // Nuevas rutas para cambiar estado y delegar
            Route::post('/{id}/estado', [TareaController::class, 'cambiarEstado'])->name('tareas.estado');
            Route::post('/{id}/delegar', [TareaController::class, 'delegar'])->name('tareas.delegar');
        });

        Route::middleware('granular.permission:TareasDocumentales,eliminar')->group(function () {
            Route::delete('/{id}', [TareaController::class, 'destroy'])->name('tareas.destroy');
            Route::delete('/documento/{id}', [TareaController::class, 'eliminarDocumento'])->name('tareas.documento.eliminar');
        });
    });

    Route::get('/notificaciones/leer-todas', function () {
        $user = Auth::user();
        if ($user) {
            $user->unreadNotifications->markAsRead();
        }
        return redirect()->back();
    })->name('notificaciones.leer_todas');
    
    // Módulo de Tesis
    Route::prefix('tesis')->group(function () {
        Route::middleware('granular.permission:GestionTesis,ver')->group(function () {
            Route::get('/', [TesisController::class, 'index'])->name('tesis.index');
            Route::get('/list', [TesisController::class, 'list'])->name('tesis.list');
            Route::get('/download/{filename}', [TesisController::class, 'download'])->name('tesis.download');
            Route::get('/preview/{filename}', [TesisController::class, 'preview'])->name('tesis.preview');
        });

        Route::middleware('granular.permission:Tesis,Gestionagregar')->group(function () {
            Route::post('/', [TesisController::class, 'store'])->name('tesis.store');
            Route::post('/exportar', [TesisController::class, 'exportar'])->name('tesis.exportar');
        });

        Route::middleware('granular.permission:GestionTesis,editar')->group(function () {
            Route::put('/{id}', [TesisController::class, 'update'])->name('tesis.update');
        });

        Route::middleware('granular.permission:GestionTesis,eliminar')->group(function () {
            Route::delete('/{id}', [TesisController::class, 'destroy'])->name('tesis.destroy');
            Route::post('/limpiar-exportacion', [TesisController::class, 'limpiarExportacion'])->name('tesis.limpiar-exportacion');
        });
    });
    // Backup routes
    Route::prefix('backup')->group(function () {
        Route::get('/', [BackupController::class, 'index'])->name('backup.index');
        Route::get('/create', [BackupController::class, 'createBackup'])->name('backup.create');
        Route::get('/download/{filename}', [BackupController::class, 'downloadBackup'])->name('backup.download');
        Route::get('/delete/{filename}', [BackupController::class, 'deleteBackup'])->name('backup.delete');
    });
    

    // Rutas para administrador de terna
    Route::prefix('terna/admin')->group(function () {
        Route::get('/', [TernaAdminController::class, 'index'])->name('terna.admin.index');
        Route::get('/create', [TernaAdminController::class, 'create'])->name('terna.admin.create');
        Route::post('/', [TernaAdminController::class, 'store'])->name('terna.admin.store');
        Route::get('/{id}', [TernaAdminController::class, 'show'])->name('terna.admin.show');
        Route::get('/{id}/edit', [TernaAdminController::class, 'edit'])->name('terna.admin.edit');
        Route::put('/{id}', [TernaAdminController::class, 'update'])->name('terna.admin.update');
        Route::delete('/{id}', [TernaAdminController::class, 'destroy'])->name('terna.admin.destroy');
        Route::post('/{id}/marcar-pagado', [TernaAdminController::class, 'marcarPagado'])->name('terna.admin.marcar-pagado');
        Route::post('integrantes', [TernaAdminController::class, 'storeIntegrante'])->name('terna.integrantes.store');
    });

    // Rutas para asistente de terna
    Route::prefix('terna/asistente')->group(function () {
        Route::get('/', [TernaAsistenteController::class, 'index'])->name('terna.asistente.index');
        Route::get('/{id}', [TernaAsistenteController::class, 'show'])->name('terna.asistente.show');
        Route::post('/{id}/completar', [TernaAsistenteController::class, 'completarProceso'])->name('terna.asistente.completar');
    });

    // Módulo de Acuses
    Route::prefix('acuses')->group(function () {
        Route::middleware('granular.permission:GestionAcuses,ver')->group(function () {
            Route::get('/', [AcuseController::class, 'index'])->name('acuses.index');
            Route::get('/{id}', [AcuseController::class, 'show'])->name('acuses.show');
            Route::get('/rastreo/{id}', [AcuseController::class, 'rastrear'])->name('acuses.rastrear');
        });

        Route::middleware('granular.permission:GestionAcuses,agregar')->group(function () {
            Route::post('/', [AcuseController::class, 'store'])->name('acuses.store');
            Route::get('/reenviar/{id}', [AcuseController::class, 'reenviarForm'])->name('acuses.reenviar.form');
            Route::post('/reenviar/{id}', [AcuseController::class, 'reenviar'])->name('acuses.reenviar');
        });

        Route::middleware('granular.permission:GestionAcuses,editar')->group(function () {
            Route::put('/{acuse}/aceptar', [AcuseController::class, 'aceptar'])->name('acuses.aceptar');
        });

        Route::middleware('granular.permission:GestionAcuses,eliminar')->group(function () {
            Route::delete('/{id}', [AcuseController::class, 'destroy'])->name('acuses.destroy');
        });

        Route::get('/descargar-adjunto/{id}', [AcuseController::class, 'descargarAdjunto'])
            ->name('acuses.adjunto.descargar');

        Route::post('/tipos', [TipoElementoController::class, 'store'])
            ->middleware('granular.permission:GestionAcuses,agregar')
            ->name('tipos.store');
    });

    Route::get('/notificaciones-acuse/{notificacion}', [NotificacionController::class, 'show'])
        ->name('notificaciones.show');
});

require __DIR__.'/auth.php';
require __DIR__.'/admin.php';