<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tutor;
use App\Models\Alumno;
use App\Models\Escuela;
use App\Models\Responsable;
use App\Models\UI;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Archivo: TutorApiController.php
 * Propósito: Controlador para gestionar datos relacionados con tutor.
 * Autor: José Balam González Rojas
 * Fecha de Creación: 2024-11-27
 * Última Modificación: 2024-12-04
 */
class TutorApiController extends Controller
{
    /**
     * Obtener los datos del tutor por su ID
     */
    public function showTutor($id)
    {
        $user = User::find($id);
        $tutor = Tutor::where('tutor_usuario', $user->email)->first();

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
        $user = User::find($id);
        $tutor = Tutor::where('tutor_usuario', $user->email)->first();
        $alumnos = Alumno::where('cesi_tutore_id', $tutor->id)->get();

        return response()->json($alumnos);
    }

    /**
     * Obtener los datos del alumno por su ID
     */
    public function showAlumno($tutorId, $id)
    {
        $user = User::find($tutorId);
        $tutor = Tutor::where('tutor_usuario', $user->email)->first();
        $idTutor = $tutor->id;
        $alumno = Alumno::with([
            'tutores',
            'salones',
            'salones.escuelas',
            'salones.maestros',
        ])
            ->where('id', $id)
            ->where('cesi_tutore_id', $idTutor)
            ->first();

        if (!$alumno) {
            return response()->json(['error' => 'Alumno no encontrado o no autorizado'], 404);
        }

        return response()->json([
            'alumno' => $alumno,
            'tutor' => $alumno->tutores,
            'salon' => $alumno->salones,
            'escuela' => $alumno->salones->escuelas->escuela_nombre,
            'maestro' => $alumno->salones->maestros->maestro_nombre,
        ]);
    }


    /**
     * Obtener los colores de la escuela relacionados al tutor
     */
    public function showEscuelaColores($id)
    {
        $user = User::find($id);
        $tutor = Tutor::where('tutor_usuario', $user->email)->first();

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
     * Obtener los responsables del tutor, excluyendo el propio tutor
     */
    public function showResponsablesByTutor($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }

        $tutor = Tutor::where('tutor_usuario', $user->email)->first();
        if (!$tutor) {
            return response()->json(['error' => 'Tutor no encontrado'], 404);
        }

        $responsables = Responsable::where('cesi_tutore_id', $tutor->id)
            ->where('responsable_nombre', '!=', $tutor->tutor_nombre)
            ->where('responsable_usuario', '!=', $tutor->tutor_usuario)
            ->get();
        if ($responsables->isEmpty()) {
            return response()->json(['message' => 'No hay responsables distintos al tutor.'], 200);
        }

        return response()->json(['responsables' => $responsables], 200);
    }
    /**
     * Obtener un responsable por su ID
     */
    public function showResponsable($tutorId, $id)
    {
        $user = User::find($tutorId);
        $tutor = Tutor::where('tutor_usuario', $user->email)->first();
        $idTutor = $tutor->id;
        $responsable = Responsable::with([
            'tutores'
        ])
            ->where('id', $id)
            ->where('cesi_tutore_id', $idTutor)
            ->first();

        if (!$responsable) {
            return response()->json(['error' => 'Responsable no encontrado o no autorizado'], 404);
        }

        return response()->json([
            'responsables' => $responsable,
            'tutores' => $responsable->tutor,
        ]);
    }

    /**
     * Actualizar la foto del tutor por su ID
     */
    public function updateFoto(Request $request, $id)
    {
        $user = User::find($id);
        $tutor = Tutor::where('tutor_usuario', $user->email)->first();

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
            return response()->json(['success' => 'Foto actualizada exitosamente']);
        }
        return response()->json(['error' => 'No se actualizo la foto']);
    }
}
