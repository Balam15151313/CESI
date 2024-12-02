<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asistencia;
use App\Models\Lista;
use App\Models\Maestro;
use Illuminate\Support\Facades\Auth;
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
 * Última Modificación: 2024-12-02
 */

class PaseApiController extends Controller
{
    /**
     * Método para generar la lista de asistencia en formato PDF
     */
    public function generarListaDeAsistencia($asistenciaId)
    {
        $asistencia = Asistencia::findOrFail($asistenciaId);
        $admin = User::find(Auth::id());
        $maestro = Maestro::where('maestro_usuario', $admin->email)->first();
        $pases = Pase::where('cesi_asistencia_id', $asistenciaId)
            ->where('pase_status', 'presente')
            ->get();
        $html = '<h1>Lista de Asistencia - ' . $asistencia->asistencia_fecha . '</h1>';
        $html .= '<p>Hora: ' . $asistencia->asistencia_hora . '</p>';
        $html .= '<p>Maestro: ' . $maestro->maestro_nombre . '</p>';
        $html .= '<table border="1" cellpadding="5" cellspacing="0" style="width:100%; margin-top: 20px;">';
        $html .= '<thead><tr><th>#</th><th>Nombre del Alumno</th><th></th></tr></thead>';
        $html .= '<tbody>';

        foreach ($pases as $index => $pase) {
            $alumno = $pase->alumno;
            $html .= '<tr><td>' . ($index + 1) . '</td><td>' . $alumno->nombre . '</td><td>' . $pase->pase_status . '</td></tr>';
        }

        $html .= '</tbody></table>';

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');

        $dompdf->render();

        $output = $dompdf->output();

        $fileName = 'asistencia_' . $asistenciaId . '.pdf';
        $path = storage_path('app/public/listas/' . $fileName);

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
     * Método para mostrar el formulario de pase de lista
     */
    public function mostrarPaseDeAsistencia($asistenciaId)
    {
        $asistencia = Asistencia::findOrFail($asistenciaId);
        $admin = User::find(Auth::id());
        $maestro = Maestro::where('maestro_usuario', $admin->email)->first();
        $alumnos = $maestro->alumnos;

        return response()->json([
            'asistencia' => $asistencia,
            'alumnos' => $alumnos,
            'maestro' => $maestro
        ], 200);
    }


    /**
     * Método para registrar el pase de asistencia
     */
    public function registrarPaseDeAsistencia(Request $request)
    {
        $request->validate([
            'alumnos' => 'required|array',
            'alumnos.*.id' => 'exists:alumnos,id',
            'alumnos.*.' => 'required|string|in:presente,ausente',
        ]);

        $admin = User::find(Auth::id());
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
                'cesi_maestro_id' => $maestro->id,
                'pase_status' => $alumno[''],
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
