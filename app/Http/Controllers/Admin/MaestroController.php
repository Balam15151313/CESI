<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateMaestroRequest;
use App\Models\Maestro;
use Illuminate\Support\Facades\Auth;
use App\Models\Escuela;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

/**
 * Archivo: MaestroController.php
 * Propósito: Controlador para gestionar maestros.
 * Autor: José Balam González Rojas
 * Fecha de Creación: 2024-11-06
 * Última Modificación: 2024-11-26
 */
class MaestroController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $adminId = Auth::id();

        $escuelas = Escuela::whereHas('administrador', function ($query) use ($adminId) {
            $query->where('cesi_administrador_id', $adminId);
        })->pluck('id');

        $nombre = $request->input('nombre');

        $maestros = Maestro::whereIn('cesi_escuela_id', $escuelas)
            ->when($nombre, function ($query, $nombre) {
                return $query->where('maestro_nombre', 'like', '%' . $nombre . '%');
            })
            ->get();

        return view('maestros.index', compact('maestros'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $adminId = Auth::id();
        $escuelas = Escuela::whereHas('administrador', function ($query) use ($adminId) {
            $query->where('cesi_administrador_id', $adminId);
        })->get();
        return view('maestros.create', compact('escuelas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validación
        $request->validate($this->validationRules(), $this->validationMessages());
        // Si la validación pasa, almacenar el maestro
        $maestro = new Maestro();
        $maestro->maestro_nombre = $request->maestro_nombre;
        $maestro->maestro_usuario = $request->maestro_usuario;
        $maestro->maestro_contraseña = Hash::make($request->maestro_contraseña);
        $maestro->maestro_telefono = $request->maestro_telefono;
        $maestro->cesi_escuela_id = $request->cesi_escuela_id;

        if ($request->hasFile('maestro_foto')) {
            $maestro->maestro_foto = $this->uploadMaestroFoto($request->file('maestro_foto'));
        }
        // Crear usuario asociado
        User::create([
            'name' => $request->maestro_nombre,
            'email' => $request->maestro_usuario,
            'password' => Hash::make($request->maestro_contraseña),
            'role' => 'maestro', // Asignar rol de maestro automáticamente
        ]);

        $maestro->save();



        return redirect()->route('maestros.index')->with('success', 'Maestro registrado correctamente');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Maestro $maestro)
    {
        $adminId = Auth::id();
        $escuelas = Escuela::whereHas('administrador', function ($query) use ($adminId) {
            $query->where('cesi_administrador_id', $adminId);
        })->get();
        return view('maestros.edit', compact('maestro', 'escuelas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMaestroRequest $request, Maestro $maestro)
    {
        // Validación para campos editables
        $request->validate($this->validationRules($maestro->id), $this->validationMessages());
        // Actualizar datos del maestro
        $maestro->maestro_nombre = $request->maestro_nombre;
        $maestro->maestro_usuario = $request->maestro_usuario;
        $maestro->maestro_telefono = $request->maestro_telefono;
        $maestro->maestro_contraseña = Hash::make($request->maestro_contraseña);
        $maestro->cesi_escuela_id = $request->cesi_escuela_id;

        // Si hay nueva foto, eliminar la anterior y guardar la nueva
        if ($request->hasFile('maestro_foto')) {
            // Eliminar foto anterior si existe
            if ($maestro->maestro_foto) {
                Storage::disk('public')->delete($maestro->maestro_foto);
            }

            // Subir nueva foto
            $imagePath = $request->file('maestro_foto')->store('maestros', 'public');
            $maestro->maestro_foto = $imagePath;
        }

        // Actualizar usuario asociado
        $user = User::where('email', $maestro->maestro_usuario)->first();
        $user->name = $request->maestro_nombre;
        $user->email = $request->maestro_usuario;
        $user->password = Hash::make($request->maestro_contraseña);
        $user->role = 'maestro';
        $user->save();

        // Guardar cambios
        $maestro->save();

        return redirect()->route('maestros.index')->with('success', 'Maestro actualizado exitosamente');
    }
    public function show($id)
    {
        // Buscar el maestro junto con su salón
        $maestro = Maestro::with('salones')->findOrFail($id);

        // Retornar la vista con los datos del maestro
        return view('maestros.show', compact('maestro'));
    }

    private function uploadMaestroFoto($file)
    {
        return $file->store('maestros', 'public');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Maestro $maestro)
    {
        // Eliminar foto si existe
        if ($maestro->maestro_foto) {
            Storage::disk('public')->delete($maestro->maestro_foto);
        }

        // Eliminar usuario asociado
        $user = User::where('email', $maestro->maestro_usuario)->first();
        $user->delete();

        // Eliminar maestro de la base de datos
        $maestro->delete();

        return redirect()->route('maestros.index')->with('success', 'El maestro se ha eliminado correctamente');
    }

    private function validationRules($maestroId = null)
    {
        $relatedUserId = null;

        // Busca el ID del usuario relacionado solo si se está editando
        if ($maestroId) {
            $maestro = Maestro::find($maestroId);
            $user = User::where('email', $maestro->maestro_usuario)->first();
            $relatedUserId = $user?->id;
        }

        return [
            'maestro_nombre' => 'required|string|max:255',
            'maestro_usuario' => [
                'required',
                'email',
                Rule::unique('cesi_maestros', 'maestro_usuario')->ignore($maestroId), // Ignora el registro actual
                Rule::unique('users', 'email')->ignore($relatedUserId), // Ignora el usuario relacionado
            ],
            'maestro_contraseña' => $maestroId ? 'nullable|string|min:8' : 'required|string|min:8', // Obligatoria solo en creación
            'maestro_telefono' => 'required|string|max:15',
            'cesi_escuela_id' => 'required|exists:cesi_escuelas,id',
        ];
    }

    private function validationMessages()
    {
        return [
            'maestro_nombre.required' => 'El nombre del maestro es obligatorio.',
            'maestro_nombre.string' => 'El nombre del maestro debe ser una cadena de texto.',
            'maestro_nombre.max' => 'El nombre del maestro no puede exceder los 255 caracteres.',

            'maestro_usuario.required' => 'El correo electrónico del maestro es obligatorio.',
            'maestro_usuario.email' => 'El correo electrónico debe tener un formato válido.',
            'maestro_usuario.unique' => 'El correo electrónico del maestro ya está registrado.',

            'maestro_contraseña.required' => 'La contraseña es obligatoria.',
            'maestro_contraseña.string' => 'La contraseña debe ser una cadena de texto.',
            'maestro_contraseña.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'maestro_contraseña.regex' => 'La contraseña debe contener al menos una letra mayúscula, una minúscula y un número.',

            'maestro_telefono.required' => 'El teléfono del maestro es obligatorio.',
            'maestro_telefono.numeric' => 'El teléfono debe contener solo números.',
            'maestro_telefono.digits' => 'El teléfono debe tener exactamente 10 dígitos.',

            'cesi_escuela_id.required' => 'La escuela es obligatoria.',
            'cesi_escuela_id.exists' => 'La escuela seleccionada no existe.',

            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe tener un formato válido.',
            'email.unique' => 'El correo electrónico ya está registrado.',

            'maestro_foto.image' => 'El archivo debe ser una imagen válida.',
            'maestro_foto.mimes' => 'La imagen debe ser de tipo jpeg, png, jpg, gif o svg.',
            'maestro_foto.max' => 'La imagen no debe exceder los 2 MB.',
        ];
    }
}
