@extends('adminlte::page')

@section('title', 'Dashboard de Rendimiento')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1 class="mb-0"><i class="fas fa-chart-line mr-2 text-primary"></i> Dashboard de Rendimiento</h1>
    <div class="d-flex">
        <div class="dropdown mr-3">
            <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="periodoDropdown" data-toggle="dropdown">
                <i class="far fa-calendar-alt mr-1"></i> Últimos 30 días
            </button>
            <div class="dropdown-menu" aria-labelledby="periodoDropdown">
                <a class="dropdown-item" href="#" data-periodo="7">Últimos 7 días</a>
                <a class="dropdown-item active" href="#" data-periodo="30">Últimos 30 días</a>
                <a class="dropdown-item" href="#" data-periodo="90">Últimos 90 días</a>
                <a class="dropdown-item" href="#" data-periodo="365">Este año</a>
            </div>
        </div>
        <button class="btn btn-primary" id="btnActualizar">
            <i class="fas fa-sync-alt mr-1"></i> Actualizar
        </button>
    </div>
</div>
@stop

@section('content')
<div class="container-fluid">
    <!-- Filtros -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body py-2">
                    <div class="d-flex justify-content-between">
                        <div class="d-flex align-items-center">
                            <span class="mr-2">Filtrar por módulo:</span>
                            <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                <label class="btn btn-outline-primary active">
                                    <input type="radio" name="modulo" value="todos" checked> Todos
                                </label>
                                <label class="btn btn-outline-primary">
                                    <input type="radio" name="modulo" value="tareas"> Tareas
                                </label>
                                <label class="btn btn-outline-primary">
                                    <input type="radio" name="modulo" value="pagos"> Pagos
                                </label>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="mr-2">Ordenar por:</span>
                            <select class="form-control form-control-sm" id="orden">
                                <option value="calificacion">Calificación</option>
                                <option value="eficiencia">Eficiencia</option>
                                <option value="tareas">Tareas completadas</option>
                                <option value="pagos">Pagos completados</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Resumen de Rendimiento -->
    <div class="row mb-4">
        <!-- Tareas Documentales -->
        <div class="col-lg-6 mb-4">
            <div class="card card-performance border-primary">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-tasks mr-2"></i>
                        <strong>Tareas Documentales</strong>
                    </div>
                    <span class="badge badge-light">Últimos 30 días</span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="d-flex justify-content-between mb-4">
                                <div class="text-center">
                                    <div class="metric-value display-4">{{ $tareasCompletadas }}</div>
                                    <div class="metric-label text-success">Completadas</div>
                                </div>
                                <div class="text-center">
                                    <div class="metric-value display-4">{{ $tareasPendientes }}</div>
                                    <div class="metric-label text-info">Pendientes</div>
                                </div>
                                <div class="text-center">
                                    <div class="metric-value display-4">{{ $tareasEnProceso }}</div>
                                    <div class="metric-label text-warning">En Proceso</div>
                                </div>
                                <div class="text-center">
                                    <div class="metric-value display-4">{{ $tareasRetrasadas }}</div>
                                    <div class="metric-label text-danger">Retrasadas</div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="font-weight-bold">Tasa de completitud</span>
                                    <span class="font-weight-bold">{{ $tasaCompletitudTareas }}%</span>
                                </div>
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar bg-success" role="progressbar" 
                                         style="width: {{ $tasaCompletitudTareas }}%">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 border-left">
                            <h6 class="text-center mb-3">Distribución por estado</h6>
                            <canvas id="tareasPieChart" height="150"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Pagos de Terna -->
        <div class="col-lg-6 mb-4">
            <div class="card card-performance border-info">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-file-invoice-dollar mr-2"></i>
                        <strong>Pagos de Terna</strong>
                    </div>
                    <span class="badge badge-light">Últimos 30 días</span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="d-flex justify-content-between mb-4">
                                <div class="text-center">
                                    <div class="metric-value display-4">{{ $pagosCompletados }}</div>
                                    <div class="metric-label text-success">Completados</div>
                                </div>
                                <div class="text-center">
                                    <div class="metric-value display-4">{{ $pagosPendientes }}</div>
                                    <div class="metric-label text-info">Pendientes</div>
                                </div>
                                <div class="text-center">
                                    <div class="metric-value display-4">{{ $pagosEnRevision }}</div>
                                    <div class="metric-label text-warning">En Revisión</div>
                                </div>
                                <div class="text-center">
                                    <div class="metric-value display-4">{{ $pagosRetrasados }}</div>
                                    <div class="metric-label text-danger">Retrasados</div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="font-weight-bold">Tasa de completitud</span>
                                    <span class="font-weight-bold">{{ $tasaCompletitudPagos }}%</span>
                                </div>
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar bg-success" role="progressbar" 
                                         style="width: {{ $tasaCompletitudPagos }}%">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 border-left">
                            <h6 class="text-center mb-3">Distribución por estado</h6>
                            <canvas id="pagosPieChart" height="150"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos principales -->
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-chart-bar mr-2 text-primary"></i> Rendimiento por Usuario</h5>
                </div>
                <div class="card-body">
                    <canvas id="userPerformanceChart" height="250"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-history mr-2 text-info"></i> Evolución de Completados</h5>
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-outline-secondary">Diario</button>
                            <button type="button" class="btn btn-outline-secondary active">Semanal</button>
                            <button type="button" class="btn btn-outline-secondary">Mensual</button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="completionHistoryChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Tabla de rendimiento -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-table mr-2 text-warning"></i> Detalle por Usuario</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-performance">
                            <thead>
                                <tr>
                                    <th>Usuario</th>
                                    <th class="text-center">Tareas</th>
                                    <th class="text-center">Eficiencia Tareas</th>
                                    <th class="text-center">Pagos</th>
                                    <th class="text-center">Eficiencia Pagos</th>
                                    <th class="text-center">Rendimiento</th>
                                    <th class="text-center">Calificación</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rendimientoUsuarios as $usuario)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar mr-3">
                                                <div class="avatar-initials bg-primary text-white">
                                                    {{ substr($usuario->nombres, 0, 1) }}{{ substr($usuario->apellidos, 0, 1) }}
                                                </div>
                                            </div>
                                            <div>
                                                <div class="font-weight-bold">{{ $usuario->nombres }} {{ $usuario->apellidos }}</div>
                                                <small class="text-muted">{{ $usuario->rol }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex flex-column">
                                            <span class="font-weight-bold">{{ $usuario->tareas_completadas }}/{{ $usuario->tareas_asignadas }}</span>
                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar bg-{{ $usuario->tareas_retrasadas > 0 ? 'danger' : 'success' }}" 
                                                    role="progressbar" 
                                                    style="width: {{ $usuario->tareas_asignadas > 0 ? ($usuario->tareas_completadas / $usuario->tareas_asignadas) * 100 : 0 }}%">
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-{{ $usuario->eficiencia_tareas > 80 ? 'success' : ($usuario->eficiencia_tareas > 60 ? 'warning' : 'danger') }}">
                                            {{ $usuario->eficiencia_tareas }}%
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex flex-column">
                                            <span class="font-weight-bold">{{ $usuario->pagos_completados }}/{{ $usuario->pagos_asignados }}</span>
                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar bg-info" 
                                                    role="progressbar" 
                                                    style="width: {{ $usuario->pagos_asignados > 0 ? ($usuario->pagos_completados / $usuario->pagos_asignados) * 100 : 0 }}%">
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-{{ $usuario->eficiencia_pagos > 80 ? 'success' : ($usuario->eficiencia_pagos > 60 ? 'warning' : 'danger') }}">
                                            {{ $usuario->eficiencia_pagos }}%
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="radial-progress" data-progress="{{ ($usuario->eficiencia_tareas + $usuario->eficiencia_pagos) / 2 }}">
                                            <svg viewBox="0 0 100 100">
                                                <circle class="bg" cx="50" cy="50" r="40"></circle>
                                                <circle class="progress" cx="50" cy="50" r="40"></circle>
                                                <text x="50" y="50" text-anchor="middle" dy="0.3em" class="percentage">
                                                    {{ round(($usuario->eficiencia_tareas + $usuario->eficiencia_pagos) / 2) }}%
                                                </text>
                                            </svg>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="star-rating">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star {{ $i <= $usuario->calificacion ? 'text-warning' : 'text-light' }}"></i>
                                            @endfor
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    .card-performance {
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        transition: transform 0.3s ease;
        height: 100%;
    }
    
    .card-performance:hover {
        transform: translateY(-5px);
    }
    
    .metric-value {
        font-weight: 700;
        display: block;
        line-height: 1.2;
    }
    
    .metric-label {
        font-size: 0.9rem;
        color: #6c757d;
        display: block;
    }
    
    .avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .avatar-initials {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 14px;
    }
    
    .table-performance tbody tr {
        transition: background-color 0.2s;
    }
    
    .table-performance tbody tr:hover {
        background-color: rgba(0,0,0,0.02);
    }
    
    .progress {
        border-radius: 10px;
    }
    
    .radial-progress {
        width: 60px;
        height: 60px;
        margin: 0 auto;
    }
    
    .radial-progress svg {
        width: 100%;
        height: 100%;
    }
    
    .radial-progress circle.bg {
        fill: none;
        stroke: #e6e6e6;
        stroke-width: 8;
    }
    
    .radial-progress circle.progress {
        fill: none;
        stroke: #4caf50;
        stroke-width: 8;
        stroke-linecap: round;
        transform: rotate(-90deg);
        transform-origin: 50% 50%;
        transition: stroke-dashoffset 0.5s ease;
    }
    
    .radial-progress text.percentage {
        font-size: 12px;
        font-weight: bold;
        fill: #333;
    }
    
    .star-rating {
        font-size: 18px;
        color: #ffc107;
    }
</style>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(document).ready(function() {
        // Gráfico de tareas por estado
        const tareasCtx = document.getElementById('tareasPieChart').getContext('2d');
        const tareasPieChart = new Chart(tareasCtx, {
            type: 'doughnut',
            data: {
                labels: ['Completadas', 'Pendientes', 'En Proceso', 'Retrasadas'],
                datasets: [{
                    data: @json($tareasPorEstado),
                    backgroundColor: [
                        'rgba(40, 167, 69, 0.8)',
                        'rgba(23, 162, 184, 0.8)',
                        'rgba(255, 193, 7, 0.8)',
                        'rgba(220, 53, 69, 0.8)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            usePointStyle: true
                        }
                    }
                }
            }
        });

        // Gráfico de pagos por estado
        const pagosCtx = document.getElementById('pagosPieChart').getContext('2d');
        const pagosPieChart = new Chart(pagosCtx, {
            type: 'doughnut',
            data: {
                labels: ['Completados', 'Pendientes', 'En Revisión', 'Retrasados'],
                datasets: [{
                    data: @json($pagosPorEstado),
                    backgroundColor: [
                        'rgba(40, 167, 69, 0.8)',
                        'rgba(23, 162, 184, 0.8)',
                        'rgba(255, 193, 7, 0.8)',
                        'rgba(220, 53, 69, 0.8)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            usePointStyle: true
                        }
                    }
                }
            }
        });

        // Gráfico de rendimiento por usuario
        const userCtx = document.getElementById('userPerformanceChart').getContext('2d');
        const userPerformanceChart = new Chart(userCtx, {
            type: 'bar',
            data: {
                labels: @json($rendimientoUsuarios->pluck('nombres')),
                datasets: [
                    {
                        label: 'Tareas Completadas',
                        data: @json($rendimientoUsuarios->pluck('tareas_completadas')),
                        backgroundColor: 'rgba(54, 162, 235, 0.7)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Pagos Completados',
                        data: @json($rendimientoUsuarios->pluck('pagos_completados')),
                        backgroundColor: 'rgba(75, 192, 192, 0.7)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Cantidad'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Usuarios'
                        }
                    }
                }
            }
        });

        // Gráfico de evolución histórica
        const historyCtx = document.getElementById('completionHistoryChart').getContext('2d');
        const completionHistoryChart = new Chart(historyCtx, {
            type: 'line',
            data: {
                labels: ['Semana 1', 'Semana 2', 'Semana 3', 'Semana 4'],
                datasets: [
                    {
                        label: 'Tareas Completadas',
                        data: @json($historicoTareas),
                        borderColor: 'rgba(54, 162, 235, 1)',
                        backgroundColor: 'rgba(54, 162, 235, 0.1)',
                        tension: 0.3,
                        fill: true
                    },
                    {
                        label: 'Pagos Completados',
                        data: @json($historicoPagos),
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'rgba(75, 192, 192, 0.1)',
                        tension: 0.3,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Cantidad Completada'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Semanas'
                        }
                    }
                }
            }
        });

        // Actualizar radial progress
        $('.radial-progress').each(function() {
            const progress = $(this).data('progress');
            const circle = $(this).find('.progress');
            const radius = circle.attr('r');
            const circumference = 2 * Math.PI * radius;
            const offset = circumference - (progress / 100) * circumference;
            
            circle.css({
                'stroke-dasharray': circumference,
                'stroke-dashoffset': offset
            });
        });

        // Filtros
        $('input[name="modulo"]').change(function() {
            const modulo = $(this).val();
            $('.card-performance').show();
            
            if (modulo === 'tareas') {
                $('.card-performance.border-info').hide();
            } else if (modulo === 'pagos') {
                $('.card-performance.border-primary').hide();
            }
        });

        // Botón actualizar
        $('#btnActualizar').click(function() {
            window.location.reload();
        });
    });
</script>
@stop