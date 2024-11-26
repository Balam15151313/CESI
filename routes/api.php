<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Api\DashboardApiController;
use App\Http\Controllers\Api\MaestrosApiController;
use App\Http\Controllers\Api\ResponsableApiController;
use App\Http\Controllers\Api\RegistroApiController;
use App\Http\Controllers\Api\LogInApiController;
use App\Http\Controllers\Api\SalonApiController;
use App\Http\Controllers\Api\EscuelaApiController;
use App\Http\Controllers\Api\AsistenciasApiController;
use App\Http\Controllers\Api\ListaApiController;
use App\Http\Controllers\Api\NotificacionApiController;
use App\Http\Controllers\Api\PaseApiController;
use App\Http\Controllers\Api\RastreoApiController;
use App\Http\Controllers\Api\RecogidaApiController;
use App\Http\Controllers\Api\SesionApiController;
use Illuminate\Support\Facades\Route;


Route::post('/login', [LogInApiController::class, 'login']);
Route::post('/logout', [LogInApiController::class, 'logout']);
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/register', [RegistroApiController::class, 'register']);
Route::middleware('auth:sanctum')->get('/dashboard/responsables-inactivos', [DashboardApiController::class, 'index']);
Route::post('/asistencias/create', [AsistenciasApiController::class, 'store']);
Route::get('/asistencias/{asistencia}', [AsistenciasApiController::class, 'show']);
Route::put('/asistencias/{asistencia}', [AsistenciasApiController::class, 'update']);
Route::delete('/asistencias/{asistencia}', [AsistenciasApiController::class, 'destroy']);
Route::get('/maestros', [MaestrosApiController::class, 'index']);
Route::post('/maestros', [MaestrosApiController::class, 'store']);
Route::get('/maestros/{maestro}', [MaestrosApiController::class, 'show']);
Route::put('/maestros/{maestro}', [MaestrosApiController::class, 'update']);
Route::delete('/maestros/{maestro}', [MaestrosApiController::class, 'destroy']);
Route::get('/responsables', [ResponsableApiController::class, 'index']); // Listar responsables
Route::post('/responsables', [ResponsableApiController::class, 'store']); // Crear responsable
Route::get('/responsables/{responsable}', [ResponsableApiController::class, 'edit']); // Mostrar un responsable
Route::put('/responsables/{responsable}', [ResponsableApiController::class, 'update']);
Route::get('/responsables/{responsable}', [ResponsableApiController::class, 'show']);  // Actualizar un responsable
Route::delete('/responsables/{responsable}', [ResponsableApiController::class, 'destroy']); // Eliminar un responsable
Route::get('/salones', [SalonApiController::class, 'index']);        // Listar salones
Route::post('/salones', [SalonApiController::class, 'store']);       // Crear salón
Route::get('/salones/{salon}', [SalonApiController::class, 'show']); // Mostrar salón
Route::put('/salones/{salon}', [SalonApiController::class, 'update']); // Actualizar salón
Route::delete('/salones/{salon}', [SalonApiController::class, 'destroy']); // Eliminar salón
Route::get('/escuelas', [EscuelaApiController::class, 'index']);
Route::post('/escuelas', [EscuelaApiController::class, 'create']);
Route::get('/escuelas/{id}', [EscuelaApiController::class, 'show']);
Route::put('/escuelas/{id}', [EscuelaApiController::class, 'update']);
Route::delete('/escuelas/{id}', [EscuelaApiController::class, 'destroy']);
Route::get('listas', [ListaApiController::class, 'index']);
Route::post('listas', [ListaApiController::class, 'create']);
Route::get('listas/{id}', [ListaApiController::class, 'show']);
Route::put('listas/{id}', [ListaApiController::class, 'update']);
Route::delete('listas/{id}', [ListaApiController::class, 'destroy']);
Route::get('notificaciones/{alumnoId}', [NotificacionApiController::class, 'index']);
Route::post('notificaciones/{alumnoId}', [NotificacionApiController::class, 'create']);
Route::get('notificaciones/{alumnoId}/{id}', [NotificacionApiController::class, 'show']);
Route::put('notificaciones/{alumnoId}/{id}', [NotificacionApiController::class, 'update']);
Route::delete('notificaciones/{alumnoId}/{id}', [NotificacionApiController::class, 'destroy']);
Route::get('pases/{alumnoId}', [PaseApiController::class, 'index']);
Route::post('pases/{alumnoId}', [PaseApiController::class, 'create']);
Route::get('pases/{alumnoId}/{id}', [PaseApiController::class, 'show']);
Route::put('pases/{alumnoId}/{id}', [PaseApiController::class, 'update']);
Route::delete('pases/{alumnoId}/{id}', [PaseApiController::class, 'destroy']);
Route::get('rastreos/{recogidaId}', [RastreoApiController::class, 'index']);
Route::post('rastreos/{recogidaId}', [RastreoApiController::class, 'create']);
Route::get('rastreos/{recogidaId}/{id}', [RastreoApiController::class, 'show']);
Route::put('rastreos/{recogidaId}/{id}', [RastreoApiController::class, 'update']);
Route::delete('rastreos/{recogidaId}/{id}', [RastreoApiController::class, 'destroy']);
Route::get('recogidas', [RecogidaApiController::class, 'index']);
Route::post('recogidas', [RecogidaApiController::class, 'create']);
Route::get('recogidas/{id}', [RecogidaApiController::class, 'show']);
Route::put('recogidas/{id}', [RecogidaApiController::class, 'update']);
Route::delete('recogidas/{id}', [RecogidaApiController::class, 'destroy']);
Route::get('recogidas/{id}/alumnos', [RecogidaApiController::class, 'alumnos']);
Route::get('sesiones', [SesionApiController::class, 'index']);
Route::post('sesiones', [SesionApiController::class, 'create']);
Route::get('sesiones/{id}', [SesionApiController::class, 'show']);
Route::put('sesiones/{id}', [SesionApiController::class, 'update']);
Route::delete('sesiones/{id}', [SesionApiController::class, 'destroy']);
Route::get('sesiones/{id}/responsable', [SesionApiController::class, 'responsable']);


