<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Archivo: Tutor.php
 * Propósito: Modelo para gestionar datos de los tutores.
 * Autor: José Balam González Rojas
 * Fecha de Creación: 2024-11-06
 * Última Modificación: 2024-12-03
 */
class Tutor extends Model
{
    /** @use HasFactory<\Database\Factories\TutorFactory> */
    use HasFactory;

    protected $table = 'cesi_tutores';
    protected $fillable = [
        'tutor_usuario',
        'tutor_contraseña',
        'tutor_nombre',
        'tutor_telefono',
        'tutor_foto',
        'cesi_escuela_id',
    ];

    public function alumnos()
    {
        return $this->hasMany(Alumno::class, 'cesi_tutore_id');
    }
    public function escuela()
    {
        return $this->belongsTo(Escuela::class, 'cesi_escuela_id');
    }
    public function responsables()
    {
        return $this->hasMany(Responsable::class, 'cesi_tutore_id');
    }
    public function reportes()
    {
        return $this->hasMany(Reporte::class, 'cesi_tutore_id');
    }
}
