<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
/**
 * Archivo: Maestro.php
 * Propósito: Modelo para gestionar datos de los maestros.
 * Autor: José Balam González Rojas
 * Fecha de Creación: 2024-11-06
 * Última Modificación: 2024-11-26
 */
class Maestro extends Model
{
    /** @use HasFactory<\Database\Factories\MaestroFactory> */
    use HasFactory;

    protected $table = 'cesi_maestros';
    protected $fillable = ['maestro_usuario',
    'maestro_contraseña',
    'maestro_nombre',
    'maestro_telefono',
    'maestro_foto',
    'cesi_escuela_id',];

    public function salones(){
        return $this->hasOne(Salon::class,'cesi_maestro_id');
    }

    public function escuelas(){
        return $this->belongsTo(Escuela::class,'cesi_escuela_id');
    }

    public function listas(){
        return $this->hasMany(Lista::class,'cesi_maestro_id');
    }
}
