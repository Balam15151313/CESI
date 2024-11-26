<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sesion extends Model
{
    /** @use HasFactory<\Database\Factories\SesionFactory> */
    use HasFactory;
    protected $table =  'cesi_sesions';
    protected $fillable = ['sesion_estado',
    'sesion_inicio',
    'sesion_fin',
    'sesion_usuario',
    'cesi_responsable_id',
    ];

    public function responsables(){
        return $this->belongsTo(Responsable::class,'cesi_responsable_id');
    }
}
