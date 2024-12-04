@extends('layouts.app')

@section('title', 'Crear Alumno')

@section('content')
    <h1 class="text-center mb-4">Crear Nuevo Alumno</h1>

    <form action="{{ route('alumnos.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="alumno_nombre" class="form-label">Nombre completo</label>
                    <input type="text" class="form-control @error('alumno_nombre') is-invalid @enderror"
                        id="alumno_nombre" name="alumno_nombre" value="{{ old('alumno_nombre') }}" required>
                    @error('alumno_nombre')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="alumno_nacimiento" class="form-label">Fecha de nacimiento</label>
                    <input type="date" class="form-control @error('alumno_nacimiento') is-invalid @enderror"
                        id="alumno_nacimiento" name="alumno_nacimiento" value="{{ old('alumno_nacimiento') }}" required>
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
                    <select class="form-select @error('cesi_salon_id') is-invalid @enderror" id="cesi_salon_id"
                        name="cesi_salon_id" required>
                        <option value="">Selecciona un salón</option>
                        @foreach ($salones as $salon)
                            <option value="{{ $salon->id }}" {{ old('cesi_salon_id') == $salon->id ? 'selected' : '' }}>
                                {{ $salon->salon_grado . ' ' . $salon->salon_grupo }}
                            </option>
                        @endforeach
                    </select>
                    @error('cesi_salon_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="cesi_tutore_id" class="form-label">Tutor asociado</label>
                    <select class="form-select @error('cesi_tutore_id') is-invalid @enderror" id="cesi_tutore_id"
                        name="cesi_tutore_id" required>
                        <option value="">Selecciona un tutor</option>
                        @foreach ($tutores as $tutor)
                            <option value="{{ $tutor->id }}"
                                {{ old('cesi_tutore_id') == $tutor->id ? 'selected' : '' }}>
                                {{ $tutor->tutor_nombre }}
                            </option>
                        @endforeach
                    </select>
                    @error('cesi_tutore_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="mb-3">
            <label for="alumno_foto" class="form-label">Foto del Alumno</label>
            <input type="file" class="form-control @error('alumno_foto') is-invalid @enderror" id="alumno_foto"
                name="alumno_foto" required accept="image/*" onchange="previewImage(event)">
            @error('alumno_foto')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div class="mt-3">
                <img id="foto_preview" class="foto-show">
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">Guardar</button>
            <a href="#" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>

    <script>
        function previewImage(event) {
            const input = event.target;
            const preview = document.getElementById('foto_preview');

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
