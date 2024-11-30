@extends('layouts.app')

@section('title', 'Editar salón')

@section('content')
    <h3>
        Editar responsable <i>{{ $responsable->responsable_nombre }}</i>
    </h3>

    <form action="{{ route('responsables.update', ['responsable' => $responsable->id]) }}" method="POST"
        enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row g-3">
            <!-- Nombre -->
            <div class="col-md-12">
                <label for="InputNombre" class="form-label">* Nombre de responsable</label>
                <input type="text" name="responsable_nombre" id="InputNombre" class="form-control"
                    placeholder="Nombre del responsable"
                    value="{{ old('responsable_nombre', $responsable->responsable_nombre) }}">
            </div>

            <!-- Usuario -->
            <div class="col-md-4">
                <label for="InputUsuario" class="form-label">Usuario</label>
                <input type="text" name="responsable_usuario" id="InputUsuario" class="form-control"
                    placeholder="usuario@ejemplo.com"
                    value="{{ old('responsable_usuario', $responsable->responsable_usuario) }}">
            </div>

            <!-- Contraseña -->
            <div class="col-md-4">
                <label for="InputPassword" class="form-label">Contraseña</label>
                <input type="password" name="responsable_contraseña" id="InputPassword" class="form-control"
                    placeholder="Ingrese una nueva contraseña (dejar vacío si no desea cambiarla)">
            </div>

            <!-- Teléfono -->
            <div class="col-md-4">
                <label for="InputTelefono" class="form-label">Teléfono</label>
                <input type="tel" name="responsable_telefono" id="InputTelefono" class="form-control"
                    placeholder="Telefono del responsable"
                    value="{{ old('responsable_telefono', $responsable->responsable_telefono) }}"required>
            </div>

            <!-- Foto del responsable -->
            <div class="col-md-4">
                <label for="InputFoto" class="form-label">Foto del responsable</label>
                <!-- Mostrar la foto actual si existe -->
                @if ($responsable->responsable_foto)
                    <div class="mb-2">
                        <img src="{{ asset('storage/' . $responsable->responsable_foto) }}" alt="Foto del responsable"
                            class="img-thumbnail" style="max-width: 100px;">
                    </div>
                @endif
                <input type="file" name="responsable_foto" id="InputFoto" class="form-control" accept="image/*">
            </div>

            <!-- Botón de envío -->
            <div class="col-md-12 text-end">
                <button type="submit" class="btn btn-primary">
                    Editar
                </button>
            </div>
        </div>
    </form>

    <!-- Errores -->
    @if ($errors->any())
        <div class="mt-3 alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
@endsection
