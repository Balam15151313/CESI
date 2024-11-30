<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Responsable;
use App\Models\User;
use App\Models\Escuela;
use App\Models\Tutor;
use App\Models\UI;
use Illuminate\Http\Request;

/**
 * Archivo: ResponsableApiController.php
 * Propósito: Controlador para gestionar datos relacionados con responsables.
 * Autor: José Balam González Rojas
 * Fecha de Creación: 2024-11-19
 * Última Modificación: 2024-11-29
 */

class ResponsableApiController extends Controller
{

    /**
     * Store a newly created responsable in storage.
     */
    public function store(Request $request)
    {
        try {
            $responsable = new Responsable();
            $responsable->fill($request->only(['responsable_nombre', 'responsable_usuario', 'responsable_telefono', 'cesi_tutore_id']));
            $responsable->responsable_contraseña = bcrypt($request->responsable_contraseña);
            $responsable->responsable_activacion = 0;
            $responsable->cesi_tutore_id = $request->cesi_tutore_id;

            $user = new User();
            $user->name = $request->responsable_nombre;
            $user->email = $request->responsable_usuario;
            $user->password = bcrypt($request->responsable_contraseña);
            $user->role = 'responsable';

            if ($request->hasFile('responsable_foto')) {
                $imagePath = $request->file('responsable_foto')->store('responsables', 'public');
                $responsable->responsable_foto = $imagePath;
            }

            $user->save();
            $responsable->save();

            return response()->json(['message' => 'Responsable creado exitosamente', 'data' => $responsable], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al crear responsable', 'message' => $e->getMessage()], 500);
        }
    }


    public function show($id)
    {
        $user = User::find($id);
        $responsable = Responsable::where('responsable_usuario', $user->email)->first();

        if (!$responsable) {
            return response()->json(['error' => 'Responsable no encontrado'], 404);
        }

        return response()->json(['data' => $responsable], 200);
    }

    /**
     * Update the specified responsable in storage.
     */
    public function update(Request $request, Responsable $responsable)
    {
        try {
            $responsable->fill($request->only(['responsable_nombre', 'responsable_usuario', 'responsable_telefono', 'cesi_tutore_id']));

            if ($request->filled('responsable_contraseña')) {
                $responsable->responsable_contraseña = bcrypt($request->responsable_contraseña);
            }

            if ($request->hasFile('responsable_foto')) {
                if ($responsable->responsable_foto) {
                    $this->deletePhoto($responsable->responsable_foto);
                }

                $imagePath = $request->file('responsable_foto')->store('responsables', 'public');
                $responsable->responsable_foto = $imagePath;
            }

            $responsable->responsable_activacion = $request->responsable_activacion;


            $user = User::find('email', $responsable->responsable_usuario);
            $user->name = $request->responsable_nombre;
            $user->email = $request->responsable_usuario;
            if ($request->filled('responsable_contraseña')) {
                $user->password = bcrypt($request->responsable_contraseña);
            }
            $user->role = 'responsable';
            $user->save();

            $responsable->save();

            return response()->json(['message' => 'Responsable actualizado exitosamente', 'data' => $responsable], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al actualizar responsable', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified responsable from storage.
     */
    public function destroy(Responsable $responsable)
    {
        try {

            if ($responsable->responsable_foto) {
                $this->deletePhoto($responsable->responsable_foto);
            }


            $user = User::find($responsable->cesi_responsable_id);
            $user->delete();

            $responsable->delete();

            return response()->json(['message' => 'Responsable eliminado exitosamente'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al eliminar responsable', 'message' => $e->getMessage()], 500);
        }
    }

    public function getSchoolColorsByResponsable($responsableId)
    {
        $user = User::find($responsableId);
        $responsable = Responsable::where('responsable_usuario', $user->email)->first();
        $tutor = Tutor::where('id', $responsable->cesi_tutore_id)->first();

        $ui = UI::where('cesi_escuela_id', $tutor->cesi_escuela_id)->first();
        $escuela = Escuela::find($tutor->cesi_escuela_id)->get()->first();
        $escuelaLogo = $escuela->escuela_logo;
        if (!$ui) {
            return response()->json(['error' => 'Colores de la escuela no encontrados'], 404);
        }

        return response()->json(
            ['ui_color1' => $ui->ui_color1, 'ui_color2' => $ui->ui_color2, 'ui_color3' => $ui->ui_color3, 'escuela_logo' => $escuelaLogo]

        );
    }


    /**
     * Delete the photo from storage.
     */
    protected function deletePhoto($photoPath)
    {
        $fullPath = public_path('storage/' . $photoPath);
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
    }
}
