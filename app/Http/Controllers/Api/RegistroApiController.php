<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\Administrador;
/**
 * Archivo: RegistroApiController.php
 * Propósito: Controlador para gestionar datos relacionados con registros.
 * Autor: José Balam González Rojas
 * Fecha de Creación: 2024-11-19
 * Última Modificación: 2024-11-26
 */

class RegistroApiController extends Controller
{
    /**
     * Registrar un nuevo usuario.
     */
    public function register(Request $request)
    {
        // Validación de los datos, incluyendo el código de acceso
        $validator = $this->validator($request->all());

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Crear el usuario después de validar
        try {
            $user = $this->create($request->all());
        } catch (\Exception $e) {
            return response()->json(['error' => 'No se pudo registrar el usuario: ' . $e->getMessage()], 500);
        }

        // Iniciar sesión automáticamente después del registro
        Auth::login($user);

        // Generación de token para autenticación
        $token = $user->createToken('API Token')->plainTextToken;

        return response()->json([
            'message' => 'Registro exitoso',
            'user' => $user,
            'token' => $token
        ], 201);
    }

    /**
     * Validar los datos del registro.
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'access_code' => ['required', 'string', 'in:78945'], // Validación del código de acceso
        ], [
            'access_code.in' => 'Código de acceso inválido para el registro.', // Mensaje de error personalizado
        ]);
    }

    /**
     * Crear el usuario en la base de datos.
     */
    protected function create(array $data)
    {
        // Verifica si el código de acceso es válido
        if ($data['access_code'] !== '78945') {
            throw new \Exception("Código de acceso inválido.");
        }

        // Crear el usuario en la tabla 'users'
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'admin', // Asignar rol de administrador automáticamente
        ]);

        // Crear un registro en la tabla 'administradores' enlazado al usuario
        Administrador::create([
            'user_id' => $user->id, // Asume que tienes una columna user_id en 'administradores' para la relación
            'administrador_usuario' => $data['name'],
            // Agrega otros campos necesarios en 'administradores', como administrador_nombre o administrador_telefono, si existen
        ]);

        return $user;
    }
}
