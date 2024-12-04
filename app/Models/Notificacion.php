<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Archivo: Notificacion.php
 * Propósito: Modelo para gestionar las notificaciones que se tienen entre los maestros y los tutores.
 * Autor: José Balam González Rojas
 * Fecha de Creación: 2024-11-06
 * Última Modificación: 2024-12-04
 */
class Notificacion extends Model
{
    /** @use HasFactory<\Database\Factories\NotificacionFactory> */
    use HasFactory;
    protected $table = 'cesi_notificaciones';
    protected $fillable = [
        'notificaciones_mensaje',
        'notificaciones_prioridad',
        'notificaciones_tipo',
        'cesi_alumno_id',
    ];

    public function alumnos()
    {
        return $this->belongsTo(Alumno::class, 'id');
    }
}
