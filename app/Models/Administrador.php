<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Escuela;

class Administrador extends Model
{
    /** @use HasFactory<\Database\Factories\AdministradorFactory> */
    use HasFactory;
    protected $table='cesi_administradors';
    protected $fillable = [
        'administrador_usuario',
        'administrador_contraseña',
        'administrador_nombre',
        'administrador_telefono',
        'administrador_foto',
    ];

    public function escuela()
    {
        return $this->belongsToMany(Escuela::class, 'cesi_privilegios', 'cesi_administrador_id', 'cesi_escuela_id');
    }
    
}
