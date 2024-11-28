<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Alumno;
use App\Models\Salon;
use App\Models\Tutor;
use App\Models\Escuela;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * Archivo: AlumnoController.php
 * Propósito: Controlador para gestionar alumnos.
 * Autor: Alexis Daniel Uribe Oleriano
 * Fecha de Creación: 2024-11-19
 * Última Modificación: 2024-11-27
 */
class AlumnoController extends Controller
{
    /**
     * Muestra una lista de alumnos asociados a las escuelas del administrador actual.
     * Permite filtrar los alumnos por nombre.
     */
    public function index(Request $request)
    {
        $adminId = Auth::id();
        $escuelas = Escuela::whereHas('administrador', function ($query) use ($adminId) {
            $query->where('cesi_administrador_id', $adminId);
        })->pluck('id');

        $nombre = $request->input('nombre');

        $alumnos = Alumno::with('salones', 'tutores')
            ->whereHas('salones', function ($query) use ($escuelas) {
                $query->whereIn('cesi_escuela_id', $escuelas);
            })
            ->when($nombre, function ($query, $nombre) {
                return $query->where('alumno_nombre', 'like', '%' . $nombre . '%');
            })
            ->get();

        return view('alumnos.index', compact('alumnos'));
    }

    /**
     * Muestra el formulario para crear un nuevo alumno.
     * Proporciona la lista de salones y tutores disponibles para la selección.
     */
    public function create()
    {
        $adminId = Auth::id();
        $escuelas = Escuela::whereHas('administrador', function ($query) use ($adminId) {
            $query->where('cesi_administrador_id', $adminId);
        })->pluck('id');

        $salones = Salon::whereIn('cesi_escuela_id', $escuelas)->get();
        $tutores = Tutor::whereIn('cesi_escuela_id', $escuelas)->get();

        return view('alumnos.create', compact('salones', 'tutores'));
    }

    /**
     * Almacena un nuevo alumno en la base de datos.
     * Valida los datos recibidos y guarda la foto del alumno en el almacenamiento público.
     */
    public function store(Request $request)
    {
        $request->validate([
            'alumno_nombre' => 'required|regex:/^[\p{L}\s]+$/u|max:255',
            'alumno_nacimiento' => 'required|date',
            'alumno_foto' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'cesi_salon_id' => 'required|exists:cesi_salons,id',
            'cesi_tutore_id' => 'required|exists:cesi_tutores,id',
        ], [
            'alumno_nombre.required' => 'El campo nombre es obligatorio.',
            'alumno_nombre.regex' => 'El nombre solo puede contener letras.',
            'alumno_nombre.max' => 'El nombre no puede exceder los 255 caracteres.',
            'alumno_nacimiento.required' => 'El campo fecha de nacimiento es obligatorio.',
            'alumno_foto.required' => 'El campo foto es obligatorio.',
            'alumno_foto.image' => 'El archivo debe ser una imagen.',
            'alumno_foto.mimes' => 'La imagen debe ser de tipo jpeg, png, jpg o gif.',
            'alumno_foto.max' => 'La imagen no debe exceder los 2 MB.',
            'cesi_salon_id.required' => 'El campo salón es obligatorio.',
            'cesi_tutore_id.required' => 'El campo tutor es obligatorio.',
        ]);

        $data = $request->all();

        if ($request->hasFile('alumno_foto')) {
            $data['alumno_foto'] = $request->file('alumno_foto')->store('alumnos', 'public');
        }

        Alumno::create($data);

        return redirect()->route('alumnos.index')->with('success', 'Alumno creado exitosamente.');
    }

    /**
     * Muestra los detalles de un alumno específico junto con su tutor asociado.
     */
    public function show(Alumno $alumno)
    {
        $tutor = $alumno->tutores;

        return view('alumnos.show', compact('alumno', 'tutor'));
    }

    /**
     * Muestra el formulario para editar la información de un alumno.
     * Proporciona la lista de salones y tutores disponibles para la edición.
     */
    public function edit(Alumno $alumno)
    {
        $adminId = Auth::id();
        $escuelas = Escuela::whereHas('administrador', function ($query) use ($adminId) {
            $query->where('cesi_administrador_id', $adminId);
        })->pluck('id');

        $salones = Salon::whereIn('cesi_escuela_id', $escuelas)->get();
        $tutores = Tutor::whereIn('cesi_escuela_id', $escuelas)->get();

        return view('alumnos.edit', compact('alumno', 'salones', 'tutores'));
    }

    /**
     * Actualiza la información de un alumno existente en la base de datos.
     * Valida los datos recibidos y maneja la actualización de la foto si es necesario.
     */
    public function update(Request $request, Alumno $alumno)
    {
        $request->validate([
            'alumno_nombre' => 'required|regex:/^[\p{L}\s]+$/u|max:255',
            'alumno_nacimiento' => 'required|date',
            'alumno_foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'cesi_salon_id' => 'required|exists:cesi_salons,id',
            'cesi_tutore_id' => 'required|exists:cesi_tutores,id',
        ], [
            'alumno_nombre.required' => 'El campo nombre es obligatorio.',
            'alumno_nombre.regex' => 'El nombre solo puede contener letras.',
            'alumno_nombre.max' => 'El nombre no puede exceder los 255 caracteres.',
            'alumno_nacimiento.required' => 'El campo fecha de nacimiento es obligatorio.',
            'alumno_foto.image' => 'El archivo debe ser una imagen.',
            'alumno_foto.mimes' => 'La imagen debe ser de tipo jpeg, png, jpg o gif.',
            'alumno_foto.max' => 'La imagen no debe exceder los 2 MB.',
            'cesi_salon_id.required' => 'El campo salón es obligatorio.',
            'cesi_tutore_id.required' => 'El campo tutor es obligatorio.',
        ]);

        $data = $request->all();

        if ($request->hasFile('alumno_foto')) {
            if ($alumno->alumno_foto) {
                Storage::delete('public/' . $alumno->alumno_foto);
            }
            $data['alumno_foto'] = $request->file('alumno_foto')->store('alumnos', 'public');
        } else {
            unset($data['alumno_foto']);
        }

        $alumno->update($data);

        return redirect()->route('alumnos.index')->with('success', 'Alumno actualizado exitosamente.');
    }

    /**
     * Elimina un alumno de la base de datos.
     * Borra también la foto asociada del almacenamiento, si existe.
     */
    public function destroy(Alumno $alumno)
    {
        if ($alumno->alumno_foto) {
            Storage::delete('public/' . $alumno->alumno_foto);
        }

        $alumno->delete();

        return redirect()->route('alumnos.index')->with('success', 'Alumno eliminado exitosamente.');
    }
}
