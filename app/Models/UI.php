<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
/**
 * Archivo: UI.php
 * Propósito: Modelo para gestionar colores de interfaz o ui.
 * Autor: José Balam González Rojas
 * Fecha de Creación: 2024-11-06
 * Última Modificación: 2024-11-26
 */
class UI extends Model
{
    /** @use HasFactory<\Database\Factories\UIFactory> */
    use HasFactory;

    protected $table =  'cesi_uis';

    protected $fillable = ['ui_color1','ui_color2','ui_color3','cesi_escuela_id'];


    public function escuela(){
        return $this->belongsTo(Escuela::class,'cesi_escuela_id');
    }

}

