<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pase;

class PaseApiController extends Controller
{
    /**
     * Mostrar todos los pases de un alumno dado.
     */
    public function index($alumnoId)
    {
        // Obtener los pases asociados al alumno dado
        $pases = Pase::where('cesi_alumno_id', $alumnoId)->get();

        if ($pases->isEmpty()) {
            return response()->json(['message' => 'No hay pases'], 200);
        }

        return response()->json(['data' => $pases], 200);
    }

    /**
     * Crear un nuevo pase para un alumno dado.
     */
    public function create(Request $request, $alumnoId)
    {
        // Validación de los datos del pase
        $validated = $request->validate([
            'pase_estatus' => 'required|string|max:50',
            'cesi_asistencia_id' => 'required|integer|exists:cesi_asistencias,id', // Asegúrate de que la ID de asistencia exista
        ]);

        // Asignar el alumno_id recibido
        $validated['cesi_alumno_id'] = $alumnoId;

        // Crear el pase
        $pase = Pase::create($validated);

        return response()->json(['message' => 'Pase creado correctamente', 'data' => $pase], 201);
    }

    /**
     * Mostrar un pase específico de un alumno dado.
     */
    public function show($alumnoId, $id)
    {
        // Buscar el pase por ID
        $pase = Pase::find($id);

        if (!$pase) {
            return response()->json(['error' => 'Pase no encontrado'], 404);
        }

        // Verificar que el pase pertenezca al alumno dado
        if ($pase->cesi_alumno_id !== $alumnoId) {
            return response()->json(['error' => 'No autorizado para ver este pase'], 403);
        }

        return response()->json(['data' => $pase], 200);
    }

    /**
     * Actualizar un pase existente de un alumno dado.
     */
    public function update(Request $request, $alumnoId, $id)
    {
        // Buscar el pase por ID
        $pase = Pase::find($id);

        if (!$pase) {
            return response()->json(['error' => 'Pase no encontrado'], 404);
        }

        // Verificar que el pase pertenezca al alumno dado
        if ($pase->cesi_alumno_id !== $alumnoId) {
            return response()->json(['error' => 'No autorizado para actualizar este pase'], 403);
        }

        // Validación de los datos a actualizar
        $validated = $request->validate([
            'pase_estatus' => 'nullable|string|max:50',
            'cesi_asistencia_id' => 'nullable|integer|exists:cesi_asistencias,id',
        ]);

        // Actualizar el pase
        $pase->update($validated);

        return response()->json(['message' => 'Pase actualizado correctamente', 'data' => $pase], 200);
    }

    /**
     * Eliminar un pase de un alumno dado.
     */
    public function destroy($alumnoId, $id)
    {
        // Buscar el pase por ID
        $pase = Pase::find($id);

        if (!$pase) {
            return response()->json(['error' => 'Pase no encontrado'], 404);
        }

        // Verificar que el pase pertenezca al alumno dado
        if ($pase->cesi_alumno_id !== $alumnoId) {
            return response()->json(['error' => 'No autorizado para eliminar este pase'], 403);
        }

        // Eliminar el pase
        $pase->delete();

        return response()->json(['message' => 'Pase eliminado correctamente'], 200);
    }
}
