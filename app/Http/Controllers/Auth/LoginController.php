<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Administrador;
use App\Models\UI;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Escuela;
use App\Models\User;

/**
 * Archivo: LoginController.php
 * Propósito: Controlador para gestionar login.
 * Autor: Altair Ricardo Villamares Villegas
 * Fecha de Creación: 2024-11-07
 * Última Modificación: 2024-11-2
 */
class LoginController extends Controller
{
    /**
     * Muestra el formulario de inicio de sesión.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Intenta autenticar al usuario y redirigir según el rol.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => [
                'required',
                'email',
                'max:255',
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/',
            ],
        ], [
            'email.required' => 'El campo correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe ser válido.',
            'email.regex' => 'El correo electrónico ingresado no es válido. Ejemplo: usuario@dominio.com.',
            'email.max' => 'El correo electrónico no debe exceder los 255 caracteres.',

            'password.required' => 'El campo contraseña es obligatorio.',
            'password.string' => 'La contraseña debe ser una cadena de texto.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.regex' => 'La contraseña debe contener al menos una letra mayúscula, una letra minúscula, un número y un carácter especial (@$!%*?&).',
        ]);


        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            if (Auth::user()->role !== 'admin') {
                Auth::logout();
                return redirect('/login')->withErrors(['No tienes acceso a esta aplicación.']);
            }

            $admin = User::find(Auth::id());
            $adminId = Administrador::where('administrador_usuario', $admin->email)->first();
            $escuelas = Escuela::whereHas('administrador', function ($query) use ($adminId) {
                $query->where('cesi_administrador_id', $adminId);
            })->get();

            $escuela = $escuelas->first();

            $ui = $escuela ? UI::where('cesi_escuela_id', $escuela->id)->first() : null;
            if ($ui) {
                session([
                    'ui_color1' => $ui->ui_color1,
                    'ui_color2' => $ui->ui_color2,
                    'ui_color3' => $ui->ui_color3,
                    'escuela_logo' => $escuela->escuela_logo ?? 'imagenes/default_logo.png',
                ]);
            } else {
                session([
                    'ui_color1' => '#ffffff',
                    'ui_color2' => '#000000',
                    'ui_color3' => '#cccccc',
                    'escuela_logo' => 'imagenes/default_logo.png',
                ]);
            }

            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'email' => 'El correo o la contraseña son incorrectos.',
        ])->onlyInput('email');
    }

    /**
     * Cierra la sesión del usuario y redirige al formulario de login.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
