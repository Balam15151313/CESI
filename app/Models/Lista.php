<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lista extends Model
{
    /** @use HasFactory<\Database\Factories\ListaFactory> */
    use HasFactory;
    protected $table = 'cesi_listas';
    protected $fillable = ['listas_pdf',
    'cesi_maestro_id'
    ];

    public function maestros(){
        return $this->belongsTo(Maestro::class,'cesi_maestro_id');
    }
}
