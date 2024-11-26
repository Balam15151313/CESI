<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notificacion extends Model
{
    /** @use HasFactory<\Database\Factories\NotificacionFactory> */
    use HasFactory;
    protected $table = 'cesi_notificaciones';
    protected $fillable = ['notificaciones_mensaje',
    'notificaciones_prioridad',
    'notificaciones_tipo',
    'cesi_alumno_id',];

    public function alumnos(){
        return $this->belongsTo(Alumno::class,'cesi_alumno_id');
    }
}
