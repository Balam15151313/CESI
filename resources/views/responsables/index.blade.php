@extends('layouts.app')

@section('title', 'Responsables')

@section('content')
    <h1 class="mb-4 text-center">Responsables</h1>

    <!-- Responsables Activos -->
    <div class="mb-4 table-responsive">
        <h3>Responsables Activos</h3>
        <table class="table table-striped table-hover" id="tabla-responsables-activos">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Teléfono</th>
                    <th>Email</th>
                    <th>Foto</th>
                    <th>Tutor</th>
                    <th>Acciones</th>
                </tr>
                <tr>
                    <th><input type="text" placeholder="Buscar Nombre" class="form-control form-control-sm"></th>
                    <th><input type="text" placeholder="Buscar Teléfono" class="form-control form-control-sm"></th>
                    <th><input type="text" placeholder="Buscar Email" class="form-control form-control-sm"></th>
                    <th></th>
                    <th><input type="text" placeholder="Buscar Tutor" class="form-control form-control-sm"></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($responsablesActivos as $responsable)
                    <tr>
                        <td>{{ $responsable->responsable_nombre }}</td>
                        <td>{{ $responsable->responsable_telefono }}</td>
                        <td>{{ $responsable->responsable_usuario }}</td>
                        <td>
                            @if ($responsable->responsable_foto)
                                <img src="{{ asset('storage/' . $responsable->responsable_foto) }}"
                                    alt="Foto del Responsable" width="50">
                            @else
                                Sin foto
                            @endif
                        </td>
                        <td>{{ $responsable->tutores->tutor_nombre }}</td>
                        <td>
                            <a href="{{ route('responsables.edit', $responsable->id) }}"
                                class="btn btn-primary btn-sm">Editar</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Responsables Inactivos -->
    <div class="table-responsive">
        <h3>Responsables Inactivos</h3>
        <table class="table table-striped table-hover" id="tabla-responsables-inactivos">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Teléfono</th>
                    <th>Email</th>
                    <th>Foto</th>
                    <th>Tutor</th>
                    <th>Acciones</th>
                </tr>
                <tr>
                    <th><input type="text" placeholder="Buscar Nombre" class="form-control form-control-sm"></th>
                    <th><input type="text" placeholder="Buscar Teléfono" class="form-control form-control-sm"></th>
                    <th><input type="text" placeholder="Buscar Email" class="form-control form-control-sm"></th>
                    <th></th>
                    <th><input type="text" placeholder="Buscar Tutor" class="form-control form-control-sm"></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($responsablesInactivos as $responsable)
                    <tr>
                        <td>{{ $responsable->responsable_nombre }}</td>
                        <td>{{ $responsable->responsable_telefono }}</td>
                        <td>{{ $responsable->responsable_usuario }}</td>
                        <td>
                            @if ($responsable->responsable_foto)
                                <img src="{{ asset('storage/' . $responsable->responsable_foto) }}"
                                    alt="Foto del Responsable" width="50">
                            @else
                                Sin foto
                            @endif
                        </td>
                        <td>{{ $responsable->tutores->tutor_nombre }}</td>
                        <td>
                            <a href="{{ route('responsables.activate', $responsable->id) }}"
                                class="btn btn-primary btn-sm">Activar</a>
                            <form action="{{ route('responsables.delete', $responsable->id) }}" method="POST"
                                style="display: inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-secondary btn-sm"
                                    onclick="return confirm('¿Estás seguro de que deseas eliminar este responsable?')">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <script>
        $(document).ready(function() {
            var tableActivos = $('#tabla-responsables-activos').DataTable({
                "pagingType": "full_numbers",
                "language": {
                    "url": "https://cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json"
                }
            });
            var tableInactivos = $('#tabla-responsables-inactivos').DataTable({
                "pagingType": "full_numbers",
                "language": {
                    "url": "https://cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json"
                }
            });

            $('#tabla-responsables-activos thead tr:eq(1) th').each(function(i) {
                $('input', this).on('keyup change', function() {
                    if (tableActivos.column(i).search() !== this.value) {
                        tableActivos
                            .column(i)
                            .search(this.value)
                            .draw();
                    }
                });
            });

            $('#tabla-responsables-inactivos thead tr:eq(1) th').each(function(i) {
                $('input', this).on('keyup change', function() {
                    if (tableInactivos.column(i).search() !== this.value) {
                        tableInactivos
                            .column(i)
                            .search(this.value)
                            .draw();
                    }
                });
            });
        });
    </script>
@endsection
