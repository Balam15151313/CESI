<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        // Validación de los datos, incluyendo el código de acceso
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

    protected function create(array $data)
    {
        // Crear el usuario en la tabla 'users'
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'admin',
        ]);

        // Crear un registro en la tabla 'administradores' enlazado al usuario
        \App\Models\Administrador::create([
            'user_id' => $user->id, 
            'administrador_usuario' => $data['email'],
            'administrador_contraseña' => Hash::make($data['password']),
            'administrador_nombre' => $data['name']
        ]);

        return $user;
    }
}
