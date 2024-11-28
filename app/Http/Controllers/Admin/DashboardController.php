<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Responsable;
use App\Models\Escuela;
use App\Models\User;
use App\Models\Administrador;
use Illuminate\Support\Facades\Auth;

/**
 * Archivo: DashboardController.php
 * Propósito: Controlador para gestionar datos de la pantalla de inicio.
 * Autor: José Balam González Rojas
 * Fecha de Creación: 2024-11-06
 * Última Modificación: 2024-11-27
 */
class DashboardController extends Controller
{
    /**
     * Muestra el dashboard del administrador con los responsables inactivos.
     * Obtiene el usuario autenticado, el administrador asociado y las escuelas del administrador.
     * Luego obtiene los responsables inactivos asociados a esas escuelas.
     */
    public function index()
    {
        $user = User::find(Auth::id());

        $admin = Administrador::where('administrador_usuario', $user->email)->first();

        $adminId = $admin->id;
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

        return view('dashboard', compact('responsablesInactivos', 'mensaje', 'admin'));
    }
}
