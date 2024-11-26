<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pase extends Model
{
    /** @use HasFactory<\Database\Factories\PaseFactory> */
    use HasFactory;
    protected $table= 'cesi_pases';
    protected $fillable = ['pase_estatus',
    'cesi_alumno_id',
    'cesi_asistencia_id',
    ];

}
