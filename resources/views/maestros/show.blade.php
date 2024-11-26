@extends('layouts.app')

@section('title', 'Ver maestro')

@section('content')
    <h1 class="text-center mb-4">Información del maestro</h1>

    <!-- Card para la información del maestro -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    @if ($maestro->maestro_foto)
                        <img src="{{ asset('storage/' . $maestro->maestro_foto) }}" alt="Foto del maestro" class="img-fluid">
                    @else
                        <p>Sin foto</p>
                    @endif
                </div>
                <div class="col-md-8">
                    <h5 class="card-title">Información del Maestro</h5>
                    <p class="card-text"><strong>Nombre:</strong> {{ $maestro->maestro_nombre }}</p>
                    <p class="card-text"><strong>Correo:</strong> {{ $maestro->maestro_usuario }}</p>
                    <p class="card-text"><strong>Teléfono:</strong> {{ $maestro->maestro_telefono }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Card para el salón encargado -->
    <div class="card">
        <div class="card-body">
            @if ($maestro->salones)
                <h5 class="card-title">Salón Encargado</h5>
                <p class="card-text"><strong>Salón:</strong> {{ $maestro->salones->salon_grado }}
                    {{ $maestro->salones->salon_grupo }}</p>
                <p class="card-text"><strong>Escuela:</strong> {{ $maestro->salones->escuelas->escuela_nombre }}</p>
                <p class="card-text"><strong>Escolaridad:</strong> {{ $maestro->salones->escuelas->escuela_escolaridad }}
                </p>
            @else
                <p class="card-text">Este maestro no tiene un salón asignado.</p>
            @endif
        </div>
    </div>

    <div class="mt-4">
        <a href="{{ route('maestros.index') }}" class="btn btn-secondary">Volver a la lista</a>
    </div>
@endsection
