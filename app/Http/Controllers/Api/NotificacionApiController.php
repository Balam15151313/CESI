<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Alumno;
use App\Models\Maestro;
use Illuminate\Http\Request;
use App\Models\Notificacion;
use App\Models\Tutor;
use App\Models\User;

/**
 * Archivo: NotificacionesApiController.php
 * Propósito: Controlador para gestionar datos relacionados con notificaciones.
 * Autor: José Balam González Rojas
 * Fecha de Creación: 2024-11-19
 * Última Modificación: 2024-12-02
 */

class NotificacionApiController extends Controller
{
    /**
     * Obtener todas las notificaciones de un alumno.
     */
    public function index($alumnoId)
    {
        $notificaciones = Notificacion::where('cesi_alumno_id', $alumnoId)->get();

        if ($notificaciones->isEmpty()) {
            return response()->json(['message' => 'No hay notificaciones'], 200);
        }

        return response()->json(['data' => $notificaciones], 200);
    }

    /**
     * Crear una nueva notificación para un alumno.
     */
    public function create(Request $request, $maestroId, $alumnoId)
    {
        $user = User::find($maestroId);
        $maestro =  Maestro::where('maestro_usuario', $user->email)->first();
        $alumno = Alumno::where('id', $alumnoId)
            ->where('cesi_salon_id', $maestro->salones->id)
            ->first();
        $validated = $request->validate([
            'notificaciones_mensaje' => 'required|string|max:255',
            'notificaciones_prioridad' => 'required|integer|min:1|max:5',
            'notificaciones_tipo' => 'required|string|max:50',
        ]);

        $validated['cesi_alumno_id'] = $alumno->id;

        $notificacion = Notificacion::create($validated);

        return response()->json(['message' => 'Notificación creada correctamente', 'data' => $notificacion], 201);
    }

    /**
     * Mostrar las notificaciones de los alumnos de un tutor.
     */
    public function show($tutorId)
    {
        $user = User::find($tutorId);

        $tutor = Tutor::where('tutor_usuario', $user->email)->first();
        if (!$tutor) {
            return response()->json(['error' => 'Tutor no encontrado'], 404);
        }

        $alumnosConNotificaciones = $tutor->alumnos()->with('notificaciones')->get();
        if ($alumnosConNotificaciones->isEmpty()) {
            return response()->json(['error' => 'El tutor no tiene alumnos asignados'], 404);
        }

        $data = $alumnosConNotificaciones->map(function ($alumnosConNotificaciones) {
            return [
                'alumno_nombre' => $alumnosConNotificaciones->alumno_nombre,
                'notificaciones' => $alumnosConNotificaciones->notificaciones
            ];
        });

        return response()->json(['data' => $data], 200);
    }

    /**
     * Actualizar una notificación de un alumno.
     */
    public function update(Request $request, $alumnoId, $id)
    {
        $notificacion = Notificacion::find($id);

        if (!$notificacion) {
            return response()->json(['error' => 'Notificación no encontrada'], 404);
        }

        if ($notificacion->cesi_alumno_id !== $alumnoId) {
            return response()->json(['error' => 'No autorizado para actualizar esta notificación'], 403);
        }

        $validated = $request->validate([
            'notificaciones_mensaje' => 'nullable|string|max:255',
            'notificaciones_prioridad' => 'nullable|integer|min:1|max:5',
            'notificaciones_tipo' => 'nullable|string|max:50',
        ]);

        $notificacion->update($validated);

        return response()->json(['message' => 'Notificación actualizada correctamente', 'data' => $notificacion], 200);
    }

    /**
     * Eliminar una notificación de un alumno.
     */
    public function destroy($alumnoId, $id)
    {
        $notificacion = Notificacion::find($id);
        if (!$notificacion) {
            return response()->json(['error' => 'Notificación no encontrada'], 404);
        }

        if ($notificacion->cesi_alumno_id != $alumnoId) {
            return response()->json(['error' => 'No autorizado para eliminar esta notificación'], 403);
        }

        $notificacion->delete();

        return response()->json(['message' => 'Notificación eliminada correctamente'], 200);
    }
}
