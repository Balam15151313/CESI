<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Archivo: Pase.php
 * Propósito: Modelo para gestionar el pase de lista generado por los maestros.
 * Autor: José Balam González Rojas
 * Fecha de Creación: 2024-11-06
 * Última Modificación: 2024-12-04
 */
class Pase extends Model
{
    /** @use HasFactory<\Database\Factories\PaseFactory> */
    use HasFactory;
    protected $table = 'cesi_pases';
    protected $fillable = ['pase_status', 'cesi_alumno_id', 'cesi_asistencia_id'];

    public function alumno()
    {
        return $this->belongsTo(Alumno::class, 'id');
    }

    public function asistencia()
    {
        return $this->belongsTo(Asistencia::class, 'id');
    }
}
