<?php

// En Privilegio.php (crea este modelo si no existe)
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Privilegio extends Model
{
    use HasFactory;

    protected $table = 'cesi_privilegios';

    protected $fillable = ['cesi_administrador_id', 'cesi_escuela_id'];
}
