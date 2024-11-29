<!DOCTYPE html>
<html lang="es">

<head>
    <!-- Meta y títulos -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú Principal</title>
    <!-- Estilos CSS -->
    <style>
        /* Estilos generales */
        body,
        html {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Contenedor principal */
        .container {
            display: flex;
            height: 100%;
            width: 100%;
        }

        /* Barra de navegación superior */
        .topbar {
            background: linear-gradient(180deg, {{ $escuela->uis->first()?->ui_color1 ?? '#333' }}, {{ $escuela->uis->first()?->ui_color2 ?? '#555' }});
            color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            position: relative;
        }

        /* Estilos para la imagen y nombre de usuario */
        .topbar .user-info {
            display: flex;
            align-items: center;
        }

        .topbar .user-info img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
            object-fit: cover;
        }

        .topbar .user-info .username {
            margin-left: 0;
        }

        /* Estilos para notificaciones */
        .topbar .notification {
            margin-left: 20px;
            position: relative;
            cursor: pointer;
            padding: 5px 10px;
            background: linear-gradient(180deg, {{ $escuela->uis->first()?->ui_color1 ?? '#333' }}, {{ $escuela->uis->first()?->ui_color2 ?? '#555' }});
            border: none;
            color: white;
            border-radius: 5px;
            font-size: 16px;
        }

        /* Menú desplegable de notificaciones */
        .dropdown-content {
            display: none;
            position: absolute;
            top: 60px;
            right: 20px;
            background-color: white;
            color: black;
            min-width: 250px;
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
            padding: 15px;
            z-index: 1;
            border-radius: 5px;
        }

        .dropdown-content h4 {
            margin: 0;
            padding-bottom: 10px;
            border-bottom: 1px solid #ddd;
        }

        .dropdown-content ul {
            list-style-type: none;
            padding: 0;
            margin: 10px 0 0 0;
        }

        .dropdown-content ul li {
            padding: 8px 0;
        }

        .dropdown-content ul li a {
            color: #333;
            text-decoration: none;
        }

        .dropdown-content ul li a:hover {
            text-decoration: underline;
        }

        /* Mostrar el menú desplegable al hacer clic */
        .notification.active+.dropdown-content {
            display: block;
        }

        /* Estilos para el menú lateral */
        .sidebar {
            width: 250px;
            background: linear-gradient(180deg,
                    {{ $escuela->uis->first()?->ui_color1 ?? '#333' }},
                    {{ $escuela->uis->first()?->ui_color2 ?? '#555' }},
                    {{ $escuela->uis->first()?->ui_color3 ?? '#777' }});
            color: white;
            display: flex;
            flex-direction: column;
            padding-top: 20px;
            align-items: center;
            height: 100%;
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 24px;
        }

        .sidebar a {
            color: white;
            padding: 15px;
            text-decoration: none;
            display: block;
            font-size: 18px;
            transition: background 0.3s;
            width: 100%;
            text-align: center;
        }

        .sidebar a:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        /* Estilos para el logotipo */
        .sidebar img {
            width: 100px;
            margin-bottom: 10px;
            border-radius: 50%;
        }

        /* Estilos para el contenido principal */
        .main-content {
            flex: 1;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .main-content h2 {
            font-size: 32px;
            margin-bottom: 20px;
        }

        .option-card {
            width: 200px;
            text-align: center;
            margin: 15px;
            padding: 15px;
            border: 1px solid {{ $escuela->uis->first()?->ui_color3 ?? '#777' }};
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s;
        }

        .option-card:hover {
            transform: scale(1.05);
        }

        .option-card img {
            width: 100px;
            height: 100px;
            margin-bottom: 10px;
        }

        .options-container {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
        }

        /* Estilos para enlaces en tarjetas */
        .option-card a {
            color: inherit;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <!-- Barra superior con notificaciones y nombre de usuario -->
    <div class="topbar">
        <!-- Botón de notificaciones -->
        <button class="notification" id="notificationBtn">
            🔔 Notificaciones
            <span class="notification-count">{{ $responsablesInactivos->count() }}</span>
        </button>
        <!-- Menú desplegable de notificaciones -->
        <div class="dropdown-content" id="dropdownMenu">
            <h4>Solicitudes para activar responsables</h4>
            @if ($responsablesInactivos->isEmpty())
                <p>No hay solicitudes pendientes.</p>
            @else
                <ul>
                    @foreach ($responsablesInactivos as $responsable)
                        <li>
                            <a href="{{ route('responsables.index', ['nombre' => $responsable->responsable_nombre]) }}">
                                Solicitud para activar: {{ $responsable->responsable_nombre }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
        <!-- Información del usuario con foto y nombre -->
        <div class="user-info">
            <img src="{{ asset('storage/' . ($admin->administrador_foto ?? 'imagenes/default_logo.png')) }}"
                alt="Foto del Administrador">

            <span class="username">{{ $admin->administrador_nombre ?? 'Usuario' }}</span>
        </div>
    </div>

    <!-- Script para el manejo del menú desplegable -->
    <script>
        // Obtener el botón y el contenido del menú
        const notificationBtn = document.getElementById('notificationBtn');
        const dropdownMenu = document.getElementById('dropdownMenu');

        // Agregar evento para mostrar el menú al hacer clic
        notificationBtn.addEventListener('click', function() {
            // Alternar la clase "active" en el botón para mostrar/ocultar el menú
            notificationBtn.classList.toggle('active');
        });

        // Hacer que el menú se cierre si se hace clic fuera de él
        window.addEventListener('click', function(e) {
            if (!notificationBtn.contains(e.target) && !dropdownMenu.contains(e.target)) {
                notificationBtn.classList.remove('active');
            }
        });
    </script>

    <div class="container">
        <!-- Menú lateral -->
        <div class="sidebar">
            <img src="{{ asset('storage/' . ($escuela->escuela_logo ?? 'imagenes/default_logo.png')) }}"
                alt="Logo de la Escuela">
            <h2>Menú</h2>
            <a href="{{ route('dashboard') }}">Inicio</a>
            <a href="{{ route('escuelas.create') }}">Escuelas</a>
            <a href="{{ route('maestros.index') }}">Maestros</a>
            <a href="{{ route('salones.index') }}">Grupos</a>
            <a href="{{ route('alumnos.index') }}">Alumnos</a>
            <a href="{{ route('tutores.index') }}">Tutores</a>
            <a href="{{ route('responsables.index') }}">Responsables</a>
            <a href="#">Reportes</a>
            <a href="{{ route('admin.edit', $admin->id) }}">Configuraciones</a>
            <a href="{{ route('logout') }}"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Cerrar Sesión</a>

            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>

        <!-- Contenido principal -->
        <div class="main-content">
            <h2>Bienvenido al Panel de Control</h2>
            <div class="options-container">
                <!-- Tarjeta para "Escuelas" -->
                <div class="option-card">
                    <a href="{{ route('escuelas.create') }}">
                        <img src="{{ asset('imagenes/Escuela.png') }}" alt="Escuelas">
                        <h3>Escuelas</h3>
                        <p>Administrar información de las escuelas</p>
                    </a>
                </div>
                <!-- Tarjeta para "Maestros" -->
                <div class="option-card">
                    <a href="{{ route('maestros.index') }}">
                        <img src="{{ asset('imagenes/Maestros.png') }}" alt="Maestros">
                        <h3>Maestros</h3>
                        <p>Administrar información de los maestros</p>
                    </a>
                </div>
                <!-- Tarjeta para "Grupos" -->
                <div class="option-card">
                    <a href="{{ route('salones.index') }}">
                        <img src="{{ asset('imagenes/grupos.png') }}" alt="Grupos">
                        <h3>Grupos</h3>
                        <p>Administrar información de los grupos</p>
                    </a>
                </div>
                <!-- Tarjeta para "Tutores" -->
                <div class="option-card">
                    <a href="{{ route('tutores.index') }}">
                        <img src="{{ asset('imagenes/tutor.png') }}" alt="Tutores">
                        <h3>Tutores</h3>
                        <p>Administrar información de los tutores</p>
                    </a>
                </div>
                <!-- Tarjeta para "Alumnos" -->
                <div class="option-card">
                    <a href="{{ route('alumnos.index') }}">
                        <img src="{{ asset('imagenes/Alumno.png') }}" alt="Alumnos">
                        <h3>Alumnos</h3>
                        <p>Administrar información de los alumnos</p>
                    </a>
                </div>
                <!-- Tarjeta para "Responsables" -->
                <div class="option-card">
                    <a href="{{ route('responsables.index') }}">
                        <img src="{{ asset('imagenes/Responsable.png') }}" alt="Responsables">
                        <h3>Responsables</h3>
                        <p>Administrar información de los responsables</p>
                    </a>
                </div>
                <!-- Tarjeta para "Reportes" -->
                <div class="option-card">
                    <a href="#">
                        <img src="{{ asset('imagenes/Reportes.png') }}" alt="Reportes">
                        <h3>Reportes</h3>
                        <p>Generar y visualizar reportes</p>
                    </a>
                </div>
                <!-- Tarjeta para "Configuraciones" -->
                <div class="option-card">
                    <a href="{{ route('admin.edit', $admin->id) }}">
                        <img src="{{ asset('imagenes/configuraciones.png') }}" alt="Configuraciones">
                        <h3>Configuraciones</h3>
                        <p>Editar datos del administrador</p>
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
