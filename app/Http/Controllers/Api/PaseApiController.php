<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pase;

/**
 * Archivo: PaseApiController.php
 * Propósito: Controlador para gestionar datos relacionados con pases.
 * Autor: José Balam González Rojas
 * Fecha de Creación: 2024-11-19
 * Última Modificación: 2024-11-27
 */

class PaseApiController extends Controller
{
    /**
     * Mostrar todos los pases de un alumno.
     */
    public function index($alumnoId)
    {
        $pases = Pase::where('cesi_alumno_id', $alumnoId)->get();

        if ($pases->isEmpty()) {
            return response()->json(['message' => 'No hay pases'], 200);
        }

        return response()->json(['data' => $pases], 200);
    }

    /**
     * Crear un nuevo pase para un alumno.
     */
    public function create(Request $request, $alumnoId)
    {
        $validated = $request->validate([
            'pase_estatus' => 'required|string|max:50',
            'cesi_asistencia_id' => 'required|integer|exists:cesi_asistencias,id',
        ]);

        $validated['cesi_alumno_id'] = $alumnoId;

        $pase = Pase::create($validated);

        return response()->json(['message' => 'Pase creado correctamente', 'data' => $pase], 201);
    }

    /**
     * Mostrar un pase específico de un alumno.
     */
    public function show($alumnoId, $id)
    {
        $pase = Pase::find($id);

        if (!$pase) {
            return response()->json(['error' => 'Pase no encontrado'], 404);
        }

        if ($pase->cesi_alumno_id !== $alumnoId) {
            return response()->json(['error' => 'No autorizado para ver este pase'], 403);
        }

        return response()->json(['data' => $pase], 200);
    }

    /**
     * Actualizar un pase de un alumno.
     */
    public function update(Request $request, $alumnoId, $id)
    {
        $pase = Pase::find($id);

        if (!$pase) {
            return response()->json(['error' => 'Pase no encontrado'], 404);
        }

        if ($pase->cesi_alumno_id !== $alumnoId) {
            return response()->json(['error' => 'No autorizado para actualizar este pase'], 403);
        }

        $validated = $request->validate([
            'pase_estatus' => 'nullable|string|max:50',
            'cesi_asistencia_id' => 'nullable|integer|exists:cesi_asistencias,id',
        ]);

        $pase->update($validated);

        return response()->json(['message' => 'Pase actualizado correctamente', 'data' => $pase], 200);
    }

    /**
     * Eliminar un pase de un alumno.
     */
    public function destroy($alumnoId, $id)
    {
        $pase = Pase::find($id);

        if (!$pase) {
            return response()->json(['error' => 'Pase no encontrado'], 404);
        }

        if ($pase->cesi_alumno_id !== $alumnoId) {
            return response()->json(['error' => 'No autorizado para eliminar este pase'], 403);
        }

        $pase->delete();

        return response()->json(['message' => 'Pase eliminado correctamente'], 200);
    }
}
