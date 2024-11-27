<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
/**
 * Archivo: Responsable.php
 * Propósito: Modelo para gestionar datos de los responsables.
 * Autor: José Balam González Rojas
 * Fecha de Creación: 2024-11-06
 * Última Modificación: 2024-11-26 - Añadida validación para evitar duplicados.
 */
class Responsable extends Model
{
    /** @use HasFactory<\Database\Factories\ResponsableFactory> */
    use HasFactory;
    protected $table = 'cesi_responsables';
    protected $fillable = ['responsable_usuario',
    'responsable_contraseña',
    'responsable_nombre',
    'responsable_telefono',
    'responsable_foto',
    'responsable_activacion',
    'cesi_tutore_id',

    ];


    public function tutores(){
        return $this->belongsTo(Tutor::class,  'cesi_tutore_id');
    }

    public function sesiones(){
        return $this->hasOne(Sesion::class,'cesi_responsable_id');
    }
}
