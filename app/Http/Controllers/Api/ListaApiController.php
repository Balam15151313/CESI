<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Lista;

class ListaApiController extends Controller
{
    /**
     * Mostrar todas las listas asociadas al maestro autenticado.
     */
    public function index()
    {
        $maestroId = Auth::id();
        $listas = Lista::where('cesi_maestro_id', $maestroId)->get();

        if ($listas->isEmpty()) {
            return response()->json(['message' => 'No hay listas registradas'], 200);
        }

        return response()->json(['data' => $listas], 200);
    }

    /**
     * Crear una nueva lista.
     */
    public function create(Request $request)
    {
        $validated = $request->validate([
            'listas_pdf' => 'required|file|mimes:pdf|max:2048',
        ]);

        $maestroId = Auth::id();

        if ($request->hasFile('listas_pdf')) {
            $filePath = $request->file('listas_pdf')->store('listas', 'public');
            $validated['listas_pdf'] = $filePath;
        }

        $validated['cesi_maestro_id'] = $maestroId;

        $lista = Lista::create($validated);

        return response()->json(['message' => 'Lista creada correctamente', 'data' => $lista], 201);
    }

    /**
     * Mostrar una lista específica.
     */
    public function show($id)
    {
        $lista = Lista::find($id);

        if (!$lista) {
            return response()->json(['error' => 'Lista no encontrada'], 404);
        }

        if ($lista->cesi_maestro_id !== Auth::id()) {
            return response()->json(['error' => 'No autorizado para ver esta lista'], 403);
        }

        return response()->json(['data' => $lista], 200);
    }

    /**
     * Actualizar una lista existente.
     */
    public function update(Request $request, $id)
    {
        $lista = Lista::find($id);

        if (!$lista) {
            return response()->json(['error' => 'Lista no encontrada'], 404);
        }

        if ($lista->cesi_maestro_id !== Auth::id()) {
            return response()->json(['error' => 'No autorizado para actualizar esta lista'], 403);
        }

        $validated = $request->validate([
            'listas_pdf' => 'nullable|file|mimes:pdf|max:2048',
        ]);

        if ($request->hasFile('listas_pdf')) {
            if ($lista->listas_pdf && Storage::exists('public/' . $lista->listas_pdf)) {
                Storage::delete('public/' . $lista->listas_pdf);
            }

            $filePath = $request->file('listas_pdf')->store('listas', 'public');
            $validated['listas_pdf'] = $filePath;
        }

        $lista->update($validated);

        return response()->json(['message' => 'Lista actualizada correctamente', 'data' => $lista], 200);
    }

    /**
     * Eliminar una lista.
     */
    public function destroy($id)
    {
        $lista = Lista::find($id);

        if (!$lista) {
            return response()->json(['error' => 'Lista no encontrada'], 404);
        }

        if ($lista->cesi_maestro_id !== Auth::id()) {
            return response()->json(['error' => 'No autorizado para eliminar esta lista'], 403);
        }

        if ($lista->listas_pdf && Storage::exists('public/' . $lista->listas_pdf)) {
            Storage::delete('public/' . $lista->listas_pdf);
        }

        $lista->delete();

        return response()->json(['message' => 'Lista eliminada correctamente'], 200);
    }
}
