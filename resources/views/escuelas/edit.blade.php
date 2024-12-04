@extends('layouts.app')

@section('title', 'Editar Escuela')

@section('content')
    <div class="container">
        <h1>Editar Escuela</h1>

        <form action="{{ route('escuelas.update', $escuela->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT') <!-- Método PUT para actualizar -->

            <!-- Nombre de la escuela -->
            <div class="mb-3">
                <label for="escuela_nombre" class="form-label">Nombre de la Escuela</label>
                <input type="text" id="escuela_nombre" name="escuela_nombre" class="form-control"
                    value="{{ $escuela->escuela_nombre }}" placeholder="Ingrese el nombre de la escuela" required>
            </div>

            <!-- Escolaridad -->
            <div class="mb-3">
                <label for="escuela_escolaridad" class="form-label">Escolaridad</label>
                <select id="escuela_escolaridad" name="escuela_escolaridad" class="form-control" required>
                    <option value="" disabled {{ !$escuela->escuela_escolaridad ? 'selected' : '' }}>
                        Seleccione una opción
                    </option>
                    <option value="Kinder" {{ $escuela->escuela_escolaridad === 'Kinder' ? 'selected' : '' }}>Kinder
                    </option>
                    <option value="Primaria" {{ $escuela->escuela_escolaridad === 'Primaria' ? 'selected' : '' }}>Primaria
                    </option>
                    <option value="Secundaria" {{ $escuela->escuela_escolaridad === 'Secundaria' ? 'selected' : '' }}>
                        Secundaria
                    </option>
                </select>
            </div>

            <!-- Mapa y coordenadas -->
            <div class="mb-3">
                <label for="map_search" class="form-label">Buscar en el mapa</label>
                <input type="text" id="map_search" class="form-control" placeholder="Buscar ubicación"
                    oninput="mostrarSugerencias(this.value)">
                <ul id="suggestions" style="list-style-type: none; padding: 0; margin-top: 10px;"></ul>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <div id="map" style="height: 400px; margin-bottom: 20px;"></div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="escuela_latitud">Latitud</label>
                        <input type="text" id="escuela_latitud" name="escuela_latitud" class="form-control"
                            value="{{ $escuela->escuela_latitud }}" readonly required>
                    </div>
                    <div class="form-group mt-3">
                        <label for="escuela_longitud">Longitud</label>
                        <input type="text" id="escuela_longitud" name="escuela_longitud" class="form-control"
                            value="{{ $escuela->escuela_longitud }}" readonly required>
                    </div>
                </div>
            </div>

            <!-- Colores -->
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="ui_color1" class="form-label">Color 1</label>
                    <input type="color" id="ui_color1" name="ui_color1" class="form-control"
                        value="{{ $escuela->ui_color1 }}" required>
                </div>
                <div class="col-md-4">
                    <label for="ui_color2" class="form-label">Color 2</label>
                    <input type="color" id="ui_color2" name="ui_color2" class="form-control"
                        value="{{ $escuela->ui_color2 }}" required>
                </div>
                <div class="col-md-4">
                    <label for="ui_color3" class="form-label">Color 3</label>
                    <input type="color" id="ui_color3" name="ui_color3" class="form-control"
                        value="{{ $escuela->ui_color3 }}" required>
                </div>
            </div>
            <div class="color-preview mb-3" id="colorPreview" style="height: 50px;"></div>

            <!-- Logotipo -->
            <div class="mb-3">
                <label for="escuela_logo" class="form-label">Logotipo</label>
                <input type="file" id="escuela_logo" name="escuela_logo" class="form-control" accept="image/*">
            </div>

            <button type="submit" class="btn btn-primary">Guardar Escuela</button>
        </form>
    </div>

    <!-- Scripts -->
    <script>
        var map = L.map('map').setView([{{ $escuela->escuela_latitud }}, {{ $escuela->escuela_longitud }}], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        var marker = L.marker([{{ $escuela->escuela_latitud }}, {{ $escuela->escuela_longitud }}], {
            draggable: true
        }).addTo(map);

        marker.on('dragend', function() {
            var latLng = marker.getLatLng();
            document.getElementById('escuela_latitud').value = latLng.lat;
            document.getElementById('escuela_longitud').value = latLng.lng;
        });

        function guardarColores() {
            const color1 = document.getElementById('ui_color1').value;
            const color2 = document.getElementById('ui_color2').value;
            const color3 = document.getElementById('ui_color3').value;

            const colorPreview = document.getElementById('colorPreview');
            colorPreview.style.background = `linear-gradient(90deg, ${color1}, ${color2}, ${color3})`;
        }
    </script>
@endsection
