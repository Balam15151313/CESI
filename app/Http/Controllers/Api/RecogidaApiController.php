<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Recogida;
use App\Models\Alumno;
use App\Models\Reporte;
use PDF;
/**
 * Archivo: RecogidaApiController.php
 * Propósito: Controlador para gestionar datos relacionados con recogidas.
 * Autor: José Balam González Rojas
 * Fecha de Creación: 2024-11-19
 * Última Modificación: 2024-11-27
 */
class RecogidaApiController extends Controller
{
    /**
     * Seleccionar alumnos del tutor disponibles para recogida.
     */
    public function alumnosSinRecogida($idTutor)
    {
        $alumnos = Alumno::where('id_tutor', $idTutor)
            ->whereDoesntHave('recogidas', function ($query) {
                $query->whereDate('recogida_fecha', now()->toDateString());
            })
            ->where('asistencia', true) // Suponiendo que hay una columna 'asistencia'
            ->get();

        if ($alumnos->isEmpty()) {
            return response()->json(['message' => 'No hay alumnos disponibles para recogida'], 200);
        }

        return response()->json(['data' => $alumnos], 200);
    }

    /**
     * Crear una nueva recogida con alumnos.
     */
    public function generarRecogida(Request $request)
    {
        $validated = $request->validate([
            'recogida_fecha' => 'required|date',
            'recogida_observaciones' => 'nullable|string',
            'recogida_estatus' => 'required|in:pendiente,completa,cancelada',
            'id_tutor' => 'required|exists:tutores,id',
            'alumnos' => 'required|array',
            'alumnos.*' => 'exists:alumnos,id',
        ]);

        $recogida = Recogida::create([
            'recogida_fecha' => $validated['recogida_fecha'],
            'recogida_observaciones' => $validated['recogida_observaciones'],
            'recogida_estatus' => $validated['recogida_estatus'],
            'id_tutor' => $validated['id_tutor'],
        ]);

        // Asignar alumnos a la recogida
        $recogida->alumnos()->attach($validated['alumnos']);

        return response()->json(['message' => 'Recogida creada correctamente', 'data' => $recogida], 201);
    }

    /**
     * Obtener las recogidas por tutor.
     */
    public function recogidasPorTutor($idTutor)
    {
        $recogidas = Recogida::where('id_tutor', $idTutor)->with('alumnos')->get();

        if ($recogidas->isEmpty()) {
            return response()->json(['message' => 'No hay recogidas registradas para este tutor'], 200);
        }

        return response()->json(['data' => $recogidas], 200);
    }

    /**
     * Mostrar recogidas por estatus.
     */
    public function recogidasPorEstatus(Request $request)
    {
        $validated = $request->validate([
            'estatus' => 'required|in:pendiente,completa,cancelada',
        ]);

        $recogidas = Recogida::where('recogida_estatus', $validated['estatus'])->with('alumnos')->get();

        if ($recogidas->isEmpty()) {
            return response()->json(['message' => 'No hay recogidas con el estatus especificado'], 200);
        }

        return response()->json(['data' => $recogidas], 200);
    }

    /**
     * Generar reporte en PDF de recogidas por tutor.
     */
    public function generarReportePDF($idTutor)
    {
        $recogidas = Recogida::where('id_tutor', $idTutor)->with('alumnos')->get();

        if ($recogidas->isEmpty()) {
            return response()->json(['message' => 'No hay datos de recogidas para generar el reporte'], 200);
        }

        $pdf = PDF::loadView('reportes.recogidas', ['recogidas' => $recogidas]);
        $filePath = 'reportes/' . uniqid('reporte_') . '.pdf';
        $pdf->save(storage_path('app/public/' . $filePath));

        // Guardar el reporte en la base de datos
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
     * Mostrar reportes generados por tutor.
     */
    public function reportesPorTutor($idTutor)
    {
        $reportes = Reporte::where('cesi_tutore_id', $idTutor)->get();

        if ($reportes->isEmpty()) {
            return response()->json(['message' => 'No hay reportes registrados para este tutor'], 200);
        }

        return response()->json(['data' => $reportes], 200);
    }
}
