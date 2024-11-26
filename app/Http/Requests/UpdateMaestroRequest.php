<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMaestroRequest extends FormRequest
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
            'maestro_telefono' => 'required|nullable|string',
        ];
    }

    public function messages():  array{
        return [
            'maestro_nombre.required'=>'El campo nombre de maestro es obligatorio',
            'maestro_usuario.required'=>'El campo usuario de maestro es obligatorio',
            'maestro_usuario.unique'=>'El campo usuario ya existe',
            'maestro_telefono.required'=>'El campo telefono de maestro es obligatorio',
            'maestro_telefono.max'=>'El campo telefono no puede tener mas de 10 caracteres',
        ];

    }
}
