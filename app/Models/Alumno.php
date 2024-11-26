<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alumno extends Model
{
    /** @use HasFactory<\Database\Factories\AlumnoFactory> */
    use HasFactory;
    protected $table = 'cesi_alumnos';
    protected $fillable = ['alumno_nombre',
    'alumno_nacimiento',
    'alumno_foto',
    'cesi_salon_id',
    'cesi_tutore_id',
    ];


    public function salones(){
        return $this->belongsTo(Salon::class,'cesi_salon_id');
    }

    public function tutores(){
        return $this->belongsTo(Tutor::class,'cesi_tutore_id');
    }

    public function recogidas(){
        return $this->belongsToMany(Recogida::class,'cesi_escogidos');
    }

    public function notificaciones(){
        return $this->hasMany(Notificacion::class,'cesi_alumno_id');
    }

    public function asistencias() {
        return $this->belongsToMany(Asistencia::class,'cesi_pases');

    }
}
