@extends('layouts.app')

@section('title', 'Ver Salón')

@section('content')
    <h1 class="text-center mb-4">Información del Salón</h1>

    <!-- Card para la información del salón -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Información del Salón</h5>
            <p class="card-text"><strong>Salón:</strong> {{ $salon->salon_grado }}
                {{ $salon->salon_grupo }}</p>
            <p class="card-text"><strong>Escuela:</strong> {{ $salon->escuelas->escuela_nombre }}</p>
            <p class="card-text"><strong>Escolaridad:</strong> {{ $salon->escuelas->escuela_escolaridad }}
            </p>
        </div>
    </div>

    <!-- Card para la información del profesor encargado -->
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    @if ($salon->maestros && $salon->maestros->maestro_foto)
                        <img src="{{ asset('storage/' . $salon->maestros->maestro_foto) }}" alt="Foto del maestro"
                            class="foto-show">
                    @else
                        <p>Sin foto del maestro</p>
                    @endif
                </div>
                <div class="col-md-8">
                    <h5 class="card-title">Información del Profesor Encargado</h5>
                    @if ($salon->maestros)
                        <p class="card-text"><strong>Nombre:</strong> {{ $salon->maestros->maestro_nombre }}</p>
                        <p class="card-text"><strong>Correo electrónico:</strong> {{ $salon->maestros->maestro_usuario }}
                        </p>
                        <p class="card-text"><strong>Teléfono:</strong> {{ $salon->maestros->maestro_telefono }}</p>
                    @else
                        <p>Este salón no tiene un maestro asignado.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <a href="{{ route('salones.index') }}" class="btn btn-secondary">Volver a la lista</a>
    </div>
@endsection
