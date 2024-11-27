<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
/**
 * Archivo: LoginApiController.php
 * Propósito: Controlador para gestionar datos relacionados con autenticación y login.
 * Autor: José Balam González Rojas
 * Fecha de Creación: 2024-11-19
 * Última Modificación: 2024-11-26
 */

class LogInApiController extends Controller
{
    /**
     * Iniciar sesión y generar un token.
     */

    public function login(Request $request)
    {
        // Validar las credenciales
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Intentar autenticación
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Credenciales inválidas.',
            ], 401);
        }

        // Obtener el usuario autenticado
        $user = Auth::user();

        // Crear token personal (si usas Sanctum)
        $token = $user->createToken('auth_token')->plainTextToken;

        // Devolver respuesta con token y rol
        return response()->json([
            'message' => 'Inicio de sesión exitoso.',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role, // Asegúrate de que el modelo User tenga el atributo 'role'
            ],
        ], 200);
    }


    /**
     * Cerrar sesión y eliminar el token.
     */
    public function logout(Request $request)
    {
        // Obtener el usuario autenticado
        $user = Auth::user();

        if ($user) {
            // Revocar todos los tokens del usuario
            $user->tokens()->delete();
        }

        // Eliminar la cookie del token
        $cookie = cookie('api_token', '', -1); // Tiempo negativo para eliminar la cookie

        // Cerrar la sesión
        Auth::logout();

        // Responder con mensaje de éxito y eliminar la cookie
        return response()->json(['message' => 'Sesión cerrada exitosamente.'], 200)->cookie($cookie);
    }
}
