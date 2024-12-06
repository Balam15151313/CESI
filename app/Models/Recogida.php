<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Archivo: Recogida.php
 * Propósito: Modelo para gestionar datos de las recogidas generadas.
 * Autor: José Balam González Rojas
 * Fecha de Creación: 2024-11-06
 * Última Modificación: 2024-12-0
 */
class Recogida extends Model
{
    /** @use HasFactory<\Database\Factories\RecogidaFactory> */
    use HasFactory;
    protected $table = 'cesi_recogidas';

    protected $fillable = [
        'recogida_fecha',
        'recogida_observaciones',
        'recogida_estatus',
        'recogida_qr',
        'cesi_responsable_id'
    ];

    public function responsables()
    {
        return $this->hasOne(Responsable::class,  'id');
    }

    public function alumnos()
    {
        return $this->belongsToMany(Alumno::class, 'cesi_escogidos', 'cesi_recogida_id', 'cesi_alumno_id')
            ->withTimestamps();
    }

    public function rastreo()
    {
        return $this->hasOne(Rastreo::class, 'cesi_recogida_id', 'id');
    }
}
