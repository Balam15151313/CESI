@extends('layouts.app')

@section('title', 'Tutores')

@section('crear_nuevo')
    <a href="{{ route('tutores.create') }}">Crear Nuevo Tutor</a>
@endsection

@section('content')
    <h1 class="mb-4 text-center">Tutores</h1>

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
                <tr>
                    <th><input type="text" placeholder="Buscar Nombre" class="form-control form-control-sm"></th>
                    <th><input type="text" placeholder="Buscar Correo" class="form-control form-control-sm"></th>
                    <th><input type="text" placeholder="Buscar Teléfono" class="form-control form-control-sm"></th>
                    <th></th>
                    <th></th>
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
                            <form action="{{ route('tutores.destroy', $tutor->id) }}" method="POST"
                                style="display: inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-primary btn-sm"
                                    onclick="return confirm('¿Estás seguro de que deseas eliminar este tutor?')">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <script>
        $(document).ready(function() {
            // Inicialización de DataTable para la tabla de tutores
            var table = $('#tabla-tutores').DataTable({
                "pagingType": "full_numbers",
                "language": {
                    "url": "https://cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json"
                }
            });

            // Configura la búsqueda por columna para cada input de búsqueda
            $('#tabla-tutores thead tr:eq(1) th').each(function(i) {
                $('input', this).on('keyup change', function() {
                    // Asegura que el valor de búsqueda se aplique solo a la columna correspondiente
                    if (table.column(i).search() !== this.value) {
                        table
                            .column(i)
                            .search(this.value)
                            .draw();
                    }
                });
            });
        });
    </script>

@endsection
