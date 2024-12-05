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
 * Última Modificación: 2024-12-04
 */
class MaestroController extends Controller
{
    /**
     * Muestra una lista de los maestros.
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
        $escuela = Escuela::whereHas('administrador', function ($query) use ($adminId) {
            $query->where('cesi_administrador_id', $adminId);
        })->get()->first();
        $ui = $escuela ? $escuela->uis->first() : null;

        return view('maestros.index', compact('maestros', 'ui', 'escuela'));
    }

    /**
     * Muestra el formulario para crear un nuevo maestro.
     */
    public function create()
    {
        $admin = User::find(Auth::id());
        $adminId = Administrador::where('administrador_usuario', $admin->email)->pluck('id')->first();
        $escuelas = Escuela::whereHas('administrador', function ($query) use ($adminId) {
            $query->where('cesi_administrador_id', $adminId);
        })->get();
        $escuela = Escuela::whereHas('administrador', function ($query) use ($adminId) {
            $query->where('cesi_administrador_id', $adminId);
        })->get()->first();
        $ui = $escuela ? $escuela->uis->first() : null;
        return view('maestros.create', compact('escuelas', 'ui'));
    }

    /**
     * Almacena un nuevo maestro en la base de datos.
     */
    public function store(Request $request)
    {
        $admin = User::find(Auth::id());
        $administrador = Administrador::where('administrador_usuario', $admin->email)->first();

        if (!$administrador) {
            return redirect()->back()->with('error', 'Administrador no encontrado.');
        }
        $adminDomain = substr(strrchr($admin->email, "@"), 1);
        $request->validate($this->validationRules($adminDomain), $this->validationMessages($adminDomain));
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
     */
    public function edit(Maestro $maestro)
    {
        $admin = User::find(Auth::id());
        $adminId = Administrador::where('administrador_usuario', $admin->email)->pluck('id')->first();
        $escuelas = Escuela::whereHas('administrador', function ($query) use ($adminId) {
            $query->where('cesi_administrador_id', $adminId);
        })->get();
        $escuela = Escuela::whereHas('administrador', function ($query) use ($adminId) {
            $query->where('cesi_administrador_id', $adminId);
        })->get()->first();
        $ui = $escuela ? $escuela->uis->first() : null;
        return view('maestros.edit', compact('maestro', 'escuelas', 'ui'));
    }

    /**
     * Actualiza un maestro existente en la base de datos.
     */
    public function update(UpdateMaestroRequest $request, Maestro $maestro)
    {
        $admin = User::find(Auth::id());
        $administrador = Administrador::where('administrador_usuario', $admin->email)->first();

        if (!$administrador) {
            return redirect()->back()->with('error', 'Administrador no encontrado.');
        }
        $adminDomain = substr(strrchr($admin->email, "@"), 1);
        $request->validate($this->validationRules($adminDomain, $maestro->id), $this->validationMessages($adminDomain));
        $maestro->maestro_nombre = $request->maestro_nombre;
        $maestro->maestro_usuario = $request->maestro_usuario;
        $maestro->maestro_telefono = $request->maestro_telefono;
        if ($request->filled('maestro_contraseña')) {
            $maestro->maestro_contraseña = Hash::make($request->maestro_contraseña);
        }

        $maestro->cesi_escuela_id = $request->cesi_escuela_id;
        if ($request->hasFile('maestro_foto')) {
            if ($maestro->maestro_foto) {
                Storage::disk('public')->delete($maestro->maestro_foto);
            }

            $imagePath = $request->file('maestro_foto')->store('maestros', 'public');
            $maestro->maestro_foto = $imagePath;
        }
        $user = User::where('email', $maestro->maestro_usuario)->first();
        if ($user) {
            $user->name = $request->maestro_nombre;
            $user->email = $request->maestro_usuario;
            if ($request->filled('maestro_contraseña')) {
                $user->password = Hash::make($request->maestro_contraseña);
            }

            $user->role = 'maestro';
            $user->save();
        }
        $maestro->save();

        return redirect()->route('maestros.index')->with('success', 'Maestro actualizado exitosamente');
    }

    /**
     * Muestra la información detallada de un maestro.
     */
    public function show($id)
    {
        $maestro = Maestro::with('salones')->findOrFail($id);
        $admin = User::find(Auth::id());
        $adminId = Administrador::where('administrador_usuario', $admin->email)->pluck('id')->first();
        $escuela = Escuela::whereHas('administrador', function ($query) use ($adminId) {
            $query->where('cesi_administrador_id', $adminId);
        })->get()->first();
        $ui = $escuela ? $escuela->uis->first() : null;

        return view('maestros.show', compact('maestro', 'ui', 'escuela'));
    }

    /**
     * Sube la foto del maestro al almacenamiento.
     */
    private function uploadMaestroFoto($file)
    {
        return $file->store('maestros', 'public');
    }

    /**
     * Elimina un maestro de la base de datos.
     */
    public function destroy(Maestro $maestro)
    {
        if ($maestro->maestro_foto) {
            Storage::disk('public')->delete($maestro->maestro_foto);
        }

        $user = User::where('email', $maestro->maestro_usuario)->first();
        if ($user) {
            $user->delete();
        }

        $maestro->delete();

        return redirect()->route('maestros.index')->with('success', 'El maestro se ha eliminado correctamente');
    }

    /**
     * Define las reglas de validación para crear o actualizar un maestro.
     */
    private function validationRules($adminDomain, $maestroId = null)
    {
        $relatedUserId = null;
        if ($maestroId) {
            $maestro = Maestro::find($maestroId);
            $user = User::where('email', $maestro->maestro_usuario)->first();
            $relatedUserId = $user?->id;
        }

        return [
            'maestro_nombre' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/',
            ],
            'maestro_usuario' => [
                'required',
                'email',
                'regex:/^[a-zA-Z0-9._%+-]+@' . preg_quote($adminDomain, '/') . '$/',
                Rule::unique('cesi_maestros', 'maestro_usuario')->ignore($maestroId),
                Rule::unique('users', 'email')->ignore($relatedUserId),
            ],
            'maestro_contraseña' => $maestroId
                ? 'nullable|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/|min:8'
                : 'required|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/|min:8',
            'maestro_telefono' => 'required|regex:/^[0-9]+$/|digits:10',
            'maestro_foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'cesi_escuela_id' => 'required|exists:cesi_escuelas,id',
        ];
    }

