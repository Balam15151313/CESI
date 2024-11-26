@extends('layouts.app')

@section('title', 'Lista de Escuelas')

@section('content')
    <h2>Lista de Escuelas</h2>

    <!-- Mensajes de éxito o error -->
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

    <!-- Tabla de escuelas -->
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Escolaridad</th>
                    <th>Dirección</th>
                    <th>Logo</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($escuelas as $escuela)
                    <tr>
                        <td>{{ $escuela->escuela_nombre }}</td>
                        <td>{{ $escuela->escuela_escolaridad }}</td>
                        <td id="direccion-{{ $escuela->id }}">Cargando...</td>
                        <td>
                            @if ($escuela->escuela_logo)
                                <img src="{{ asset('storage/' . $escuela->escuela_logo) }}"
                                    alt="Logo de {{ $escuela->escuela_nombre }}" class="logo-thumbnail" height="50px" width="50px">
                            @else
                                No disponible
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('escuelas.edit', $escuela->id) }}" class="btn btn-primary">Editar</a>
                            <form action="{{ route('escuelas.destroy', $escuela->id) }}" method="POST"
                                style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-secondary"
                                    onclick="return confirm('¿Estás seguro de eliminar esta escuela?')">Eliminar</button>
                            </form>
                        </td>
                    </tr>

                    <!-- Script para obtener dirección -->
                    <script>
                        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat={{ $escuela->escuela_latitud }}&lon={{ $escuela->escuela_longitud }}&addressdetails=1`)
                            .then(response => response.json())
                            .then(data => {
                                const direccion = data.display_name || 'Dirección no disponible';
                                document.getElementById('direccion-{{ $escuela->id }}').innerText = direccion;
                            })
                            .catch(error => {
                                console.error('Error al obtener la dirección:', error);
                                document.getElementById('direccion-{{ $escuela->id }}').innerText = 'Dirección no disponible';
                            });
                    </script>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection

@push('styles')
    <style>
        .alert {
            margin-bottom: 20px;
            padding: 10px;
            border-radius: 5px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
        }

        .table-container {
            margin-top: 30px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .table th,
        .table td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }

        .table th {
            background-color: #f8f9fa;
        }

        .btn {
            padding: 6px 12px;
            margin-right: 10px;
            border-radius: 5px;
            font-size: 14px;
            text-decoration: none;
        }

        .btn-primary {
            background-color: #007bff;
            color: white;
        }

        .btn-danger {
            background-color: #6c757d;
            color: white;
        }

        .logo-thumbnail {
            width: 50px;
            height: 50px;
            object-fit: cover;
        }
    </style>
@endpush
