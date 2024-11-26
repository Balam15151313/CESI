<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Administrador;

class Escuela extends Model
{
    /** @use HasFactory<\Database\Factories\EscuelaFactory> */
    use HasFactory;
    protected $table = 'cesi_escuelas';
    protected $fillable = ['escuela_nombre',
    'escuela_escolaridad',
    'escuela_latitud',
    'escuela_longitud',
    'escuela_logo',
    ];

    public function uis(){
        return $this->hasMany(UI::class,'cesi_escuela_id');
    }

    public function salones(){
        return $this->hasMany(Salon::class,'cesi_escuela_id');
    }

    public function tutores(){
        return $this->hasMany(Tutor::class,'cesi_escuela_id');
    }

    public function maestros(){
        return $this->hasMany(Maestro::class,'cesi_escuela_id');
    }
    public function administrador()
    {
        return $this->belongsToMany(Administrador::class, 'cesi_privilegios', 'cesi_escuela_id', 'cesi_administrador_id');
    }

}
