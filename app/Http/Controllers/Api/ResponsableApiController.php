<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Responsable;
use App\Models\User;
use App\Models\Escuela;
use App\Models\Tutor;
use App\Models\UI;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Archivo: ResponsableApiController.php
 * Propósito: Controlador para gestionar datos relacionados con responsables.
 * Autor: José Balam González Rojas
 * Fecha de Creación: 2024-11-19
 * Última Modificación: 2024-12-03
 */

class ResponsableApiController extends Controller
{

    /**
     * Store a newly created responsable in storage.
     */
    public function store(Request $request, $id)
    {
        try {
            $user = User::find($id);
            $tutor = Tutor::where('tutor_usuario', $user->email)->first();
            $responsable = new Responsable();
            $responsable->fill($request->only(['responsable_nombre', 'responsable_usuario', 'responsable_telefono']));
            $responsable->responsable_contraseña = bcrypt($request->responsable_contraseña);
            $responsable->responsable_activacion = 0;
            $responsable->cesi_tutore_id = $tutor->id;
            $user2 = new User();
            $user2->name = $request->responsable_nombre;
            $user2->email = $request->responsable_usuario;
            $user2->password = bcrypt($request->responsable_contraseña);
            $user2->role = 'responsable';



            if ($request->hasFile('responsable_foto')) {
                $imagePath = $request->file('responsable_foto')->store('responsables', 'public');
                $responsable->responsable_foto = $imagePath;
            }

            $user2->save();
            $responsable->save();

            return response()->json(['message' => 'Responsable creado exitosamente', 'data' => $responsable], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al crear responsable', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Mostrar el recurso especificado.
     */
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
     * Actualizar la foto del responsable por su ID
     */
    public function updateFoto(Request $request, $id)
    {
        $user = User::find($id);
        $responsable = Responsable::where('responsable_usuario', $user->email)->first();

        if (!$responsable) {
            return response()->json(['error' => 'Responsable no encontrado'], 404);
        }
        if ($request->hasFile('responsable_foto') && $request->file('responsable_foto')->isValid()) {
            if ($responsable->responsable_foto) {
                Storage::delete('public/' . $responsable->responsable_foto);
            }
            $fotoPath = $request->file('responsable_foto')->store('responsables', 'public');
            $responsable->responsable_foto = $fotoPath;
            $responsable->save();
            return response()->json(['success' => 'Foto actualizada exitosamente']);
        }
        return response()->json(['error' => 'No se actualizo la foto']);
    }


    /**
     * Remove the specified responsable from storage.
     */
    public function destroy($responsableId)
    {
        try {
            $user = User::find($responsableId);
            $responsable = Responsable::where('responsable_usuario', $user->email)->first();


            if ($responsable->responsable_foto) {
                $this->deletePhoto($responsable->responsable_foto);
            }
            $user = User::where('email', $responsable->responsable_usuario)->first();;
            $user->delete();
            $responsable->delete();

            return response()->json(['message' => 'Responsable eliminado exitosamente'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al eliminar responsable', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Obtener los colores de la escuela asociada al responsable.
     */

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
