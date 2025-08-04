@extends('adminlte::page')

@section('title', 'Editar Proceso de Pago')

@section('content_header')
<div class="elegant-header">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h1 class="mb-0"><i class="fas fa-edit mr-2 text-primary"></i> Editar Proceso: {{ $pagoTerna->codigo }}</h1>
            <p class="subtitle">Administración de pagos de terna</p>
        </div>
    </div>
</div>
@stop

@section('content')
<div class="container-fluid">
    <div class="card card-elegant">
        <div class="card-header">
            <h5 class="card-title mb-0">Editar Información</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('terna.admin.update', $pagoTerna->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Descripción</label>
                            <input type="text" class="form-control" name="descripcion" value="{{ $pagoTerna->descripcion }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Fecha de Defensa</label>
                            <input type="date" class="form-control" name="fecha_defensa" value="{{ $pagoTerna->fecha_defensa->format('Y-m-d') }}" required>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Responsable</label>
                            <input type="text" class="form-control" name="responsable" value="{{ $pagoTerna->responsable }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Fecha Límite</label>
                            <input type="datetime-local" class="form-control" name="fecha_limite" value="{{ $pagoTerna->fecha_limite->format('Y-m-d\TH:i') }}" required>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Asistente Asignado</label>
                            <select class="form-control" name="id_asistente" required>
                                @foreach($asistentes as $asistente)
                                    <option value="{{ $asistente->id_usuario }}" {{ $pagoTerna->id_asistente == $asistente->id_usuario ? 'selected' : '' }}>
                                        {{ $asistente->nombres }} {{ $asistente->apellidos }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('terna.admin.index') }}" class="btn btn-outline-secondary btn-elegant mr-2">
                        <i class="fas fa-times mr-1"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary btn-elegant">
                        <i class="fas fa-save mr-1"></i> Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop