<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MotoristasOrdenes extends Model
{
    use HasFactory;
    protected $table = 'motoristas_ordenes';
    public $timestamps = false;
}
