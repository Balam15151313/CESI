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
 * Última Modificación: 2024-11-27
 */
class RegistroApiController extends Controller
{
    /**
     * Registrar un nuevo usuario.
     * Este método valida los datos de la solicitud, crea un nuevo usuario,
     * y luego lo autentica, generando un token de acceso.
     */
    public function register(Request $request)
    {
        $validator = $this->validator($request->all());

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $user = $this->create($request->all());
        } catch (\Exception $e) {
            return response()->json(['error' => 'No se pudo registrar el usuario: ' . $e->getMessage()], 500);
        }

        Auth::login($user);

        $token = $user->createToken('API Token')->plainTextToken;

        return response()->json([
            'message' => 'Registro exitoso',
            'user' => $user,
            'token' => $token
        ], 201);
    }

    /**
     * Validar los datos del registro.
     * Este método valida los datos de la solicitud, incluyendo la verificación del código de acceso.
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
     * Crear el usuario en la base de datos.
     * Este método crea un nuevo registro de usuario en la tabla 'users' y lo vincula con un registro en la tabla 'administradores'.
     */
    protected function create(array $data)
    {
        if ($data['access_code'] !== '78945') {
            throw new \Exception("Código de acceso inválido.");
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'admin',
        ]);

        Administrador::create([
            'user_id' => $user->id,
            'administrador_usuario' => $data['name'],
        ]);

        return $user;
    }
}
