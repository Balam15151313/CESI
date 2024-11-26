<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\UI;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Escuela;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // Validación de los datos de inicio de sesión
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);
    
        // Intento de inicio de sesión
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
    
            // Verificar si el usuario tiene el rol de administrador
            if (Auth::user()->role !== 'admin') {
                Auth::logout();
                return redirect('/login')->withErrors(['No tienes acceso a esta aplicación.']);
            }
    
            $adminId = Auth::id();
    
            // Filtrar las escuelas asociadas a ese administrador
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
    
        // Mensaje de error si las credenciales son incorrectas
        return back()->withErrors([
            'email' => 'El correo o la contraseña son incorrectos.',
        ])->onlyInput('email');
    }
    
    

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
