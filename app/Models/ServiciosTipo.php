<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiciosTipo extends Model
{
    use HasFactory;
    protected $table = 'servicios_tipo';
    public $timestamps = false;

    protected $fillable = [
        'nombre', 'servicios_id', 'posicion', 'activo',
    ];
}
