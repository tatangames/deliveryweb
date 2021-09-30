<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdenesCupones extends Model
{
    use HasFactory;
    protected $table = 'ordenes_cupones';
    public $timestamps = false;
}
