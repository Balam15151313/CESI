@extends('layouts.app')

@section('title', 'Ver Tutor')

@section('content')
    </head>

    <body>
        <h1 class="text-center mb-4">Información del Tutor</h1>

        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        @if ($tutor->tutor_foto)
                            <img src="{{ asset('storage/' . $tutor->tutor_foto) }}" alt="Foto del Tutor" class="foto-show">
                        @else
                            <p>Sin foto</p>
                        @endif
                    </div>
                    <div class="col-md-8">
                        <h5 class="card-title">Nombre: {{ $tutor->tutor_nombre }}</h5>
                        <p class="card-text">Correo electrónico: {{ $tutor->tutor_usuario }}</p>
                        <p class="card-text">Teléfono: {{ $tutor->tutor_telefono }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <h2 class="text-center">Información asociada</h2>

            <div class="card">
                <div class="card-body">
                    <h3>Datos de los Alumnos</h3>
                    @foreach ($tutor->alumnos as $alumno)
                        <div class="border rounded p-3 mb-3">
                            <div class="row">
                                <div class="col-md-4">
                                    @if ($alumno->alumno_foto)
                                        <img src="{{ asset('storage/' . $alumno->alumno_foto) }}" alt="Foto del Alumno"
                                            class="foto-show">
                                    @else
                                        <p>Sin foto</p>
                                    @endif
                                </div>
                                <div class="col-md-8">
                                    <p class="card-text">Nombre: {{ $alumno->alumno_nombre }}</p>
                                    <p class="card-text">Fecha de nacimiento: {{ $alumno->alumno_nacimiento }}</p>
                                    <p class="card-text">Salón:
                                        {{ $alumno->salones->salon_grado . ' ' . $alumno->salones->salon_grupo }}</p>
                                    <p class="card-text">Escuela: {{ $alumno->salones->escuelas->escuela_nombre }}</p>
                                    <p class="card-text">Escolaridad: {{ $alumno->salones->escuelas->escuela_escolaridad }}
                                    </p>
                                    @if ($alumno->salones->maestros)
                                        <p class="card-text">Docente: {{ $alumno->salones->maestros->maestro_nombre }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <h3 class="mt-4">Datos de los Responsables</h3>
                    @foreach ($tutor->responsables as $responsable)
                        @if ($tutor->tutor_usuario != $responsable->responsable_usuario)
                            <div class="border rounded p-3 mb-3">
                                <p class="card-text">Nombre: {{ $responsable->responsable_nombre }}</p>
                                <p class="card-text">Correo electrónico: {{ $responsable->responsable_usuario }}</p>
                                <p class="card-text">Teléfono: {{ $responsable->responsable_telefono }}</p>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>

        <div class="mt-4">
            <a href="{{ route('tutores.index') }}" class="btn btn-secondary">Volver a la lista</a>
        </div>
    @endsection
