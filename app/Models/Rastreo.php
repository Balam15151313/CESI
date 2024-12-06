<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Archivo: Rastreo.php
 * Propósito: Modelo para gestionar los datos de las ubicaciones de los tutores.
 * Autor: José Balam González Rojas
 * Fecha de Creación: 2024-11-06
 * Última Modificación: 2024-12-04
 */
class Rastreo extends Model
{
    /** @use HasFactory<\Database\Factories\RastreoFactory> */
    use HasFactory;
    protected $table = 'cesi_rastreos';
    protected $fillable = [
        'rastreo_longitud',
        'rastreo_latitud',
        'cesi_recogida_id',
    ];


    public function recogida()
    {
        return $this->belongsTo(Recogida::class, 'cesi_recogida_id', 'id');
    }
}
