<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Recogida;
use App\Models\Alumno;
use App\Models\Reporte;
use App\Models\Responsable;
use App\Models\Tutor;
use App\Models\User;
use Barryvdh\DomPDF\Facade\PDF;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Archivo: RecogidaApiController.php
 * Propósito: Controlador para gestionar datos relacionados con recogidas.
 * Autor: José Balam González Rojas
 * Fecha de Creación: 2024-11-19
 * Última Modificación: 2024-12-02
 */
class RecogidaApiController extends Controller
{
    /**
     * Obtener los alumnos disponibles para recogida por tutor en la fecha actual.
     * Este método busca a los alumnos asignados a un tutor que no hayan sido recogidos
     * en la fecha actual y estén presentes en la asistencia.
     */
    public function alumnosSinRecogida($idTutor)
    {
        $user = User::find($idTutor);
        $responsable = Responsable::where('responsable_usuario', $user->email)->firstOrFail();
        $tutor = Tutor::where('id', $responsable->cesi_tutore_id)->firstOrFail();
        if (!$tutor) {
            return response()->json(['message' => 'Tutor no encontrado'], 404);
        }

        $hoy = now()->toDateString();

        $alumnos = DB::table('cesi_alumnos')
            ->where('cesi_alumnos.cesi_tutore_id', $tutor->id)
            ->whereNotExists(function ($query) use ($hoy) {
                $query->select(DB::raw(1))
                    ->from('cesi_recogidas')
                    ->join('cesi_escogidos', 'cesi_recogidas.id', '=', 'cesi_escogidos.cesi_recogida_id')
                    ->whereColumn('cesi_alumnos.id', 'cesi_escogidos.cesi_alumno_id')
                    ->whereDate('cesi_recogidas.recogida_fecha', $hoy);
            })
            ->join('cesi_pases', 'cesi_alumnos.id', '=', 'cesi_pases.cesi_alumno_id')
            ->join('cesi_asistencias', 'cesi_pases.cesi_asistencia_id', '=', 'cesi_asistencias.id')
            ->whereDate('cesi_asistencias.asistencia_fecha', $hoy)
            ->where('cesi_pases.pase_estatus', 'presente')
            ->select('cesi_alumnos.*')
            ->get();

        if ($alumnos->isEmpty()) {
            return response()->json(['message' => 'No hay alumnos disponibles para recogida'], 200);
        }

        return response()->json(['data' => $alumnos], 200);
    }

    /**
     * Crear una nueva recogida de alumnos para un responsable en una fecha específica.
     * Este método valida los datos de la solicitud, filtra los alumnos que no han sido
     * recogidos en la fecha indicada y genera una nueva recogida con los alumnos seleccionados.
     */

