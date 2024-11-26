<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddMaestroRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'maestro_nombre' => 'required|string|max:255',
            'maestro_usuario' => 'required|email',
            'maestro_contraseña' => 'required|string|min:6',
            'maestro_telefono' => 'required|nullable|string',
            'maestro_foto' => 'required|nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    public function messages():  array{
        return [
            'maestro_nombre.required'=>'El campo nombre de maestro es obligatorio',
            'maestro_usuario.required'=>'El campo usuario de maestro es obligatorio',
            'maestro_usuario.unique'=>'El campo usuario ya existe',
            'maestro_contraseña.required'=>'El campo contraseña de maestro es obligatorio',
            'maestro_telefono.required'=>'El campo telefono de maestro es obligatorio',
            'maestro_telefono.max'=>'El campo telefono no puede tener mas de 10 caracteres',
            'maestro_foto.required'=>'El campo foto de maestro es obligatorio',
        ];

    }
}
