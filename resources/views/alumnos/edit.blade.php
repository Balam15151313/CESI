@extends('layouts.app')

@section('title', 'Editar Alumno')

@section('content')
    <h1 class="text-center mb-4">Editar Alumno</h1>

    <form action="{{ route('alumnos.update', $alumno->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="alumno_nombre" class="form-label">Nombre completo</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" class="form-control @error('alumno_nombre') is-invalid @enderror"
                            id="alumno_nombre" name="alumno_nombre"
                            value="{{ old('alumno_nombre', $alumno->alumno_nombre) }}" required
                            placeholder="Ingresa el nombre completo">
                    </div>
                    @error('alumno_nombre')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="alumno_nacimiento" class="form-label">Fecha de nacimiento</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                        <input type="date" class="form-control @error('alumno_nacimiento') is-invalid @enderror"
                            id="alumno_nacimiento" name="alumno_nacimiento"
                            value="{{ old('alumno_nacimiento', $alumno->alumno_nacimiento) }}" required>
                    </div>
                    @error('alumno_nacimiento')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="cesi_salon_id" class="form-label">Salón asociado</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-users"></i></span>
                        <select class="form-select @error('cesi_salon_id') is-invalid @enderror" id="cesi_salon_id"
                            name="cesi_salon_id" required>
                            <option value="">Selecciona un salón</option>
                            @foreach ($salones as $salon)
                                <option value="{{ $salon->id }}"
                                    {{ old('cesi_salon_id', $alumno->cesi_salon_id) == $salon->id ? 'selected' : '' }}>
                                    {{ $salon->salon_grado . ' ' . $salon->salon_grupo }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('cesi_salon_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="cesi_tutore_id" class="form-label">Tutor asociado</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user-graduate"></i></span>
                        <select class="form-select @error('cesi_tutore_id') is-invalid @enderror" id="cesi_tutore_id"
                            name="cesi_tutore_id" required>
                            <option value="">Selecciona un tutor</option>
                            @foreach ($tutores as $tutor)
                                <option value="{{ $tutor->id }}"
                                    {{ old('cesi_tutore_id', $alumno->cesi_tutore_id) == $tutor->id ? 'selected' : '' }}>
                                    {{ $tutor->tutor_nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('cesi_tutore_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="mb-3">
            <label for="alumno_foto" class="form-label">Foto</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-image"></i></span>
                <input type="file" class="form-control @error('alumno_foto') is-invalid @enderror" id="alumno_foto"
                    name="alumno_foto" accept="image/*">
            </div>
            @if ($alumno->alumno_foto)
                <img src="{{ asset('storage/' . $alumno->alumno_foto) }}" alt="Foto actual" width="100"
                    class="foto-show">
            @endif
            <img id="imagenPrevisualizacion" class="foto-show">
            @error('alumno_foto')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">Actualizar</button>
            <a href="{{ route('alumnos.index') }}" class="btn btn-secondary">Volver a la lista</a>
        </div>
    </form>

    <script>
        const inputImagen = document.getElementById('alumno_foto');
        const imagenPrevisualizacion = document.getElementById('imagenPrevisualizacion');
        const imagenActual = document.querySelector('img[alt="Foto actual"]');

        let imagenOriginalSrc = imagenActual ? imagenActual.src : null;

        inputImagen.addEventListener('change', () => {
            const archivo = inputImagen.files[0];
            const lector = new FileReader();

            lector.onload = (e) => {
                imagenPrevisualizacion.src = e.target.result;
                imagenPrevisualizacion.style.display = 'block';
                if (imagenActual) {
                    imagenActual.style.display = 'none';
                }
            }

            if (archivo) {
                lector.readAsDataURL(archivo);
            } else {
                imagenPrevisualizacion.src = '#';
                imagenPrevisualizacion.style.display = 'none';
                if (imagenActual) {
                    imagenActual.style.display = 'block';
                    imagenActual.src = imagenOriginalSrc;
                }
            }
        });
    </script>
@endsection
