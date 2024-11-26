<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Salon;
use Illuminate\Support\Facades\Auth;
use App\Models\Escuela;
use App\Models\Maestro;
use Illuminate\Http\Request;

class SalonController extends Controller
{
    public function index(Request $request)
    {
        $adminId = Auth::id();

        // Obtener las escuelas asociadas al administrador
        $escuelas = Escuela::whereHas('administrador', function ($query) use ($adminId) {
            $query->where('cesi_administrador_id', $adminId);
        })->get();

        // Obtener los IDs de las escuelas
        $escuelaId = $escuelas->pluck('id');

        // Construir la consulta base
        $query = Salon::with(['escuelas', 'maestros'])
            ->whereIn('cesi_escuela_id', $escuelaId); // Usar whereIn para comparar varios IDs

        // Filtrar por 'grado' si es proporcionado y no es nulo
        if ($request->filled('grado')) {
            $query->where('salon_grado', 'like', '%' . $request->input('grado') . '%');
        }

        // Filtrar por 'grupo' si es proporcionado y no es nulo
        if ($request->filled('grupo')) {
            $query->where('salon_grupo', 'like', '%' . $request->input('grupo') . '%');
        }

        // Solo mostrar los salones que tengan valores no nulos en 'salon_grado' y 'salon_grupo'
        if ($request->has('grado') && $request->input('grado') !== null) {
            $query->whereNotNull('salon_grado');
        }

        if ($request->has('grupo') && $request->input('grupo') !== null) {
            $query->whereNotNull('salon_grupo');
        }

        // Ejecutar la consulta
        $salones = $query->get();

        // Devolver la vista con los resultados
        return view('salones.index', compact('salones'));
    }


    public function create()
    {
        $adminId = Auth::id();
        $escuelas = Escuela::whereHas('administrador', function ($query) use ($adminId) {
            $query->where('cesi_administrador_id', $adminId);
        })->get();
        $escuelaIds = $escuelas->pluck('id');
        $maestros = Maestro::whereIn('cesi_escuela_id', $escuelaIds)->get();

        return view('salones.create', compact('escuelas', 'maestros'));
    }

    public function store(Request $request)
    {
        // Validar con la función de validaciones
        $this->validateRequest($request);

        Salon::create($request->all());

        return redirect()->route('salones.index')->with('success', 'Salón creado exitosamente');
    }

    public function edit(Salon $salon)
    {
        $adminId = Auth::id();
        $escuelas = Escuela::whereHas('administrador', function ($query) use ($adminId) {
            $query->where('cesi_administrador_id', $adminId);
        })->get();

        $escuelaIds = $escuelas->pluck('id');
        $maestros = Maestro::whereIn('cesi_escuela_id', $escuelaIds)->get();

        return view('salones.edit', compact('salon', 'escuelas', 'maestros'));
    }

    public function update(Request $request, Salon $salon)
    {
        // Validar con la función de validaciones
        $this->validateRequest($request);

        $salon->update($request->all());

        return redirect()->route('salones.index')->with('success', 'Salón actualizado exitosamente');
    }

    public function show($id)
    {
        // Buscar el salón junto con el maestro relacionado
        $salon = Salon::with('maestros')->findOrFail($id);

        // Retornar la vista con los datos del salón
        return view('salones.show', compact('salon'));
    }

    public function destroy(Salon $salon)
    {
        $salon->delete();

        return redirect()->route('salones.index')->with('success', 'Salón eliminado exitosamente');
    }

    /**
     * Validar los datos de la solicitud.
     */
    private function validateRequest(Request $request, $salonId = null)
    {
        // Reglas básicas de validación
        $rules = [
            'salon_grado' => ['nullable', 'string', 'max:2'],
            'salon_grupo' => ['nullable', 'string', 'max:2'],
            'cesi_escuela_id' => ['required', 'exists:cesi_escuelas,id'],
            'cesi_maestro_id' => ['required', 'exists:cesi_maestros,id'],
        ];

        // Validación personalizada para evitar duplicados
        $rules['salon_grado'][] = function ($attribute, $value, $fail) use ($request, $salonId) {
            $exists = Salon::where('salon_grado', $request->input('salon_grado'))
                ->where('salon_grupo', $request->input('salon_grupo'))
                ->where('cesi_escuela_id', $request->input('cesi_escuela_id'))
                ->when($salonId, function ($query) use ($salonId) {
                    $query->where('id', '<>', $salonId); // Excluir el salón actual si es una actualización
                })
                ->exists();

            if ($exists) {
                $fail("El grupo {$request->input('salon_grado')}°{$request->input('salon_grupo')} ya está asignado en esta escuela.");
            }
        };

        // Mensajes personalizados
        $messages = [
            'salon_grado.max' => 'El grado no puede tener más de 2 caracteres.',
            'salon_grupo.max' => 'El grupo no puede tener más de 2 caracteres.',
            'cesi_escuela_id.required' => 'La escuela es obligatoria.',
            'cesi_escuela_id.exists' => 'La escuela seleccionada no existe.',
            'cesi_maestro_id.required' => 'El maestro es obligatorio.',
            'cesi_maestro_id.exists' => 'El maestro seleccionado no existe.',
        ];

        // Ejecutar la validación
        $request->validate($rules, $messages);
    }

}
