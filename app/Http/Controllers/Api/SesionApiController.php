<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sesion;

/**
 * Archivo: SesionApiController.php
 * Propósito: Controlador para gestionar datos relacionados con sesiones.
 * Autor: José Balam González Rojas
 * Fecha de Creación: 2024-11-19
 * Última Modificación: 2024-11-27
 */

class SesionApiController extends Controller
{
    /**
     * Mostrar todas las sesiones.
     */
    public function index()
    {
        $sesiones = Sesion::all();

        if ($sesiones->isEmpty()) {
            return response()->json(['message' => 'No hay sesiones disponibles'], 200);
        }

        return response()->json(['data' => $sesiones], 200);
    }

    /**
     * Crear una nueva sesión.
     */
    public function create(Request $request)
    {
        $validated = $request->validate([
            'sesion_estado' => 'required|string|max:255',
            'sesion_inicio' => 'required|date',
            'sesion_fin' => 'nullable|date',
            'sesion_usuario' => 'required|string|max:255',
            'cesi_responsable_id' => 'required|exists:responsables,id',
        ]);

        $sesion = Sesion::create($validated);

        return response()->json(['message' => 'Sesión creada correctamente', 'data' => $sesion], 201);
    }

    /**
     * Mostrar una sesión específica.
     */
    public function show($id)
    {
        $sesion = Sesion::find($id);

        if (!$sesion) {
            return response()->json(['error' => 'Sesión no encontrada'], 404);
        }

        return response()->json(['data' => $sesion], 200);
    }

    /**
     * Actualizar una sesión existente.
     */
    public function update(Request $request, $id)
    {
        $sesion = Sesion::find($id);

        if (!$sesion) {
            return response()->json(['error' => 'Sesión no encontrada'], 404);
        }

        $validated = $request->validate([
            'sesion_estado' => 'nullable|string|max:255',
            'sesion_inicio' => 'nullable|date',
            'sesion_fin' => 'nullable|date',
            'sesion_usuario' => 'nullable|string|max:255',
            'cesi_responsable_id' => 'nullable|exists:responsables,id',
        ]);

        $sesion->update($validated);

        return response()->json(['message' => 'Sesión actualizada correctamente', 'data' => $sesion], 200);
    }

    /**
     * Eliminar una sesión.
     */
    public function destroy($id)
    {
        $sesion = Sesion::find($id);

        if (!$sesion) {
            return response()->json(['error' => 'Sesión no encontrada'], 404);
        }

        $sesion->delete();

        return response()->json(['message' => 'Sesión eliminada correctamente'], 200);
    }

    /**
     * Obtener el responsable asociado a una sesión.
     */
    public function responsable($id)
    {
        $sesion = Sesion::find($id);

        if (!$sesion) {
            return response()->json(['error' => 'Sesión no encontrada'], 404);
        }

        $responsable = $sesion->responsables;

        return response()->json(['data' => $responsable], 200);
    }
}
