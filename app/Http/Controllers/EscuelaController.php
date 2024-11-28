<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Escuela;
use App\Models\Privilegio;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Administrador;

/**
 * Archivo: RegisterController.php
 * Propósito: Controlador para gestionar registro de escuelas y sus colores.
 * Autor: Altair Ricardo Villamares Villegas
 * Fecha de Creación: 2024-11-07
 * Última Modificación: 2024-11-28
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

        return view('escuelas.index', compact('escuelas'));
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

        return view('escuelas.create');
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
        return view('escuelas.edit', compact('escuela'));
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

        if ($escuela->escuela_logo && Storage::exists('public/' . $escuela->escuela_logo)) {
            Storage::delete('public/' . $escuela->escuela_logo);
        }

        $escuela->uis()->delete();

        Privilegio::where('cesi_escuela_id', $escuela->id)->delete();

        $escuela->delete();

        session()->forget(['escuela_logo', 'ui_color1', 'ui_color2', 'ui_color3']);

        return redirect()->route('escuelas.index')->with('success', 'Escuela eliminada exitosamente');
    }
}
