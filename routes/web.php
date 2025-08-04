<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TesisController;
use App\Http\Controllers\TareaController;
use App\Http\Controllers\AcuseController;
use App\Http\Controllers\RendimientoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TernaAsistenteController;
use App\Http\Controllers\TernaAdminController;
use App\Http\Controllers\TipoElementoController;
use App\Http\Controllers\DocumentoController;
use App\Http\Controllers\DocumentoEnvioController;
use App\Http\Controllers\NotificacionController;
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
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Rutas de tareas con permisos granulares
    Route::middleware('granular.permission:TareasDocumentales,ver')->group(function () {
        Route::get('/tareas', [TareaController::class, 'index'])->name('tareas.index');
        Route::get('/tareas/{id}', [TareaController::class, 'show'])->name('tareas.show');
        Route::get('/tareas/{id}/historial', [TareaController::class, 'historialBitacora'])->name('tareas.historial');
    });
    
    Route::middleware('granular.permission:TareasDocumentales,agregar')->group(function () {
        Route::get('/tareas/create', [TareaController::class, 'create'])->name('tareas.create');
        Route::post('/tareas', [TareaController::class, 'store'])->name('tareas.store');
        Route::post('/tareas/upload', [TareaController::class, 'upload'])->name('tareas.upload');
    });
    
    Route::middleware('granular.permission:TareasDocumentales,editar')->group(function () {
        Route::get('/tareas/{id}/edit', [TareaController::class, 'edit'])->name('tareas.edit');
        Route::put('/tareas/{id}', [TareaController::class, 'update'])->name('tareas.update');
    });
    
    Route::middleware('granular.permission:TareasDocumentales,eliminar')->group(function () {
        Route::delete('/tareas/{id}', [TareaController::class, 'destroy'])->name('tareas.destroy');
        Route::delete('/tareas/documento/{id}', [TareaController::class, 'eliminarDocumento'])->name('tareas.documento.eliminar');
    });

    Route::get('/notificaciones/leer-todas', function() {
        $user = Auth::user();
        if ($user) {
            $user->unreadNotifications->markAsRead();
        }
        return redirect()->back();
    })->name('notificaciones.leer_todas');

     Route::get('/perfil', [ProfileController::class, 'show'])
        ->name('profile.show')
        ->middleware('can:ver-Perfil');
    
    Route::get('/perfil/editar', [ProfileController::class, 'edit'])
        ->name('profile.edit')
        ->middleware('can:editar-Perfil');
    
    Route::patch('/perfil', [ProfileController::class, 'update'])
        ->name('profile.update')
        ->middleware('can:editar-Perfil');
    
    Route::patch('/perfil/password', [ProfileController::class, 'password'])
        ->name('profile.password')
        ->middleware('can:editar-Perfil');
   
    Route::delete('/perfil', [ProfileController::class, 'destroy'])
        ->name('profile.destroy')
        ->middleware('can:eliminar-Perfil');


    //Modulo de Tesis

    Route::prefix('tesis')->group(function () {
    // Ver (index, list, download, preview)
    Route::middleware('granular.permission:GestionTesis,ver')->group(function () {
        Route::get('/', [TesisController::class, 'index'])->name('tesis.index');
        Route::get('/list', [TesisController::class, 'list'])->name('tesis.list');
        Route::get('/download/{filename}', [TesisController::class, 'download'])->name('tesis.download');
        Route::get('/preview/{filename}', [TesisController::class, 'preview'])->name('tesis.preview');
    });

    // Agregar (store, exportar)
    Route::middleware('granular.permission:Tesis,Gestionagregar')->group(function () {
        Route::post('/', [TesisController::class, 'store'])->name('tesis.store');
        Route::post('/exportar', [TesisController::class, 'exportar'])->name('tesis.exportar');
    });

    // Editar (update)
    Route::middleware('granular.permission:GestionTesis,editar')->group(function () {
        Route::put('/{id}', [TesisController::class, 'update'])->name('tesis.update');
    });

    // Eliminar (destroy, limpiar-exportación)
    Route::middleware('granular.permission:GestionTesis,eliminar')->group(function () {
        Route::delete('/{id}', [TesisController::class, 'destroy'])->name('tesis.destroy');
        Route::post('/limpiar-exportacion', [TesisController::class, 'limpiarExportacion'])->name('tesis.limpiar-exportacion');
    });
});

