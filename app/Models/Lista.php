<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Archivo: Lista.php
 * Propósito: Modelo para gestionar datos de las listas que tendrá cada grupo.
 * Autor: José Balam González Rojas
 * Fecha de Creación: 2024-11-06
 * Última Modificación: 2024-12-02
 */
class Lista extends Model
{
    /** @use HasFactory<\Database\Factories\ListaFactory> */
    use HasFactory;
    protected $table = 'cesi_listas';
    protected $fillable = ['listas_pdf', 'cesi_maestro_id'];

    public function maestros()
    {
        return $this->belongsTo(Maestro::class, 'cesi_maestro_id');
    }

    // Relación con Asistencia (opcional, si se requiere)
    public function asistencias()
    {
        return $this->hasMany(Asistencia::class, 'cesi_lista_id');
    }
}
