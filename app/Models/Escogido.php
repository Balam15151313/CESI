<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Archivo: Escogido.php
 * Propósito: Modelo para gestionar los datos de los alumnos escogidos.
 * Autor: José Balam González Rojas
 * Fecha de Creación: 2024-12-01
 * Última Modificación: 2024-12-01
 */

class Escogido extends Pivot
{
    use HasFactory;

    protected $table = 'cesi_escogidos';

    public function alumnos()
    {
        return $this->belongsToMany(Alumno::class, 'cesi_escogidos', 'cesi_recogida_id', 'cesi_alumno_id')
            ->using(Escogido::class)
            ->withTimestamps();
    }
}
