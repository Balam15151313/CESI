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

        .img {
            max-width: 50px;
            max-height: 50px;
            display: none;
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
                padding: 100px;
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

        /* Estilos para el botón deshabilitado */
        .btn-submit:disabled {
            cursor: not-allowed;
            opacity: 0.6;
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


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const formularios = document.querySelectorAll('form');

            formularios.forEach(function(formulario) {
                formulario.addEventListener('submit', function(event) {
                    const btnEnviar = formulario.querySelector('.btn-submit');

                    if (btnEnviar) {
                        btnEnviar.disabled = true;
                        const textoOriginal = btnEnviar.innerHTML;
                        btnEnviar.dataset.originalText = textoOriginal;
                        btnEnviar.innerHTML = 'Guardando';
                    }
                });
            });

            @if ($errors->any())
                const botones = document.querySelectorAll('.btn-submit');
                botones.forEach(function(btn) {
                    btn.disabled = false;
                    btn.innerHTML = btn.dataset.originalText || 'Guardar';
                });
            @endif
        });
    </script>
</body>

</html>
