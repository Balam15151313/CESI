@extends('layouts.app')

@section('title', 'Editar salón')

@section('content')
    <h1 class="text-center mb-4">Editar salón <i>{{ $salon->salon_grado }} {{ $salon->salon_grupo }}</i></h1>

    <form action="{{ route('salones.update', $salon->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="salon_grado" class="form-label">Grado</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-school"></i></span>
                        <input type="text" class="form-control @error('salon_grado') is-invalid @enderror"
                            id="salon_grado" name="salon_grado" value="{{ old('salon_grado', $salon->salon_grado) }}"
                            required placeholder="Ingresa el grado">
                        @error('salon_grado')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label for="salon_grupo" class="form-label">Grupo</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-users"></i></span>
                        <input type="text" class="form-control @error('salon_grupo') is-invalid @enderror"
                            id="salon_grupo" name="salon_grupo" value="{{ old('salon_grupo', $salon->salon_grupo) }}"
                            required placeholder="Ingresa el grupo">
                        @error('salon_grupo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="cesi_escuela_id" class="form-label">Escuela</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-school"></i></span>
                        <select name="cesi_escuela_id" id="cesi_escuela_id"
                            class="form-select @error('cesi_escuela_id') is-invalid @enderror" required>
                            <option selected disabled>Seleccione una escuela</option>
                            @foreach ($escuelas as $escuela)
                                <option value="{{ $escuela->id }}"
                                    {{ old('cesi_escuela_id', $salon->cesi_escuela_id) == $escuela->id ? 'selected' : '' }}>
                                    {{ $escuela->escuela_nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('cesi_escuela_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label for="cesi_maestro_id" class="form-label">Maestro</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-chalkboard-teacher"></i></span>
                        <select class="form-select @error('cesi_maestro_id') is-invalid @enderror" id="cesi_maestro_id"
                            name="cesi_maestro_id">
                            <option selected disabled>Seleccione un maestro</option>
                            @foreach ($maestros as $maestro)
                                <option value="{{ $maestro->id }}"
                                    {{ old('cesi_maestro_id', $salon->cesi_maestro_id) == $maestro->id ? 'selected' : '' }}>
                                    {{ $maestro->maestro_nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('cesi_maestro_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4 text-end">
            <button type="submit" class="btn btn-primary">Actualizar salón</button>
            <a href="{{ route('salones.index') }}" class="btn btn-secondary">Volver a la lista</a>
        </div>
    </form>

    @if ($errors->any())
        <div class="mt-4 alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
@endsection
