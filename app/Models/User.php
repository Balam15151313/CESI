<?php

// User.php

namespace App\Models;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
/**
 * Archivo: User.php
 * Propósito: Modelo para gestionar datos de los usuarios.
 * Autor: José Balam González Rojas
 * Fecha de Creación: 2024-11-06
 * Última Modificación: 2024-11-26 - Añadida validación para evitar duplicados.
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];


    // Relación con el modelo Administrador
    public function administrador()
    {
        return $this->hasOne(Administrador::class, 'user_id'); // Asegúrate de que 'user_id' sea el campo que conecta al usuario con el administrador
    }
}

