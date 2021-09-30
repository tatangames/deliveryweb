<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Propietarios extends Model
{
    use HasFactory;
    protected $table = 'propietarios';
    public $timestamps = false;
}
