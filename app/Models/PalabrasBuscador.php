<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PalabrasBuscador extends Model
{
    use HasFactory;
    protected $table = 'palabras_buscador';
    public $timestamps = false;
}
