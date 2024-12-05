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
use App\Models\User;
use App\Models\Administrador;
use Carbon\Carbon;

/**
 * Archivo: AlumnoController.php
 * Propósito: Controlador para gestionar alumnos.
 * Autor: Alexis Daniel Uribe Oleriano
 * Fecha de Creación: 2024-11-19
 * Última Modificación: 2024-12-05
 */
class AlumnoController extends Controller
{
    /**
     * Muestra una lista de alumnos asociados a las escuelas del administrador actual.
     * Permite filtrar los alumnos por nombre.
     */
    public function index(Request $request)
    {
        $admin = User::find(Auth::id());
        $adminId = Administrador::where('administrador_usuario', $admin->email)->pluck('id')->first();
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


        $escuela = Escuela::whereHas('administrador', function ($query) use ($adminId) {
            $query->where('cesi_administrador_id', $adminId);
        })->get()->first();
        $ui = $escuela ? $escuela->uis->first() : null;

        return view('alumnos.index', compact('alumnos', 'ui', 'escuela'));
    }

    /**
     * Muestra el formulario para crear un nuevo alumno.
     * Proporciona la lista de salones y tutores disponibles para la selección.
     */
    public function create()
    {
        $admin = User::find(Auth::id());
        $adminId = Administrador::where('administrador_usuario', $admin->email)->pluck('id')->first();
        $escuela = Escuela::whereHas('administrador', function ($query) use ($adminId) {
            $query->where('cesi_administrador_id', $adminId);
        })->get()->first();
        if (!$escuela) {
            return redirect()->back()->with('error', 'Genere una escuela primero.');
        }
        $escuelas = Escuela::whereHas('administrador', function ($query) use ($adminId) {
            $query->where('cesi_administrador_id', $adminId);
        })->pluck('id');

        $salones = Salon::whereIn('cesi_escuela_id', $escuelas)->get();
        $tutores = Tutor::whereIn('cesi_escuela_id', $escuelas)->get();
        if ($salones->isEmpty()) {
            return redirect()->back()->with('error', 'Genere al menos un salón primero.');
        }

        if ($tutores->isEmpty()) {
            return redirect()->back()->with('error', 'Genere al menos un tutor primero.');
        }
        $ui = $escuela ? $escuela->uis->first() : null;

        $escolaridad = $escuela->escuela_escolaridad;
        $añoActual = date('Y');

        switch ($escolaridad) {
            case 'Kinder':
                $fechaMinima = ($añoActual - 5) . '-01-01';
                $fechaMaxima = ($añoActual - 3) . '-12-31';
                break;
            case 'Primaria':
                $fechaMinima = ($añoActual - 12) . '-01-01';
                $fechaMaxima = ($añoActual - 6) . '-12-31';
                break;
            case 'Secundaria':
                $fechaMinima = ($añoActual - 15) . '-01-01';
                $fechaMaxima = ($añoActual - 13) . '-12-31';
                break;
            default:
                $fechaMinima = ($añoActual - 15) . '-01-01';
                $fechaMaxima = ($añoActual - 3) . '-12-31';
        }

        return view('alumnos.create', compact('salones', 'tutores', 'escuela', 'ui', 'fechaMinima', 'fechaMaxima'));
    }

