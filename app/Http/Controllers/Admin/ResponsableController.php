<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Responsable;
use App\Models\Escuela;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Archivo: ResponsableController.php
 * Propósito: Controlador para gestionar responsables.
 * Autor: José Balam González Rojas
 * Fecha de Creación: 2024-11-06
 * Última Modificación: 2024-11-26
 */
class ResponsableController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $nombre = $request->input('nombre');
        $adminId = Auth::id();

        // Obtener los IDs de las escuelas asociadas al administrador
        $escuelas = Escuela::whereHas('administrador', function ($query) use ($adminId) {
            $query->where('cesi_administrador_id', $adminId);
        })->pluck('id');

        // Responsables activos
        $responsablesActivos = Responsable::with(['tutores' => function ($query) use ($escuelas) {
            $query->whereIn('cesi_escuela_id', $escuelas); // Usamos whereIn por si hay múltiples escuelas
        }])
        ->where('responsable_activacion', 1)  // Responsables activos
        ->whereHas('tutores', function ($query) use ($escuelas) {
            $query->whereIn('cesi_escuela_id', $escuelas);
        })
        // Solo aplicar filtro de nombre si es diferente de null o vacío
        ->when($nombre, function ($query, $nombre) {
            if (!empty($nombre)) {
                return $query->where('responsable_nombre', 'like', '%' . $nombre . '%');
            }
        })
        ->get();

        // Responsables inactivos
        $responsablesInactivos = Responsable::with(['tutores' => function ($query) use ($escuelas) {
            $query->whereIn('cesi_escuela_id', $escuelas);
        }])
        ->where('responsable_activacion', 0)  // Responsables no activos
        ->whereHas('tutores', function ($query) use ($escuelas) {
            $query->whereIn('cesi_escuela_id', $escuelas);
        })
        // Solo aplicar filtro de nombre si es diferente de null o vacío
        ->when($nombre, function ($query, $nombre) {
            if (!empty($nombre)) {
                return $query->where('responsable_nombre', 'like', '%' . $nombre . '%');
            }
        })
        ->get();

        return view('responsables.index', compact('responsablesActivos', 'responsablesInactivos'));
    }


    public function activate(Responsable $responsable)
    {
        $responsable->update(['responsable_activacion' => 1]);
        return redirect()->route('responsables.index')->with('success', 'Responsable activado correctamente.');
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        abort(404);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        abort(404);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Buscar el responsable junto con su tutor
        $responsable = Responsable::with('tutores')->findOrFail($id);

        // Retornar la vista con los datos
        return view('responsables.show', compact('responsable'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Responsable $responsable)
    {
        return view('responsables.edit', compact('responsable'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Responsable $responsable)
    {
        // Validación de los campos
        $request->validationRules();

        // Actualizar los datos del responsable
        $responsable->responsable_nombre = $request->responsable_nombre;
        $responsable->responsable_usuario = $request->responsable_usuario;
        $responsable->responsable_contraseña = bcrypt($request->responsable_contraseña); // Si la contraseña se recibe
        $responsable->responsable_telefono = $request->responsable_telefono;

        // Manejo de actualización de imagen
        if ($request->hasFile('responsable_foto')) {
            // Si ya existe una foto, eliminarla antes de almacenar la nueva
            if ($responsable->responsable_foto) {
                $previousPhotoPath = public_path('storage/' . $responsable->responsable_foto);
                if (file_exists($previousPhotoPath)) {
                    unlink($previousPhotoPath); // Eliminar foto anterior
                }
            }

            // Almacenar nueva foto
            $imagePath = $request->file('responsable_foto')->store('responsables', 'public');
            $responsable->responsable_foto = $imagePath;
        }

        $user = User::find('email',$responsable->responsable_usuario);
        $user->name = $request->responsable_nombre;
        $user->email = $request->responsable_usuario;
        if ($request->filled('responsable_contraseña')) {
            $user->password = bcrypt($request->responsable_contraseña);
        }
        $user->role = 'responsable';
        $user->save();

        // Guardar los cambios
        $responsable->save();

        return redirect()->route('responsables.index')->with('success', 'Responsable actualizado exitosamente');
    }

    public function validationRules($responsableId = null)
    {
        $relatedUserId = null;

        // Busca el ID del usuario relacionado solo si se está editando
        if ($responsableId) {
            $responsable = Responsable::find($responsableId);
            $user = User::where('email', $responsable->responsable_usuario)->first();
            $relatedUserId = $user?->id;
        }
        return [
            'rules' => [
                'responsable_nombre' => 'required|string|max:255',
                'responsable_usuario' => [
                    'required',
                    'email',
                    Rule::unique('cesi_responsables', 'responsable_usuario')->ignore($responsableId), // Ignora el registro actual
                    Rule::unique('users', 'email')->ignore($relatedUserId), // Ignora el usuario relacionado
                ],
                'responsable_contraseña' => 'nullable|string|min:6',
                'responsable_telefono' => 'nullable|regex:/^[0-9]{10,11}$/',
                'responsable_foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ],
            'messages' => [
                'responsable_nombre.required' => 'El nombre del responsable es obligatorio.',
                'responsable_nombre.string' => 'El nombre del responsable debe ser una cadena de texto.',
                'responsable_nombre.max' => 'El nombre del responsable no puede exceder los 255 caracteres.',

                'responsable_usuario.required' => 'El correo electrónico del responsable es obligatorio.',
                'responsable_usuario.email' => 'El correo electrónico debe tener un formato válido.',
                'responsable_usuario.unique' => 'Este correo electrónico ya está registrado.',

                'responsable_contraseña.nullable' => 'La contraseña es opcional.',
                'responsable_contraseña.string' => 'La contraseña debe ser una cadena de texto.',
                'responsable_contraseña.min' => 'La contraseña debe tener al menos 6 caracteres.',

                'responsable_telefono.regex' => 'El número de teléfono debe contener entre 10 y 11 dígitos.',

                'responsable_foto.image' => 'El archivo debe ser una imagen.',
                'responsable_foto.mimes' => 'La imagen debe estar en formato jpeg, png, jpg o gif.',
                'responsable_foto.max' => 'La imagen no puede superar los 2 MB.',
            ],
        ];
    }
    /**
     * Remove the specified resource from storage.
     */
    public function delete(Responsable $responsable)
    {
        $responsable->delete();
        return redirect()->route('responsables.index')->with('success', 'Responsable eliminado correctamente.');
    }


}
