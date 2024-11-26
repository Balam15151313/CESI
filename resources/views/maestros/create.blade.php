@extends('layouts.app')

@section('title', 'Crear maestro')

@section('content')
    <h1 class="text-center mb-4">Crear Nuevo maestro</h1>

    <form action="{{ route('maestros.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="maestro_nombre" class="form-label">Nombre completo</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" class="form-control @error('maestro_nombre') is-invalid @enderror"
                            id="maestro_nombre" name="maestro_nombre" value="{{ old('maestro_nombre') }}" required
                            placeholder="Ingresa el nombre completo">
                        @error('maestro_nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="maestro_telefono" class="form-label">Teléfono</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                        <input type="tel" class="form-control @error('maestro_telefono') is-invalid @enderror"
                            id="maestro_telefono" name="maestro_telefono" value="{{ old('maestro_telefono') }}" required
                            placeholder="Ingresa el teléfono">
                        @error('maestro_telefono')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="maestro_usuario" class="form-label">Correo electrónico</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="email" class="form-control @error('maestro_usuario') is-invalid @enderror"
                            id="maestro_usuario" name="maestro_usuario" value="{{ old('maestro_usuario') }}" required
                            placeholder="Ingresa el correo electrónico">
                        @error('maestro_usuario')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="maestro_contraseña" class="form-label">Contraseña</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control @error('maestro_contraseña') is-invalid @enderror"
                            id="maestro_contraseña" name="maestro_contraseña" required placeholder="Ingresa la contraseña">
                        @error('maestro_contraseña')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-3">
            <label for="cesi_escuela_id" class="form-label">Escuela asociada</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-school"></i></span>
                <select class="form-select @error('cesi_escuela_id') is-invalid @enderror" id="cesi_escuela_id"
                    name="cesi_escuela_id" required>
                    <option value="">Selecciona una escuela</option>
                    @foreach ($escuelas as $escuela)
                        <option value="{{ $escuela->id }}" {{ old('cesi_escuela_id') == $escuela->id ? 'selected' : '' }}>
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
            <label for="maestro_foto" class="form-label">Foto del maestro</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-image"></i></span>
                <input type="file" class="form-control @error('maestro_foto') is-invalid @enderror" id="maestro_foto"
                    name="maestro_foto" required accept="image/*">
                @error('maestro_foto')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <img id="imagenPrevisualizacion" src="#" alt="Vista previa de la imagen"
                style="max-width: 200px; max-height: 200px; display: none;">
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">Guardar</button>
            <a href="{{ route('maestros.index') }}" class="btn btn-secondary">Volver a la lista</a>
        </div>
    </form>

    <script>
        const inputImagen = document.getElementById('maestro_foto');
        const imagenPrevisualizacion = document.getElementById('imagenPrevisualizacion');

        inputImagen.addEventListener('change', () => {
            const archivo = inputImagen.files[0];
            const lector = new FileReader();

            lector.onload = (e) => {
                imagenPrevisualizacion.src = e.target.result;
                imagenPrevisualizacion.style.display = 'block';
            }

            if (archivo) {
                lector.readAsDataURL(archivo);
            } else {
                imagenPrevisualizacion.src = '#';
                imagenPrevisualizacion.style.display = 'none';
            }
        });
    </script>
@endsection
