<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Responsable;
use App\Models\Escuela;
use Illuminate\Support\Facades\Auth;

/**
 * Archivo: DashboardController.php
 * Propósito: Controlador para gestionar datos de la pantalla de inicio.
 * Autor: José Balam González Rojas
 * Fecha de Creación: 2024-11-06
 * Última Modificación: 2024-11-26
 */
class DashboardController extends Controller
{
    public function index()
    {
        $adminId = Auth::id();

        $escuelas = Escuela::whereHas('administrador', function ($query) use ($adminId) {
            $query->where('cesi_administrador_id', $adminId);
        })->pluck('id');

        if ($escuelas->isEmpty()) {
            $responsablesInactivos = collect();
        } else {
            $responsablesInactivos = Responsable::with(['tutores' => function ($query) use ($escuelas) {
                    $query->whereIn('cesi_escuela_id', $escuelas);
                }])
                ->where('responsable_activacion', false)
                ->whereHas('tutores', function ($query) use ($escuelas) {
                    $query->whereIn('cesi_escuela_id', $escuelas);
                })
                ->get();
        }
        if ($responsablesInactivos->isEmpty()) {
            $mensaje = 'No hay responsables inactivos asociados a las escuelas de este administrador.';
        } else {
            $mensaje = null;
        }

        return view('dashboard', compact('responsablesInactivos', 'mensaje'));
    }
}
