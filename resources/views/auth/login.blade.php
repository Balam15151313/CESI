<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #F2F2F2;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            display: flex;
            width: 70%;
            height: 80%;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            border-radius: 10px;
            overflow: hidden;
        }

        .login-section {
            width: 40%;
            padding: 30px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
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
            margin: 0 40px;
        }

        h2 {
            color: #D96828;
            margin-bottom: 20px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 15px;
            width: 100%;
        }

        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            color: #000000;
        }

        .form-group input {
            width: 100%;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 30px;
            background-color: #F2F2F2;
            font-size: 14px;
            color: #733917;
        }

        .btn-primary,
        .btn-secondary {
            border: none;
            padding: 15px;
            width: 100%;
            cursor: pointer;
            font-size: 16px;
            border-radius: 30px;
            margin-bottom: 10px;
            font-weight: bold;
        }

        .btn-primary {
            background-color: #D96828;
            color: white;
        }

        .btn-primary:hover {
            background-color: #D97730;
        }

        .btn-secondary {
            background-color: #F27F2F;
            color: white;
        }

        .btn-secondary:hover {
            background-color: #D96828;
        }

        .image-section {
            width: 60%;
            background: url('{{ asset('imagenes/escuelasimagen.png') }}') no-repeat center center;
            background-size: cover;
        }

        /* Modal styles */
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
    </style>
</head>

<body>

    <div class="container">
        <!-- Sección de Login -->
        <div class="login-section">
            <!-- Logos encima del formulario -->
            <div class="logos">
                <img src="{{ asset('imagenes/empresa.png') }}" alt="Logo Empresa">
                <img src="{{ asset('imagenes/gato.png') }}" alt="Logo Gato">
            </div>

            <h2>Inicio de Sesión</h2>

            <!-- Formulario de Inicio de Sesión -->
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="form-group">
                    <label for="email">Correo Electrónico</label>
                    <input type="text" id="email" name="email" value="{{ old('email') }}" required>
                </div>
                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="btn-primary">Iniciar Sesión</button>
            </form>

            <a href="{{ route('register') }}">
                <button class="btn-secondary">Registrarse</button>
            </a>
        </div>

        <!-- Sección de Imagen -->
        <div class="image-section"></div>
    </div>

    <!-- Modal para mostrar errores -->
    <div id="errorModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">Errores en el formulario</div>
            <div class="modal-body">
                <ul id="errorList"></ul>
            </div>
            <div class="modal-footer">
                <button onclick="closeModal()">Cerrar</button>
            </div>
        </div>
    </div>

    <script>
        // Mostrar modal si hay errores
        @if ($errors->any())
            const errorModal = document.getElementById('errorModal');
            const errorList = document.getElementById('errorList');
            const errors = @json($errors->all());
            errors.forEach(error => {
                const li = document.createElement('li');
                li.textContent = error;
                errorList.appendChild(li);
            });
            errorModal.style.display = 'flex';
        @endif

        // Función para cerrar el modal
        function closeModal() {
            document.getElementById('errorModal').style.display = 'none';
        }
    </script>

</body>

</html>
