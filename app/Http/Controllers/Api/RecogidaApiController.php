<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Recogida;
use App\Models\Alumno;
use App\Models\Maestro;
use App\Models\Rastreo;
use App\Models\Reporte;
use App\Models\Responsable;
use App\Models\Salon;
use App\Models\Tutor;
use App\Models\User;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Archivo: RecogidaApiController.php
 * Propósito: Controlador para gestionar datos relacionados con recogidas.
 * Autor: José Balam González Rojas
 * Fecha de Creación: 2024-11-19
 * Última Modificación: 2024-12-04
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
            ->where('cesi_pases.pase_status', 'presente')
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
                'recogida_fecha' => now()->toDateString(),
                'recogida_observaciones' => $request->recogida_observaciones ?? 'No hay observación',
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
        $user = User::find($idResponsable);
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
     * Obtener las recogidas por estatus.
     * Este método permite filtrar las recogidas por su estatus (pendiente, completa, o cancelada).
     */
    public function recogidasPorEstatus(Request $request, $maestroId)
    {
        $user = User::find($maestroId);
        $maestro = Maestro::where('maestro_usuario', $user->email)->first();

        if (!$maestro) {
            return response()->json(['message' => 'El maestro no existe'], 404);
        }

        $validated = $request->validate([
            'estatus' => 'required|in:pendiente,completa,cancelada',
        ]);

        $salon = Salon::where('cesi_maestro_id', $maestro->id)->first();

        if (!$salon) {
            return response()->json(['message' => 'No se encontró el salón del maestro'], 404);
        }

        $alumnosIds = Alumno::where('cesi_salon_id', $salon->id)->pluck('id');
        $recogidas = Recogida::where('recogida_estatus', $validated['estatus'])
            ->whereHas('alumnos', function ($query) use ($alumnosIds) {
                $query->whereIn('cesi_alumnos.id', $alumnosIds);
            })
            ->get();

        if ($recogidas->isEmpty()) {
            return response()->json(['message' => 'No hay recogidas con el estatus especificado para los alumnos del salón del maestro'], 200);
        }

        return response()->json(['data' => $recogidas], 200);
    }


    /**
     * Obtiene las recogidas asociadas a los alumnos del salón de un maestro.
     *
     */
    public function recogidasDeMaestro(Request $request, $maestroId)
    {
        $user = User::find($maestroId);
        $maestro = Maestro::where('maestro_usuario', $user->email)->first();

        if (!$maestro) {
            return response()->json(['message' => 'El maestro no existe'], 404);
        }

        $salon = Salon::where('cesi_maestro_id', $maestro->id)->first();

        if (!$salon) {
            return response()->json(['message' => 'No se encontró el salón del maestro'], 404);
        }

        $alumnosIds = Alumno::where('cesi_salon_id', $salon->id)->pluck('id');
        $recogidas = Recogida::whereHas('alumnos', function ($query) use ($alumnosIds) {
            $query->whereIn('cesi_alumnos.id', $alumnosIds);
        })
            ->with('alumnos', 'responsables.usuario') // Incluye la relación 'usuario' para obtener datos del User
            ->get();

        if ($recogidas->isEmpty()) {
            return response()->json(['message' => 'No hay recogidas registradas para los alumnos del salón del maestro'], 200);
        }
        $recogidasData = $recogidas->map(function ($recogida) {
            $responsable = Responsable::find($recogida->cesi_responsable_id);
            $usuarioResponsable = User::where('email', $responsable->responsable_usuario)->first();

            return  $recogida;
        });

        return response()->json([
            'maestro' => [
                'nombre' => $maestro->maestro_nombre,
                'email' => $maestro->maestro_usuario,
            ],
            'recogidas' => $recogidasData,
        ], 200);
    }


    /**
     * Generar un reporte en PDF de las recogidas asociadas a un tutor.
     * Este método genera un archivo PDF con la lista de recogidas de los alumnos de un tutor
     * y lo guarda en el almacenamiento.
     */


    public function generarReportePDF($idTutor)
    {
        $tutor = Tutor::find($idTutor);
        $alumnos = Alumno::where('cesi_tutore_id', $tutor->id)->pluck('id');

        if (!$tutor) {
            return response()->json(['message' => 'El tutor no existe.'], 400);
        }
        $reporteExistente = Reporte::where('cesi_tutore_id', $tutor->id)
            ->whereDate('created_at', now()->toDateString())
            ->first();

        if ($reporteExistente) {
            return response()->json(['message' => 'Ya se ha generado un reporte para este tutor hoy.'], 400);
        }

        $recogidas = Recogida::whereHas('alumnos', function ($query) use ($alumnos) {
            $query->whereIn('cesi_alumnos.id', $alumnos);
        })->with('alumnos')->get();

        if ($recogidas->isEmpty()) {
            return response()->json(['message' => 'No hay datos de recogidas para generar el reporte'], 200);
        }
        $html = '<!DOCTYPE html>
         <html lang="es">
         <head>
             <meta charset="UTF-8">
             <meta name="viewport" content="width=device-width, initial-scale=1.0">
             <title>Reporte de Recogidas</title>
             <style>
                 body {
                     font-family: Arial, sans-serif;
                     margin: 0;
                     padding: 0;
                     background-color: #f4f4f4;
                     color: #333;
                 }

                 .container {
                     width: 100%;
                     max-width: 800px;
                     margin: 20px auto;
                     background-color: #fff;
                     padding: 20px;
                     border-radius: 8px;
                     box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                 }

                 h1 {
                     text-align: center;
                     color: #4CAF50;
                 }

                 .recogida-details {
                     margin-bottom: 20px;
                 }

                 .recogida-details table {
                     width: 100%;
                     border-collapse: collapse;
                     margin: 20px 0;
                 }

                 .recogida-details th,
                 .recogida-details td {
                     padding: 10px;
                     text-align: left;
                     border: 1px solid #ddd;
                 }

                 .recogida-details th {
                     background-color: #f2f2f2;
                     font-weight: bold;
                 }

                 .footer {
                     text-align: center;
                     margin-top: 40px;
                     font-size: 12px;
                     color: #777;
                 }
             </style>
         </head>
         <body>
             <div class="container">
                 <h1>Reporte de Recogidas de Alumnos</h1>
                 <p><strong>Fecha de reporte:</strong> ' . now()->toDateString() . '</p>';

        foreach ($recogidas as $recogida) {
            $html .= '
                 <div class="recogida-details">
                     <h2>Recogida del ' . $recogida->recogida_fecha . '</h2>
                     <p><strong>Responsable:</strong> ' . $recogida->responsables->responsable_nombre . '</p>
                     <p><strong>Observaciones:</strong> ' . $recogida->recogida_observaciones . '</p>
                     <p><strong>Estatus:</strong> ' . $recogida->recogida_estatus . '</p>

                     <h3>Alumnos Recogidos</h3>
                     <table>
                         <thead>
                             <tr>
                                 <th>Nombre del Alumno</th>
                                 <th>Estado de Recogida</th>
                             </tr>
                         </thead>
                         <tbody>';

            foreach ($recogida->alumnos as $alumno) {
                $html .= '
                             <tr>
                                 <td>' . $alumno->alumno_nombre . '</td>
                                 <td>' . $recogida->recogida_estatus . '</td>
                             </tr>';
            }

            $html .= '
                         </tbody>
                     </table>
                 </div>';
        }

        $html .= '
                 <div class="footer">
                     <p>Reporte generado por el sistema de recogidas.</p>
                 </div>
             </div>
         </body>
         </html>';
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $output = $dompdf->output();

        $directory = storage_path('app/public/reportes');
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }
        $fileName = 'reporte_' . now()->toDateString() . '_tutor_' . $tutor->id . '.pdf';
        $path = $directory . '/' . $fileName;

        file_put_contents($path, $output);

        $reporte = Reporte::create([
            'reporte_pdf' => 'storage/reportes/' . $fileName,
            'cesi_tutore_id' => $tutor->id,
        ]);

        return response()->json([
            'message' => 'Reporte generado correctamente',
            'data' => ['url' => asset('storage/reportes/' . $fileName)],
        ], 201);
    }




    /**
     * Obtener los reportes generados para un tutor.
     * Este método devuelve todos los reportes generados por un tutor en particular.
     */
    public function reportesPorTutor($idTutor)
    {
        $user = User::find($idTutor);
        if (!$user) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }

        $tutor = Tutor::where('tutor_usuario', $user->email)->with('reportes')->first();
        if (!$tutor || $tutor->reportes->isEmpty()) {
            return response()->json(['message' => 'No hay reportes registrados para este tutor'], 200);
        }

        return response()->json(['data' => $tutor->reportes], 200);
    }
    /*
     * Método para actualizar el estatus de una recogida
     */

    public function actualizarEstatusRecogida(Request $request, $recogidaId)
    {
        $validated = $request->validate([
            'estatus' => 'required|in:pendiente,completa,cancelada',
        ]);

        $recogida = Recogida::find($recogidaId);

        if (!$recogida) {
            return response()->json(['message' => 'Recogida no encontrada'], 404);
        }

        $recogida->recogida_estatus = $validated['estatus'];
        $recogida->save();

        return response()->json(['message' => 'Estatus de la recogida actualizado correctamente', 'data' => $recogida], 200);
    }

    /*
     * Método para completar una recogida
     */

    public function completarRecogida($recogidaId)
    {
        try {
            $recogida = Recogida::findOrFail($recogidaId);
            $responsable = Responsable::where('id', $recogida->cesi_responsable_id)->first();
            $tutor = Tutor::where('id', $responsable->cesi_tutore_id)->first();
            $recogida->update([
                'recogida_estatus' => 'completa',
            ]);
            Rastreo::where('cesi_recogida_id', $recogida->id)->delete();

            return response()->json([
                'message' => 'Recogida marcada como completa y ubicación cancelada',
                'tutor_id' => $tutor->id,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al completar la recogida',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /*
    * Método para actualizar la observación de una recogida
    */
    public function actualizarObservacionRecogida(Request $request, $recogidaId)
    {
        $validated = $request->validate([
            'observacion' => 'required|string|max:255',
        ]);

        $recogida = Recogida::find($recogidaId);

        if (!$recogida) {
            return response()->json(['message' => 'Recogida no encontrada'], 404);
        }
        $recogida->recogida_observaciones = $validated['observacion'];
        $recogida->save();

        return response()->json(['message' => 'Observación de la recogida actualizada correctamente', 'data' => $recogida], 200);
    }
}
