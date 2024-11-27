<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
/**
 * Archivo: Reporte.php
 * Propósito: Modelo para gestionar datos de los reportes generados.
 * Autor: José Balam González Rojas
 * Fecha de Creación: 2024-11-06
 * Última Modificación: 2024-11-26
 */
class Reporte extends Model
{
    /** @use HasFactory<\Database\Factories\ReporteFactory> */
    use HasFactory;
    protected $table = 'cesi_tutores';
    protected $fillable = ['reporte_pdf',
    'cesi_tutore_id',
    ];


    public function tutores(){
        return $this->belongsTo(Tutor::class,'cesi_tutore_id');
    }
}
