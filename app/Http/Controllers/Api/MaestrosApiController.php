<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Maestro;
use App\Models\Escuela;
use App\Models\UI;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Archivo: MaestrosApiController.php
 * Propósito: Controlador para gestionar datos relacionados con maestros.
 * Autor: José Balam González Rojas
 * Fecha de Creación: 2024-11-19
 * Última Modificación: 2024-12-02
 */
class MaestrosApiController extends Controller
{
    /**
     * Obtener los colores de la escuela asociada al maestro.
     */
    public function obtenerColoresDeEscuela($maestroId)
    {
        $user = User::find($maestroId);
        $maestro = Maestro::where('maestro_usuario', $user->email)->first();

        $ui = UI::where('cesi_escuela_id', $maestro->cesi_escuela_id)->first();
        $escuela = Escuela::find($maestro->cesi_escuela_id)->get()->first();
        $escuelaLogo = $escuela->escuela_logo;
        if (!$ui) {
            return response()->json(['error' => 'Colores de la escuela no encontrados'], 404);
        }
        return response()->json(
            ['ui_color1' => $ui->ui_color1, 'ui_color2' => $ui->ui_color2, 'ui_color3' => $ui->ui_color3, 'escuela_logo' => $escuelaLogo]
        );
    }

    /**
     * Mostrar el recurso especificado.
     */
    public function show($id)
    {
        $user = User::find($id);
        $maestro = Maestro::where('maestro_usuario', $user->email)->first();

        if (!$maestro) {
            return response()->json(['error' => 'Maestro no encontrado'], 404);
        }

        return response()->json(['data' => $maestro], 200);
    }


    /**
     * Actualizar la foto del maestro por su ID
     */
    public function updateFoto(Request $request, $id)
    {
        $request->validate([
            'maestro_foto' => 'required|image|mimes:jpeg,png,jpg,gif',
        ]);
        $user = User::find($id);
        $maestro = Maestro::where('maestro_usuario', $user->email)->first();

        if (!$maestro) {
            return response()->json(['error' => 'Maestro no encontrado'], 404);
        }
        if ($request->hasFile('maestro_foto') && $request->file('maestro_foto')->isValid()) {
            if ($maestro->maestro_foto) {
                Storage::delete('public/' . $maestro->maestro_foto);
            }
            $fotoPath = $request->file('maestro_foto')->store('maestros', 'public');
            $maestro->maestro_foto = $fotoPath;
            $maestro->save();
        }

        return response()->json(['success' => 'Foto actualizada exitosamente']);
    }
}
