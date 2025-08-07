@extends('adminlte::page')

@section('title', 'Reenviar Acuse')

@section('content_header')
<div class="elegant-header">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h1 class="mb-0"><i class="fas fa-share mr-2 text-warning"></i> Reenviar Acuse</h1>
            <p class="subtitle">Universidad Nacional Autónoma de Honduras - Posgrado en Informática Administrativa</p>
        </div>
        <div class="header-icon">
            <i class="fas fa-paper-plane"></i>
        </div>
    </div>
</div>
@stop

@section('content')
<div class="container-fluid">
    <div class="card card-elegant">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-share-alt mr-2 text-muted"></i> 
                Reenviar Acuse AR-{{ str_pad($acuse->id_acuse, 5, '0', STR_PAD_LEFT) }}
            </h5>
        </div>
        <div class="card-body">
            <form action="{{ route('acuses.reenviar', $acuse->id_acuse) }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>Nuevo Destinatario</label>
                    <select class="form-control form-control-elegant" name="nuevo_destinatario" required>
                        <option value="">Seleccionar destinatario</option>
                        @foreach($usuarios as $usuario)
                            @if($usuario->id_usuario != auth()->user()->id_usuario)
                                <option value="{{ $usuario->id_usuario }}">
                                    {{ $usuario->nombres }} {{ $usuario->apellidos }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>
                
                <h5 class="mt-4"><i class="fas fa-boxes mr-2"></i> Elementos del Acuse</h5>
                <div class="list-group mb-4">
                    @foreach($acuse->elementos as $elemento)
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0">{{ $elemento->nombre }}</h6>
                                    <small class="text-muted">{{ $elemento->tipo->categoria }}</small>
                                </div>
                                <span class="badge badge-primary badge-pill">x{{ $elemento->cantidad }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('acuses.index') }}" class="btn btn-outline-secondary btn-elegant mr-2">
                        <i class="fas fa-times mr-1"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary btn-elegant">
                        <i class="fas fa-share mr-1"></i> Reenviar Acuse
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .list-group-item {
        border-radius: 8px;
        margin-bottom: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.03);
        transition: all 0.3s ease;
    }
    
    .list-group-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.05);
    }
</style>
@endsection