<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TiposServicio extends Model
{
    use HasFactory;
    protected $table = 'tipos_servicio';
    public $timestamps = false;
}
