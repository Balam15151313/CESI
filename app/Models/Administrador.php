<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Escuela;

/**
 * Archivo: Administrador.php
 * Propósito: Modelo para gestionar datos de los administradores.
 * Autor: José Balam González Rojas
 * Fecha de Creación: 2024-11-06
 * Última Modificación: 2024-12-04
 */
class Administrador extends Model
{
    /** @use HasFactory<\Database\Factories\AdministradorFactory> */
    use HasFactory;
    protected $table = 'cesi_administradors';
    protected $fillable = [
        'administrador_usuario',
        'administrador_contraseña',
        'administrador_nombre',
        'administrador_telefono',
        'administrador_foto',
    ];

    public function escuela()
    {
        return $this->belongsToMany(Escuela::class, 'cesi_privilegios', 'cesi_administrador_id', 'id');
    }
}
