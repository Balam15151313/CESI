<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\EscuelaController;
use App\Http\Controllers\Admin\SalonController;
use App\Http\Controllers\Admin\MaestroController;
use App\Http\Controllers\Admin\ResponsableController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\TutorController;
use App\Http\Controllers\Admin\AlumnoController;
use App\Http\Controllers\Admin\AdminController;

/**
 * Archivo: web.php
 * Propósito: Genera las rutas de la página.
 * Autor: José Balam González Rojas
 * Fecha de Creación: 2024-11-06
 * Última Modificación: 2024-12-01
 */
Route::get('/', function () {
    return view('auth/login');
});


Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('register', [RegisterController::class, 'register']);

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware('auth')
    ->name('dashboard');


Route::get('alumnos', [AlumnoController::class, 'index'])->name('alumnos.index');
Route::get('alumnos/create', [AlumnoController::class, 'create'])->name('alumnos.create');
Route::post('alumnos', [AlumnoController::class, 'store'])->name('alumnos.store');
Route::get('alumnos/{alumno}', [AlumnoController::class, 'show'])->name('alumnos.show');
Route::get('alumnos/{alumno}/edit', [AlumnoController::class, 'edit'])->name('alumnos.edit');
Route::put('alumnos/{alumno}', [AlumnoController::class, 'update'])->name('alumnos.update');
Route::delete('alumnos/{alumno}', [AlumnoController::class, 'destroy'])->name('alumnos.destroy');

Route::get('tutores', [TutorController::class, 'index'])->name('tutores.index');
Route::get('tutores/create', [TutorController::class, 'create'])->name('tutores.create');
Route::post('tutores', [TutorController::class, 'store'])->name('tutores.store');
Route::get('tutores/{tutor}', [TutorController::class, 'show'])->name('tutores.show');
Route::get('tutores/{tutor}/edit', [TutorController::class, 'edit'])->name('tutores.edit');
Route::put('tutores/{tutor}', [TutorController::class, 'update'])->name('tutores.update');
Route::delete('tutores/{tutor}', [TutorController::class, 'destroy'])->name('tutores.destroy');

Route::resource('escuelas', EscuelaController::class);

Route::get('salones', [SalonController::class, 'index'])->name('salones.index');
Route::get('salones/create', [SalonController::class, 'create'])->name('salones.create');
Route::post('salones', [SalonController::class, 'store'])->name('salones.store');
Route::get('salones/{salon}', [SalonController::class, 'show'])->name('salones.show');
Route::get('salones/{salon}/edit', [SalonController::class, 'edit'])->name('salones.edit');
Route::put('salones/{salon}', [SalonController::class, 'update'])->name('salones.update');
Route::delete('salones/{salon}', [SalonController::class, 'destroy'])->name('salones.destroy');

Route::get('maestros', [MaestroController::class, 'index'])->name('maestros.index');
Route::get('maestros/create', [MaestroController::class, 'create'])->name('maestros.create');
Route::post('maestros', [MaestroController::class, 'store'])->name('maestros.store');
Route::get('maestros/{maestro}', [MaestroController::class, 'show'])->name('maestros.show');
Route::get('maestros/{maestro}/edit', [MaestroController::class, 'edit'])->name('maestros.edit');
Route::put('/maestros/{maestro}', [MaestroController::class, 'update'])->name('maestros.update');
Route::delete('maestros/{maestro}', [MaestroController::class, 'destroy'])->name('maestros.destroy');
Route::get('/admin/edit/{id}', [AdminController::class, 'edit'])->name('admin.edit');
Route::put('/admin/update/{id}', [AdminController::class, 'update'])->name('admin.update');
Route::get('responsables', [ResponsableController::class, 'index'])->name('responsables.index');
Route::get('responsables/{responsable}/edit', [ResponsableController::class, 'edit'])->name('responsables.edit');
Route::put('responsables/{responsable}', [ResponsableController::class, 'update'])->name('responsables.update');
Route::get('responsables/activate/{responsable}', [ResponsableController::class, 'activate'])->name('responsables.activate');
Route::get('responsables/delete/{responsable}', [ResponsableController::class, 'delete'])->name('responsables.delete');
Route::get('/responsables/{id}', [ResponsableController::class, 'show'])->name('responsables.show');
