<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Salon;
use Illuminate\Support\Facades\Auth;
use App\Models\Escuela;
use Illuminate\Validation\Rule;
use App\Models\Maestro;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Administrador;

/**
 * Archivo: SalonController.php
 * Propósito: Controlador para gestionar salones.
 * Autor: José Balam González Rojas
 * Fecha de Creación: 2024-11-06
 * Última Modificación: 2024-12-04
 */
class SalonController extends Controller
{
    /**
     * Muestra una lista de salones filtrados por grado, grupo y escuela.
     */
    public function index(Request $request)
    {
        $admin = User::find(Auth::id());
        $adminId = Administrador::where('administrador_usuario', $admin->email)->pluck('id')->first();

        $escuelas = Escuela::whereHas('administrador', function ($query) use ($adminId) {
            $query->where('cesi_administrador_id', $adminId);
        })->get();

        $escuelaId = $escuelas->pluck('id');

        $query = Salon::with(['escuelas', 'maestros'])
            ->whereIn('cesi_escuela_id', $escuelaId);

        if ($request->filled('grado')) {
            $query->where('salon_grado', 'like', '%' . $request->input('grado') . '%');
        }

        if ($request->filled('grupo')) {
            $query->where('salon_grupo', 'like', '%' . $request->input('grupo') . '%');
        }

        if ($request->has('grado') && $request->input('grado') !== null) {
            $query->whereNotNull('salon_grado');
        }

        if ($request->has('grupo') && $request->input('grupo') !== null) {
            $query->whereNotNull('salon_grupo');
        }

        $salones = $query->get();
        $escuela = Escuela::whereHas('administrador', function ($query) use ($adminId) {
            $query->where('cesi_administrador_id', $adminId);
        })->get()->first();
        $ui = $escuela ? $escuela->uis->first() : null;

        return view('salones.index', compact('salones', 'ui', 'escuela'));
    }

    /**
     * Muestra el formulario para crear un nuevo salón.
     */
    public function create()
    {
        $admin = User::find(Auth::id());
        $adminId = Administrador::where('administrador_usuario', $admin->email)->pluck('id')->first();
        $escuelas = Escuela::whereHas('administrador', function ($query) use ($adminId) {
            $query->where('cesi_administrador_id', $adminId);
        })->get();
        $escuelaIds = $escuelas->pluck('id');
        $maestros = Maestro::whereIn('cesi_escuela_id', $escuelaIds)->get();
        $escuela = Escuela::whereHas('administrador', function ($query) use ($adminId) {
            $query->where('cesi_administrador_id', $adminId);
        })->get()->first();
        $ui = $escuela ? $escuela->uis->first() : null;

        return view('salones.create', compact('escuelas', 'maestros', 'ui'));
    }

    /**
     * Almacena un nuevo salón en la base de datos.
     */
    public function store(Request $request)
    {
        $this->validateRequest($request);
        $maestro = Maestro::find($request->input('cesi_maestro_id'));

        if ($maestro && $maestro->salones) {
            return redirect()->back()->with('error', 'Este maestro ya tiene un salón asignado.');
        }
        Salon::create($request->all());

        return redirect()->route('salones.index')->with('success', 'Salón creado exitosamente');
    }

    /**
     * Muestra el formulario para editar un salón existente.
     */
    public function edit(Salon $salon)
    {
        $admin = User::find(Auth::id());
        $adminId = Administrador::where('administrador_usuario', $admin->email)->pluck('id')->first();
        $escuelas = Escuela::whereHas('administrador', function ($query) use ($adminId) {
            $query->where('cesi_administrador_id', $adminId);
        })->get();

        $escuelaIds = $escuelas->pluck('id');
        $maestros = Maestro::whereIn('cesi_escuela_id', $escuelaIds)->get();
        $escuela = Escuela::whereHas('administrador', function ($query) use ($adminId) {
            $query->where('cesi_administrador_id', $adminId);
        })->get()->first();
        $ui = $escuela ? $escuela->uis->first() : null;
        return view('salones.edit', compact('salon', 'escuelas', 'maestros', 'ui'));
    }

    /**
     * Actualiza los datos de un salón existente.
     */
    public function update(Request $request, Salon $salon)
    {
        $this->validateRequest($request, $salon->id);

        $salon->update($request->all());

        return redirect()->route('salones.index')->with('success', 'Salón actualizado exitosamente');
    }

    /**
     * Muestra los detalles de un salón específico.
     */
    public function show($id)
    {
        $admin = User::find(Auth::id());
        $adminId = Administrador::where('administrador_usuario', $admin->email)->pluck('id')->first();
        $escuela = Escuela::whereHas('administrador', function ($query) use ($adminId) {
            $query->where('cesi_administrador_id', $adminId);
        })->get()->first();
        $ui = $escuela ? $escuela->uis->first() : null;
        $salon = Salon::with('maestros')->findOrFail($id);

        return view('salones.show', compact('salon', 'ui', 'escuela'));
    }

    /**
     * Elimina un salón de la base de datos.
     */
    public function destroy(Salon $salon)
    {
        $salon->delete();

        return redirect()->route('salones.index')->with('success', 'Salón eliminado exitosamente');
    }

    /**
     * Valida los datos de la solicitud para la creación o actualización de un salón.
     */
    private function validateRequest(Request $request, $id = null)
    {
        $rules = [
            'salon_grado' => [
                'nullable',
                'string',
                'max:2',
                Rule::unique('cesi_salons', 'salon_grado')
                    ->where(function ($query) use ($request) {
                        return $query->where('salon_grupo', $request->input('salon_grupo'))
                            ->where('cesi_escuela_id', $request->input('cesi_escuela_id'));
                    })
                    ->ignore($id),
            ],

            'salon_grupo' => ['nullable', 'string', 'max:2'],
            'cesi_escuela_id' => ['required', 'exists:cesi_escuelas,id'],
            'cesi_maestro_id' => ['required', 'exists:cesi_maestros,id'],
        ];

        $messages = [
            'salon_grado.unique' => 'El grado y grupo ya están asignados en esta escuela.',
            'salon_grado.max' => 'El grado no puede tener más de 2 caracteres.',
            'salon_grupo.max' => 'El grupo no puede tener más de 2 caracteres.',
            'cesi_escuela_id.required' => 'La escuela es obligatoria.',
            'cesi_escuela_id.exists' => 'La escuela seleccionada no existe.',
            'cesi_maestro_id.required' => 'El maestro es obligatorio.',
            'cesi_maestro_id.exists' => 'El maestro seleccionado no existe.',
        ];

        $request->validate($rules, $messages);
    }
}
