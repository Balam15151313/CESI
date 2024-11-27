<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Recogida;
/**
 * Archivo: RecogidaApiController.php
 * Propósito: Controlador para gestionar datos relacionados con la recogida de estudiantes.
 * Autor: José Balam González Rojas
 * Fecha de Creación: 2024-11-19
 * Última Modificación: 2024-11-26
 */

class RecogidaApiController extends Controller
{
    /**
     * Mostrar todas las recogidas.
     */
    public function index()
    {
        $recogidas = Recogida::all();

        if ($recogidas->isEmpty()) {
            return response()->json(['message' => 'No hay recogidas disponibles'], 200);
        }

        return response()->json(['data' => $recogidas], 200);
    }

    /**
     * Crear una nueva recogida.
     */
    public function create(Request $request)
    {
        // Validación de los datos de la recogida
        $validated = $request->validate([
            'recogida_fecha' => 'required|date',
            'recogida_observaciones' => 'nullable|string',
            'recogida_estatus' => 'required|in:pendiente,completa,cancelada',  // Ejemplo de estatus
        ]);

        // Crear la recogida
        $recogida = Recogida::create($validated);

        return response()->json(['message' => 'Recogida creada correctamente', 'data' => $recogida], 201);
    }

    /**
     * Mostrar una recogida específica.
     */
    public function show($id)
    {
        // Buscar la recogida por ID
        $recogida = Recogida::find($id);

        if (!$recogida) {
            return response()->json(['error' => 'Recogida no encontrada'], 404);
        }

        return response()->json(['data' => $recogida], 200);
    }

    /**
     * Actualizar una recogida existente.
     */
    public function update(Request $request, $id)
    {
        // Buscar la recogida por ID
        $recogida = Recogida::find($id);

        if (!$recogida) {
            return response()->json(['error' => 'Recogida no encontrada'], 404);
        }

        // Validación de los datos a actualizar
        $validated = $request->validate([
            'recogida_fecha' => 'nullable|date',
            'recogida_observaciones' => 'nullable|string',
            'recogida_estatus' => 'nullable|in:pendiente,completa,cancelada',
        ]);

        // Actualizar la recogida
        $recogida->update($validated);

        return response()->json(['message' => 'Recogida actualizada correctamente', 'data' => $recogida], 200);
    }

    /**
     * Eliminar una recogida.
     */
    public function destroy($id)
    {
        // Buscar la recogida por ID
        $recogida = Recogida::find($id);

        if (!$recogida) {
            return response()->json(['error' => 'Recogida no encontrada'], 404);
        }

        // Eliminar la recogida
        $recogida->delete();

        return response()->json(['message' => 'Recogida eliminada correctamente'], 200);
    }

    /**
     * Obtener los alumnos asociados a una recogida.
     */
    public function alumnos($id)
    {
        // Buscar la recogida por ID
        $recogida = Recogida::find($id);

        if (!$recogida) {
            return response()->json(['error' => 'Recogida no encontrada'], 404);
        }

        // Obtener los alumnos asociados a la recogida
        $alumnos = $recogida->alumnos;

        return response()->json(['data' => $alumnos], 200);
    }
}