    public function generarRecogida(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            $responsable = Responsable::where('responsable_usuario', $user->email)->firstOrFail();
            $tutor = Tutor::where('id', $responsable->cesi_tutore_id)->firstOrFail();

            $hoy = now()->toDateString();

            $alumnosSinRecogida = DB::table('cesi_alumnos')
                ->where('cesi_alumnos.cesi_tutore_id', $tutor->id)
                ->whereNotExists(function ($query) use ($hoy) {
                    $query->select(DB::raw(1))
                        ->from('cesi_recogidas')
                        ->join('cesi_escogidos', 'cesi_recogidas.id', '=', 'cesi_escogidos.cesi_recogida_id')
                        ->whereColumn('cesi_alumnos.id', 'cesi_escogidos.cesi_alumno_id')
                        ->whereDate('cesi_recogidas.recogida_fecha', $hoy);
                })
                ->join('cesi_pases', 'cesi_alumnos.id', '=', 'cesi_pases.cesi_alumno_id')
                ->join('cesi_asistencias', 'cesi_pases.cesi_asistencia_id', '=', 'cesi_asistencias.id')
                ->whereDate('cesi_asistencias.asistencia_fecha', $hoy)
                ->where('cesi_pases.pase_status', 'presente')
                ->select('cesi_alumnos.*')
                ->get();

            if ($alumnosSinRecogida->isEmpty()) {
                return response()->json(['message' => 'No hay alumnos disponibles para recogida en esta fecha.'], 400);
            }

            if ($request->hasFile('recogida_qr') && $request->file('recogida_qr')->isValid()) {
                $imageData = $request->input('recogida_qr');
                $imageParts = explode(',', $imageData);
                $decodedImage = base64_decode(end($imageParts));
                $imagePath = 'recogidas_imagenes/' . uniqid() . '.png';
                Storage::disk('public')->put($imagePath, $decodedImage);
            } else {
                $imagePath = null;
            }


            $recogida = Recogida::create([
                'recogida_fecha' => $request->recogida_fecha,
                'recogida_observaciones' => $request->recogida_observaciones,
                'recogida_estatus' => 'pendiente',
                'cesi_responsable_id' => $responsable->id,
                'recogida_qr' => $imagePath,
            ]);
            $recogida->alumnos()->attach($alumnosSinRecogida->pluck('id'));

            return response()->json([
                'message' => 'Recogida creada correctamente',
                'data' => [
                    'recogida' => $recogida,
                    'alumnos_asociados' => $alumnosSinRecogida,
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al generar recogida',
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Crear una nueva recogida de alumnos para un tutor en una fecha específica.
     * Este método valida los datos de la solicitud, filtra los alumnos que no han sido
     * recogidos en la fecha indicada y genera una nueva recogida con los alumnos seleccionados.
     */

    public function generarRecogidaTutor(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            $tutor = Tutor::where('tutor_usuario', $user->email)->firstOrFail();
            $responsable = Responsable::where('cesi_tutore_id', $tutor->id)->firstOrFail();
            $hoy = now()->toDateString();

            $alumnosSinRecogida = DB::table('cesi_alumnos')
                ->where('cesi_alumnos.cesi_tutore_id', $tutor->id)
                ->whereNotExists(function ($query) use ($hoy) {
                    $query->select(DB::raw(1))
                        ->from('cesi_recogidas')
                        ->join('cesi_escogidos', 'cesi_recogidas.id', '=', 'cesi_escogidos.cesi_recogida_id')
                        ->whereColumn('cesi_alumnos.id', 'cesi_escogidos.cesi_alumno_id')
                        ->whereDate('cesi_recogidas.recogida_fecha', $hoy);
                })
                ->join('cesi_pases', 'cesi_alumnos.id', '=', 'cesi_pases.cesi_alumno_id')
                ->join('cesi_asistencias', 'cesi_pases.cesi_asistencia_id', '=', 'cesi_asistencias.id')
                ->whereDate('cesi_asistencias.asistencia_fecha', $hoy)
                ->where('cesi_pases.pase_status', 'presente')
                ->select('cesi_alumnos.*')
                ->get();

            if ($alumnosSinRecogida->isEmpty()) {
                return response()->json(['message' => 'No hay alumnos disponibles para recogida en esta fecha.'], 400);
            }

            if ($request->hasFile('recogida_qr') && $request->file('recogida_qr')->isValid()) {
                $imageData = $request->input('recogida_qr');
                $imageParts = explode(',', $imageData);
                $decodedImage = base64_decode(end($imageParts));
                $imagePath = 'recogidas_imagenes/' . uniqid() . '.png';
                Storage::disk('public')->put($imagePath, $decodedImage);
            } else {
                $imagePath = null;
            }


            $recogida = Recogida::create([
                'recogida_fecha' => $request->recogida_fecha,
                'recogida_observaciones' => $request->recogida_observaciones,
                'recogida_estatus' => 'pendiente',
                'cesi_responsable_id' => $responsable->id,
                'recogida_qr' => $imagePath,
            ]);
            $recogida->alumnos()->attach($alumnosSinRecogida->pluck('id'));

            return response()->json([
                'message' => 'Recogida creada correctamente',
                'data' => [
                    'recogida' => $recogida,
                    'alumnos_asociados' => $alumnosSinRecogida,
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al generar recogida',
                'message' => $e->getMessage(),
            ], 500);
        }
    }



    /**
     * Obtener las recogidas asociadas a los alumnos de un responsable.
     * Este método devuelve todas las recogidas registradas para los alumnos de un tutor específico.
     */
    public function recogidasPorResponsable($idResponsable)
    {
        // Buscar al usuario y al responsable relacionado
        $user = User::findOrFail($idResponsable);
        $responsable = Responsable::where('responsable_usuario', $user->email)->firstOrFail();

        $recogidas = Recogida::where('cesi_responsable_id', $responsable->id)
            ->with('alumnos')
            ->get();

        if ($recogidas->isEmpty()) {
            return response()->json(['message' => 'No hay recogidas registradas para este responsable'], 200);
        }

        return response()->json(['data' => $recogidas], 200);
    }

    /**
     * Obtener las recogidas asociadas a los alumnos de un tutor.
     * Este método devuelve todas las recogidas registradas para los alumnos de un tutor específico.
     */
    public function recogidasPorTutor($idTutor)
    {
        $user = User::find($idTutor);
        $tutor = Tutor::where('tutor_usuario', $user->email)->firstOrFail();
        $responsable = Responsable::where('cesi_tutore_id', $tutor->id)->firstOrFail();
        $recogidas = Recogida::where('cesi_responsable_id', $responsable->id)
            ->with('alumnos')
            ->get();

        if ($recogidas->isEmpty()) {
            return response()->json(['message' => 'No hay recogidas registradas para este tutor'], 200);
        }

        return response()->json(['data' => $recogidas], 200);
    }

    /**
     * Obtener las recogidas por estatus.
     * Este método permite filtrar las recogidas por su estatus (pendiente, completa, o cancelada).
     */
    public function recogidasPorEstatus(Request $request)
    {

        $validated = $request->validate([
            'estatus' => 'required|in:pendiente,completa,cancelada',
        ]);

        $recogidas = Recogida::where('recogida_estatus', $validated['estatus'])
            ->with('alumnos')
            ->get();
        if ($recogidas->isEmpty()) {
            return response()->json(['message' => 'No hay recogidas con el estatus especificado'], 200);
        }
        return response()->json(['data' => $recogidas], 200);
    }

    /**
     * Generar un reporte en PDF de las recogidas asociadas a un tutor.
     * Este método genera un archivo PDF con la lista de recogidas de los alumnos de un tutor
     * y lo guarda en el almacenamiento.
     */
    public function generarReportePDF($idTutor)
    {
        $user = User::find($idTutor);
        $tutor = Tutor::where('tutor_usuario', $user->email)->first();
        $alumnos = Alumno::where('cesi_tutore_id', $tutor->id)->pluck('id');

        $recogidas = Recogida::whereHas('alumnos', function ($query) use ($alumnos) {
            $query->whereIn('cesi_alumnos.id', $alumnos);
        })->with('alumnos')->get();

        if ($recogidas->isEmpty()) {
            return response()->json(['message' => 'No hay datos de recogidas para generar el reporte'], 200);
        }

        $pdf = PDF::loadView('reportes.recogidas', ['recogidas' => $recogidas]);
        $filePath = 'reportes/' . uniqid('reporte_') . '.pdf';
        $pdf->save(storage_path('app/public/' . $filePath));

        $reporte = Reporte::create([
            'reporte_pdf' => $filePath,
            'cesi_tutore_id' => $idTutor,
        ]);

        return response()->json([
            'message' => 'Reporte generado correctamente',
            'data' => ['url' => asset('storage/' . $filePath)],
        ], 201);
    }

    /**
     * Obtener los reportes generados para un tutor.
     * Este método devuelve todos los reportes generados por un tutor en particular.
     */
    public function reportesPorTutor($idTutor)
    {
        $user = User::find($idTutor);
        $tutor = Tutor::where('tutor_usuario', $user->email)->first();
        $reportes = Reporte::where('cesi_tutore_id', $tutor->id)->get();
        if ($reportes->isEmpty()) {
            return response()->json(['message' => 'No hay reportes registrados para este tutor'], 200);
        }
        return response()->json(['data' => $reportes], 200);
    }
}
