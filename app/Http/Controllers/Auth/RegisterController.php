<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

/**
 * Archivo: RegisterController.php
 * Propósito: Controlador para gestionar registro de usuarios administradores.
 * Autor: Altair Ricardo Villamares Villegas
 * Fecha de Creación: 2024-11-07
 * Última Modificación: 2024-11-28
 */
class RegisterController extends Controller
{

    /**
     * Muestra el formulario de registro.
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Registra al usuario y lo redirige al dashboard.
     */
    public function register(Request $request)
    {
        $validator = $this->validator($request->all());

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }


        $user = $this->create($request->all());


        Auth::login($user);


        return redirect('/dashboard');
    }

    /**
     * Valida los datos del registro.
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/'],
            'email' => ['required', 'string', 'email', 'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/'],
            'access_code' => ['required', 'string', 'in:78945'],
        ], [
            'name.required' => 'El campo nombre es obligatorio.',
            'name.string' => 'El nombre debe ser una cadena de texto.',
            'name.max' => 'El nombre no debe exceder los 255 caracteres.',
            'name.regex' => 'El nombre solo puede contener letras (incluyendo tildes y la letra ñ) y espacios.',

            'email.required' => 'El campo correo electrónico es obligatorio.',
            'email.string' => 'El correo electrónico debe ser una cadena de texto.',
            'email.email' => 'El correo electrónico debe tener un formato válido.',
            'email.regex' => 'El correo electrónico no es válido. Ejemplo: usuario@dominio.com.',
            'email.max' => 'El correo electrónico no debe exceder los 255 caracteres.',
            'email.unique' => 'El correo electrónico ya está registrado.',

            'password.required' => 'El campo contraseña es obligatorio.',
            'password.string' => 'La contraseña debe ser una cadena de texto.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
            'password.regex' => 'La contraseña debe contener al menos una letra mayúscula, una letra minúscula, un número y un carácter especial (@$!%*?&).',

            'access_code.required' => 'El campo código de acceso es obligatorio.',
            'access_code.string' => 'El código de acceso debe ser una cadena de texto.',
            'access_code.in' => 'Código de acceso inválido para el registro.',
        ]);
    }


    /**
     * Crea un nuevo usuario en la base de datos.
     */
    protected function create(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'admin',
        ]);
        \App\Models\Administrador::create([
            'user_id' => $user->id,
            'administrador_usuario' => $data['email'],
            'administrador_contraseña' => Hash::make($data['password']),
            'administrador_nombre' => $data['name']
        ]);

        return $user;
    }
}
