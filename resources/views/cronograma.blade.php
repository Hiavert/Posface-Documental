@extends('adminlte::page')

@section('title', 'Cronograma de Procesos')

@section('content_header')
    <div class="unah-header">
        <h1><i class="fas fa-calendar-alt mr-2"></i> Cronograma de Procesos</h1>
        <p class="mb-0">Universidad Nacional Autónoma de Honduras - Posgrado en Informática Administrativa</p>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <!-- Filtros -->
        <div class="filter-box">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Estado</label>
                        <select class="form-control">
                            <option>Todos</option>
                            <option>Pendiente</option>
                            <option>En Progreso</option>
                            <option>Completado</option>
                            <option>Atrasado</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Responsable</label>
                        <select class="form-control">
                            <option>Todos</option>
                            <option>Departamento Académico</option>
                            <option>Comité de Tesis</option>
                            <option>Estudiante</option>
                            <option>Asesor</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Fecha Inicio</label>
                        <input type="date" class="form-control">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Fecha Fin</label>
                        <input type="date" class="form-control">
                    </div>
                </div>
            </div>
            <div class="text-right">
                <button class="btn btn-unah"><i class="fas fa-filter mr-1"></i> Filtrar</button>
                <button class="btn btn-outline-unah"><i class="fas fa-redo mr-1"></i> Restablecer</button>
            </div>
        </div>

        <!-- Estadísticas Rápidas -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-info"><i class="far fa-clock"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Tareas Pendientes</span>
                        <span class="info-box-number">8</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-success"><i class="fas fa-check-circle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Tareas Completadas</span>
                        <span class="info-box-number">12</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-warning"><i class="fas fa-exclamation-triangle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Por Vencer</span>
                        <span class="info-box-number">3</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-danger"><i class="fas fa-exclamation-circle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Atrasadas</span>
                        <span class="info-box-number">2</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vista de Cronograma -->
        <div class="card">
            <div class="card-header card-header-unah">
                <h3 class="card-title"><i class="fas fa-project-diagram mr-2"></i> Cronograma de Procesos</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool text-white" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                    <div class="btn-group">
                        <button type="button" class="btn btn-tool text-white dropdown-toggle" data-toggle="dropdown">
                            <i class="fas fa-wrench"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" role="menu">
                            <a href="#" class="dropdown-item">Vista Tabla</a>
                            <a href="#" class="dropdown-item">Vista Calendario</a>
                            <a href="#" class="dropdown-item">Vista Tarjetas</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Timeline -->
                <div class="timeline-container">
                    <div class="timeline-line"></div>
                    
                    <!-- Item 1 -->
                    <div class="timeline-item">
                        <div class="timeline-badge"></div>
                        <div class="timeline-content urgent">
                            <div class="timeline-header">
                                <h4 class="timeline-title">Aprobación de Tema</h4>
                                <div class="timeline-date">
                                    <i class="far fa-calendar-alt mr-1"></i> 15 Jun - 30 Jun 2025
                                </div>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="badge badge-resp"><i class="fas fa-user mr-1"></i> Comité de Tesis</span>
                                <span class="badge badge-danger"><i class="fas fa-exclamation-circle mr-1"></i> Atrasado</span>
                            </div>
                            <p>Presentación y aprobación formal del tema de tesis ante el comité evaluador.</p>
                            <div class="progress progress-xs mb-2">
                                <div class="progress-bar bg-danger" style="width: 65%"></div>
                            </div>
                            <div class="text-right">
                                <button class="btn btn-sm btn-outline-secondary"><i class="fas fa-edit mr-1"></i> Editar</button>
                                <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash-alt mr-1"></i> Eliminar</button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Item 2 -->
                    <div class="timeline-item">
                        <div class="timeline-badge"></div>
                        <div class="timeline-content warning">
                            <div class="timeline-header">
                                <h4 class="timeline-title">Desarrollo Capítulos 1-3</h4>
                                <div class="timeline-date">
                                    <i class="far fa-calendar-alt mr-1"></i> 1 Jul - 31 Ago 2025
                                </div>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="badge badge-resp"><i class="fas fa-user mr-1"></i> Estudiante</span>
                                <span class="badge badge-warning"><i class="fas fa-exclamation-triangle mr-1"></i> Por Vencer</span>
                            </div>
                            <p>Elaboración de marco teórico, metodología y estado del arte.</p>
                            <div class="progress progress-xs mb-2">
                                <div class="progress-bar bg-warning" style="width: 40%"></div>
                            </div>
                            <div class="text-right">
                                <button class="btn btn-sm btn-outline-secondary"><i class="fas fa-edit mr-1"></i> Editar</button>
                                <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash-alt mr-1"></i> Eliminar</button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Item 3 -->
                    <div class="timeline-item">
                        <div class="timeline-badge"></div>
                        <div class="timeline-content on-time">
                            <div class="timeline-header">
                                <h4 class="timeline-title">Revisión Preliminar</h4>
                                <div class="timeline-date">
                                    <i class="far fa-calendar-alt mr-1"></i> 1 Sep - 15 Sep 2025
                                </div>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="badge badge-resp"><i class="fas fa-user mr-1"></i> Asesor</span>
                                <span class="badge badge-success"><i class="fas fa-check-circle mr-1"></i> En Tiempo</span>
                            </div>
                            <p>Primera revisión por parte del asesor y ajustes iniciales.</p>
                            <div class="progress progress-xs mb-2">
                                <div class="progress-bar bg-success" style="width: 15%"></div>
                            </div>
                            <div class="text-right">
                                <button class="btn btn-sm btn-outline-secondary"><i class="fas fa-edit mr-1"></i> Editar</button>
                                <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash-alt mr-1"></i> Eliminar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
    <style>
        :root {
            --unah-primary: #0b2e59;
            --unah-secondary: #a6192e;
            --unah-accent: #007bff;
        }
        
        .unah-header {
            background-color: var(--unah-primary);
            color: white;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        
        .unah-header h1 {
            font-size: 1.8rem;
            margin-bottom: 5px;
        }
        
        .unah-header p {
            font-size: 0.9rem;
            opacity: 0.9;
            margin-bottom: 0;
        }
        
        .card-header-unah {
            background-color: var(--unah-primary) !important;
            color: white !important;
        }
        
        .btn-unah {
            background-color: var(--unah-secondary);
            border-color: var(--unah-secondary);
            color: white;
        }
        
        .btn-unah:hover {
            background-color: #8c1525;
            border-color: #8c1525;
            color: white;
        }
        
        .btn-outline-unah {
            color: var(--unah-secondary);
            border-color: var(--unah-secondary);
        }
        
        .btn-outline-unah:hover {
            background-color: var(--unah-secondary);
            color: white;
        }
        
        .filter-box {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .timeline-container {
            position: relative;
            margin: 20px 0;
        }
        
        .timeline-line {
            position: absolute;
            top: 0;
            bottom: 0;
            left: 20px;
            width: 4px;
            background-color: var(--unah-accent);
        }
        
        .timeline-item {
            position: relative;
            margin-bottom: 30px;
            padding-left: 60px;
        }
        
        .timeline-badge {
            position: absolute;
            left: 10px;
            top: 0;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background-color: var(--unah-accent);
            border: 3px solid white;
            z-index: 2;
        }
        
        .timeline-content {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }
        
        .timeline-content:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .timeline-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            border-bottom: 1px solid #eee;
            padding-bottom: 8px;
        }
        
        .timeline-title {
            font-weight: 600;
            color: var(--unah-primary);
            margin: 0;
        }
        
        .timeline-date {
            font-size: 0.9em;
            color: #6c757d;
        }
        
        .badge-resp {
            background-color: #6610f2;
            font-weight: normal;
        }
        
        .urgent {
            border-left: 4px solid #dc3545;
        }
        
        .warning {
            border-left: 4px solid #ffc107;
        }
        
        .on-time {
            border-left: 4px solid #28a745;
        }
        
        .progress-xs {
            height: 8px;
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Efecto hover en tarjetas de timeline
            $('.timeline-content').hover(
                function() {
                    $(this).css('transform', 'translateY(-3px)');
                    $(this).css('box-shadow', '0 5px 15px rgba(0,0,0,0.1)');
                },
                function() {
                    $(this).css('transform', 'translateY(0)');
                    $(this).css('box-shadow', '0 2px 5px rgba(0,0,0,0.05)');
                }
            );
            
            // Animación para elementos urgentes
            function pulseUrgent() {
                $('.urgent').animate({
                    opacity: 0.8
                }, 800).animate({
                    opacity: 1
                }, 800, pulseUrgent);
            }
            pulseUrgent();
        });
    </script>
@stop