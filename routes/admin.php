<?php

use App\Http\Controllers\RolController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*Route::middleware(['auth', 'granular.permission:Roles,ver'])->group(function () {
    // Rutas para gestión de roles
    Route::resource('roles', RolController::class);
    Route::get('roles/{id}/permisos', [RolController::class, 'permisos'])->name('roles.permisos');
    Route::post('roles/{id}/permisos', [RolController::class, 'actualizarPermisos'])->name('roles.actualizar-permisos');
    Route::put('roles/{id}/estado', [RolController::class, 'cambiarEstado'])->name('roles.cambiar-estado');
});*/

Route::middleware(['auth', 'granular.permission:Usuarios,ver'])->group(function () {
    Route::resource('usuarios', UserController::class)->except(['show', 'destroy']);
    Route::put('usuarios/{usuario}/estado', [UserController::class, 'cambiarEstado'])->name('usuarios.cambiar-estado');

    Route::delete('usuarios/{usuario}', [UserController::class, 'destroy'])
        ->name('usuarios.destroy')
        ->middleware('granular.permission:Usuarios,eliminar');
});

Route::middleware(['auth'])->group(function () {
    // Rutas para gestión de roles con permisos granulares
    Route::get('roles', [RolController::class, 'index'])
        ->middleware('granular.permission:Roles,ver')
        ->name('roles.index');

    Route::get('roles/create', [RolController::class, 'create'])
        ->middleware('granular.permission:Roles,agregar')
        ->name('roles.create');

    Route::post('roles', [RolController::class, 'store'])
        ->middleware('granular.permission:Roles,agregar')
        ->name('roles.store');

    Route::get('roles/{id}/edit', [RolController::class, 'edit'])
        ->middleware('granular.permission:Roles,editar')
        ->name('roles.edit');

    Route::put('roles/{id}', [RolController::class, 'update'])
        ->middleware('granular.permission:Roles,editar')
        ->name('roles.update');

    Route::delete('roles/{id}', [RolController::class, 'destroy'])
        ->middleware('granular.permission:Roles,eliminar')
        ->name('roles.destroy');

    Route::get('roles/{id}/permisos', [RolController::class, 'permisos'])
        ->middleware('granular.permission:Roles,editar')
        ->name('roles.permisos');

    Route::post('roles/{id}/permisos', [RolController::class, 'actualizarPermisos'])
        ->middleware('granular.permission:Roles,editar')
        ->name('roles.actualizar-permisos');

    Route::put('roles/{id}/estado', [RolController::class, 'cambiarEstado'])
        ->middleware('granular.permission:Roles,editar')
        ->name('roles.cambiar-estado');
});

// Ejemplos de rutas con permisos granulares específicos
/*Route::middleware(['auth'])->group(function () {
    // Rutas que requieren permiso de agregar
    Route::post('roles', [RolController::class, 'store'])->middleware('granular.permission:Roles,agregar');
    // Rutas que requieren permiso de editar
    Route::put('roles/{id}', [RolController::class, 'update'])->middleware('granular.permission:Roles,editar');
    // Rutas que requieren permiso de eliminar
    Route::delete('roles/{id}', [RolController::class, 'destroy'])->middleware('granular.permission:Roles,eliminar');
});*/

Route::middleware(['auth', 'can:ver-Bitacora'])->group(function () {
    Route::get('bitacora', [\App\Http\Controllers\BitacoraController::class, 'index'])->name('bitacora.index');
}); 