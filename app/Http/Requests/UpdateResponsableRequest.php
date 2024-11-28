<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Archivo: UpdateResponsableRequest.php
 * Propósito: Request para reglas al actualizar responsables.
 * Autor: José Balam González Rojas
 * Fecha de Creación: 2024-11-06
 * Última Modificación: 2024-11-27
 */
class UpdateResponsableRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para realizar esta solicitud.
     * En este caso, se retorna 'false', lo que significa que la solicitud no está autorizada.
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Obtiene las reglas de validación que deben aplicarse a la solicitud.
     * Estas reglas determinan qué campos son obligatorios y qué tipo de validaciones deben aplicarse.
     */
    public function rules()
    {
        return [
            'responsable_nombre' => 'required|string|max:255',
            'responsable_usuario' => 'required|email',
            'responsable_contraseña' => 'required|string|min:6',
            'responsable_telefono' => 'required|nullable|string',
            'responsable_foto' => 'required|nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    /**
     * Define los mensajes personalizados para las reglas de validación.
     * Si alguna validación no se cumple, se devolverán estos mensajes específicos.
     */
    public function messages()
    {
        return [
            'responsable_nombre.required' => 'El campo nombre de responsable es obligatorio',
            'responsable_usuario.required' => 'El campo usuario de responsable es obligatorio',
            'responsable_usuario.unique' => 'El campo usuario ya existe',
            'responsable_contraseña.required' => 'El campo contraseña de responsable es obligatorio',
            'responsable_telefono.required' => 'El campo teléfono de responsable es obligatorio',
            'responsable_telefono.max' => 'El campo teléfono no puede tener más de 10 caracteres',
            'responsable_foto.required' => 'El campo foto de responsable es obligatorio',
        ];
    }
}
