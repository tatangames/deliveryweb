<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CuponProducto extends Model
{
    use HasFactory;
    protected $table = 'cupon_producto';
    public $timestamps = false;
}
