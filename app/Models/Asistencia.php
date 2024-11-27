<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Archivo: Asistencia.php
 * Propósito: Modelo para gestionar las asistencias que tendran los alumnos.
 * Autor: José Balam González Rojas
 * Fecha de Creación: 2024-11-06
 * Última Modificación: 2024-11-26
 */
class Asistencia extends Model
{
    /** @use HasFactory<\Database\Factories\AsistenciaFactory> */
    use HasFactory;
    protected $table = 'cesi_asistencias';
    protected $fillable = ['asistencia_fecha',
    'asistencia_hora',
    ];

    public function alumnos() {
        return $this->belongsToMany(Alumno::class,'cesi_pases');

    }
}
