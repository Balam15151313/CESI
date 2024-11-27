<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Salon;
use App\Models\Escuela;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
/**
 * Archivo: SalonApiController.php
 * Propósito: Controlador para gestionar datos relacionados con salones.
 * Autor: José Balam González Rojas
 * Fecha de Creación: 2024-11-19
 * Última Modificación: 2024-11-26 - Añadida validación para evitar duplicados.
 */

class SalonApiController extends Controller
{
    /**
     * Listar salones con filtros dinámicos.
     */
    public function index(Request $request)
    {
        // Obtener el ID del administrador autenticado
        $adminId = Auth::id();

        // Obtener las escuelas asociadas al administrador
        $escuelas = Escuela::whereHas('administrador', function ($query) use ($adminId) {
            $query->where('cesi_administrador_id', $adminId);
        })->pluck('id');

        // Consulta base para obtener salones
        $query = Salon::with(['escuela', 'maestro'])
            ->whereIn('cesi_escuela_id', $escuelas);

        // Aplicar filtros dinámicos
        $query->when($request->grado, function ($query, $grado) {
            $query->where('salon_grado', 'like', '%' . $grado . '%');
        })
        ->when($request->grupo, function ($query, $grupo) {
            $query->where('salon_grupo', 'like', '%' . $grupo . '%');
        });

        // Obtener los resultados
        $salones = $query->get();

        return response()->json($salones, 200);
    }

    /**
     * Crear un nuevo salón.
     */
    public function store(Request $request)
    {
        $validationResult = $this->validateRequest($request);

        if ($validationResult !== true) {
            return $validationResult; // Retorna el error de validación
        }

        // Crear el salón
        $salon = Salon::create($request->all());

        return response()->json([
            'message' => 'Salón creado exitosamente.',
            'data' => $salon,
        ], 201);
    }

    /**
     * Mostrar la información de un salón específico.
     */
    public function show(Salon $salon)
    {
        $salon->load(['escuela', 'maestro']);

        return response()->json($salon, 200);
    }

    /**
     * Actualizar la información de un salón.
     */
    public function update(Request $request, Salon $salon)
    {
        $validationResult = $this->validateRequest($request);

        if ($validationResult !== true) {
            return $validationResult; // Retorna el error de validación
        }

        // Actualizar el salón
        $salon->update($request->all());

        return response()->json([
            'message' => 'Salón actualizado exitosamente.',
            'data' => $salon,
        ], 200);
    }

    /**
     * Eliminar un salón.
     */
    public function destroy(Salon $salon)
    {
        $salon->delete();

        return response()->json(['message' => 'Salón eliminado exitosamente.'], 200);
    }

    /**
     * Validar los datos de la solicitud.
     */
    private function validateRequest(Request $request)
    {
        $rules = [
            'salon_grado' => 'nullable|string|max:2',
            'salon_grupo' => 'nullable|string|max:2',
            'cesi_escuela_id' => 'required|exists:cesi_escuelas,id',
            'cesi_maestro_id' => 'required|exists:cesi_maestros,id',
        ];

        $messages = [
            'salon_grado.max' => 'El grado no puede tener más de 2 caracteres.',
            'salon_grupo.max' => 'El grupo no puede tener más de 2 caracteres.',
            'cesi_escuela_id.required' => 'La escuela es obligatoria.',
            'cesi_escuela_id.exists' => 'La escuela seleccionada no existe.',
            'cesi_maestro_id.required' => 'El maestro es obligatorio.',
            'cesi_maestro_id.exists' => 'El maestro seleccionado no existe.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors(),
            ], 422);
        }

        return true; // Validación exitosa
    }
}
