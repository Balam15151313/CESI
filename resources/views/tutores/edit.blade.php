@extends('layouts.app')

@section('title', 'Editar Tutor')

@section('content')
    <h1 class="mb-4 text-center">Editar Tutor</h1>

    <form action="{{ route('tutores.update', $tutor->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="tutor_nombre" class="form-label">Nombre completo</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" class="form-control @error('tutor_nombre') is-invalid @enderror"
                            id="tutor_nombre" name="tutor_nombre" value="{{ old('tutor_nombre', $tutor->tutor_nombre) }}"
                            required placeholder="Ingresa el nombre completo">
                        @error('tutor_nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="tutor_telefono" class="form-label">Teléfono</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                        <input type="tel" class="form-control @error('tutor_telefono') is-invalid @enderror"
                            id="tutor_telefono" name="tutor_telefono"
                            value="{{ old('tutor_telefono', $tutor->tutor_telefono) }}" required
                            placeholder="Ingresa el teléfono">
                        @error('tutor_telefono')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="tutor_usuario" class="form-label">Correo electrónico</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="text" class="form-control @error('tutor_usuario') is-invalid @enderror"
                            id="tutor_usuario" name="tutor_usuario"
                            value="{{ old('tutor_usuario', $tutor->tutor_usuario) }}" required
                            placeholder="Ingresa el correo electrónico">
                        @error('tutor_usuario')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="tutor_contraseña" class="form-label">Contraseña</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control @error('tutor_contraseña') is-invalid @enderror"
                            id="tutor_contraseña" name="tutor_contraseña" placeholder="Ingresa la nueva contraseña">
                        @error('tutor_contraseña')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <small class="form-text text-muted">Dejar en blanco para mantener la contraseña actual</small>
                </div>
            </div>
        </div>

        <div class="mb-3">
            <label for="cesi_escuela_id" class="form-label">Escuela asociada</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-school"></i></span>
                <select class="form-select @error('cesi_escuela_id') is-invalid @enderror" id="cesi_escuela_id"
                    name="cesi_escuela_id" required>
                    @foreach ($escuelas as $escuela)
                        <option value="{{ $escuela->id }}"
                            {{ old('cesi_escuela_id', $tutor->cesi_escuela_id) == $escuela->id ? 'selected' : '' }}>
                            {{ $escuela->escuela_nombre }}
                        </option>
                    @endforeach
                    @error('cesi_escuela_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label for="tutor_foto" class="form-label">Foto del tutor</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-image"></i></span>
                <input type="file" class="form-control @error('tutor_foto') is-invalid @enderror" id="tutor_foto"
                    name="tutor_foto" accept="image/*">
                @error('tutor_foto')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            @if ($tutor->tutor_foto)
                <img src="{{ asset('storage/' . $tutor->tutor_foto) }}" alt="Foto actual" width="100" class="foto-show">
            @endif
            <img id="imagenPrevisualizacion" class="foto-show">
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary btn-submit">Actualizar</button>
            <a href="{{ route('tutores.index') }}" class="btn btn-secondary">Volver a la lista</a>
        </div>
    </form>

    <script>
        const inputImagen = document.getElementById('tutor_foto');
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
