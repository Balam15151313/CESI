<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Archivo: UpdateMaestroRequest.php
 * Propósito: Request para reglas al actualizar maestros.
 * Autor: José Balam González Rojas
 * Fecha de Creación: 2024-11-06
 * Última Modificación: 2024-11-27
 */
class UpdateMaestroRequest extends FormRequest
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
            'maestro_telefono' => 'required|nullable|string',
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
            'maestro_telefono.required' => 'El campo teléfono de maestro es obligatorio',
            'maestro_telefono.max' => 'El campo teléfono no puede tener más de 10 caracteres',
        ];
    }
}
