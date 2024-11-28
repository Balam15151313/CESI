<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alumno;
use App\Models\Salon;
use App\Models\Tutor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

/**
 * Archivo: AC.php
 * Propósito: Controlador para gestionar alumnos.
 * Autor: Alexis Daniel Uribe Oleriano
 * Fecha de Creación: 2024-11-19
 * Última Modificación: 2024-11-27
 */
class AlumnoController extends Controller
{
    // Constantes para mensajes de éxito
    const SUCCESS_CREATED = 'Alumno creado correctamente.';
    const SUCCESS_UPDATED = 'Alumno actualizado correctamente.';
    const SUCCESS_DELETED = 'Alumno eliminado correctamente.';

    /**
     * Muestra la lista de alumnos.
     */
    public function index()
    {
        $alumnos = Alumno::with(['salones', 'tutores'])->get();
        view('alumnos.index', compact('alumnos'));
    }

    /**
     * Muestra el formulario para crear un nuevo alumno.
     */
    public function create()
    {
        $salones = Salon::all();
        $tutores = Tutor::all();
        view('alumnos.create', compact('salones', 'tutores'));
    }

    /**
     * Almacena un nuevo alumno en la base de datos.
     */
    public function store()
    {
        request()->validate([
            'alumno_nombre' => 'required|string|max:255',
            'alumno_nacimiento' => 'required|date',
            'cesi_salon_id' => 'required|exists:cesi_salones,id',
            'cesi_tutor_id' => 'required|exists:cesi_tutores,id',
            'alumno_foto' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'alumno_nombre.required' => 'El nombre del alumno es requerido',
            'alumno_nacimiento.required' => 'La fecha de nacimiento del alumno es requerida',
            'cesi_salon_id.required' => 'El salón es requerido',
            'cesi_salon_id.exists' => 'El salón seleccionado no existe',
            'cesi_tutor_id.required' => 'El tutor es requerido',
            'cesi_tutor_id.exists' => 'El tutor seleccionado no existe',
            'alumno_foto.required' => 'La foto es requerida',
            'alumno_foto.image' => 'El archivo debe ser una imagen.',
            'alumno_foto.mimes' => 'El archivo debe ser de tipo: jpeg, png, jpg, gif.',
            'alumno_foto.max' => 'El archivo no debe pesar más de 2048 kilobytes.',
        ]);

        try {
            $alumno = new Alumno();
            $alumno->alumno_nombre = request('alumno_nombre');
            $alumno->alumno_nacimiento = request('alumno_nacimiento');
            $alumno->cesi_salon_id = request('cesi_salon_id');
            $alumno->cesi_tutor_id = request('cesi_tutor_id');

            if (request()->hasFile('alumno_foto')) {
                $imagePath = Storage::disk('public')->putFile('alumnos', request()->file('alumno_foto'));
                $alumno->alumno_foto = basename($imagePath);
            }

            $alumno->save();

            redirect()->route('alumnos.index')
                ->with('success', self::SUCCESS_CREATED);
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Error al crear alumno: ' . $e->getMessage());
            back()->withErrors(['error' => 'Error al crear alumno: ' . $e->getMessage()]);
            back()->withErrors(['error' => 'Error al crear alumno: ' . $e->getMessage()]);
        }
    }

    /**
     * Muestra los detalles de un alumno.
     */
    public function show(Alumno $alumno)
    {
        $alumno->load('salones', 'tutores');
        view('alumnos.show', compact('alumno'));
    }

    /**
     * Muestra el formulario para editar un alumno existente.
     */
    public function edit(Alumno $alumno)
    {
        $salones = Salon::all();
        $tutores = Tutor::all();
        view('alumnos.edit', compact('alumno', 'salones', 'tutores'));
    }

    /**
     * Actualiza los datos de un alumno en la base de datos.
     */
    public function update(Alumno $alumno)
    {
        request()->validate([
            'alumno_nombre' => 'required|string|max:255',
            'alumno_nacimiento' => 'required|date',
            'cesi_salon_id' => 'required|exists:cesi_salones,id',
            'cesi_tutor_id' => 'required|exists:cesi_tutores,id',
            'alumno_foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'alumno_nombre.required' => 'El nombre del alumno es requerido',
            'alumno_nacimiento.required' => 'La fecha de nacimiento del alumno es requerida',
            'cesi_salon_id.required' => 'El salón es requerido',
            'cesi_salon_id.exists' => 'El salón seleccionado no existe',
            'cesi_tutor_id.required' => 'El tutor es requerido',
            'cesi_tutor_id.exists' => 'El tutor seleccionado no existe',
            'alumno_foto.image' => 'El archivo debe ser una imagen.',
            'alumno_foto.mimes' => 'El archivo debe ser de tipo: jpeg, png, jpg, gif.',
            'alumno_foto.max' => 'El archivo no debe pesar más de 2048 kilobytes.',
        ]);

        try {
            $alumno->alumno_nombre = request('alumno_nombre');
            $alumno->alumno_nacimiento = request('alumno_nacimiento');
            $alumno->cesi_salon_id = request('cesi_salon_id');
            $alumno->cesi_tutor_id = request('cesi_tutor_id');

            if (request()->hasFile('alumno_foto')) {
                if ($alumno->alumno_foto) {
                    Storage::delete('public/alumnos/' . basename($alumno->alumno_foto));
                }
                $imagePath = Storage::disk('public')->putFile('alumnos', request()->file('alumno_foto'));
                $alumno->alumno_foto = basename($imagePath);
            }

            $alumno->save();

            redirect()->route('alumnos.index')
                ->with('success', self::SUCCESS_UPDATED);
        } catch (\Illuminate\Database\QueryException $e) {
            back()->withErrors(['error' => 'Error al actualizar alumno']);
        }
    }

    /**
     * Elimina un alumno de la base de datos.
     */
    public function destroy(Alumno $alumno)
    {
        try {
            if ($alumno->alumno_foto) {
                Storage::delete('public/alumnos/' . basename($alumno->alumno_foto));
            }

            $alumno->delete();

            redirect()->route('alumnos.index')
                ->with('success', self::SUCCESS_DELETED);
        } catch (\Illuminate\Database\QueryException $e) {
            back()->withErrors(['error' => 'Error al eliminar alumno']);
        }
    }
}
