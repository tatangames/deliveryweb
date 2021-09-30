<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MotoristasAsignados extends Model
{
    use HasFactory;
    protected $table = 'motoristas_asignados';
    public $timestamps = false;
}
