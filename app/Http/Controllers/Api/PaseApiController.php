<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Alumno;
use App\Models\Asistencia;
use App\Models\Lista;
use App\Models\Maestro;
use App\Models\Pase;
use App\Models\User;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;

/**
 * Archivo: PaseApiController.php
 * Propósito: Controlador para gestionar datos relacionados con pases.
 * Autor: José Balam González Rojas
 * Fecha de Creación: 2024-11-19
 * Última Modificación: 2024-12-03
 */

class PaseApiController extends Controller
{
    /**
     * Método para generar la lista de asistencia en formato PDF
     */
    public function generarListaDeAsistencia($asistenciaId, $maestroId)
    {
        $asistencia = Asistencia::findOrFail($asistenciaId);
        $admin = User::find($maestroId);
        $maestro = Maestro::where('maestro_usuario', $admin->email)->first();
        $pases = Pase::where('cesi_asistencia_id', $asistenciaId)
            ->where('pase_status', 'presente')
            ->get();

        $html = '
            <!DOCTYPE html>
            <html lang="es">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Lista de Asistencia</title>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        margin: 0;
                        padding: 0;
                        background-color: #f4f4f4;
                        color: #333;
                    }

                    .container {
                        width: 80%;
                        margin: 30px auto;
                        background-color: #fff;
                        padding: 20px;
                        border-radius: 8px;
                        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                    }

                    h1 {
                        text-align: center;
                        color: #4CAF50;
                    }

                    p {
                        font-size: 16px;
                        margin-bottom: 10px;
                    }

                    table {
                        width: 100%;
                        border-collapse: collapse;
                        margin-top: 20px;
                        text-align: left;
                    }

                    th, td {
                        padding: 12px;
                        border: 1px solid #ddd;
                    }

                    th {
                        background-color: #f2f2f2;
                        font-weight: bold;
                    }

                    tr:nth-child(even) {
                        background-color: #f9f9f9;
                    }

                    tr:hover {
                        background-color: #f1f1f1;
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
                    <h1>Lista de Asistencia - ' . $asistencia->asistencia_fecha . '</h1>
                    <p><strong>Hora:</strong> ' . $asistencia->asistencia_hora . '</p>
                    <p><strong>Maestro:</strong> ' . $maestro->maestro_nombre . '</p>

                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nombre del Alumno</th>
                                <th>Estatus</th>
                            </tr>
                        </thead>
                        <tbody>';

        foreach ($pases as $index => $pase) {
            $alumno = Alumno::find($pase->cesi_alumno_id);

            $html .= '
                            <tr>
                                <td>' . ($index + 1) . '</td>
                                <td>' . $alumno->alumno_nombre . '</td>
                                <td>' . $pase->pase_status . '</td>
                            </tr>';
        }

        $html .= '
                        </tbody>
                    </table>

                    <div class="footer">
                        <p>Reporte generado por el sistema de asistencia.</p>
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

        $directory = storage_path('app/public/listas');
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true); // Crear el directorio con permisos
        }

        $fileName = 'asistencia_' . $asistenciaId . '.pdf';
        $path = $directory . '/' . $fileName;

        file_put_contents($path, $output);

        $lista = new Lista();
        $lista->listas_pdf = 'storage/listas/' . $fileName;
        $lista->cesi_maestro_id = $maestro->id;
        $lista->save();

        return response()->json([
            'success' => true,
            'message' => 'Lista de asistencia generada correctamente.',
            'pdf_url' => url('storage/listas/' . $fileName)
        ], 200);
    }

    /**
     * Método para mostrar las listas generadas en pdf
     */

    public function mostrarListasPorTutor($maestroId)
    {
        $admin = User::find($maestroId);
        $maestro = Maestro::where('maestro_usuario', $admin->email)->first();
        $listas = Lista::where('cesi_maestro_id', $maestro->id)->get();


        if ($listas->isEmpty()) {
            return response()->json(['message' => 'No hay listas registradas para este tutor.'], 404);
        }
        return response()->json([
            'success' => true,
            'data' => $listas,
        ], 200);
    }

    /**
     * Método para mostrar el formulario de pase de lista
     */
    public function mostrarPaseDeAsistencia($asistenciaId, $maestroId)
    {
        $asistencia = Asistencia::findOrFail($asistenciaId);
        $admin = User::find($maestroId);
        $maestro = Maestro::where('maestro_usuario', $admin->email)->first();
        $alumnos = $maestro->salones->alumno;

        return response()->json([
            'asistencia' => $asistencia,
            'alumnos' => $alumnos,
            'maestro' => $maestro
        ], 200);
    }


    /**
     * Método para registrar el pase de asistencia
     */
    public function registrarPaseDeAsistencia(Request $request, $idMaestro)
    {
        /*$request->validate([
            'alumnos' => 'required|array',
            'alumnos.*.id' => 'exists:cesi_alumnos,id',
            'alumnos.*.' => 'required|string|in:presente,ausente',
        ]);*/

        $admin = User::find($idMaestro);
        $maestro = Maestro::where('maestro_usuario', $admin->email)->first();
        $asistencia = Asistencia::create([
            'asistencia_fecha' => now()->toDateString(),
            'asistencia_hora' => now()->toTimeString(),
            'cesi_maestro_id' => $maestro->id,
        ]);

        $pases = [];
        foreach ($request->alumnos as $alumno) {
            $pases[] = Pase::create([
                'cesi_alumno_id' => $alumno['id'],
                'cesi_asistencia_id' => $asistencia->id,
                'pase_status' => $alumno['status'],
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Pase de lista registrado correctamente.',
            'asistencia' => $asistencia,
            'pases' => $pases
        ], 200);
    }
}
