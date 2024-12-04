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

        .sidebar img {
            width: 100px;
            margin-bottom: 10px;
            border-radius: 50%;
        }

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

        #imagenPrevisualizacion {
            width: 150px;
            height: 150px;
            object-fit: cover;
            display: none;
            margin: 10px auto;
            border: 1px solid #dee2e6;
            border-radius: 5px;
        }

        /* Estilos Generales*/
        body {
            font-family: Arial, sans-serif;
            background-color: #F2F2F2;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            width: 100vw;
            overflow: hidden;
            box-sizing: border-box;
        }

        .container {
            display: flex;
            width: 70%;
            height: 80%;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            border-radius: 10px;
            overflow: hidden;
            flex-direction: row;
            justify-content: space-between;
            margin: auto;
        }

        .login-section {
            width: 40%;
            max-width: 400px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            justify-content: center;
            margin: 0;
        }

        .form-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
        }

        .form-wrapper {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            width: 100%;
        }

        .form-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
        }

        .logos {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            margin-bottom: 20px;
            margin-top: -30px;
        }

        .logos img {
            max-width: 100px;
            max-height: 60px;
            margin: 0 20px;
        }

        h2 {
            color: #FFFFFFFF;
            margin-bottom: 20px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            color: #000000;
        }

        .form-group input {
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 30px;
            background-color: #F2F2F2;
            font-size: 16px;
            color: #733917;
        }

        .button-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
            gap: 10px;
        }



        .image-section {
            width: 60%;
            background: url('/imagenes/escuelasimagen.png') no-repeat center center;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .register-image-section {
            width: 50%;
            background: url('/imagenes/empresa2.png') no-repeat center center;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .register-form-section {
            width: 50%;
            max-width: 500px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-start;
            margin: 0;
        }

        .register-form-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
        }

        .register-form-wrapper {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            width: 100%;
        }

        .register-form-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
        }

        .register-form-group {
            margin-bottom: 10px;
        }

        .register-form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            color: #000000;
            font-size: 13px;
        }

        .register-form-group input {
            width: 95%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 30px;
            background-color: #F2F2F2;
            font-size: 13px;
            color: #733917;
        }

        .register-button {
            border: none;
            padding: 8px;
            width: auto;
            cursor: pointer;
            font-size: 13px;
            border-radius: 30px;
            font-weight: bold;
            text-align: center;
            display: block;
            text-decoration: none;
            background-color: #D96828;
            color: white;
        }

        .register-button:hover {
            background-color: #D97730;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            width: 90%;
            max-width: 400px;
        }

        .modal-header {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .modal-body {
            font-size: 16px;
            color: #333;
        }

        .modal-footer {
            text-align: right;
        }

        .modal-footer button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            background-color: #D96828;
            color: white;
            cursor: pointer;
        }

        .modal-footer button:hover {
            background-color: #D97730;
        }

        @media (max-width: 768px) {
            body {
                flex-direction: column;
            }

            .container {
                width: 100vw;
                height: 100vh;
                flex-direction: column;
                justify-content: flex-start;
                align-items: center;
                box-shadow: none;
                padding: 10px;
                margin: 0;
            }

            .login-section {
                width: 100%;
                height: 400px;
                padding: 20px;
            }

            .logos img {
                max-width: 80px;
                max-height: 50px;
                margin: 0 10px;
            }

            h2 {
                font-size: 1.5rem;
            }

            .form-group input {
                padding: 12px;
                font-size: 14px;
            }


            .image-section {
                width: 100%;
                height: 200px;
                background-size: cover;
            }

            .register-form-section {
                width: 80%;
                max-width: 400px;
                padding: 30px;
            }

            .register-form-group input {
                padding: 10px;
                font-size: 8px;
            }

            .register-button {
                font-size: 10px;
                padding: 8px;
            }

            .register-image-section {
                width: 100%;
                height: 200px;
                background-size: cover;
            }
        }

        @media (max-width: 480px) {
            .logos img {
                max-width: 60px;
                max-height: 40px;
                margin: 0 5px;
            }

            h2 {
                font-size: 1.25rem;
            }

            .form-group input {
                padding: 10px;
                font-size: 12px;
            }

            .modal-content {
                width: 100%;
            }

            /* Registro */
            .register-form-group {
                margin-bottom: 10px;
            }

            .register-form-group label {
                font-size: 12px;
            }

            .register-form-group input {
                padding: 8px;
                font-size: 12px;
            }

            .register-button {
                padding: 8px;
                font-size: 12px;
            }
        }

        .foto-show {
            width: 100%;
            max-width: 150px;
            height: auto;
            object-fit: cover;
            display: block;
            margin-left: auto;
            margin-right: auto;
            border: 1px solid #dee2e6;
            border-radius: 5px;
        }

        .custom-shadow {
            box-shadow:
                0 4px 8px rgba(0, 0, 0, 0.0),
                0 -4px 8px rgba(0, 0, 0, 0.0),
                4px 0 8px rgba(0, 0, 0, 0.0),
                -4px 0 8px rgba(0, 0, 0, 0.0);
        }

        /* Sombreado a los contenedores internos */
        .custom-shadow-internal {
            box-shadow:
                0 4px 8px rgba(216, 95, 95, 0.1),
                0 -4px 8px rgba(0, 0, 0, 0.1),
                4px 0 8px rgba(0, 0, 0, 0.1),
                -4px 0 8px rgba(0, 0, 0, 0.1);
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
