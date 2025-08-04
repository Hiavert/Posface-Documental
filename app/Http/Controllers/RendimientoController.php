<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tarea;
use App\Models\User;
use App\Models\PagoTerna;
use Carbon\Carbon;

class RendimientoController extends Controller
{
    public function dashboard()
    {
        // Estadísticas para Tareas Documentales (últimos 30 días)
        $tareasCompletadas = Tarea::where('estado', 'Completada')
            ->where('updated_at', '>=', now()->subDays(30))
            ->count();
        
        $tareasPendientes = Tarea::where('estado', 'Pendiente')->count();
        $tareasEnProceso = Tarea::where('estado', 'En Proceso')->count();
        $tareasRetrasadas = Tarea::where('fecha_vencimiento', '<', now())
            ->where('estado', '!=', 'Completada')
            ->count();
        
        $totalTareas = $tareasCompletadas + $tareasPendientes + $tareasEnProceso;
        $tasaCompletitudTareas = $totalTareas > 0 ? round(($tareasCompletadas / $totalTareas) * 100) : 0;
        
        // Estadísticas para Pagos de Terna (últimos 30 días)
        $pagosCompletados = PagoTerna::where('estado', 'pagado')
            ->where('fecha_pago', '>=', now()->subDays(30))
            ->count();
            
        $pagosPendientes = PagoTerna::where('estado', 'pendiente_pago')->count();
        $pagosEnRevision = PagoTerna::where('estado', 'en_revision')->count();
        $pagosRetrasados = PagoTerna::where('estado', 'en_revision')
            ->where('fecha_limite', '<', now())
            ->count();
            
        $totalPagos = $pagosCompletados + $pagosPendientes + $pagosEnRevision;
        $tasaCompletitudPagos = $totalPagos > 0 ? round(($pagosCompletados / $totalPagos) * 100) : 0;
        
        // Obtener rendimiento por usuario
        $rendimientoUsuarios = User::withCount([
            'tareasAsignadas as tareas_asignadas',
            'tareasAsignadas as tareas_completadas' => function($query) {
                $query->where('estado', 'Completada');
            },
            'tareasAsignadas as tareas_retrasadas' => function($query) {
                $query->where('fecha_vencimiento', '<', now())
                      ->where('estado', '!=', 'Completada');
            },
            'pagosAsignados as pagos_asignados',
            'pagosAsignados as pagos_completados' => function($query) {
                $query->where('estado', 'pagado');
            }
        ])
        ->get()
        ->map(function($user) {
            // Cálculo de eficiencia para tareas
            $user->eficiencia_tareas = $user->tareas_asignadas > 0 
                ? round(($user->tareas_completadas / $user->tareas_asignadas) * 100)
                : 0;
                
            // Cálculo de eficiencia para pagos
            $user->eficiencia_pagos = $user->pagos_asignados > 0 
                ? round(($user->pagos_completados / $user->pagos_asignados) * 100)
                : 0;
                
            // Calificación general
            $promedioEficiencia = ($user->eficiencia_tareas + $user->eficiencia_pagos) / 2;
            $user->calificacion = min(5, max(1, round($promedioEficiencia / 20)));
            
            return $user;
        });
        
        // Datos para gráficos
        $tareasPorEstado = [
            $tareasCompletadas,
            $tareasPendientes,
            $tareasEnProceso,
            $tareasRetrasadas
        ];
        
        $pagosPorEstado = [
            $pagosCompletados,
            $pagosPendientes,
            $pagosEnRevision,
            $pagosRetrasados
        ];
        
        // Datos históricos (últimas 4 semanas)
        $historicoTareas = [];
        $historicoPagos = [];
        
        for ($i = 3; $i >= 0; $i--) {
            $start = now()->subWeeks($i+1);
            $end = now()->subWeeks($i);
            
            $historicoTareas[] = Tarea::where('estado', 'Completada')
                ->whereBetween('updated_at', [$start, $end])
                ->count();
                
            $historicoPagos[] = PagoTerna::where('estado', 'pagado')
                ->whereBetween('fecha_pago', [$start, $end])
                ->count();
        }
        
        return view('espacio', compact(
            'tareasCompletadas',
            'tareasPendientes',
            'tareasEnProceso',
            'tareasRetrasadas',
            'tasaCompletitudTareas',
            'pagosCompletados',
            'pagosPendientes',
            'pagosEnRevision',
            'pagosRetrasados',
            'tasaCompletitudPagos',
            'rendimientoUsuarios',
            'tareasPorEstado',
            'pagosPorEstado',
            'historicoTareas',
            'historicoPagos'
        ));
    }
}