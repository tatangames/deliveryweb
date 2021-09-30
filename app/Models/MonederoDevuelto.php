<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonederoDevuelto extends Model
{
    use HasFactory;
    protected $table = 'monedero_devuelto';
    public $timestamps = false;
}
