<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tutor;
use App\Models\Escuela;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TutorController extends Controller
{
    public function index(Request $request)
    {
        $adminId = Auth::id();
        $escuelas = Escuela::whereHas('administrador', function ($query) use ($adminId) {
            $query->where('cesi_administrador_id', $adminId);
        })->pluck('id');

        $nombre = $request->input('nombre');

        $tutores = Tutor::whereIn('cesi_escuela_id', $escuelas)
            ->when($nombre, function ($query, $nombre) {
                return $query->where('tutor_nombre', 'like', '%' . $nombre . '%');
            })
            ->get();

        return view('tutores.index', compact('tutores'));

    }

    public function create()
    {
        $adminId = Auth::id();
        $escuelas = Escuela::whereHas('administrador', function ($query) use ($adminId) {
            $query->where('cesi_administrador_id', $adminId);
        })->get();

        return view('tutores.create', compact('escuelas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tutor_usuario' => 'required|regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/|unique:cesi_tutores',
            'tutor_contraseña' => [
                'required',
                'string',
                'min:9', 
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/'
            ],
            'tutor_nombre' => 'required|regex:/^[\p{L}\s]+$/u|max:255',
            'tutor_telefono' => 'required|regex:/^[0-9]{10}$/', 
            'tutor_foto' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', 
            'cesi_escuela_id' => 'required|exists:cesi_escuelas,id',
        ], [
            'tutor_usuario.required' => 'El campo correo electrónico es obligatorio.',
            'tutor_usuario.regex' => 'El correo electrónico no tiene un formato válido.',
            'tutor_usuario.unique' => 'El correo electrónico ya está registrado.',
            'tutor_contraseña.required' => 'El campo contraseña es obligatorio.',
            'tutor_contraseña.min' => 'La contraseña debe tener al menos 9 caracteres.', 
            'tutor_contraseña.regex' => 'La contraseña debe contener al menos una mayúscula, una minúscula, un número y un carácter especial (@$!%*?&).',
            'tutor_nombre.required' => 'El campo nombre es obligatorio.',
            'tutor_nombre.regex' => 'El nombre solo puede contener letras.',
            'tutor_nombre.max' => 'El nombre no puede exceder los 255 caracteres.', 
            'tutor_telefono.required' => 'El campo teléfono es obligatorio.',
            'tutor_telefono.regex' => 'El teléfono debe tener 10 dígitos.', 
            'tutor_foto.required' => 'El campo foto es obligatorio.',
            'tutor_foto.image' => 'El archivo debe ser una imagen.',
            'tutor_foto.mimes' => 'La imagen debe ser de tipo jpeg, png, jpg o gif.',
            'tutor_foto.max' => 'La imagen no debe exceder los 2 MB.', 
            'cesi_escuela_id.required' => 'El campo escuela es obligatorio.',
            'cesi_escuela_id.exists' => 'La escuela seleccionada no es válida.',
        ]);

        $data = $request->all();
        $data['tutor_contraseña'] = bcrypt($request->tutor_contraseña); 

        if ($request->hasFile('tutor_foto')) {
            $data['tutor_foto'] = $request->file('tutor_foto')->store('tutores', 'public');
        }

        Tutor::create($data);

        return redirect()->route('tutores.index')->with('success', 'Tutor creado exitosamente.');
    }

    public function show(Tutor $tutor)
    {
        return view('tutores.show', compact('tutor')); 
    }

    public function edit(Tutor $tutor)
    {
        $adminId = Auth::id();
        $escuelas = Escuela::whereHas('administrador', function ($query) use ($adminId) {
            $query->where('cesi_administrador_id', $adminId);
        })->get();

        return view('tutores.edit', compact('tutor', 'escuelas'));
    }

    public function update(Request $request, Tutor $tutor)
    {
        $request->validate([
            'tutor_usuario' => 'required|regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/|unique:cesi_tutores,tutor_usuario,' . $tutor->id,
            'tutor_contraseña' => [
                'nullable',
                'string',
                'min:9', 
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/'
            ],
            'tutor_nombre' => 'required|regex:/^[\p{L}\s]+$/u|max:255',
            'tutor_telefono' => 'required|regex:/^[0-9]{10}$/', 
            'tutor_foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', 
            'cesi_escuela_id' => 'required|exists:cesi_escuelas,id',
        ], [
            'tutor_usuario.required' => 'El campo correo electrónico es obligatorio.',
            'tutor_usuario.regex' => 'El correo electrónico no tiene un formato válido.',
            'tutor_usuario.unique' => 'El correo electrónico ya está registrado.',
            'tutor_contraseña.min' => 'La contraseña debe tener al menos 9 caracteres.', 
            'tutor_contraseña.regex' => 'La contraseña debe contener al menos una mayúscula, una minúscula, un número y un carácter especial (@$!%*?&).',
            'tutor_nombre.required' => 'El campo nombre es obligatorio.',
            'tutor_nombre.regex' => 'El nombre solo puede contener letras.',
            'tutor_nombre.max' => 'El nombre no puede exceder los 255 caracteres.', 
            'tutor_telefono.required' => 'El campo teléfono es obligatorio.',
            'tutor_telefono.regex' => 'El teléfono debe tener 10 dígitos.', 
            'tutor_foto.image' => 'El archivo debe ser una imagen.',
            'tutor_foto.mimes' => 'La imagen debe ser de tipo jpeg, png, jpg o gif.',
            'tutor_foto.max' => 'La imagen no debe exceder los 2 MB.', 
            'cesi_escuela_id.required' => 'El campo escuela es obligatorio.',
            'cesi_escuela_id.exists' => 'La escuela seleccionada no es válida.',
        ]);

        $data = $request->all();

        // Lógica para la contraseña
        if ($request->has('tutor_contraseña') && trim($request->tutor_contraseña) !== '') { 
            $data['tutor_contraseña'] = bcrypt($request->tutor_contraseña);
        } else {
            unset($data['tutor_contraseña']);
        }

        // Lógica para la foto 
        if ($request->hasFile('tutor_foto')) { 
            if ($tutor->tutor_foto) { 
                Storage::delete('public/' . $tutor->tutor_foto); 
            } 
            $data['tutor_foto'] = $request->file('tutor_foto')->store('tutores', 'public'); 
        }

        $tutor->update($data);

        return redirect()->route('tutores.index')->with('success', 'Tutor actualizado exitosamente.');
    }

    public function destroy(Tutor $tutor)
    {
        if ($tutor->tutor_foto) {
            Storage::delete('public/' . $tutor->tutor_foto);
        }

        $tutor->delete();

        return redirect()->route('tutores.index')->with('success', 'Tutor eliminado exitosamente.');
    }
}