@extends('layouts.app')

@section('title', 'Editar Administrador')

@section('content')
    <h1 class="mb-4 text-center">Editar Administrador</h1>

    <form action="{{ route('admin.update', $admin->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="administrador_nombre" class="form-label">Nombre completo</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" class="form-control @error('administrador_nombre') is-invalid @enderror"
                            id="administrador_nombre" name="administrador_nombre"
                            value="{{ old('administrador_nombre', $admin->administrador_nombre) }}" required
                            placeholder="Ingresa el nombre completo">
                        @error('administrador_nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label for="administrador_usuario" class="form-label">Usuario</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" class="form-control @error('administrador_usuario') is-invalid @enderror"
                            id="administrador_usuario" name="administrador_usuario"
                            value="{{ old('administrador_usuario', $admin->administrador_usuario) }}" required
                            placeholder="Ingresa el usuario">
                        @error('administrador_usuario')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="administrador_telefono" class="form-label">Teléfono</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                        <input type="text" class="form-control @error('administrador_telefono') is-invalid @enderror"
                            id="administrador_telefono" name="administrador_telefono"
                            value="{{ old('administrador_telefono', $admin->administrador_telefono) }}" required
                            placeholder="Ingresa el teléfono">
                        @error('administrador_telefono')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label for="administrador_foto" class="form-label">Foto de Perfil</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-image"></i></span>
                        <input type="file" class="form-control @error('administrador_foto') is-invalid @enderror"
                            id="administrador_foto" name="administrador_foto" accept="image/*">
                        @error('administrador_foto')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    @if ($admin->administrador_foto)
                        <img src="{{ asset('storage/' . $admin->administrador_foto) }}" alt="Foto actual" width="100"
                            class="foto-show">
                    @endif
                    <img id="imagenPrevisualizacion" class="foto-show">
                </div>
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">Actualizar</button>
        </div>
    </form>

    <script>
        const inputImagen = document.getElementById('administrador_foto');
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
