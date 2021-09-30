<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContadorSms extends Model
{
    use HasFactory;
    protected $table = 'contador_sms';
    public $timestamps = false;
}
