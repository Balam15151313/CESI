<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Archivo: Lista.php
 * Propósito: Modelo para gestionar datos de las listas que tendra cada grupo.
 * Autor: José Balam González Rojas
 * Fecha de Creación: 2024-11-06
 * Última Modificación: 2024-11-26
 */
class Lista extends Model
{
    /** @use HasFactory<\Database\Factories\ListaFactory> */
    use HasFactory;
    protected $table = 'cesi_listas';
    protected $fillable = ['listas_pdf',
    'cesi_maestro_id'
    ];

    public function maestros(){
        return $this->belongsTo(Maestro::class,'cesi_maestro_id');
    }
}
