@extends('layouts.app')

@section('title', 'Salones')

@section('crear_nuevo')
    <a href="{{ route('salones.create') }}">Crear Nuevo Salón</a>
@endsection

@section('content')
    <h1 class="mb-4 text-center">Salones</h1>

    <div class="table-responsive">
        <table class="table table-striped table-hover" id="tabla-salones">
            <thead>
                <tr>
                    <th>Grado</th>
                    <th>Grupo</th>
                    <th>Escuela</th>
                    <th>Maestro</th>
                    <th>Acciones</th>
                </tr>
                <tr>
                    <th><input type="text" placeholder="Buscar Grado" class="form-control form-control-sm"></th>
                    <th><input type="text" placeholder="Buscar Grupo" class="form-control form-control-sm"></th>
                    <th><input type="text" placeholder="Buscar Escuela" class="form-control form-control-sm"></th>
                    <th><input type="text" placeholder="Buscar Maestro" class="form-control form-control-sm"></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($salones as $salon)
                    <tr>
                        <td>{{ $salon->salon_grado }}</td>
                        <td>{{ $salon->salon_grupo }}</td>
                        <td>{{ $salon->escuelas->escuela_nombre }}</td>
                        <td>{{ $salon->maestros->maestro_nombre }}</td>
                        <td>
                            <a href="{{ route('salones.edit', $salon->id) }}" class="btn btn-primary btn-sm">Editar</a>
                            <a href="{{ route('salones.show', $salon->id) }}" class="btn btn-secondary btn-sm">Ver</a>
                            <form action="{{ route('salones.destroy', $salon->id) }}" method="POST"
                                style="display: inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-primary btn-sm"
                                    onclick="return confirm('¿Estás seguro de que deseas eliminar este salón?')">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <script>
        $(document).ready(function() {
            // Usamos el ID correcto para la tabla
            var table = $('#tabla-salones').DataTable({
                "pagingType": "full_numbers",
                "language": {
                    "url": "https://cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json"
                }
            });

            // Configura la búsqueda por columna para cada input de búsqueda
            $('#tabla-salones thead tr:eq(1) th').each(function(i) {
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
