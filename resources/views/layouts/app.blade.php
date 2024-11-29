<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard')</title>

    <link rel="stylesheet" href="{{ asset('css/reset.css') }}">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">

    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>
    <style>
        /* Estilos de layout app */
        .dashboard {
            display: flex;
            height: 100vh;
            width: 100vw;
        }

        .sidebar {
            width: 250px;
            color: white;
            padding-top: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .sidebar a {
            color: white;
            padding: 15px;
            text-decoration: none;
            display: block;
            font-size: 18px;
            text-align: center;
            width: 100%;
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
            overflow-y: auto;
        }

        .content {
            padding: 20px;
        }

        .table-responsive {
            overflow-x: auto;
        }

        .table {
            width: 100%;
            max-width: 100%;
            margin-bottom: 1rem;
            color: #212529;
        }

        .table th,
        .table td {
            padding: 0.75rem;
            vertical-align: top;
            border-top: 1px solid #dee2e6;
        }

        /* Fotos para show */
        .foto-show {
            max-width: 150px;
            max-height: 150px;
            border: 1px solid #dee2e6;
            border-radius: 5px;
        }

        #suggestions {
            list-style-type: none;
            padding: 0;
            margin-top: 5px;
            max-height: 150px;
            overflow-y: auto;
            border: 1px solid #ddd;
            background: white;
            display: none;
            position: absolute;
            z-index: 1000;
        }

        #suggestions li {
            padding: 5px;
            cursor: pointer;
        }

        #suggestions li:hover {
            background-color: #f0f0f0;
        }
    </style>
</head>

<body>
    <div class="dashboard">
        <!-- Menú lateral con el logo y los colores personalizados -->
        <div class="sidebar"
            style="background: linear-gradient(to bottom, {{ $ui->ui_color1 ?? '#333' }}, {{ $ui->ui_color2 ?? '#555' }}, {{ $ui->ui_color3 ?? '#777' }});">
            <h2>Menú</h2>
            <!-- Vista previa del logo personalizado de la escuela -->
            <img src="{{ asset('storage/' . ($escuela->escuela_logo ?? 'imagenes/default_logo.png')) }}"
                alt="Logo de la Escuela">
            <a href="{{ route('dashboard') }}">Menú Principal</a>
            @yield('crear_nuevo')
        </div>

        <!-- Contenido principal -->
        <div class="main-content">
            <div class="content">
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                @if (session('info'))
                    <div class="alert alert-info">
                        {{ session('info') }}
                    </div>
                @endif

                @yield('content')
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous">
    </script>
</body>

</html>