    /**
     * Devuelve los mensajes de error para la validación de los campos del maestro.
     */
    private function validationMessages($adminDomain)
    {
        return [
            'maestro_nombre.required' => 'El nombre del maestro es obligatorio.',
            'maestro_nombre.string' => 'El nombre del maestro debe ser una cadena de texto.',
            'maestro_nombre.max' => 'El nombre del maestro no puede exceder los 255 caracteres.',
            'maestro_nombre.regex' => 'El nombre del maestro solo puede contener letras y espacios.',

            'maestro_usuario.required' => 'El correo electrónico del maestro es obligatorio.',
            'maestro_usuario.unique' => 'El correo electrónico del maestro ya está registrado.',
            'maestro_usuario.regex' => 'El correo electrónico del maestro debe tener el dominio ' . $adminDomain . '.',
            'maestro_usuario.email' => 'El correo electrónico ingresado no es válido. Por favor, usa un formato como "usuario@' . $adminDomain . '".',

            'maestro_contraseña.required' => 'La contraseña es obligatoria.',
            'maestro_contraseña.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'maestro_contraseña.regex' => 'La contraseña debe contener al menos una mayúscula, una minúscula, un número y un carácter especial (@$!%*?&).',

            'maestro_telefono.required' => 'El teléfono del maestro es obligatorio.',
            'maestro_telefono.digits' => 'El número de teléfono debe contener exactamente 10 dígitos.',
            'maestro_telefono.regex' => 'El número de teléfono debe ser numérico.',

            'cesi_escuela_id.required' => 'La escuela es obligatoria.',
            'cesi_escuela_id.exists' => 'La escuela seleccionada no existe.',

            'maestro_foto.image' => 'El archivo debe ser una imagen válida.',
            'maestro_foto.mimes' => 'La imagen debe ser de tipo jpeg, png, jpg, gif o svg.',
            'maestro_foto.max' => 'La imagen no debe exceder los 2 MB.',
        ];
    }
}
