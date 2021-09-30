<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContadorSmsTable extends Migration
{
    /**
     * contar intentos de sms
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contador_sms', function (Blueprint $table) {
            $table->id();
            $table->string('telefono', 20);
            $table->dateTime('fecha');

            //1: pantalla login
            //2: recuperacion de contraseña
            //3: pantalla sms para registrarse (txt reenviar por contador)

            $table->integer('tipo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contador_sms');
    }
}
