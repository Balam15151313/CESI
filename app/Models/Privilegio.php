<?php

// En Privilegio.php (crea este modelo si no existe)
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Archivo: Privilegio.php
 * Propósito: Modelo para gestionar la conexión entre la escuela con el administrador.
 * Autor: José Balam González Rojas
 * Fecha de Creación: 2024-11-06
 * Última Modificación: 2024-12-04
 */
class Privilegio extends Model
{
    use HasFactory;

    protected $table = 'cesi_privilegios';

    protected $fillable = ['cesi_administrador_id', 'cesi_escuela_id'];

    public function administrador()
    {
        return $this->belongsTo(Administrador::class, 'id');
    }

    public function escuela()
    {
        return $this->belongsTo(Escuela::class, 'id');
    }
}
