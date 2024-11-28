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
 * Última Modificación: 2024-11-27
 */

class DashboardApiController extends Controller
{
    /**
     * Muestra los responsables inactivos asociados al administrador autenticado.
     */
    public function index()
    {
        $adminId = Auth::id();

        $escuelas = Escuela::whereHas('administrador', function ($query) use ($adminId) {
            $query->where('cesi_administrador_id', $adminId);
        })->pluck('id');

        if ($escuelas->isEmpty()) {
            return response()->json([
                'message' => 'No se encontraron escuelas asociadas al administrador.'
            ], 404);
        }

        $responsablesInactivos = Responsable::with(['tutores' => function ($query) use ($escuelas) {
            $query->whereIn('cesi_escuela_id', $escuelas)
                ->select('id', 'cesi_escuela_id');
        }])
            ->where('responsable_activacion', 0)
            ->whereHas('tutores', function ($query) use ($escuelas) {
                $query->whereIn('cesi_escuela_id', $escuelas);
            })
            ->get();

        if ($responsablesInactivos->isEmpty()) {
            return response()->json([
                'message' => 'No se encontraron responsables inactivos asociados a las escuelas del administrador.'
            ], 404);
        }

        return response()->json([
            'responsables_inactivos' => $responsablesInactivos
        ], 200);
    }
}
