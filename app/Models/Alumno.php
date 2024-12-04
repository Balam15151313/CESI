<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Archivo: Alumno.php
 * Propósito: Modelo para gestionar los datos de los alumnos.
 * Autor: José Balam González Rojas
 * Fecha de Creación: 2024-11-06
 * Última Modificación: 2024-12-03
 */
class Alumno extends Model
{
    /** @use HasFactory<\Database\Factories\AlumnoFactory> */
    use HasFactory;
    protected $table = 'cesi_alumnos';
    protected $fillable = [
        'alumno_nombre',
        'alumno_nacimiento',
        'alumno_foto',
        'cesi_salon_id',
        'cesi_tutore_id',
    ];


    public function salones()
    {
        return $this->belongsTo(Salon::class, 'cesi_salon_id');
    }

    public function tutores()
    {
        return $this->belongsTo(Tutor::class, 'cesi_tutore_id');
    }

    public function recogidas()
    {
        return $this->belongsToMany(Recogida::class, 'cesi_escogidos', 'cesi_alumno_id', 'cesi_recogida_id')
            ->withTimestamps();
    }

    public function pases()
    {
        return $this->hasMany(Pase::class, 'cesi_pase_id');
    }
    public function notificaciones()
    {
        return $this->hasMany(Notificacion::class, 'cesi_alumno_id');
    }

    public function asistencias()
    {
        return $this->belongsToMany(Asistencia::class, 'cesi_pases');
    }
}
