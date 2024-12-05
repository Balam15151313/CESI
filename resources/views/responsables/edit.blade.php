@extends('layouts.app')

@section('title', 'Editar responsable')

@section('content')
    <h1 class="mb-4 text-center">Editar responsable</h1>

    <form action="{{ route('responsables.update', ['responsable' => $responsable->id]) }}" method="POST"
        enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="responsable_nombre" class="form-label">Nombre completo</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" class="form-control @error('responsable_nombre') is-invalid @enderror"
                            id="responsable_nombre" name="responsable_nombre"
                            value="{{ old('responsable_nombre', $responsable->responsable_nombre) }}" required
                            placeholder="Ingresa el nombre completo">
                        @error('responsable_nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label for="responsable_telefono" class="form-label">Teléfono</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                        <input type="tel" class="form-control @error('responsable_telefono') is-invalid @enderror"
                            id="responsable_telefono" name="responsable_telefono"
                            value="{{ old('responsable_telefono', $responsable->responsable_telefono) }}" required
                            placeholder="Ingresa el teléfono">
                        @error('responsable_telefono')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="responsable_usuario" class="form-label">Correo electrónico</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="text" class="form-control @error('responsable_usuario') is-invalid @enderror"
                            id="responsable_usuario" name="responsable_usuario"
                            value="{{ old('responsable_usuario', $responsable->responsable_usuario) }}" required
                            placeholder="Ingresa el correo electrónico">
                        @error('responsable_usuario')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label for="responsable_contraseña" class="form-label">Contraseña</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control @error('responsable_contraseña') is-invalid @enderror"
                            id="responsable_contraseña" name="responsable_contraseña"
                            placeholder="Ingresa la nueva contraseña">
                        @error('responsable_contraseña')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <small class="form-text text-muted">Dejar en blanco para mantener la contraseña actual</small>
                </div>
            </div>
        </div>

        <div class="mb-3">
            <label for="responsable_foto" class="form-label">Foto del responsable</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-image"></i></span>
                <input type="file" class="form-control @error('responsable_foto') is-invalid @enderror"
                    id="responsable_foto" name="responsable_foto" accept="image/*">
                @error('responsable_foto')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            @if ($responsable->responsable_foto)
                <img src="{{ asset('storage/' . $responsable->responsable_foto) }}" alt="Foto actual" width="100"
                    class="foto-show">
            @endif
            <img id="imagenPrevisualizacion" class="foto-show">
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary btn-submit">Actualizar</button>
            <a href="{{ route('responsables.index') }}" class="btn btn-secondary">Volver a la lista</a>
        </div>
    </form>

    <script>
        const inputImagen = document.getElementById('responsable_foto');
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
            };

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
