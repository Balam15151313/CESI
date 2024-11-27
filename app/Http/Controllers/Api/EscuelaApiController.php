<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Escuela;
use App\Models\Privilegio;
/**
 * Archivo: EscuelaApiController.php
 * Propósito: Controlador para gestionar datos relacionados con escuelas.
 * Autor: José Balam González Rojas
 * Fecha de Creación: 2024-11-19
 * Última Modificación: 2024-11-26 - Añadida validación para evitar duplicados.
 */

class EscuelaApiController extends Controller
{
    /**
     * Mostrar todas las escuelas asociadas al administrador autenticado.
     */
    public function index()
    {
        $adminId = Auth::id();
        $escuelas = Escuela::whereHas('administrador', function ($query) use ($adminId) {
            $query->where('cesi_administrador_id', $adminId);
        })->get();

        if ($escuelas->isEmpty()) {
            return response()->json(['message' => 'No hay escuelas registradas'], 200);
        }

        return response()->json(['data' => $escuelas], 200);
    }

    /**
     * Crear una nueva escuela.
     */
    public function create(Request $request)
    {
        $adminId = Auth::id();
        $yaTieneEscuela = Privilegio::where('cesi_administrador_id', $adminId)->exists();

        if ($yaTieneEscuela) {
            return response()->json(['error' => 'Ya has creado una escuela'], 403);
        }

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'direccion' => 'required|string|max:255',
            'escuela_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        DB::transaction(function () use ($validated, $adminId) {
            if (request()->hasFile('escuela_logo')) {
                $validated['escuela_logo'] = request()->file('escuela_logo')->store('logos', 'public');
            }

            $escuela = Escuela::create($validated);

            Privilegio::create([
                'cesi_administrador_id' => $adminId,
                'cesi_escuela_id' => $escuela->id,
            ]);
        });

        return response()->json(['message' => 'Escuela creada correctamente'], 201);
    }

    /**
     * Mostrar una escuela específica.
     */
    public function show($id)
    {
        $escuela = Escuela::find($id);

        if (!$escuela) {
            return response()->json(['error' => 'Escuela no encontrada'], 404);
        }

        if ($escuela->cesi_administrador_id !== Auth::id()) {
            return response()->json(['error' => 'No autorizado para ver esta escuela'], 403);
        }

        return response()->json(['data' => $escuela], 200);
    }

    /**
     * Actualizar una escuela existente.
     */
    public function update(Request $request, $id)
    {
        $escuela = Escuela::find($id);

        if (!$escuela) {
            return response()->json(['error' => 'Escuela no encontrada'], 404);
        }

        if ($escuela->cesi_administrador_id !== Auth::id()) {
            return response()->json(['error' => 'No autorizado para actualizar esta escuela'], 403);
        }

        $validated = $request->validate([
            'nombre' => 'nullable|string|max:255',
            'direccion' => 'nullable|string|max:255',
            'escuela_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('escuela_logo')) {
            if ($escuela->escuela_logo && Storage::exists('public/' . $escuela->escuela_logo)) {
                Storage::delete('public/' . $escuela->escuela_logo);
            }
            $validated['escuela_logo'] = $request->file('escuela_logo')->store('logos', 'public');
        }

        $escuela->update($validated);

        return response()->json(['message' => 'Escuela actualizada correctamente', 'data' => $escuela], 200);
    }

    /**
     * Eliminar una escuela.
     */
    public function destroy($id)
    {
        $escuela = Escuela::find($id);

        if (!$escuela) {
            return response()->json(['error' => 'Escuela no encontrada'], 404);
        }

        if ($escuela->cesi_administrador_id !== Auth::id()) {
            return response()->json(['error' => 'No autorizado para eliminar esta escuela'], 403);
        }

        DB::transaction(function () use ($escuela) {
            if ($escuela->escuela_logo && Storage::exists('public/' . $escuela->escuela_logo)) {
                Storage::delete('public/' . $escuela->escuela_logo);
            }

            Privilegio::where('cesi_escuela_id', $escuela->id)->delete();
            $escuela->delete();
        });

        return response()->json(['message' => 'Escuela eliminada correctamente'], 200);
    }
}
