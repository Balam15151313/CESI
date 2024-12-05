@extends('layouts.app')

@section('title', 'Ver Salón')

@section('content')
    <h1 class="mb-4 text-center">Información del Salón</h1>

    <!-- Card para la información del salón -->
    <div class="mb-4 card">
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

    <!-- Card para la información de alumnos -->
    <div class="card">
        <div class="card-body">

            @if ($salon->alumno && $salon->alumno->isNotEmpty())
                <ul class="list-group">
                    <h5 class="mt-4 card-title">Alumnos en el Salón</h5>
                    @foreach ($salon->alumno as $alumno)
                        <li class="list-group-item d-flex align-items-center">
                            <!-- Columna de la Foto del Alumno -->
                            <div class="col-md-3">
                                <img src="{{ asset('storage/' . $alumno->alumno_foto) }}" alt="Foto del alumno"
                                    class="foto-show img-fluid" class="foto-show">
                            </div>

                            <!-- Columna de la Información del Alumno -->
                            <div class="col-md-9">
                                <p><strong>Nombre:</strong> {{ $alumno->alumno_nombre }}</p>
                                <p><strong>Fecha de nacimiento:</strong> {{ $alumno->alumno_nacimiento }}</p>
                                <p><strong>Escuela:</strong> {{ $salon->escuelas->escuela_nombre }}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <p>No hay alumnos asignados a este salón.</p>
            @endif
        </div>
    </div>


    <div class="mt-4">
        <a href="{{ route('salones.index') }}" class="btn btn-secondary">Volver a la lista</a>
    </div>
@endsection
