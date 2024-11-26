<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asistencia extends Model
{
    /** @use HasFactory<\Database\Factories\AsistenciaFactory> */
    use HasFactory;
    protected $table = 'cesi_asistencias';
    protected $fillable = ['asistencia_fecha',
    'asistencia_hora',
    ];

    public function alumnos() {
        return $this->belongsToMany(Alumno::class,'cesi_pases');

    }
}
