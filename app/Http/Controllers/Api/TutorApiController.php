<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tutor;
use App\Models\Alumno;
use App\Models\Responsable;
use App\Models\Escuela;
use App\Models\UI;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
/**
 * Archivo: TutorApiController.php
 * Propósito: Controlador para gestionar datos relacionados con tutor.
 * Autor: José Balam González Rojas
 * Fecha de Creación: 2024-11-27
 * Última Modificación: 2024-11-27
 */
class TutorApiController extends Controller
{
    /**
     * Obtener los datos del tutor por su ID
     */
    public function showTutor($id)
    {
        $tutor = Tutor::find($id);

        if (!$tutor) {
            return response()->json(['error' => 'Tutor no encontrado'], 404);
        }

        return response()->json($tutor);
    }

    /**
     * Obtener los alumnos del tutor por su ID
     */
    public function showAlumnosByTutor($id)
    {
        $alumnos = Alumno::where('cesi_tutore_id', $id)->get();

        return response()->json($alumnos);
    }

    /**
     * Obtener los datos del alumno por su ID
     */
    public function showAlumno($id)
    {
        $alumno = Alumno::find($id);

        if (!$alumno) {
            return response()->json(['error' => 'Alumno no encontrado'], 404);
        }

        return response()->json($alumno);
    }

    /**
     * Obtener los colores de la escuela relacionados al tutor
     */
    public function showEscuelaColores($id)
    {
        $ui = DB::table('cesi_tutores AS T')
            ->join('cesi_alumnos AS A', 'A.cesi_tutore_id', '=', 'T.id')
            ->join('cesi_grupos AS G', 'A.cesi_grupo_id', '=', 'G.id')
            ->join('cesi_escuelas AS E', 'G.cesi_escuela_id', '=', 'E.id')
            ->join('cesi_uis AS U', 'U.cesi_escuela_id', '=', 'E.id')
            ->where('T.id', '=', $id)
            ->select('U.ui_color1', 'U.ui_color2', 'U.ui_color3', 'U.logo')
            ->first();

        if (!$ui) {
            return response()->json(['error' => 'Colores de la escuela no encontrados'], 404);
        }

        return response()->json($ui);
    }

    /**
     * Obtener los responsables del tutor, excluyendo el propio tutor
     */
    public function showResponsablesByTutor($id)
    {
        $responsables = Responsable::where('cesi_tutore_id', $id)
            ->where('tutor_usuario', '!=', function ($query) use ($id) {
                $query->select('tutor_usuario')->from('cesi_tutores')->where('id', $id);
            })
            ->get();

        return response()->json($responsables);
    }

    /**
     * Obtener un responsable por su ID
     */
    public function showResponsable($id)
    {
        $responsable = Responsable::find($id);

        if (!$responsable) {
            return response()->json(['error' => 'Responsable no encontrado'], 404);
        }

        return response()->json($responsable);
    }

    /**
     * Actualizar la foto del tutor por su ID
     */
    public function updateFoto(Request $request, $id)
    {
        $request->validate([
            'tutor_foto' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        $tutor = Tutor::find($id);

        if (!$tutor) {
            return response()->json(['error' => 'Tutor no encontrado'], 404);
        }
        if ($request->hasFile('tutor_foto') && $request->file('tutor_foto')->isValid()) {
            if ($tutor->tutor_foto) {
                Storage::delete('public/' . $tutor->tutor_foto);
            }
            $fotoPath = $request->file('tutor_foto')->store('tutores', 'public');
            $tutor->tutor_foto = $fotoPath;
            $tutor->save();
        }

        return response()->json(['success' => 'Foto actualizada exitosamente']);
    }
}
