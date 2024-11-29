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
use App\Models\Administrador;

/**
 * Archivo: MaestroController.php
 * Propósito: Controlador para gestionar maestros.
 * Autor: José Balam González Rojas
 * Fecha de Creación: 2024-11-06
 * Última Modificación: 2024-11-28
 */
class MaestroController extends Controller
{
    /**
     * Muestra una lista de los maestros.
     * Obtiene las escuelas asociadas al administrador autenticado y filtra los maestros por nombre si se proporciona.
     */
    public function index(Request $request)
    {
        $admin = User::find(Auth::id());
        $adminId = Administrador::where('administrador_usuario', $admin->email)->pluck('id')->first();

        $escuelas = Escuela::whereHas('administrador', function ($query) use ($adminId) {
            $query->where('cesi_administrador_id', $adminId);
        })->pluck('id');

        $nombre = $request->input('nombre');

        $maestros = Maestro::whereIn('cesi_escuela_id', $escuelas)
            ->when($nombre, function ($query, $nombre) {
                return $query->where('maestro_nombre', 'like', '%' . $nombre . '%');
            })
            ->get();
        $escuela = Escuela::whereHas('administrador', function ($query) use ($admin) {
            $query->where('cesi_administrador_id', $admin->id);
        })->get()->first();


        return view('maestros.index', compact('maestros', 'escuela'));
    }

    /**
     * Muestra el formulario para crear un nuevo maestro.
     * Obtiene las escuelas asociadas al administrador autenticado para la creación de un maestro.
     */
    public function create()
    {
        $admin = User::find(Auth::id());
        $adminId = Administrador::where('administrador_usuario', $admin->email)->pluck('id')->first();
        $escuelas = Escuela::whereHas('administrador', function ($query) use ($adminId) {
            $query->where('cesi_administrador_id', $adminId);
        })->get();
        return view('maestros.create', compact('escuelas'));
    }

    /**
     * Almacena un nuevo maestro en la base de datos.
     * Valida los datos, guarda al maestro y crea el usuario asociado.
     */
    public function store(Request $request)
    {

        $request->validate($this->validationRules(), $this->validationMessages());
        $maestro = new Maestro();
        $maestro->maestro_nombre = $request->maestro_nombre;
        $maestro->maestro_usuario = $request->maestro_usuario;
        $maestro->maestro_contraseña = Hash::make($request->maestro_contraseña);
        $maestro->maestro_telefono = $request->maestro_telefono;
        $maestro->cesi_escuela_id = $request->cesi_escuela_id;

        if ($request->hasFile('maestro_foto')) {
            $maestro->maestro_foto = $this->uploadMaestroFoto($request->file('maestro_foto'));
        }
        User::create([
            'name' => $request->maestro_nombre,
            'email' => $request->maestro_usuario,
            'password' => Hash::make($request->maestro_contraseña),
            'role' => 'maestro',
        ]);

        $maestro->save();



        return redirect()->route('maestros.index')->with('success', 'Maestro registrado correctamente');
    }

    /**
     * Muestra el formulario para editar un maestro existente.
     * Obtiene las escuelas asociadas al administrador autenticado para editar el maestro.
     */
    public function edit(Maestro $maestro)
    {
        $admin = User::find(Auth::id());
        $adminId = Administrador::where('administrador_usuario', $admin->email)->pluck('id')->first();
        $escuelas = Escuela::whereHas('administrador', function ($query) use ($adminId) {
            $query->where('cesi_administrador_id', $adminId);
        })->get();
        return view('maestros.edit', compact('maestro', 'escuelas'));
    }

    /**
     * Actualiza un maestro existente en la base de datos.
     * Valida los campos editables, actualiza la información del maestro y su usuario asociado.
     */
    public function update(UpdateMaestroRequest $request, Maestro $maestro)
    {
        $request->validate($this->validationRules($maestro->id), $this->validationMessages());
        $maestro->maestro_nombre = $request->maestro_nombre;
        $maestro->maestro_usuario = $request->maestro_usuario;
        $maestro->maestro_telefono = $request->maestro_telefono;
        $maestro->maestro_contraseña = Hash::make($request->maestro_contraseña);
        $maestro->cesi_escuela_id = $request->cesi_escuela_id;
        if ($request->hasFile('maestro_foto')) {
            if ($maestro->maestro_foto) {
                Storage::disk('public')->delete($maestro->maestro_foto);
            }

            $imagePath = $request->file('maestro_foto')->store('maestros', 'public');
            $maestro->maestro_foto = $imagePath;
        }

        $user = User::where('email', $maestro->maestro_usuario)->first();
        $user->name = $request->maestro_nombre;
        $user->email = $request->maestro_usuario;
        $user->password = Hash::make($request->maestro_contraseña);
        $user->role = 'maestro';
        $user->save();
        $maestro->save();

        return redirect()->route('maestros.index')->with('success', 'Maestro actualizado exitosamente');
    }

    /**
     * Muestra la información detallada de un maestro.
     * Busca al maestro junto con su salón y muestra los detalles.
     */
    public function show($id)
    {
        $maestro = Maestro::with('salones')->findOrFail($id);
        $admin = User::find(Auth::id());
        $escuela = Escuela::whereHas('administrador', function ($query) use ($admin) {
            $query->where('cesi_administrador_id', $admin->id);
        })->get()->first();

        return view('maestros.show', compact('maestro', 'escuela'));
    }

    /**
     * Sube la foto del maestro al almacenamiento.
     * Almacena la imagen en el disco público.
     */
    private function uploadMaestroFoto($file)
    {
        return $file->store('maestros', 'public');
    }

    /**
     * Elimina un maestro de la base de datos.
     * Elimina la foto asociada y el usuario relacionado antes de eliminar al maestro.
     */
    public function destroy(Maestro $maestro)
    {
        if ($maestro->maestro_foto) {
            Storage::disk('public')->delete($maestro->maestro_foto);
        }

        $user = User::where('email', $maestro->maestro_usuario)->first();
        $user->delete();
        $maestro->delete();

        return redirect()->route('maestros.index')->with('success', 'El maestro se ha eliminado correctamente');
    }

    /**
     * Define las reglas de validación para crear o actualizar un maestro.
     */
    private function validationRules($maestroId = null)
    {
        $relatedUserId = null;
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
                Rule::unique('cesi_maestros', 'maestro_usuario')->ignore($maestroId),
                Rule::unique('users', 'email')->ignore($relatedUserId),
            ],
            'maestro_contraseña' => $maestroId ? 'nullable|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/|min:8' : 'required|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/|min:8',
            'maestro_telefono' => 'required|regex:/^[0-9]$/|digits:10',
            'cesi_escuela_id' => 'required|exists:cesi_escuelas,id',
        ];
    }

    /**
     * Devuelve los mensajes de error para la validación de los campos del maestro.
     */
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

            'maestro_contraseña.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'maestro_contraseña.regex' => 'La contraseña debe contener al menos una mayúscula, una minúscula, un número y un carácter especial (@$!%*?&).',

            'maestro_telefono.required' => 'El teléfono del maestro es obligatorio.',
            'maestro_telefono.digits' => 'El número de teléfono debe contener exactamente 10 dígitos.',
            'maestro_telefono.regex' => 'El número de teléfono debe ser numerico.',

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
