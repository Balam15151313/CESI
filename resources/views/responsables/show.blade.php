@extends('layouts.app')

@section('title', 'Ver responsable')

@section('content')
    <h1 class="text-center mb-4">Información del responsable</h1>

    <!-- Card para la información del responsable -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    @if ($responsable->responsable_foto)
                        <img src="{{ asset('storage/' . $responsable->responsable_foto) }}" alt="Foto del responsable"
                            class="img-fluid">
                    @else
                        <p>Sin foto</p>
                    @endif
                </div>
                <div class="col-md-8">
                    <h5 class="card-title">Información del Responsable</h5>
                    <p class="card-text"><strong>Nombre:</strong> {{ $responsable->responsable_nombre }}</p>
                    <p class="card-text"><strong>Correo electrónico:</strong> {{ $responsable->responsable_usuario }}</p>
                    <p class="card-text"><strong>Teléfono:</strong> {{ $responsable->responsable_telefono }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Card para la información del tutor -->
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    @if ($responsable->tutores->tutor_foto)
                        <img src="{{ asset('storage/' . $responsable->tutores->tutor_foto) }}" alt="Foto del tutor"
                            class="foto-show">
                    @else
                        <p>Sin foto</p>
                    @endif
                </div>
                <div class="col-md-8">
                    <h5 class="card-title">Información del Tutor</h5>
                    <p class="card-text"><strong>Nombre:</strong> {{ $responsable->tutores->tutor_nombre }}</p>
                    <p class="card-text"><strong>Correo electrónico:</strong> {{ $responsable->tutores->tutor_usuario }}</p>
                    <p class="card-text"><strong>Teléfono:</strong> {{ $responsable->tutores->tutor_telefono }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <a href="{{ route('responsablees.index') }}" class="btn btn-secondary">Volver a la lista</a>
    </div>
@endsection
