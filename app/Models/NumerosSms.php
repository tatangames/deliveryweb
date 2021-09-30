<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NumerosSms extends Model
{
    use HasFactory;
    protected $table = 'numeros_sms';
    public $timestamps = false;
}
