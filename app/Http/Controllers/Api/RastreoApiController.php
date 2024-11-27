<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Rastreo;
/**
 * Archivo: RastreoApiController.php
 * Propósito: Controlador para gestionar datos relacionados con rastreo.
 * Autor: José Balam González Rojas
 * Fecha de Creación: 2024-11-19
 * Última Modificación: 2024-11-26
 */

class RastreoApiController extends Controller
{
    /**
     * Mostrar todos los rastreos de una recogida dada.
     */
    public function index($recogidaId)
    {
        // Obtener los rastreos asociados a la recogida
        $rastreos = Rastreo::where('cesi_recogida_id', $recogidaId)->get();

        if ($rastreos->isEmpty()) {
            return response()->json(['message' => 'No hay rastreos para esta recogida'], 200);
        }

        return response()->json(['data' => $rastreos], 200);
    }

    /**
     * Crear un nuevo rastreo para una recogida dada.
     */
    public function create(Request $request, $recogidaId)
    {
        // Validación de los datos del rastreo
        $validated = $request->validate([
            'rastreo_longitud' => 'required|numeric',
            'rastreo_latitud' => 'required|numeric',
        ]);

        // Asignar el recogida_id recibido
        $validated['cesi_recogida_id'] = $recogidaId;

        // Crear el rastreo
        $rastreo = Rastreo::create($validated);

        return response()->json(['message' => 'Rastreo creado correctamente', 'data' => $rastreo], 201);
    }

    /**
     * Mostrar un rastreo específico de una recogida dada.
     */
    public function show($recogidaId, $id)
    {
        // Buscar el rastreo por ID
        $rastreo = Rastreo::find($id);

        if (!$rastreo) {
            return response()->json(['error' => 'Rastreo no encontrado'], 404);
        }

        // Verificar que el rastreo pertenezca a la recogida dada
        if ($rastreo->cesi_recogida_id !== $recogidaId) {
            return response()->json(['error' => 'No autorizado para ver este rastreo'], 403);
        }

        return response()->json(['data' => $rastreo], 200);
    }

    /**
     * Actualizar un rastreo existente de una recogida dada.
     */
    public function update(Request $request, $recogidaId, $id)
    {
        // Buscar el rastreo por ID
        $rastreo = Rastreo::find($id);

        if (!$rastreo) {
            return response()->json(['error' => 'Rastreo no encontrado'], 404);
        }

        // Verificar que el rastreo pertenezca a la recogida dada
        if ($rastreo->cesi_recogida_id !== $recogidaId) {
            return response()->json(['error' => 'No autorizado para actualizar este rastreo'], 403);
        }

        // Validación de los datos a actualizar
        $validated = $request->validate([
            'rastreo_longitud' => 'nullable|numeric',
            'rastreo_latitud' => 'nullable|numeric',
        ]);

        // Actualizar el rastreo
        $rastreo->update($validated);

        return response()->json(['message' => 'Rastreo actualizado correctamente', 'data' => $rastreo], 200);
    }

    /**
     * Eliminar un rastreo de una recogida dada.
     */
    public function destroy($recogidaId, $id)
    {
        // Buscar el rastreo por ID
        $rastreo = Rastreo::find($id);

        if (!$rastreo) {
            return response()->json(['error' => 'Rastreo no encontrado'], 404);
        }

        // Verificar que el rastreo pertenezca a la recogida dada
        if ($rastreo->cesi_recogida_id !== $recogidaId) {
            return response()->json(['error' => 'No autorizado para eliminar este rastreo'], 403);
        }

        // Eliminar el rastreo
        $rastreo->delete();

        return response()->json(['message' => 'Rastreo eliminado correctamente'], 200);
    }
}
