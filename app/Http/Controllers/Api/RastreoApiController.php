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
 * Última Modificación: 2024-11-27
 */

class RastreoApiController extends Controller
{
    /**
     * Mostrar todos los rastreos de una recogida.
     */
    public function index($recogidaId)
    {
        $rastreos = Rastreo::where('cesi_recogida_id', $recogidaId)->get();

        if ($rastreos->isEmpty()) {
            return response()->json(['message' => 'No hay rastreos para esta recogida'], 200);
        }

        return response()->json(['data' => $rastreos], 200);
    }

    /**
     * Crear un nuevo rastreo para una recogida.
     */
    public function create(Request $request, $recogidaId)
    {
        $validated = $request->validate([
            'rastreo_longitud' => 'required|numeric',
            'rastreo_latitud' => 'required|numeric',
        ]);

        $validated['cesi_recogida_id'] = $recogidaId;

        $rastreo = Rastreo::create($validated);

        return response()->json(['message' => 'Rastreo creado correctamente', 'data' => $rastreo], 201);
    }

    /**
     * Mostrar un rastreo específico de una recogida.
     */
    public function show($recogidaId, $id)
    {
        $rastreo = Rastreo::find($id);

        if (!$rastreo) {
            return response()->json(['error' => 'Rastreo no encontrado'], 404);
        }

        if ($rastreo->cesi_recogida_id != $recogidaId) {
            return response()->json(['error' => 'No autorizado para ver este rastreo'], 403);
        }

        return response()->json(['data' => $rastreo], 200);
    }

    /**
     * Actualizar un rastreo de una recogida.
     */
    public function update(Request $request, $recogidaId, $id)
    {
        $rastreo = Rastreo::find($id);

        if (!$rastreo) {
            return response()->json(['error' => 'Rastreo no encontrado'], 404);
        }

        if ($rastreo->cesi_recogida_id != $recogidaId) {
            return response()->json(['error' => 'No autorizado para actualizar este rastreo'], 403);
        }

        $validated = $request->validate([
            'rastreo_longitud' => 'nullable|numeric',
            'rastreo_latitud' => 'nullable|numeric',
        ]);

        $rastreo->update($validated);

        return response()->json(['message' => 'Rastreo actualizado correctamente', 'data' => $rastreo], 200);
    }

    /**
     * Eliminar un rastreo de una recogida.
     */
    public function destroy($recogidaId, $id)
    {
        $rastreo = Rastreo::find($id);

        if (!$rastreo) {
            return response()->json(['error' => 'Rastreo no encontrado'], 404);
        }

        if ($rastreo->cesi_recogida_id != $recogidaId) {
            return response()->json(['error' => 'No autorizado para eliminar este rastreo'], 403);
        }

        $rastreo->delete();

        return response()->json(['message' => 'Rastreo eliminado correctamente'], 200);
    }
}
