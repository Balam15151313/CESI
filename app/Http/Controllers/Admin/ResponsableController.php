<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Responsable;
use App\Models\Escuela;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Administrador;
use App\Models\Tutor;

/**
 * Archivo: ResponsableController.php
 * Propósito: Controlador para gestionar responsables.
 * Autor: José Balam González Rojas
 * Fecha de Creación: 2024-11-06
 * Última Modificación: 2024-12-03
 * Última Modificación: 2024-12-03
 */
class ResponsableController extends Controller
{
    /**
     * Muestra una lista de responsables activos e inactivos.
     */
    public function index(Request $request)
    {
        $nombre = $request->input('nombre');
        $admin = User::find(Auth::id());
        $adminId = Administrador::where('administrador_usuario', $admin->email)->pluck('id')->first();
        $escuelas = Escuela::whereHas('administrador', function ($query) use ($adminId) {
            $query->where('cesi_administrador_id', $adminId);
        })->pluck('id');

        $responsablesActivos = Responsable::with(['tutores' => function ($query) use ($escuelas) {
            $query->whereIn('cesi_escuela_id', $escuelas);
        }])
            ->where('responsable_activacion', 1)
            ->whereHas('tutores', function ($query) use ($escuelas) {
                $query->whereIn('cesi_escuela_id', $escuelas);
            })
            ->when($nombre, function ($query, $nombre) {
                if (!empty($nombre)) {
                    return $query->where('responsable_nombre', 'like', '%' . $nombre . '%');
                }
            })
            ->get();

        $responsablesInactivos = Responsable::with(['tutores' => function ($query) use ($escuelas) {
            $query->whereIn('cesi_escuela_id', $escuelas);
        }])
            ->where('responsable_activacion', 0)
            ->whereHas('tutores', function ($query) use ($escuelas) {
                $query->whereIn('cesi_escuela_id', $escuelas);
            })
            ->when($nombre, function ($query, $nombre) {
                if (!empty($nombre)) {
                    return $query->where('responsable_nombre', 'like', '%' . $nombre . '%');
                }
            })
            ->get();
        $escuela = Escuela::whereHas('administrador', function ($query) use ($adminId) {
            $query->where('cesi_administrador_id', $adminId);
        })->get()->first();
        $ui = $escuela ? $escuela->uis->first() : null;
        return view('responsables.index', compact('responsablesActivos', 'responsablesInactivos', 'escuela', 'ui'));
    }

    /**
     * Activa un responsable cambiando su estado de activación a 1.
     */
    public function activate(Responsable $responsable)
    {
        $responsable->update(['responsable_activacion' => 1]);
        return redirect()->route('responsables.index')->with('success', 'Responsable activado correctamente.');
    }

    /**
     * Muestra un formulario de creación de un nuevo responsable. Este método está deshabilitado.
     */
    public function create()
    {
        abort(404);
    }

    /**
     * Almacena un nuevo responsable en la base de datos. Este método está deshabilitado.
     */
    public function store(Request $request)
    {
        abort(404);
    }

    /**
     * Muestra los detalles de un responsable y sus tutores asociados.
     */
    public function show($id)
    {
        $admin = User::find(Auth::id());
        $adminId = Administrador::where('administrador_usuario', $admin->email)->pluck('id')->first();
        $escuela = Escuela::whereHas('administrador', function ($query) use ($adminId) {
            $query->where('cesi_administrador_id', $adminId);
        })->get()->first();
        $ui = $escuela ? $escuela->uis->first() : null;
        $responsable = Responsable::with('tutores')->findOrFail($id);

        return view('responsables.show', compact('responsable', 'ui', 'escuela'));
    }

    /**
     * Muestra el formulario para editar un responsable específico.
     */
    public function edit(Responsable $responsable)
    {
        $admin = User::find(Auth::id());
        $adminId = Administrador::where('administrador_usuario', $admin->email)->pluck('id')->first();
        $escuela = Escuela::whereHas('administrador', function ($query) use ($adminId) {
            $query->where('cesi_administrador_id', $adminId);
        })->first();
        $ui = $escuela ? $escuela->uis->first() : null;
        $tutor = Tutor::where('id', $responsable->cesi_tutore_id)->first();

        if ($tutor && $tutor->tutor_usuario === $responsable->responsable_usuario) {
            return redirect()->route('responsables.index')
                ->with('error', 'El responsable está relacionado con un tutor similar. Modifíquelo en tutores.');
        }
        return view('responsables.edit', compact('responsable', 'ui', 'escuela'));
    }

