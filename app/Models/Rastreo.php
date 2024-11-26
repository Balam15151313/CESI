<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rastreo extends Model
{
    /** @use HasFactory<\Database\Factories\RastreoFactory> */
    use HasFactory;
    protected $table = 'cesi_rastreos';
    protected $fillable = ['rastreo_longitud',
    'rastreo_latitud',
    'cesi_recogida_id',
    ];


    public function recogidas() {
        return $this->belongsTo(Recogida::class,'cesi_recogida_id');

    }
}