// Administrador de Terna
Route::prefix('terna/admin')->middleware(['auth', 'can:ver-Terna'])->group(function () {
    Route::get('/', [TernaAdminController::class, 'index'])->name('terna.admin.index');
    Route::get('/create', [TernaAdminController::class, 'create'])->name('terna.admin.create');
    Route::post('/', [TernaAdminController::class, 'store'])->name('terna.admin.store');
    Route::get('/{id}', [TernaAdminController::class, 'show'])->name('terna.admin.show');
    Route::get('/{id}/edit', [TernaAdminController::class, 'edit'])->name('terna.admin.edit');
    Route::put('/{id}', [TernaAdminController::class, 'update'])->name('terna.admin.update');
    Route::delete('/{id}', [TernaAdminController::class, 'destroy'])->name('terna.admin.destroy');
    Route::post('/{id}/marcar-pagado', [TernaAdminController::class, 'marcarPagado'])->name('terna.admin.marcar-pagado');
});

// Asistente de Terna
Route::prefix('terna/asistente')->middleware(['auth', 'can:ver-TernaAux'])->group(function () {
    Route::get('/', [TernaAsistenteController::class, 'index'])->name('terna.asistente.index');
    Route::get('/{id}', [TernaAsistenteController::class, 'show'])->name('terna.asistente.show');
    Route::post('/{id}/completar', [TernaAsistenteController::class, 'completarProceso'])->name('terna.asistente.completar');
});
    // Módulo de Acuses
    Route::prefix('acuses')->group(function () {
        // Rutas de visualización
        Route::middleware('granular.permission:GestionAcuses,ver')->group(function () {
            Route::get('/', [AcuseController::class, 'index'])->name('acuses.index');
            Route::get('/{id}', [AcuseController::class, 'show'])->name('acuses.show');
            Route::get('/rastreo/{id}', [AcuseController::class, 'rastrear'])->name('acuses.rastrear');
        });
        
        // Rutas de creación
        Route::middleware('granular.permission:GestionAcuses,agregar')->group(function () {
            Route::post('/', [AcuseController::class, 'store'])->name('acuses.store');
            Route::get('/reenviar/{id}', [AcuseController::class, 'reenviarForm'])->name('acuses.reenviar.form');
            Route::post('/reenviar/{id}', [AcuseController::class, 'reenviar'])->name('acuses.reenviar');
        });
        
        // Rutas de edición
        Route::middleware('granular.permission:GestionAcuses,editar')->group(function () {
            Route::put('/{acuse}/aceptar', [AcuseController::class, 'aceptar'])->name('acuses.aceptar');
        });
        
        // Rutas de eliminación
        Route::middleware('granular.permission:GestionAcuses,eliminar')->group(function () {
            Route::delete('/{id}', [AcuseController::class, 'destroy'])->name('acuses.destroy');
        });
        
        // Nueva ruta para descargar adjuntos
        Route::get('/descargar-adjunto/{id}', [AcuseController::class, 'descargarAdjunto'])
            ->name('acuses.adjunto.descargar');
        
        // Ruta para tipos de elementos
        Route::post('/tipos', [TipoElementoController::class, 'store'])
            ->middleware('granular.permission:GestionAcuses,agregar')
            ->name('tipos.store');
    });
    
    // Ruta de notificaciones específica
    Route::get('/notificaciones-acuse/{notificacion}', [NotificacionController::class, 'show'])
        ->name('notificaciones.show');
    });

require __DIR__.'/auth.php';
require __DIR__.'/admin.php';