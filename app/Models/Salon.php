<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
/**
 * Archivo: Salon.php
 * Propósito: Modelo para gestionar datos de salones.
 * Autor: José Balam González Rojas
 * Fecha de Creación: 2024-11-06
 * Última Modificación: 2024-11-26 - Añadida validación para evitar duplicados.
 */
class Salon extends Model
{
    /** @use HasFactory<\Database\Factories\SalonFactory> */
    use HasFactory;
    protected $table = 'cesi_salons';
    protected $fillable = ['salon_grado',
    'salon_grupo',
    'cesi_escuela_id',
    'cesi_maestro_id',];


    public function maestros(){
        return $this->belongsTo(Maestro::class,'cesi_maestro_id');
    }

    public function escuelas(){
        return $this->belongsTo(Escuela::class,'cesi_escuela_id');
    }

    public function alumno(){
        return $this->hasMany(Alumno::class,'cesi_alumno_id');
    }
}
