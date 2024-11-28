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
 * Última Modificación: 2024-11-27
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'access_code' => ['required', 'string', 'in:78945'],
        ], [
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
