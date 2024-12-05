<!-- resources/views/tutores/crear_tutor.blade.php -->
@extends('layouts.app')

@section('title', 'Crear Tutor')

@section('content')
    <h1 class="mb-4 text-center">Crear Nuevo Tutor</h1>

    <form action="{{ route('tutores.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="tutor_nombre" class="form-label">Nombre completo</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" class="form-control @error('tutor_nombre') is-invalid @enderror"
                            id="tutor_nombre" name="tutor_nombre" value="{{ old('tutor_nombre') }}" required
                            placeholder="Ingresa el nombre completo">
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
                            id="tutor_telefono" name="tutor_telefono" value="{{ old('tutor_telefono') }}" required
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
                        <input type="email" class="form-control @error('tutor_usuario') is-invalid @enderror"
                            id="tutor_usuario" name="tutor_usuario" value="{{ old('tutor_usuario') }}" required
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
                            id="tutor_contraseña" name="tutor_contraseña" required placeholder="Ingresa la contraseña">
                        @error('tutor_contraseña')
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
            <label for="tutor_foto" class="form-label">Foto del Tutor</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-image"></i></span>
                <input type="file" class="form-control @error('tutor_foto') is-invalid @enderror" id="tutor_foto"
                    name="tutor_foto" required accept="image/*" onchange="previewImage(event)">
                @error('tutor_foto')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mt-3">
                <!-- Añadimos 'style="display: none;"' para ocultar la imagen inicialmente -->
                <img id="imagenPrevisualizacion" class="foto-show" style="display: none;">
            </div>
        </div>

        <div class="mt-4">
            <!-- Añadimos la clase 'btn-submit' al botón 'Guardar' -->
            <button type="submit" class="btn btn-primary btn-submit">Guardar</button>
            <a href="{{ route('tutores.index') }}" class="btn btn-secondary">Volver a la lista</a>
        </div>
    </form>

    <script>
        function previewImage(event) {
            const input = event.target;
            const preview = document.getElementById('imagenPrevisualizacion');

            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };

                reader.readAsDataURL(input.files[0]);
            } else {
                preview.style.display = 'none';
            }
        }
    </script>
@endsection
