<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EtiquetasServicio extends Model
{
    use HasFactory;
    protected $table = 'etiquetas_servicio';
    public $timestamps = false;
}
