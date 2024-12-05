<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Administrador</title>
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
            width: 80%;
            height: 90%;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            border-radius: 10px;
            overflow: hidden;
        }

        .image-section {
            width: 50%;
            background: url('{{ asset('imagenes/empresa2.png') }}') no-repeat center center;
            background-size: cover;
        }

        .form-section {
            width: 50%;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: center;
        }

        .form-header {
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .form-header img {
            max-width: 110px;
            max-height: 70px;
        }

        .form-header h2 {
            flex: 1;
            text-align: center;
            color: #D96828;
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

        .btn-primary {
            border: none;
            padding: 15px;
            width: 100%;
            cursor: pointer;
            font-size: 16px;
            border-radius: 30px;
            margin-top: 10px;
            font-weight: bold;
            background-color: #D96828;
            color: white;
        }

        .btn-primary:hover {
            background-color: #D97730;
        }

        /* Modal Styles */
        .modal {
            display: none;
            /* Oculto por defecto */
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
            background-color: #fff;
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
            color: #fff;
            cursor: pointer;
        }

        .modal-footer button:hover {
            background-color: #D97730;
        }
    </style>
</head>

<body>

    <div class="container">
        <!-- Imagen a la izquierda -->
        <div class="image-section"></div>

        <!-- Formulario a la derecha -->
        <div class="form-section">
            <!-- Encabezado con logos y título centrado -->
            <div class="form-header">
                <img src="{{ asset('imagenes/empresa.png') }}" alt="Logo Empresa">
                <h2>Registro de Administrador</h2>
                <img src="{{ asset('imagenes/gato.png') }}" alt="Logo Gato">
            </div>

            <form method="POST" action="{{ route('register') }}">
                @csrf
                <div class="form-group">
                    <label for="name">Nombre</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required>
                </div>
                <div class="form-group">
                    <label for="email">Correo Electrónico</label>
                    <input type="text" id="email" name="email" value="{{ old('email') }}" required>
                </div>
                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" required>
                    <small>Debe incluir al menos una mayúscula, una minúscula, un número y un símbolo @$!%*?& </small>
                </div>
                <div class="form-group">
                    <label for="password_confirmation">Confirmar Contraseña</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required>
                </div>
                <div class="form-group">
                    <label for="access_code">Código de Acceso</label>
                    <input type="text" id="access_code" name="access_code" required>
                </div>
                <button type="submit" class="btn-primary">Registrarse</button>
            </form>
        </div>
    </div>

    <!-- Modal para mostrar errores -->
    <div id="errorModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">Errores en el formulario</div>
            <div class="modal-body">
                <ul id="errorList">
                    <!-- Aquí se inyectan los errores -->
                </ul>
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

            // Limpiar los errores en la sesión usando JavaScript
            window.history.replaceState({}, document.title, window.location.pathname);
        @endif

        // Función para cerrar el modal
        function closeModal() {
            document.getElementById('errorModal').style.display = 'none';
        }
    </script>


</body>

</html>