    /**
     * Almacena un nuevo alumno en la base de datos.
     * Valida los datos recibidos y guarda la foto del alumno en el almacenamiento público.
     */
    public function store(Request $request)
    {
        $request->validate([
            'alumno_nombre' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/',
            ],
            'alumno_nacimiento' => [
                'required',
                'date',
                'before_or_equal:today',
                function ($attribute, $value, $fail) use ($request) {
                    $this->validateEdad($value, $request->input('cesi_salon_id'), $fail);
                },
            ],
            'alumno_foto' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'cesi_salon_id' => 'required|exists:cesi_salons,id',
            'cesi_tutore_id' => 'required|exists:cesi_tutores,id',
        ], [
            'alumno_nombre.required' => 'El campo nombre es obligatorio.',
            'alumno_nombre.regex' => 'El nombre solo puede contener letras.',
            'alumno_nombre.max' => 'El nombre no puede exceder los 255 caracteres.',
            'alumno_nacimiento.required' => 'El campo fecha de nacimiento es obligatorio.',
            'alumno_nacimiento.before_or_equal' => 'La fecha de nacimiento no puede ser en el futuro.',
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
        $admin = User::find(Auth::id());
        $adminId = Administrador::where('administrador_usuario', $admin->email)->pluck('id')->first();
        $escuela = Escuela::whereHas('administrador', function ($query) use ($adminId) {
            $query->where('cesi_administrador_id', $adminId);
        })->get()->first();
        $ui = $escuela ? $escuela->uis->first() : null;
        return view('alumnos.show', compact('alumno', 'tutor', 'escuela', 'ui'));
    }

    /**
     * Muestra el formulario para editar la información de un alumno.
     * Proporciona la lista de salones y tutores disponibles para la edición.
     */
    public function edit(Alumno $alumno)
    {
        $admin = User::find(Auth::id());
        $adminId = Administrador::where('administrador_usuario', $admin->email)->pluck('id')->first();
        $escuelas = Escuela::whereHas('administrador', function ($query) use ($adminId) {
            $query->where('cesi_administrador_id', $adminId);
        })->pluck('id');

        $salones = Salon::whereIn('cesi_escuela_id', $escuelas)->get();
        $tutores = Tutor::whereIn('cesi_escuela_id', $escuelas)->get();
        $escuela = Escuela::whereHas('administrador', function ($query) use ($adminId) {
            $query->where('cesi_administrador_id', $adminId);
        })->get()->first();
        $ui = $escuela ? $escuela->uis->first() : null;

        $salon = $alumno->salones()->with('escuelas')->first();

        if ($salon && $salon->escuelas) {
            $escolaridad = $salon->escuelas->escuela_escolaridad;
            $añoActual = date('Y');

            switch ($escolaridad) {
                case 'Kinder':
                    $fechaMinima = ($añoActual - 5) . '-01-01';
                    $fechaMaxima = ($añoActual - 3) . '-12-31';
                    break;
                case 'Primaria':
                    $fechaMinima = ($añoActual - 12) . '-01-01';
                    $fechaMaxima = ($añoActual - 6) . '-12-31';
                    break;
                case 'Secundaria':
                    $fechaMinima = ($añoActual - 15) . '-01-01';
                    $fechaMaxima = ($añoActual - 13) . '-12-31';
                    break;
                default:
                    return redirect()->back()->with('error', 'El nivel de escolaridad del alumno no es válido.');
            }

            return view('alumnos.edit', compact('alumno', 'salones', 'tutores', 'escuela', 'ui', 'fechaMinima', 'fechaMaxima'));
        } else {
            return redirect()->back()->with('error', 'No se pudo obtener la información de la escolaridad del alumno.');
        }
    }

    /**
     * Actualiza la información de un alumno existente en la base de datos.
     * Valida los datos recibidos y maneja la actualización de la foto si es necesario.
     */
    public function update(Request $request, Alumno $alumno)
    {
        $request->validate([
            'alumno_nombre' => 'required|regex:/^[\p{L}\s]+$/u|max:255',
            'alumno_nacimiento' => [
                'required',
                'date',
                'before_or_equal:today',
                function ($attribute, $value, $fail) use ($alumno) {
                    $this->validateEdad($value, $alumno->salones->id, $fail);
                },
            ],
            'alumno_foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'cesi_salon_id' => 'required|exists:cesi_salons,id',
            'cesi_tutore_id' => 'required|exists:cesi_tutores,id',
        ], [
            'alumno_nombre.required' => 'El campo nombre es obligatorio.',
            'alumno_nombre.regex' => 'El nombre solo puede contener letras.',
            'alumno_nombre.max' => 'El nombre no puede exceder los 255 caracteres.',
            'alumno_nacimiento.required' => 'El campo fecha de nacimiento es obligatorio.',
            'alumno_nacimiento.before_or_equal' => 'La fecha de nacimiento no puede ser en el futuro.',
            'alumno_nacimiento.after_or_equal' => 'La fecha de nacimiento no puede ser en el pasado.',
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

    /**
     * Valida la edad de un alumno en función del tipo de escolaridad asociado a su salón.
     *
     * @param string $value Fecha de nacimiento del alumno.
     * @param int $salonId ID del salón asociado al alumno.
     * @param \Closure $fail Función de callback para manejar fallos de validación.
     *
     * @return void
     */
    private function validateEdad($value, $salonId, $fail)
    {
        $edad = Carbon::parse($value)->age;
        $salon = Salon::find($salonId);
        if ($salon && $salon->escuelas) {
            $escuela = $salon->escuelas;
            $escolaridad = $salon->escuelas->escuela_escolaridad;

            if ($escolaridad === 'Kinder' && ($edad < 3 || $edad > 5)) {
                $fail('La fecha de nacimiento no corresponde al nivel Kinder.');
            } elseif ($escolaridad === 'Primaria' && ($edad < 6 || $edad > 12)) {
                $fail('La fecha de nacimiento no corresponde al nivel Primaria.');
            } elseif ($escolaridad === 'Secundaria' && ($edad < 13 || $edad > 15)) {
                $fail('La fecha de nacimiento no corresponde al nivel Secundaria.');
            }
        }
    }
}
