<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Archivo: AddMaestroRequest.php
 * Propósito: Request para reglas al crear maestros.
 * Autor: José Balam González Rojas
 * Fecha de Creación: 2024-11-06
 * Última Modificación: 2024-11-27
 */
class AddMaestroRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para realizar esta solicitud.
     * Retorna verdadero si el usuario está autorizado, de lo contrario falso.
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Obtiene las reglas de validación que deben aplicarse a la solicitud.
     * Define los criterios de validación para cada campo de la solicitud.
     */
    public function rules()
    {
        return [
            'maestro_nombre' => 'required|string|max:255',
            'maestro_usuario' => 'required|email',
            'maestro_contraseña' => 'required|string|min:6',
            'maestro_telefono' => 'required|nullable|string',
            'maestro_foto' => 'required|nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    /**
     * Define los mensajes personalizados para las reglas de validación.
     * Estos mensajes se muestran cuando las validaciones no son cumplidas.
     */
    public function messages()
    {
        return [
            'maestro_nombre.required' => 'El campo nombre de maestro es obligatorio',
            'maestro_usuario.required' => 'El campo usuario de maestro es obligatorio',
            'maestro_usuario.unique' => 'El campo usuario ya existe',
            'maestro_contraseña.required' => 'El campo contraseña de maestro es obligatorio',
            'maestro_telefono.required' => 'El campo teléfono de maestro es obligatorio',
            'maestro_telefono.max' => 'El campo teléfono no puede tener más de 10 caracteres',
            'maestro_foto.required' => 'El campo foto de maestro es obligatorio',
        ];
    }
}
