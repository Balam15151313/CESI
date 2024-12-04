@extends('layouts.app')

@section('title', 'Crear escuela')

@section('content')
    <h1>Agregar Nueva Escuela</h1>
    <form action="{{ route('escuelas.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- Información de la escuela -->
        <div class="mb-3 row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="escuela_nombre">Nombre de la Escuela</label>
                    <input type="text" id="escuela_nombre" name="escuela_nombre" class="form-control" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="escuela_escolaridad">Escolaridad</label>
                    <select id="escuela_escolaridad" name="escuela_escolaridad" class="form-control" required>
                        <option value="" disabled selected>Seleccione una opción</option>
                        <option value="Kinder">Kinder</option>
                        <option value="Primaria">Primaria</option>
                        <option value="Secundaria">Secundaria</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Buscar ubicación en el mapa -->
        <div class="mb-3 form-group">
            <label for="map_search">Buscar en el mapa</label>
            <input type="text" id="map_search" class="form-control" placeholder="Buscar ubicación"
                oninput="mostrarSugerencias(this.value)">
            <ul id="suggestions" style="list-style-type: none; padding: 0; margin-top: 10px;"></ul>
        </div>

        <!-- Mapa y campos de latitud/longitud -->
        <div class="row">
            <!-- Mapa a la izquierda -->
            <div class="col-md-6">
                <div id="map" style="height: 400px; margin-top: 20px;"></div>
            </div>

            <!-- Latitud, Longitud y colores a la derecha -->
            <div class="col-md-6">
                <!-- Latitud -->
                <div class="form-group">
                    <label for="escuela_latitud">Latitud</label>
                    <input type="text" id="escuela_latitud" name="escuela_latitud" class="form-control" readonly
                        required>
                </div>
                <!-- Longitud -->
                <div class="mt-3 form-group">
                    <label for="escuela_longitud">Longitud</label>
                    <input type="text" id="escuela_longitud" name="escuela_longitud" class="form-control" readonly
                        required>
                </div>

                <!-- Colores personalizados -->
                <div class="mt-3 row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="ui_color1">Color 1</label>
                            <input type="color" id="ui_color1" name="ui_color1" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="ui_color2">Color 2</label>
                            <input type="color" id="ui_color2" name="ui_color2" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="ui_color3">Color 3</label>
                            <input type="color" id="ui_color3" name="ui_color3" class="form-control" required>
                        </div>
                    </div>
                </div>

                <!-- Botón para guardar colores -->
                <button type="button" class="mt-3 btn btn-primary" onclick="guardarColores()">Guardar Colores</button>

                <!-- Vista previa del gradiente -->
                <div class="color-preview" id="colorPreview" style="height: 50px; margin-top: 20px;"></div>
            </div>
        </div>

        <!-- Logotipo -->
        <div class="mt-3 form-group">
            <label for="escuela_logo">Logotipo</label>
            <input type="file" id="escuela_logo" name="escuela_logo" class="form-control" accept="image/*">
        </div>

        <button type="submit" class="mt-3 btn btn-primary">Guardar Escuela</button>
    </form>

    <script>
        var map = L.map('map').setView([19.432608, -99.133209], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        var marker = L.marker([19.432608, -99.133209], {
            draggable: true
        }).addTo(map);

        marker.on('dragend', function() {
            var latLng = marker.getLatLng();
            document.getElementById('escuela_latitud').value = latLng.lat;
            document.getElementById('escuela_longitud').value = latLng.lng;
        });

        let timeout;

        function mostrarSugerencias(query) {
            if (timeout) clearTimeout(timeout);

            if (!query.trim()) {
                document.getElementById('suggestions').style.display = 'none';
                return;
            }

            timeout = setTimeout(function() {
                fetch(
                        `https://nominatim.openstreetmap.org/search?format=json&q=${query}&addressdetails=1&limit=5&accept-language=es`
                    )
                    .then(response => response.json())
                    .then(data => {
                        const suggestionsList = document.getElementById('suggestions');
                        suggestionsList.innerHTML = '';

                        if (data.length > 0) {
                            data.forEach(location => {
                                const listItem = document.createElement('li');
                                listItem.textContent = location.display_name;

                                listItem.onclick = () => {
                                    const lat = location.lat;
                                    const lon = location.lon;
                                    map.setView([lat, lon], 13);
                                    marker.setLatLng([lat, lon]);

                                    document.getElementById('escuela_latitud').value = lat;
                                    document.getElementById('escuela_longitud').value = lon;
                                    document.getElementById('map_search').value = location
                                        .display_name;

                                    suggestionsList.style.display = 'none';
                                };

                                suggestionsList.appendChild(listItem);
                            });
                            suggestionsList.style.display = 'block';
                        } else {
                            suggestionsList.style.display = 'none';
                        }
                    });
            }, 500);
        }

        function guardarColores() {
            const color1 = document.getElementById('ui_color1').value;
            const color2 = document.getElementById('ui_color2').value;
            const color3 = document.getElementById('ui_color3').value;

            const colorPreview = document.getElementById('colorPreview');
            colorPreview.style.background = `linear-gradient(90deg, ${color1}, ${color2}, ${color3})`;
        }
    </script>
@endsection
