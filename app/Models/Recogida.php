<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
/**
 * Archivo: Recogida.php
 * Propósito: Modelo para gestionar datos de las recogidas generadas.
 * Autor: José Balam González Rojas
 * Fecha de Creación: 2024-11-06
 * Última Modificación: 2024-11-26
 */
class Recogida extends Model
{
    /** @use HasFactory<\Database\Factories\RecogidaFactory> */
    use HasFactory;
    protected $table = 'cesi_recogidas';
    protected $fillable = ['recogida_fecha',
    'recogida_observaciones',
    'recogida_estatus',
    ];


    protected function alumnos(){
        return $this->belongsToMany(Alumno::class,'cesi_escogidos');
    }
}
