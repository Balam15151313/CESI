@extends('layouts.app')

@section('title', 'Maestros')

@section('crear_nuevo')
    <a href="{{ route('maestros.create') }}">Crear Nuevo maestro</a>
@endsection

@section('content')
    <h1 class="mb-4 text-center">Maestros</h1>

    <div class="table-responsive">
        <table class="table table-striped table-hover" id="tabla-maestros">
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
                @foreach ($maestros as $maestro)
                    <tr>
                        <td>{{ $maestro->maestro_nombre }}</td>
                        <td>{{ $maestro->maestro_usuario }}</td>
                        <td>{{ $maestro->maestro_telefono }}</td>
                        <td>
                            @if ($maestro->maestro_foto)
                                <img src="{{ asset('storage/' . $maestro->maestro_foto) }}" alt="Foto del maestro"
                                    width="50">
                            @else
                                Sin foto
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('maestros.edit', $maestro->id) }}" class="btn btn-primary btn-sm">Editar</a>
                            <a href="{{ route('maestros.show', $maestro->id) }}" class="btn btn-secondary btn-sm">Ver</a>
                            <form action="{{ route('maestros.destroy', $maestro->id) }}" method="POST"
                                style="display: inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-primary btn-sm"
                                    onclick="return confirm('¿Estás seguro de que deseas eliminar este maestro?')">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <script>
        $(document).ready(function() {
            var table = $('#tabla-maestros').DataTable({
                "pagingType": "full_numbers",
                "language": {
                    "url": "https://cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json"
                }
            });

            // Configura la búsqueda por columna
            $('#tabla-maestros thead tr:eq(1) th').each(function(i) {
                $('input', this).on('keyup change', function() {
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
