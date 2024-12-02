<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Archivo: Asistencia.php
 * Propósito: Modelo para gestionar las asistencias de los alumnos.
 * Autor: José Balam González Rojas
 * Fecha de Creación: 2024-11-06
 * Última Modificación: 2024-12-02
 */
class Asistencia extends Model
{
    /** @use HasFactory<\Database\Factories\AsistenciaFactory> */
    use HasFactory;
    protected $table = 'cesi_asistencias';
    protected $fillable = ['asistencia_fecha', 'asistencia_hora'];

    public function alumnos()
    {
        return $this->belongsToMany(Alumno::class, 'cesi_pases');
    }

    public function pases()
    {
        return $this->hasMany(Pase::class, 'cesi_alumno_id');
    }

    public function lista()
    {
        return $this->belongsTo(Lista::class, 'cesi_lista_id');
    }
}
