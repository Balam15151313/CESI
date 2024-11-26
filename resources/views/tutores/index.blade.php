@extends('layouts.app')

@section('title', 'Tutores')

@section('crear_nuevo')
    <a href="{{ route('tutores.create') }}">Crear Nuevo Tutor</a>
@endsection

@section('content')
    <h1 class="text-center mb-4">Tutores</h1>

    <div class="table-responsive">
        <table class="table table-striped table-hover" id="tabla-tutores">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Correo Electrónico</th>
                    <th>Teléfono</th>
                    <th>Foto</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($tutores as $tutor)
                    <tr>
                        <td>{{ $tutor->tutor_nombre }}</td>
                        <td>{{ $tutor->tutor_usuario }}</td>
                        <td>{{ $tutor->tutor_telefono }}</td>
                        <td>
                            @if ($tutor->tutor_foto)
                                <img src="{{ asset('storage/' . $tutor->tutor_foto) }}" alt="Foto del Tutor" width="50">
                            @else
                                Sin foto
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('tutores.edit', $tutor->id) }}" class="btn btn-primary btn-sm">Editar</a>
                            <a href="{{ route('tutores.show', $tutor->id) }}" class="btn btn-secondary btn-sm">Ver</a>
                            <form action="{{ route('tutores.destroy', $tutor->id) }}" method="POST" style="display: inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-primary btn-sm" onclick="return confirm('¿Estás seguro de que deseas eliminar este tutor?')">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <script>
        $(document).ready(function () {
            $('#tabla-tutores').DataTable({
                "pagingType": "full_numbers",
                "language": {
                    "url": "https://cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json"
                }
            });
        });
    </script>
@endsection