    /**
     * Actualiza los datos de un responsable en la base de datos.
     */
    public function update(Request $request, Responsable $responsable)
    {
        $request->validate($this->validationRules($responsable->id), $this->validationMessages());

        $responsable->responsable_nombre = $request->responsable_nombre;
        $responsable->responsable_usuario = $request->responsable_usuario;

        if ($request->filled('responsable_contraseña')) {
            $responsable->responsable_contraseña = bcrypt($request->responsable_contraseña);
        }

        $responsable->responsable_telefono = $request->responsable_telefono;

        if ($request->hasFile('responsable_foto')) {
            if ($responsable->responsable_foto) {
                $previousPhotoPath = public_path('storage/' . $responsable->responsable_foto);
                if (file_exists($previousPhotoPath)) {
                    unlink($previousPhotoPath);
                }
            }

            $imagePath = $request->file('responsable_foto')->store('responsables', 'public');
            $responsable->responsable_foto = $imagePath;
        }

        $user = User::where('email', $responsable->responsable_usuario)->first();
        $user->name = $request->responsable_nombre;
        $user->email = $request->responsable_usuario;
        if ($request->filled('responsable_contraseña')) {
            $user->password = bcrypt($request->responsable_contraseña);
        }

        $user->role = 'responsable';
        $user->save();

        $responsable->save();

        return redirect()->route('responsables.index')->with('success', 'Responsable actualizado exitosamente');
    }


    /**
     * Define las reglas de validación para la creación o edición de un responsable.
     */
    private function validationRules($responsableId = null)
    {
        $relatedUserId = null;

        if ($responsableId) {
            $responsable = Responsable::find($responsableId);
            $user = User::where('email', $responsable->responsable_usuario)->first();
            $relatedUserId = $user?->id;
        }

        return [
            'responsable_nombre' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/',
            ],
            'responsable_usuario' => [
                'required',
                'email',
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
                Rule::unique('cesi_responsables', 'responsable_usuario')->ignore($responsableId),
                Rule::unique('users', 'email')->ignore($relatedUserId),
            ],
            'responsable_contraseña' => $responsableId
                ? 'nullable|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/|min:8'
                : 'required|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/|min:8',
            'responsable_telefono' => 'required|regex:/^[0-9]+$/|digits:10',
            'responsable_foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    private function validationMessages()
    {
        return [
            'responsable_nombre.required' => 'El nombre del responsable es obligatorio.',
            'responsable_nombre.string' => 'El nombre del responsable debe ser una cadena de texto.',
            'responsable_nombre.max' => 'El nombre del responsable no puede exceder los 255 caracteres.',
            'responsable_nombre.regex' => 'El nombre del responsable solo puede contener letras y espacios.',

            'responsable_usuario.required' => 'El correo electrónico del responsable es obligatorio.',
            'responsable_usuario.unique' => 'Este correo electrónico ya está registrado.',
            'responsable_usuario.email' => 'El correo electrónico ingresado no es válido. Por ejemplo, usa un formato como "usuario@dominio.com".',
            'responsable_usuario.regex' => 'El correo electrónico ingresado no es válido. Por ejemplo, usa un formato como "usuario@dominio.com".',

            'responsable_contraseña.nullable' => 'La contraseña es opcional.',
            'responsable_contraseña.regex' => 'La contraseña debe contener al menos una mayúscula, una minúscula, un número y un carácter especial (@$!%*?&).',
            'responsable_contraseña.min' => 'La contraseña debe tener al menos 8 caracteres.',

            'responsable_telefono.required' => 'El teléfono del responsable es obligatorio.',
            'responsable_telefono.digits' => 'El número de teléfono debe contener exactamente 10 dígitos.',
            'responsable_telefono.regex' => 'El número de teléfono debe ser numérico.',

            'responsable_foto.image' => 'El archivo debe ser una imagen.',
            'responsable_foto.mimes' => 'La imagen debe estar en formato jpeg, png, jpg o gif.',
            'responsable_foto.max' => 'La imagen no puede superar los 2 MB.',
        ];
    }

    /**
     * Elimina un responsable de la base de datos.
     */
    public function delete(Responsable $responsable)
    {
        $user = User::where('email', $responsable->responsable_usuario)->first();;
        $tutor = Tutor::where('id', $responsable->cesi_tutore_id)->first();

        if ($tutor && $tutor->tutor_usuario === $responsable->responsable_usuario) {
            return redirect()->route('responsables.index')
                ->with('error', 'El responsable está relacionado con un tutor similar. Eliminelo en tutores.');
        }
        $user->delete();
        $responsable->delete();
        return redirect()->route('responsables.index')->with('success', 'Responsable eliminado correctamente.');
    }
}
