@extends('layouts.app')

@section('title', 'Alumnos')

@section('crear_nuevo')
    <a href="{{ route('alumnos.create') }}">Crear Nuevo Alumno</a>
@endsection

@section('content')
    <h1 class="mb-4 text-center">Alumnos</h1>

    <div class="table-responsive">
        <table class="table table-striped table-hover" id="tabla-alumnos">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Fecha de Nacimiento</th>
                    <th>Salón</th>
                    <th>Tutor</th>
                    <th>Foto</th>
                    <th>Acciones</th>
                </tr>
                <tr>
                    <th>
                        <input type="text" placeholder="Buscar Nombre" class="buscador-columna">
                    </th>
                    <th>
                        <input type="text" placeholder="Buscar Fecha" class="buscador-columna">
                    </th>
                    <th>
                        <input type="text" placeholder="Buscar Salón" class="buscador-columna">
                    </th>
                    <th>
                        <input type="text" placeholder="Buscar Tutor" class="buscador-columna">
                    </th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($alumnos as $alumno)
                    <tr>
                        <td>{{ $alumno->alumno_nombre }}</td>
                        <td>{{ $alumno->alumno_nacimiento }}</td>
                        <td>{{ $alumno->salones->salon_grado . ' ' . $alumno->salones->salon_grupo }}</td>
                        <td>{{ $alumno->tutores->tutor_nombre }}</td>
                        <td>
                            @if ($alumno->alumno_foto)
                                <img src="{{ asset('storage/' . $alumno->alumno_foto) }}" alt="Foto del Alumno"
                                    width="50">
                            @else
                                Sin foto
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('alumnos.edit', $alumno->id) }}" class="btn btn-primary btn-sm">Editar</a>
                            <a href="{{ route('alumnos.show', $alumno->id) }}" class="btn btn-secondary btn-sm">Ver</a>
                            <form action="{{ route('alumnos.destroy', $alumno->id) }}" method="POST"
                                style="display: inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-primary btn-sm"
                                    onclick="return confirm('¿Estás seguro de que deseas eliminar este alumno?')">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <script>
        $(document).ready(function() {
            var table = $('#tabla-alumnos').DataTable({
                "pagingType": "full_numbers",
                "language": {
                    "url": "https://cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json"
                }
            });

            $('#tabla-alumnos thead tr:eq(1) th').each(function(i) {
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
