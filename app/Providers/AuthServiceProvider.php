<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
       
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot()
    {
        $this->registerPolicies();

        // Gates para los módulos del menú
        Gate::define('ver-dashboard', function ($user) {
            return $user->tienePermiso('dashboard');
        });
        Gate::define('ver-Reportes', function ($user) {
            return $user->tienePermiso('Reportes');
        });
        Gate::define('ver-Roles', function ($user) {
            return $user->tienePermiso('Roles');
        });
        Gate::define('ver-TareasDocumentales', function ($user) {
            \Log::info('Gate ver-TareasDocumentales', [
                'user_id' => $user->id_usuario,
                'roles' => $user->roles->pluck('nombre_rol'),
                'permiso' => $user->tienePermiso('TareasDocumentales')
            ]);
            return $user->tienePermiso('TareasDocumentales');
        });
        Gate::define('ver-Terna', function ($user) {
            return $user->tienePermiso('Terna');
        });
        Gate::define('ver-TernaAux', function ($user) {
            return $user->tienePermiso('TernaAux');
        });
        Gate::define('ver-Usuarios', function ($user) {
            return $user->tienePermiso('Usuarios');
        });
        Gate::define('ver-Cronograma', function ($user) {
            return $user->tienePermiso('Cronograma');
        });
        Gate::define('ver-Objetos', function ($user) {
            return $user->tienePermiso('Objetos');
        });
        Gate::define('ver-Bitacora', function ($user) {
            // Solo administradores o usuarios con permiso explícito
            return $user->tienePermiso('Bitacora') || (property_exists($user, 'es_admin') && $user->es_admin);
        });
        Gate::define('ver-GestionAcuses', function ($user) {
            return $user->tienePermiso('GestionAcuses');
        });
        Gate::define('ver-GestionTesis', function ($user) {
            return $user->tienePermiso('GestionTesis');
        });
        Gate::define('ver-Documentos', function ($user) {
            return $user->tienePermiso('Documentos');
        });
        Gate::define('ver-Recepcion', function ($user) {
            return $user->tienePermiso('Recepcion');
        });
         Gate::define('ver-GestorRendimiento', function ($user) {
            return $user->tienePermiso('GestorRendimiento');
        });
         Gate::define('ver-Perfil', function ($user) {
            \Log::info('Gate ver-Perfil', [
                'user_id' => $user->id_usuario,
                'permiso' => $user->tienePermiso('Perfil')
            ]);
            return $user->tienePermiso('Perfil');
        });
        
    }
} 