@extends('adminlte::page')

@section('title', 'Mi Perfil')

@section('content_header')
    <div class="unah-header">
         <h1 class="mb'0">
        <i class="fas fa-user-circle mr-2"></i>Mi Perfil
    </h1>
        <p class="mb-0">Universidad Nacional Autónoma de Honduras - Posgrado en Informática Administrativa</p>
    </div>
    @if (session('success'))
        <div class="alert alert-success" id="successAlert">
            {{ session('success') }}
        </div>
    @endif
@stop

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-lg border-0 rounded-lg overflow-hidden">
                <div class="card-header bg-gradient-primary py-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <h3 class="card-title mb-0 text-white">
                            <i class="fas fa-id-card mr-2"></i>Información Personal
                        </h3>
                        <span class="badge badge-light">
                            {{ $user->created_at->format('d/m/Y') }}
                        </span>
                    </div>
                </div>
                
                <div class="card-body p-0">
                    <div class="row no-gutters">
                        <div class="col-md-4 bg-light-primary d-flex flex-column align-items-center justify-content-center p-4">
                            <div class="position-relative mb-3">
                                <img src="{{ Auth::user()->adminlte_image() }}" 
                                     class="img-fluid rounded-circle shadow border border-4 border-white"
                                     alt="Avatar"
                                     width="180">
                                <div class="position-absolute bottom-0 end-0 bg-white rounded-circle p-1 shadow-sm">
                                    @if($user->estado)
                                        <span class="badge badge-success p-2 rounded-circle" title="Usuario Activo">
                                            <i class="fas fa-check"></i>
                                        </span>
                                    @else
                                        <span class="badge badge-danger p-2 rounded-circle" title="Usuario Inactivo">
                                            <i class="fas fa-times"></i>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="text-center mt-3">
                                <h5 class="font-weight-bold text-dark mb-0">{{ $user->nombres }}</h5>
                                <h5 class="font-weight-bold text-dark">{{ $user->apellidos }}</h5>
                                <div class="text-muted small mt-1">
                                    <i class="fas fa-user-tag mr-1"></i>
                                    {{ $user->roles->first()->nombre_rol ?? 'Sin rol asignado' }}
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-8">
                            <div class="p-4">
                                <div class="profile-details">
                                    <div class="detail-item border-bottom py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="icon-circle bg-primary mr-3">
                                                <i class="fas fa-user-tag text-white"></i>
                                            </div>
                                            <div>
                                                <h6 class="text-muted mb-0">Usuario</h6>
                                                <p class="font-weight-bold mb-0 text-dark">{{ $user->usuario }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="detail-item border-bottom py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="icon-circle bg-info mr-3">
                                                <i class="fas fa-envelope text-white"></i>
                                            </div>
                                            <div>
                                                <h6 class="text-muted mb-0">Email</h6>
                                                <p class="font-weight-bold mb-0 text-dark">{{ $user->email }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="detail-item border-bottom py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="icon-circle bg-warning mr-3">
                                                <i class="fas fa-id-card text-white"></i>
                                            </div>
                                            <div>
                                                <h6 class="text-muted mb-0">Identidad</h6>
                                                <p class="font-weight-bold mb-0 text-dark">{{ $user->identidad }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="detail-item border-bottom py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="icon-circle bg-success mr-3">
                                                <i class="fas fa-user-check text-white"></i>
                                            </div>
                                            <div>
                                                <h6 class="text-muted mb-0">Estado</h6>
                                                <p class="font-weight-bold mb-0">
                                                    @if($user->estado)
                                                        <span class="text-success">Activo</span>
                                                    @else
                                                        <span class="text-danger">Inactivo</span>
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="detail-item py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="icon-circle bg-secondary mr-3">
                                                <i class="fas fa-calendar-alt text-white"></i>
                                            </div>
                                            <div>
                                                <h6 class="text-muted mb-0">Fecha de Registro</h6>
                                                <p class="font-weight-bold mb-0 text-dark">
                                                    {{ $user->created_at->format('d/m/Y H:i') }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card-footer bg-light text-center py-3">
                    <small class="text-muted">
                        Última actualización: {{ $user->updated_at->format('d/m/Y H:i') }}
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    .unah-header {
        background: linear-gradient(135deg, #0b2e59, #1a5a8d);
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        color: white;
        margin-bottom: 25px;
    }
    .bg-light-primary {
        background-color: #e8f4ff;
    }
    
    .icon-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .profile-details .detail-item {
        transition: all 0.3s ease;
    }
    
    .profile-details .detail-item:hover {
        background-color: #f8f9fa;
        transform: translateX(5px);
    }
    
    .card {
        border: none;
        box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.08);
        transition: transform 0.3s ease;
    }
    
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.75rem 2rem rgba(0, 0, 0, 0.12);
    }
    
    .bg-gradient-primary {
        background: linear-gradient(120deg, #3b82f6, #1d4ed8);
    }
    
    .border-light {
        border-color: #e9ecef !important;
    }
</style>
@stop

@section('js')
<script>
    $(document).ready(function() {
        // Efecto sutil al cargar
        $('.card').hide().fadeIn(800);
        $('.detail-item').each(function(i) {
            $(this).delay(100 * i).animate({opacity: 1}, 300);
        });
    });
</script>
@stop
