<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CuponEnvio extends Model
{
    use HasFactory;
    protected $table = 'cupon_envio';
    public $timestamps = false;
}
