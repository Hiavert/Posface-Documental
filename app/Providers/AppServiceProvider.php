<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Tarea;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            $contadorPendientes = 0;
            if (auth()->check()) {
                $contadorPendientes = Tarea::where('estado', 'pendiente')
                    ->where('fk_id_usuario_asignado', auth()->id())
                    ->count();
            }
            $view->with('contadorPendientes', $contadorPendientes);
        });
    }
}
