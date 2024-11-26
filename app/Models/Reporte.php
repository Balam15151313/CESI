<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reporte extends Model
{
    /** @use HasFactory<\Database\Factories\ReporteFactory> */
    use HasFactory;
    protected $table = 'cesi_tutores';
    protected $fillable = ['reporte_pdf',
    'cesi_tutore_id',
    ];


    public function tutores(){
        return $this->belongsTo(Tutor::class,'cesi_tutore_id');
    }
}
