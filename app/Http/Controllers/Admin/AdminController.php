<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Administrador;
use App\Models\User;
use App\Models\Escuela;
use Illuminate\Validation\Rule;

/**
 * Archivo: AdministradorController.php
 * Propósito: Controlador para gestionar el registro y actualización de administradores.
 * Autor: Altair Ricardo Villamares Villegas
 * Fecha de Creación: 2024-11-26
 * Última Modificación: 2024-11-28
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

        return view('admin.configuraciones', compact('admin', 'escuela'));
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
            'administrador_nombre' => 'required|string|max:255',
            'administrador_usuario' => [
                'required',
                'email',
                Rule::unique('cesi_administradors', 'administrador_usuario')->ignore($adminId),
                Rule::unique('users', 'email')->ignore($relatedUserId),
            ],
            'administrador_telefono' => 'nullable|regex:/^[0-9]{10}$/',
            'administrador_foto' => 'nullable|image|max:2048',
        ], [
            'administrador_nombre.required' => 'El nombre del administrador es obligatorio.',
            'administrador_nombre.string' => 'El nombre del administrador debe ser una cadena de texto.',
            'administrador_nombre.max' => 'El nombre del administrador no puede tener más de 255 caracteres.',

            'administrador_usuario.required' => 'El usuario del administrador es obligatorio.',
            'administrador_usuario.string' => 'El usuario del administrador debe ser una cadena de texto.',
            'administrador_usuario.max' => 'El usuario del administrador no puede tener más de 255 caracteres.',
            'administrador_usuario.unique' => 'El usuario ingresado ya está en uso. Por favor, elige otro.',

            'administrador_telefono.regex' => 'El número de teléfono debe contener entre 10 dígitos.',
            'administrador_telefono.regex' => 'El número de teléfono debe ser numerico.',
            'administrador_telefono.max' => 'El teléfono no puede tener más de 20 caracteres.',

            'administrador_foto.image' => 'El archivo debe ser una imagen.',
            'administrador_foto.max' => 'La imagen no puede exceder 2MB.',
        ]);

        $admin->administrador_nombre = $request->administrador_nombre;
        $admin->administrador_usuario = $request->administrador_usuario;
        $admin->administrador_telefono = $request->administrador_telefono;

        if ($request->hasFile('administrador_foto')) {
            if ($admin->administrador_foto) {
                Storage::delete('public/storage/' . $admin->administrador_foto);
            }
            $path = $request->file('administrador_foto')->store('administradores', 'public');
            $admin->administrador_foto = $path;
        }

        $user = User::where('email', $admin->administrador_usuario)->first();
        $user->name = $request->administrador_nombre;
        $user->email = $request->administrador_usuario;
        $user->role = 'admin';
        $user->save();

        $admin->update();

        return redirect()->route('admin.edit', $admin->id)->with('success', 'Datos actualizados correctamente.');
    }
}
