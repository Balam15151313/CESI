<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asistencia;
use Illuminate\Http\Request;

/**
 * Archivo: AsistenciasApiController.php
 * Propósito: Controlador para gestionar datos relacionados con asistencias.
 * Autor: José Balam González Rojas
 * Fecha de Creación: 2024-11-19
 * Última Modificación: 2024-11-27
 */


class AsistenciasApiController extends Controller
{
    /**
     * Almacena una nueva asistencia.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'asistencia_fecha' => 'required|date',
            'asistencia_hora' => 'required|date_format:H:i',
        ]);
        $asistencia = new Asistencia();
        $asistencia->asistencia_fecha = $validated['asistencia_fecha'];
        $asistencia->asistencia_hora = $validated['asistencia_hora'];
        $asistencia->save();
        return response()->json(['message' => 'Asistencia creada exitosamente', 'asistencia' => $asistencia], 201);
    }

    /**
     * Muestra una asistencia específica.
     */
    public function show($id)
    {
        // Busca la asistencia por su ID
        $asistencia = Asistencia::find($id);
        if (!$asistencia) {
            return response()->json(['error' => 'Asistencia no encontrada'], 404);
        }
        return response()->json(['data' => $asistencia], 200);
    }

    /**
     * Actualiza una asistencia existente.
     */
    public function update(Request $request, Asistencia $asistencia)
    {
        $asistencia->asistencia_fecha = $request->asistencia_fecha;
        $asistencia->asistencia_hora = $request->asistencia_hora;

        $asistencia->save();

        return response()->json(['message' => 'Asistencia actualizada exitosamente', 'asistencia' => $asistencia], 200);
    }

    /**
     * Elimina una asistencia específica.
     */
    public function destroy(Asistencia $asistencia)
    {
        $asistencia->delete();

        return response()->json(['message' => 'Asistencia eliminada exitosamente', 'asistencia' => $asistencia], 200);
    }
}
