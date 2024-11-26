<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sesion;

class SesionApiController extends Controller
{
    /**
     * Mostrar todas las sesiones.
     */
    public function index()
    {
        // Obtener todas las sesiones
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
        // Validación de los datos de la sesión
        $validated = $request->validate([
            'sesion_estado' => 'required|string|max:255',
            'sesion_inicio' => 'required|date',
            'sesion_fin' => 'nullable|date',
            'sesion_usuario' => 'required|string|max:255',
            'cesi_responsable_id' => 'required|exists:responsables,id', // Asegúrate de que el responsable exista
        ]);

        // Crear la sesión
        $sesion = Sesion::create($validated);

        return response()->json(['message' => 'Sesión creada correctamente', 'data' => $sesion], 201);
    }

    /**
     * Mostrar una sesión específica.
     */
    public function show($id)
    {
        // Buscar la sesión por ID
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
        // Buscar la sesión por ID
        $sesion = Sesion::find($id);

        if (!$sesion) {
            return response()->json(['error' => 'Sesión no encontrada'], 404);
        }

        // Validación de los datos a actualizar
        $validated = $request->validate([
            'sesion_estado' => 'nullable|string|max:255',
            'sesion_inicio' => 'nullable|date',
            'sesion_fin' => 'nullable|date',
            'sesion_usuario' => 'nullable|string|max:255',
            'cesi_responsable_id' => 'nullable|exists:responsables,id',
        ]);

        // Actualizar la sesión
        $sesion->update($validated);

        return response()->json(['message' => 'Sesión actualizada correctamente', 'data' => $sesion], 200);
    }

    /**
     * Eliminar una sesión.
     */
    public function destroy($id)
    {
        // Buscar la sesión por ID
        $sesion = Sesion::find($id);

        if (!$sesion) {
            return response()->json(['error' => 'Sesión no encontrada'], 404);
        }

        // Eliminar la sesión
        $sesion->delete();

        return response()->json(['message' => 'Sesión eliminada correctamente'], 200);
    }

    /**
     * Obtener el responsable asociado a una sesión.
     */
    public function responsable($id)
    {
        // Buscar la sesión por ID
        $sesion = Sesion::find($id);

        if (!$sesion) {
            return response()->json(['error' => 'Sesión no encontrada'], 404);
        }

        // Obtener el responsable asociado a la sesión
        $responsable = $sesion->responsables;

        return response()->json(['data' => $responsable], 200);
    }
}
