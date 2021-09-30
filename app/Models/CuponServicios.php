<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CuponServicios extends Model
{
    use HasFactory;
    protected $table = 'cupon_servicios';
    public $timestamps = false;
}
