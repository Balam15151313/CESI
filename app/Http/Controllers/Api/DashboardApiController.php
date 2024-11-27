<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Responsable;
use App\Models\Escuela;
use Illuminate\Support\Facades\Auth;
/**
 * Archivo: DashboardApiController.php
 * Propósito: Controlador para gestionar datos de la pantalla de inicio.
 * Autor: José Balam González Rojas
 * Fecha de Creación: 2024-11-19
 * Última Modificación: 2024-11-26
 */

class DashboardApiController extends Controller
{
    /**
     * Proteger el controlador con middleware de autenticación.
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Mostrar responsables inactivos asociados al administrador autenticado.
     */
    public function index()
    {
        // Obtener el ID del administrador autenticado
        $adminId = Auth::id();

        // Obtener las escuelas asociadas al administrador
        $escuelas = Escuela::whereHas('administrador', function ($query) use ($adminId) {
            $query->where('cesi_administrador_id', $adminId);
        })->pluck('id');

        if ($escuelas->isEmpty()) {
            return response()->json([
                'message' => 'No se encontraron escuelas asociadas al administrador.'
            ], 404);
        }

        // Obtener responsables inactivos asociados a las escuelas
        $responsablesInactivos = Responsable::with(['tutores' => function ($query) use ($escuelas) {
            $query->whereIn('cesi_escuela_id', $escuelas)
                  ->select('id', 'cesi_escuela_id'); // Limitar columnas cargadas
        }])
        ->where('responsable_activacion', 0) // Solo responsables inactivos
        ->whereHas('tutores', function ($query) use ($escuelas) {
            $query->whereIn('cesi_escuela_id', $escuelas);
        })
        ->get();

        // Si no se encuentran responsables inactivos
        if ($responsablesInactivos->isEmpty()) {
            return response()->json([
                'message' => 'No se encontraron responsables inactivos asociados a las escuelas del administrador.'
            ], 404);
        }

        // Devolver la lista de responsables inactivos
        return response()->json([
            'responsables_inactivos' => $responsablesInactivos
        ], 200);
    }
}
