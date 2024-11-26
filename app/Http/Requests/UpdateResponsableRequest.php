<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateResponsableRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'responsable_nombre' => 'required|string|max:255',
            'responsable_usuario' => 'required|email',
            'responsable_contraseña' => 'required|string|min:6',
            'responsable_telefono' => 'required|nullable|string',
            'responsable_foto' => 'required|nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    public function messages():  array{
        return [
            'responsable_nombre.required'=>'El campo nombre de responsable es obligatorio',
            'responsable_usuario.required'=>'El campo usuario de responsable es obligatorio',
            'responsable_usuario.unique'=>'El campo usuario ya existe',
            'responsable_contraseña.required'=>'El campo contraseña de responsable es obligatorio',
            'responsable_telefono.required'=>'El campo telefono de responsable es obligatorio',
            'responsable_telefono.max'=>'El campo telefono no puede tener mas de 10 caracteres',
            'responsable_foto.required'=>'El campo foto de responsable es obligatorio',
        ];

    }
}
