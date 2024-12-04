<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Escuela;
use App\Models\Privilegio;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Administrador;
use App\Models\Maestro;
use App\Models\Responsable;
use App\Models\Tutor;

/**
 * Archivo: RegisterController.php
 * Propósito: Controlador para gestionar registro de escuelas y sus colores.
 * Autor: Altair Ricardo Villamares Villegas
 * Fecha de Creación: 2024-11-07
 * Última Modificación: 2024-12-01
 */
class EscuelaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $admin = User::find(Auth::id());
        $adminId = Administrador::where('administrador_usuario', $admin->email)->pluck('id')->first();

        $escuelas = Escuela::whereHas('administrador', function ($query) use ($adminId) {
            $query->where('cesi_administrador_id', $adminId);
        })->get();
        $escuela = Escuela::whereHas('administrador', function ($query) use ($adminId) {
            $query->where('cesi_administrador_id', $adminId);
        })->get()->first();
        $ui = $escuela ? $escuela->uis->first() :  null;

        return view('escuelas.index', compact('escuelas', 'ui'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $admin = User::find(Auth::id());
        $adminId = Administrador::where('administrador_usuario', $admin->email)->pluck('id')->first();
        $yaTieneEscuela = Privilegio::where('cesi_administrador_id', $adminId)->exists();

        if ($yaTieneEscuela) {
            return redirect()->route('escuelas.index')->with('error', 'Ya has creado una escuela.');
        }
        $escuela = Escuela::whereHas('administrador', function ($query) use ($admin) {
            $query->where('cesi_administrador_id', $admin->id);
        })->get()->first();
        $ui = $escuela ? $escuela->uis->first() :  null;

        return view('escuelas.create', compact('ui', 'escuela'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $admin = User::find(Auth::id());
        $adminId = Administrador::where('administrador_usuario', $admin->email)->pluck('id')->first();
        $adminExists = \App\Models\Administrador::find($adminId);

        if (!$adminExists) {
            return redirect()->route('escuelas.index')->with('error', 'El administrador no existe en el sistema.');
        }
        $yaTieneEscuela = Privilegio::where('cesi_administrador_id', $adminId)->exists();

        if ($yaTieneEscuela) {
            return redirect()->route('escuelas.index')->with('error', 'Ya has creado una escuela.');
        }

        $data = $request->validate([
            'escuela_nombre' => 'required|string|max:255',
            'escuela_escolaridad' => 'required|string|max:255',
            'escuela_latitud' => 'required|string|max:255',
            'escuela_longitud' => 'required|string|max:255',
            'escuela_logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'ui_color1' => 'required|string',
            'ui_color2' => 'required|string',
            'ui_color3' => 'required|string'
        ], [
            'escuela_nombre.required' => 'El nombre de la escuela es obligatorio.',
            'escuela_nombre.string' => 'El nombre de la escuela debe ser una cadena de texto.',
            'escuela_nombre.max' => 'El nombre de la escuela no debe exceder los 255 caracteres.',
            'escuela_escolaridad.required' => 'La escolaridad de la escuela es obligatoria.',
            'escuela_escolaridad.string' => 'La escolaridad debe ser una cadena de texto.',
            'escuela_escolaridad.max' => 'La escolaridad no debe exceder los 255 caracteres.',
            'escuela_latitud.required' => 'La latitud de la escuela es obligatoria.',
            'escuela_latitud.string' => 'La latitud debe ser una cadena de texto.',
            'escuela_latitud.max' => 'La latitud no debe exceder los 255 caracteres.',
            'escuela_longitud.required' => 'La longitud de la escuela es obligatoria.',
            'escuela_longitud.string' => 'La longitud debe ser una cadena de texto.',
            'escuela_longitud.max' => 'La longitud no debe exceder los 255 caracteres.',
            'escuela_logo.image' => 'El logo debe ser una imagen.',
            'escuela_logo.mimes' => 'El logo debe ser un archivo de tipo: jpeg, png, jpg.',
            'escuela_logo.max' => 'El tamaño máximo del logo es de 2MB.',
            'ui_color1.required' => 'El color 1 es obligatorio.',
            'ui_color2.required' => 'El color 2 es obligatorio.',
            'ui_color3.required' => 'El color 3 es obligatorio.',
        ]);

        if ($request->hasFile('escuela_logo')) {
            $data['escuela_logo'] = $request->file('escuela_logo')->store('logos', 'public');
        }
        $escuela = Escuela::create($data);
        \App\Models\UI::create([
            'ui_color1' => $data['ui_color1'],
            'ui_color2' => $data['ui_color2'],
            'ui_color3' => $data['ui_color3'],
            'cesi_escuela_id' => $escuela->id
        ]);
        Privilegio::create([
            'cesi_administrador_id' => $adminId,
            'cesi_escuela_id' => $escuela->id,
        ]);
        session([
            'escuela_logo' => $data['escuela_logo'] ?? 'imagenes/default_logo.png',
            'ui_color1' => $data['ui_color1'],
            'ui_color2' => $data['ui_color2'],
            'ui_color3' => $data['ui_color3']
        ]);

        return redirect()->route('escuelas.index')->with('success', 'Escuela creada correctamente');
    }



    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $escuela = Escuela::findOrFail($id);
        $ui = $escuela ? $escuela->uis->first() :  null;
        return view('escuelas.edit', compact('escuela', 'ui'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'escuela_nombre' => 'required|string|max:255',
            'escuela_escolaridad' => 'required|string|max:255',
            'escuela_latitud' => 'required|string|max:255',
            'escuela_longitud' => 'required|string|max:255',
            'escuela_logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'ui_color1' => 'required|string',
            'ui_color2' => 'required|string',
            'ui_color3' => 'required|string'
        ], [
            'escuela_nombre.required' => 'El nombre de la escuela es obligatorio.',
            'escuela_nombre.string' => 'El nombre de la escuela debe ser una cadena de caracteres.',
            'escuela_nombre.max' => 'El nombre de la escuela no puede exceder los 255 caracteres.',
            'escuela_escolaridad.required' => 'La escolaridad de la escuela es obligatoria.',
            'escuela_escolaridad.string' => 'La escolaridad debe ser una cadena de caracteres.',
            'escuela_escolaridad.max' => 'La escolaridad no puede exceder los 255 caracteres.',
            'escuela_latitud.required' => 'La latitud de la escuela es obligatoria.',
            'escuela_latitud.string' => 'La latitud debe ser una cadena de caracteres.',
            'escuela_latitud.max' => 'La latitud no puede exceder los 255 caracteres.',
            'escuela_longitud.required' => 'La longitud de la escuela es obligatoria.',
            'escuela_longitud.string' => 'La longitud debe ser una cadena de caracteres.',
            'escuela_longitud.max' => 'La longitud no puede exceder los 255 caracteres.',
            'escuela_logo.image' => 'El logo debe ser una imagen.',
            'escuela_logo.mimes' => 'El logo debe ser un archivo de tipo jpeg, png o jpg.',
            'escuela_logo.max' => 'El tamaño máximo del logo es de 2 MB.',
            'ui_color1.required' => 'El color UI primario es obligatorio.',
            'ui_color1.string' => 'El color UI primario debe ser una cadena de caracteres.',
            'ui_color2.required' => 'El color UI secundario es obligatorio.',
            'ui_color2.string' => 'El color UI secundario debe ser una cadena de caracteres.',
            'ui_color3.required' => 'El color UI terciario es obligatorio.',
            'ui_color3.string' => 'El color UI terciario debe ser una cadena de caracteres.'
        ]);

        $escuela = Escuela::findOrFail($id);
        if ($request->hasFile('escuela_logo')) {
            if ($escuela->escuela_logo && Storage::exists('public/' . $escuela->escuela_logo)) {
                Storage::delete('public/' . $escuela->escuela_logo);
            }
            $data['escuela_logo'] = $request->file('escuela_logo')->store('logos', 'public');
        }
        $escuela->update($data);

        $ui = $escuela->uis()->first();
        if ($ui) {
            $ui->update([
                'ui_color1' => $data['ui_color1'],
                'ui_color2' => $data['ui_color2'],
                'ui_color3' => $data['ui_color3']
            ]);
        } else {
            \App\Models\UI::create([
                'ui_color1' => $data['ui_color1'],
                'ui_color2' => $data['ui_color2'],
                'ui_color3' => $data['ui_color3'],
                'cesi_escuela_id' => $escuela->id
            ]);
        }

        session([
            'escuela_logo' => $data['escuela_logo'] ?? session('escuela_logo', 'logos/default_logo.png'),
            'ui_color1' => $data['ui_color1'],
            'ui_color2' => $data['ui_color2'],
            'ui_color3' => $data['ui_color3']
        ]);

        return redirect()->route('escuelas.index')->with('success', 'Escuela actualizada exitosamente');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $escuela = Escuela::findOrFail($id);
        $escuelaId = $escuela->id;
        $tutores = Tutor::where('cesi_escuela_id', $escuelaId)->get();
        foreach ($tutores as $tutor) {
            $user = User::where('email', $tutor->tutor_usuario)->first();
            if ($user) {
                $user->delete();
            }
        }

        $responsables = Responsable::with(['tutores' => function ($query) use ($escuelaId) {
            $query->whereIn('cesi_escuela_id', $escuelaId);
        }]);
        foreach ($responsables as $responsable) {
            $user = User::where('email', $responsable->responsable_usuario)->first();
            if ($user) {
                $user->delete();
            }
        }

        $maestros = Maestro::where('cesi_escuela_id', $escuelaId)->get();
        foreach ($maestros as $maestro) {
            $user = User::where('email', $maestro->maestro_usuario)->first();
            if ($user) {
                $user->delete();
            }
        }

        if ($escuela->escuela_logo && Storage::exists('public/' . $escuela->escuela_logo)) {
            Storage::delete('public/' . $escuela->escuela_logo);
        }
        Privilegio::where('cesi_escuela_id', $escuelaId)->delete();

        $escuela->delete();

        session()->forget(['escuela_logo', 'ui_color1', 'ui_color2', 'ui_color3']);
        return redirect()->route('escuelas.index')->with('success', 'Escuela eliminada exitosamente');
    }
}
