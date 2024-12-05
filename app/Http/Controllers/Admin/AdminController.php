<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Administrador;
use App\Models\User;
use App\Models\Escuela;
use App\Models\Maestro;
use Illuminate\Validation\Rule;

/**
 * Archivo: AdministradorController.php
 * Propósito: Controlador para gestionar el registro y actualización de administradores.
 * Autor: Altair Ricardo Villamares Villegas
 * Fecha de Creación: 2024-11-26
 * Última Modificación: 2024-12-05
 */
class AdminController extends Controller
{
    /**
     * Muestra el formulario de configuración del administrador.
     */
    public function edit($id)
    {
        $admin = Administrador::find($id);
        $escuela = Escuela::whereHas('administrador', function ($query) use ($admin) {
            $query->where('cesi_administrador_id', $admin->id);
        })->get()->first();
        $ui = $escuela ? $escuela->uis->first() :  null;

        return view('admin.configuraciones', compact('admin', 'ui', 'escuela'));
    }

    /**
     * Actualiza los datos del administrador.
     */
    public function update(Request $request, $adminId)
    {
        $admin = Administrador::find($adminId);
        $user = User::where('email', $admin->administrador_usuario)->first();
        $relatedUserId = $user?->id;

        if (!$adminId) {
            return back()->with('error', 'Administrador no encontrado.');
        }

        $request->validate([
            'administrador_nombre' => ['required', 'string', 'max:255', 'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/'],
            'administrador_usuario' => [
                'required',
                'email',
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
                Rule::unique('cesi_administradors', 'administrador_usuario')->ignore($adminId),
                Rule::unique('users', 'email')->ignore($relatedUserId),
            ],
            'administrador_telefono' => 'required|regex:/^[0-9]+$/|digits:10',
            'administrador_foto' => 'nullable|image|max:2048|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'administrador_nombre.required' => 'El nombre del administrador es obligatorio.',
            'administrador_nombre.string' => 'El nombre del administrador debe ser una cadena de texto.',
            'administrador_nombre.max' => 'El nombre del administrador no puede tener más de 255 caracteres.',
            'administrador_nombre.regex' => 'El nombre del administrador solo puede contener letras (incluyendo tildes y la letra ñ) y espacios.',

            'administrador_usuario.required' => 'El usuario del administrador es obligatorio.',
            'administrador_usuario.email' => 'El correo electrónico ingresado no es válido. Por ejemplo, usa un formato como "usuario@dominio.com".',
            'administrador_usuario.regex' => 'El correo electrónico ingresado no es válido. Por ejemplo, usa un formato como "usuario@dominio.com".',
            'administrador_usuario.unique' => 'El correo electrónico ingresado ya está registrado. Por favor, elige otro.',

            'administrador_telefono.required' => 'El teléfono del administrador es obligatorio.',
            'administrador_telefono.regex' => 'El número de teléfono debe ser numérico.',
            'administrador_telefono.digits' => 'El número de teléfono debe contener exactamente 10 dígitos.',
            'administrador_telefono.max' => 'El número de teléfono no puede tener más de 20 caracteres.',

            'administrador_foto.image' => 'El archivo debe ser una imagen.',
            'administrador_foto.max' => 'La imagen no puede exceder los 2MB.',
            'administrador_foto.mimes' => 'La imagen debe ser de tipo jpeg, png, jpg o gif.',
        ]);



        $admin->administrador_nombre = $request->administrador_nombre;
        $admin->administrador_usuario = $request->administrador_usuario;
        $admin->administrador_telefono = $request->administrador_telefono;

        // Extraer el dominio del correo del administrador
        $adminDomain = substr(strrchr($admin->administrador_usuario, "@"), 1);

        // Obtener la escuela asociada al administrador
        $escuela = Escuela::whereHas('administrador', function ($query) use ($adminId) {
            $query->where('cesi_administrador_id', $adminId);
        })->first();

        if ($escuela) {
            $escuelaIds = [$escuela->id];

            $maestros = Maestro::whereIn('cesi_escuela_id', $escuelaIds)->get();

            foreach ($maestros as $maestro) {
                $currentUsername = strstr($maestro->maestro_usuario, '@', true);
                $maestro->maestro_usuario = $currentUsername . '@' . $adminDomain;
                $maestro->save();
            }
        }
        if ($request->hasFile('administrador_foto')) {
            if ($admin->administrador_foto) {
                Storage::delete('public/storage/' . $admin->administrador_foto);
            }
            $path = $request->file('administrador_foto')->store('administradores', 'public');
            $admin->administrador_foto = $path;
        }

        if ($user) {
            $user->name = $request->administrador_nombre;
            $user->email = $request->administrador_usuario;
            $user->role = 'admin';
            $user->save();
        } else {
            return response()->json(['error' => 'Usuario no encontrado.'], 404);
        }

        $admin->update();


        return redirect()->route('admin.edit', $admin->id)->with('success', 'Datos actualizados correctamente.');
    }
}
