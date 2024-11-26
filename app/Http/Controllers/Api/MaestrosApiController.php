<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateMaestroRequest;
use App\Models\Maestro;
use App\Models\Escuela;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class MaestrosApiController extends Controller
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

        return response()->json(['maestros' => $maestros]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $maestro = Maestro::find($id);

        if (!$maestro) {
            return response()->json(['error' => 'Maestro no encontrado'], 404);
        }

        return response()->json(['data' => $maestro], 200);
    }

    /**
     * Store a newly created resource in storage.
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

        return response()->json(['message' => 'Maestro creado exitosamente', 'maestro' => $maestro], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMaestroRequest $request, Maestro $maestro)
    {
        $request->validate($this->validationRules($maestro->id), $this->validationMessages());

        $maestro->maestro_nombre = $request->maestro_nombre;
        $maestro->maestro_usuario = $request->maestro_usuario;

        if ($request->filled('maestro_contraseña')) {
            $maestro->maestro_contraseña = Hash::make($request->maestro_contraseña);
        }

        $maestro->maestro_telefono = $request->maestro_telefono;
        $maestro->cesi_escuela_id = $request->cesi_escuela_id;

        if ($request->hasFile('maestro_foto')) {
            if ($maestro->maestro_foto && Storage::exists('public/' . $maestro->maestro_foto)) {
                Storage::delete('public/' . $maestro->maestro_foto);
            }
            $maestro->maestro_foto = $this->uploadMaestroFoto($request->file('maestro_foto'));
        }

        $user = User::where('email', $maestro->maestro_usuario)->first();
        $user->name = $request->maestro_nombre;
        $user->email = $request->maestro_usuario;
        $user->password = Hash::make($request->maestro_contraseña);
        $user->role = 'maestro';
        $user->save();

        // Guardar cambios
        $maestro->save();

        $maestro->save();

        return response()->json(['message' => 'Maestro actualizado exitosamente', 'maestro' => $maestro]);
    }

    /**
     * Centralized validation rules.
     */
    private function validationRules($maestroId = null)
    {
        return [
            'maestro_nombre' => 'required|string|max:255',
            'maestro_usuario' => 'required|email|unique:cesi_maestros,maestro_usuario' . ($maestroId ? ',' . $maestroId : ''),
            'maestro_contraseña' => 'nullable|string|min:8',
            'maestro_telefono' => 'required|string|max:15',
            'cesi_escuela_id' => 'required|exists:cesi_escuelas,id',
        ];
    }

    /**
     * Centralized validation messages in Spanish.
     */
    private function validationMessages()
    {
        return [
            'maestro_nombre.required' => 'El nombre del maestro es obligatorio.',
            'maestro_nombre.string' => 'El nombre del maestro debe ser una cadena de texto.',
            'maestro_nombre.max' => 'El nombre del maestro no puede exceder los 255 caracteres.',
            'maestro_usuario.required' => 'El correo electrónico del maestro es obligatorio.',
            'maestro_usuario.email' => 'El correo electrónico debe tener un formato válido.',
            'maestro_usuario.unique' => 'El correo electrónico ya está registrado.',
            'maestro_contraseña.required' => 'La contraseña es obligatoria.',
            'maestro_contraseña.string' => 'La contraseña debe ser una cadena de texto.',
            'maestro_contraseña.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'maestro_telefono.required' => 'El teléfono del maestro es obligatorio.',
            'maestro_telefono.string' => 'El teléfono debe ser una cadena de texto.',
            'maestro_telefono.max' => 'El teléfono no puede exceder los 15 caracteres.',
            'cesi_escuela_id.required' => 'La escuela es obligatoria.',
            'cesi_escuela_id.exists' => 'La escuela seleccionada no existe.',
        ];
    }

    /**
     * Handle the upload of the maestro's photo.
     */
    private function uploadMaestroFoto($file)
    {
        return $file->store('maestros', 'public');
    }
}
