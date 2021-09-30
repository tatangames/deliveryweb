<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InformacionAdmin extends Model
{
    use HasFactory;
    protected $table = 'informacion_admin';
    public $timestamps = false;
}
