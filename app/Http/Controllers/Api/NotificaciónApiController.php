<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notificacion;
/**
 * Archivo: NotificacionesApiController.php
 * Propósito: Controlador para gestionar datos relacionados con notificaciones.
 * Autor: José Balam González Rojas
 * Fecha de Creación: 2024-11-19
 * Última Modificación: 2024-11-26 - Añadida validación para evitar duplicados.
 */

class NotificacionApiController extends Controller
{
    /**
     * Mostrar todas las notificaciones asociadas al alumno dado.
     */
    public function index($alumnoId)
    {
        // Obtener las notificaciones del alumno dado
        $notificaciones = Notificacion::where('cesi_alumno_id', $alumnoId)->get();

        if ($notificaciones->isEmpty()) {
            return response()->json(['message' => 'No hay notificaciones'], 200);
        }

        return response()->json(['data' => $notificaciones], 200);
    }

    /**
     * Crear una nueva notificación para un alumno dado.
     */
    public function create(Request $request, $alumnoId)
    {
        // Validación de los datos de la notificación
        $validated = $request->validate([
            'notificaciones_mensaje' => 'required|string|max:255',
            'notificaciones_prioridad' => 'required|integer|min:1|max:5',
            'notificaciones_tipo' => 'required|string|max:50',
        ]);

        // Asignar el alumno_id recibido
        $validated['cesi_alumno_id'] = $alumnoId;

        // Crear la notificación
        $notificacion = Notificacion::create($validated);

        return response()->json(['message' => 'Notificación creada correctamente', 'data' => $notificacion], 201);
    }

    /**
     * Mostrar una notificación específica de un alumno dado.
     */
    public function show($alumnoId, $id)
    {
        // Buscar la notificación por ID
        $notificacion = Notificacion::find($id);

        if (!$notificacion) {
            return response()->json(['error' => 'Notificación no encontrada'], 404);
        }

        // Verificar que la notificación pertenezca al alumno dado
        if ($notificacion->cesi_alumno_id !== $alumnoId) {
            return response()->json(['error' => 'No autorizado para ver esta notificación'], 403);
        }

        return response()->json(['data' => $notificacion], 200);
    }

    /**
     * Actualizar una notificación existente de un alumno dado.
     */
    public function update(Request $request, $alumnoId, $id)
    {
        // Buscar la notificación por ID
        $notificacion = Notificacion::find($id);

        if (!$notificacion) {
            return response()->json(['error' => 'Notificación no encontrada'], 404);
        }

        // Verificar que la notificación pertenezca al alumno dado
        if ($notificacion->cesi_alumno_id !== $alumnoId) {
            return response()->json(['error' => 'No autorizado para actualizar esta notificación'], 403);
        }

        // Validación de los datos a actualizar
        $validated = $request->validate([
            'notificaciones_mensaje' => 'nullable|string|max:255',
            'notificaciones_prioridad' => 'nullable|integer|min:1|max:5',
            'notificaciones_tipo' => 'nullable|string|max:50',
        ]);

        // Actualizar la notificación
        $notificacion->update($validated);

        return response()->json(['message' => 'Notificación actualizada correctamente', 'data' => $notificacion], 200);
    }

    /**
     * Eliminar una notificación de un alumno dado.
     */
    public function destroy($alumnoId, $id)
    {
        // Buscar la notificación por ID
        $notificacion = Notificacion::find($id);

        if (!$notificacion) {
            return response()->json(['error' => 'Notificación no encontrada'], 404);
        }

        // Verificar que la notificación pertenezca al alumno dado
        if ($notificacion->cesi_alumno_id !== $alumnoId) {
            return response()->json(['error' => 'No autorizado para eliminar esta notificación'], 403);
        }

        // Eliminar la notificación
        $notificacion->delete();

        return response()->json(['message' => 'Notificación eliminada correctamente'], 200);
    }
}
